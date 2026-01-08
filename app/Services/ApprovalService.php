<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ApprovalService
{
    /**
     * Get the next approval step based on current status.
     * Smart logic: Skip step 3 if coordinator (step 1) is same person as chairman (step 3)
     */
    public function getNextStep(Model $record, int $currentStep): ?int
    {
        // For PR and Leave Request only (not Handover which has different flow)
        if (
            $record instanceof \App\Models\PurchaseRequisition ||
            $record instanceof \App\Models\LeaveRequest
        ) {

            // Check if we should skip step 3 (chairman) because coordinator = chairman
            if ($currentStep === 2) {
                $type = $record instanceof \App\Models\PurchaseRequisition ?
                    'purchase_requisition' : 'leave_request';

                // Get coordinator (step 1) info
                $coordinator = \App\Services\ConfigService::getApproverForStep(
                    $type,
                    1,
                    $record->department
                );

                // Get chairman (step 3) info
                $chairman = \App\Services\ConfigService::getChairman();

                // If coordinator email matches chairman email, skip step 3
                if (
                    $coordinator && $chairman &&
                    strtolower($coordinator['email']) === strtolower($chairman['email'])
                ) {
                    return null; // No next step - go straight to approved
                }
            }
        }

        // Normal logic for other cases
        return $currentStep < 3 ? $currentStep + 1 : null;
    }

    /**
     * Check if the current user is authorized to approve the current step.
     */
    public function canApprove(Model $record): bool
    {
        $user = Auth::user();
        if (!$user)
            return false;

        // In a real app, check roles. For verified flexibility based on legacy:
        // Step 1 (Koordinator): Any admin or specific role
        // Step 2 (Accounting): 'accounting' role or admin
        // Step 3 (Ketua Yayasan): 'superadmin' or specific role

        // For now, allow logged-in admins to approve for development speed, 
        // but ideally check against $user->role

        // Example role check mapping:
        $requiredRole = match ($record->current_approval_step) {
            1 => ['admin', 'superadmin', 'koordinator'],
            2 => ['admin', 'superadmin', 'accounting'],
            3 => ['admin', 'superadmin', 'ketua_yayasan'],
            default => []
        };

        return in_array($user->role, $requiredRole) || $user->role === 'superadmin';
    }

    /**
     * Approve the record and advance the step.
     */
    public function approve(Model $record, ?string $signatureData = null, ?string $newPrNumber = null): void
    {
        $currentStep = $record->current_approval_step;

        // Handle PR Number Update (for Purchase Requisitions at step 2 - Accounting)
        if ($newPrNumber && $record instanceof \App\Models\PurchaseRequisition && $currentStep === 2) {
            $oldPrNumber = $record->pr_number;
            $record->pr_number = $newPrNumber;

            // Log the change
            \Illuminate\Support\Facades\Log::info("PR Number changed by accounting", [
                'pr_id' => $record->id,
                'old_number' => $oldPrNumber,
                'new_number' => $newPrNumber,
                'approver' => \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::user()->email : 'Email Approver',
            ]);
        }

        // Record the approval in the related approval table if exists
        if (method_exists($record, 'approvals')) {
            $role = 'unknown';

            if ($record instanceof \App\Models\HandoverForm) {
                $role = match ($currentStep) {
                    1 => 'recipient',
                    2 => 'koordinator',
                    3 => 'hrd',
                    default => 'unknown'
                };
            } else {
                // Default for PR and Leave Request
                $role = match ($currentStep) {
                    1 => 'koordinator',
                    2 => 'accounting',
                    3 => 'ketua_yayasan',
                    default => 'unknown'
                };

                // Adjust for LeaveRequest specific roles
                if ($record instanceof \App\Models\LeaveRequest) {
                    $role = match ($currentStep) {
                        1 => 'koordinator',
                        2 => 'hrd',
                        default => 'unknown'
                    };
                }
            }

            $approverName = Auth::check() ? (Auth::user()->name ?? Auth::user()->username) : 'Guest Approver';
            $approverEmail = Auth::check() ? Auth::user()->email : 'guest@email.com';

            // Try to resolve name from ConfigService if Guest
            if (!Auth::check()) {
                $department = $record->department ?? null;
                $config = \App\Services\ConfigService::getApproverForStep(
                    $record instanceof \App\Models\PurchaseRequisition ? 'purchase_requisition' :
                    ($record instanceof \App\Models\LeaveRequest ? 'leave_request' : 'handover_form'),
                    $currentStep,
                    $department
                );
                if ($config) {
                    $approverName = $config['name'] . " (via Email)";
                    $approverEmail = $config['email'];
                }
            }

            // Handle Signature Image
            $signaturePath = Auth::check() ? (Auth::user()->signature_path ?? null) : null;

            if ($signatureData) {
                // Decode Base64
                $image = str_replace('data:image/png;base64,', '', $signatureData);
                $image = str_replace(' ', '+', $image);
                $imageName = 'signatures/' . uniqid() . '.png';
                \Illuminate\Support\Facades\Storage::disk('public')->put($imageName, base64_decode($image));
                $signaturePath = $imageName;
            }

            $record->approvals()->updateOrCreate(
                ['role' => $role],
                [
                    'approver_name' => $approverName,
                    'approver_email' => $approverEmail,
                    'approved_at' => now(),
                    'signature_path' => $signaturePath,
                    'approval_order' => $currentStep
                ]
            );
        }

        $nextStep = $this->getNextStep($record, $currentStep);

        if ($nextStep) {
            $record->current_approval_step = $nextStep;
            $record->status = 'Pending';

            // Notify Next Approvers
            $this->sendNotification($record, 'next_step');
        } else {
            // No next step, fully approved
            $record->status = 'Approved';

            // Deduct leave quota if this is a Leave Request
            if ($record instanceof \App\Models\LeaveRequest) {
                $this->deductLeaveQuota($record);
            }

            // Notify Requester of Completion
            $this->sendNotification($record, 'approved');
        }

        $record->save();
    }

    /**
     * Reject the record.
     */
    public function reject(Model $record, string $reason = null): void
    {
        $record->status = 'Rejected';
        // Optionally save reason in notes or separate table
        if ($reason) {
            $record->notes = ($record->notes ? $record->notes . "\n" : "") . "Rejected: " . $reason;
        }
        $record->save();

        // Notify Requester of Rejection
        $this->sendNotification($record, 'rejected', $reason);
    }

    /**
     * Send email notification based on action
     */
    public function sendNotification(Model $record, string $action, ?string $reason = null): void
    {
        try {
            // Determine Resouce URL (Primitive way, can be improved)
            $resourceName = null;
            if ($record instanceof \App\Models\PurchaseRequisition)
                $resourceName = 'purchase-requisitions';
            elseif ($record instanceof \App\Models\LeaveRequest)
                $resourceName = 'leave-requests';
            elseif ($record instanceof \App\Models\HandoverForm)
                $resourceName = 'handover-forms';

            // Admin panel URL
            $url = url('/admin/' . $resourceName . '/' . $record->id); // View page

            if ($action === 'approved' || $action === 'rejected') {
                // Notify Creator
                $creator = $record->creator;
                $email = $creator ? $creator->email : null;

                // If Handover, the "Requester/Creator" might technically be ICT, generally.
                // But specifically for Handover, the process starts with ICT creating it.

                if ($email) {
                    \Illuminate\Support\Facades\Mail::to($email)
                        ->send(new \App\Mail\RequestStatusMail($record, ucfirst($action), $url, $reason));
                }
                return;
            }

            if ($action === 'created' || $action === 'next_step') {
                $nextStep = $record->current_approval_step;

                // Ensure step is initialized if null/0 (Common on creation)
                if (!$nextStep || $nextStep < 1) {
                    $nextStep = 1;
                    $record->current_approval_step = 1;
                    $record->saveQuietly(); // Save without triggering events loop
                }

                // Determine resource type
                $type = '';
                if ($record instanceof \App\Models\PurchaseRequisition)
                    $type = 'purchase_requisition';
                elseif ($record instanceof \App\Models\LeaveRequest)
                    $type = 'leave_request';
                elseif ($record instanceof \App\Models\HandoverForm)
                    $type = 'handover_form';

                // Special case for Handover Step 1 (Recipient)
                if ($type === 'handover_form' && $nextStep == 1) {
                    $recipientEmail = $record->recipient_email;

                    if (\App\Services\ConfigService::TEST_MODE) {
                        $recipientEmail = \App\Services\ConfigService::TEST_EMAIL;
                    }

                    if ($recipientEmail) {
                        $recipientName = $record->recipient_name ?? 'Recipient';
                        \Illuminate\Support\Facades\Mail::to($recipientEmail)
                            ->send(new \App\Mail\ApprovalRequestMail($record, 'Recipient', $url, $recipientName));
                    }
                    return;
                }

                // Use ConfigService to get specific approver
                $department = $record->department ?? ($record->recipient_department ?? null);

                // Fallback for department...

                $approverConfig = \App\Services\ConfigService::getApproverForStep($type, $nextStep, $department);

                // Generate Signed URL
                $signedUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                    'approval.review',
                    now()->addDays(7),
                    ['type' => $type, 'id' => $record->id]
                );

                if ($approverConfig && !empty($approverConfig['email'])) {
                    $email = $approverConfig['email'];
                    $approverName = $approverConfig['name'] ?? 'Approver';
                    // Send directly to the configured email
                    \Illuminate\Support\Facades\Mail::to($email)
                        ->send(new \App\Mail\ApprovalRequestMail($record, $approverConfig['name'] ?? 'Approver', $signedUrl, $approverName));
                } else {
                    // Fallback...
                    \Illuminate\Support\Facades\Log::warning("No specific approver config found for Type: $type, Step: $nextStep, Dept: $department");
                }
            }

        } catch (\Exception $e) {
            // Log error but don't stop the process
            \Illuminate\Support\Facades\Log::error('Error sending approval notification', [
                'error' => $e->getMessage(),
                'record_type' => get_class($record),
                'record_id' => $record->id ?? null,
            ]);
        }
    }

    /**
     * Deduct leave quota when leave is approved.
     */
    protected function deductLeaveQuota(\App\Models\LeaveRequest $leaveRequest): void
    {
        try {
            $quota = \App\Models\LeaveQuota::where('user_id', $leaveRequest->created_by)
                ->where('quota_year', date('Y'))
                ->first();

            if ($quota) {
                $daysToDeduct = $leaveRequest->request_days ?? $leaveRequest->duration_days ?? 1;
                $quota->useQuota($daysToDeduct);

                \Illuminate\Support\Facades\Log::info('Leave quota deducted', [
                    'user_id' => $leaveRequest->created_by,
                    'leave_request_id' => $leaveRequest->id,
                    'days_deducted' => $daysToDeduct,
                    'quota_used' => $quota->quota_used,
                    'remaining' => $quota->remaining_quota,
                ]);
            } else {
                \Illuminate\Support\Facades\Log::warning('No leave quota found for deduction', [
                    'user_id' => $leaveRequest->created_by,
                    'leave_request_id' => $leaveRequest->id,
                    'year' => date('Y'),
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error deducting leave quota', [
                'error' => $e->getMessage(),
                'leave_request_id' => $leaveRequest->id,
            ]);
        }
    }

    /**
     * Restore leave quota when leave is cancelled/rejected after approval.
     */
    public function restoreLeaveQuota(\App\Models\LeaveRequest $leaveRequest): void
    {
        try {
            // Only restore if it was previously approved
            if ($leaveRequest->getOriginal('status') !== 'Approved') {
                return;
            }

            $quota = \App\Models\LeaveQuota::where('user_id', $leaveRequest->created_by)
                ->where('quota_year', date('Y'))
                ->first();

            if ($quota) {
                $daysToRestore = $leaveRequest->request_days ?? $leaveRequest->duration_days ?? 1;
                $quota->restoreQuota($daysToRestore);

                \Illuminate\Support\Facades\Log::info('Leave quota restored', [
                    'user_id' => $leaveRequest->created_by,
                    'leave_request_id' => $leaveRequest->id,
                    'days_restored' => $daysToRestore,
                    'quota_used' => $quota->quota_used,
                    'remaining' => $quota->remaining_quota,
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error restoring leave quota', [
                'error' => $e->getMessage(),
                'leave_request_id' => $leaveRequest->id,
            ]);
        }
    }
}
