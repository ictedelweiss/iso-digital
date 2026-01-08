<?php

namespace App\Http\Controllers;

use App\Services\ApprovalService;
use App\Services\ConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Models\PurchaseRequisition;
use App\Models\LeaveRequest;
use App\Models\HandoverForm;

class ApprovalController extends Controller
{
    public function review(Request $request, string $type, int $id)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Link expired or invalid.');
        }

        $record = match ($type) {
            'purchase_requisition' => PurchaseRequisition::with(['items', 'documents'])->findOrFail($id),
            'leave_request' => LeaveRequest::findOrFail($id),
            'handover_form' => HandoverForm::with('approvals')->findOrFail($id),
            default => abort(404),
        };

        if ($record->status === 'Approved' || $record->status === 'Rejected') {
            return view('approval.processed', ['record' => $record]);
        }

        // Determine if this step is essentially "done" for this specific link usage 
        // (logic can be refined, but for now rely on status)

        return view('approval.review', [
            'record' => $record,
            'type' => $type,
            'id' => $id,
        ]);
    }

    public function submit(Request $request, string $type, int $id)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Link expired or invalid.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'signature' => 'required_if:action,approve', // Base64 signature
            'reason' => 'required_if:action,reject',
            'new_pr_number' => 'nullable|string|max:50|unique:purchase_requisitions,pr_number,' . $id,
        ]);

        $record = match ($type) {
            'purchase_requisition' => PurchaseRequisition::findOrFail($id),
            'leave_request' => LeaveRequest::findOrFail($id),
            'handover_form' => HandoverForm::findOrFail($id),
            default => abort(404),
        };

        $service = new ApprovalService();

        if ($request->action === 'approve') {
            $service->approve($record, $request->input('signature'), $request->input('new_pr_number')); // Pass signature and new PR number
            $message = 'Document approved successfully.';
            $status = 'success';
        } else {
            $service->reject($record, $request->input('reason'));
            $message = 'Document rejected.';
            $status = 'danger';
        }

        return redirect()->route('approval.done', ['status' => $status]);
    }

    public function done(Request $request)
    {
        return view('approval.done', ['status' => $request->get('status', 'success')]);
    }
}
