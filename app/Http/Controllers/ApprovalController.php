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

        $user = auth()->user();

        return view('approval.review', [
            'record' => $record,
            'type' => $type,
            'id' => $id,
            'hasSignature' => $user && !empty($user->signature_path),
        ]);
    }

    public function submit(Request $request, string $type, int $id)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Link expired or invalid.');
        }

        $user = auth()->user();

        $request->validate([
            'action' => 'required|in:approve,reject',
            'signature' => ($request->action === 'approve' && (!$user || empty($user->signature_path))) ? 'required' : 'nullable',
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
            $signature = $request->input('signature');

            // Signature Reuse Logic
            $signatureForService = $signature;

            if ($user) {
                if ($signature) {
                    // User provided a NEW signature -> Save to profile
                    $path = $this->storeSignature($signature, 'user_' . $user->id);
                    $user->signature_path = $path;
                    $user->save();

                    // Pass null so service uses Auth::user()->signature_path
                    $signatureForService = null;
                } elseif (!empty($user->signature_path)) {
                    // User has signature and reused it (input empty)
                    $signatureForService = null;
                }
            }

            $service->approve($record, $signatureForService, $request->input('new_pr_number'));
            $message = 'Document approved successfully.';
            $status = 'success';
        } else {
            $service->reject($record, $request->input('reason'));
            $message = 'Document rejected.';
            $status = 'danger';
        }

        return redirect()->route('approval.done', ['status' => $status]);
    }

    private function storeSignature($data, $prefix)
    {
        // Duplicate helper, maybe move to trait later
        $data = preg_replace('/^data:image\/\w+;base64,/', '', $data);
        $data = base64_decode($data);
        $filename = 'signatures/' . $prefix . '_' . time() . '.png';
        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $data);
        return $filename;
    }

    public function done(Request $request)
    {
        return view('approval.done', ['status' => $request->get('status', 'success')]);
    }
}
