<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class ApprovalStatusWidget extends Widget
{
    protected static string $view = 'filament.widgets.approval-status-widget';

    public ?Model $record = null;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        // Only show on resource view pages, not on the dashboard
        return request()->routeIs('filament.admin.resources.*.view');
    }

    public function getApprovalStatusData(): array
    {
        if (!$this->record) {
            return [
                'status' => 'Unknown',
                'message' => 'No record available',
                'color' => 'gray',
            ];
        }

        // If fully approved
        if (method_exists($this->record, 'isFullyApproved') && $this->record->isFullyApproved()) {
            return [
                'status' => 'Fully Approved',
                'message' => 'This document has been approved by all required approvers.',
                'color' => 'success',
            ];
        }

        // If rejected
        if ($this->record->status === 'Rejected') {
            return [
                'status' => 'Rejected',
                'message' => 'This document has been rejected.',
                'color' => 'danger',
            ];
        }

        // Pending approval
        $currentStep = $this->record->current_approval_step ?? 1;

        // Determine document type
        $type = '';
        if ($this->record instanceof \App\Models\PurchaseRequisition) {
            $type = 'purchase_requisition';
        } elseif ($this->record instanceof \App\Models\LeaveRequest) {
            $type = 'leave_request';
        } elseif ($this->record instanceof \App\Models\HandoverForm) {
            $type = 'handover_form';
        }

        // Get approver info
        $department = $this->record->department ?? null;
        $approverConfig = \App\Services\ConfigService::getApproverForStep($type, $currentStep, $department);

        if ($approverConfig) {
            return [
                'status' => 'Pending Approval',
                'message' => "Waiting for approval from: <strong>{$approverConfig['name']}</strong> ({$approverConfig['role_display']})",
                'color' => 'warning',
                'step' => "Step {$currentStep}",
            ];
        }

        return [
            'status' => 'Pending',
            'message' => 'Waiting for approval...',
            'color' => 'info',
        ];
    }
}
