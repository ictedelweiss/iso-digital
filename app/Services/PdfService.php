<?php

namespace App\Services;

use App\Models\PurchaseRequisition;
use App\Models\LeaveRequest;
use App\Models\HandoverForm;
use App\Models\Meeting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class PdfService
{
    /**
     * Generate PDF for Purchase Requisition
     */
    public function generatePrPdf(PurchaseRequisition $pr): string
    {
        $pr->load(['items', 'approvals', 'creator']);

        $data = [
            'pr' => $pr,
            'items' => $pr->items,
            'approvals' => $pr->approvals,
            'total' => $pr->items->sum(fn($item) => $item->qty * $item->price),
        ];

        $pdf = Pdf::loadView('pdf.purchase-requisition', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'pr_' . $pr->pr_number . '_' . date('Ymd_His') . '.pdf';
        $path = 'pdf/pr/' . $filename;

        // Store PDF
        \Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Generate PDF for Leave Request
     */
    public function generateLeavePdf(LeaveRequest $leave): string
    {
        $leave->load(['approvals', 'creator']);

        $data = [
            'leave' => $leave,
            'approvals' => $leave->approvals,
        ];

        $pdf = Pdf::loadView('pdf.leave-request', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'leave_' . $leave->id . '_' . date('Ymd_His') . '.pdf';
        $path = 'pdf/leave/' . $filename;

        \Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Generate PDF for Handover Form
     */
    public function generateHandoverPdf(HandoverForm $handover): string
    {
        $handover->load(['approvals', 'creator']);

        $data = [
            'handover' => $handover,
            'approvals' => $handover->approvals,
        ];

        $pdf = Pdf::loadView('pdf.handover-form', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'handover_' . $handover->id . '_' . date('Ymd_His') . '.pdf';
        $path = 'pdf/handover/' . $filename;

        \Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Generate attendance list PDF for Meeting
     */
    public function generateMeetingAttendancePdf(Meeting $meeting): string
    {
        $meeting->load('attendees');

        $data = [
            'meeting' => $meeting,
            'attendees' => $meeting->attendees,
        ];

        $pdf = Pdf::loadView('pdf.meeting-attendance', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'attendance_' . $meeting->id . '_' . date('Ymd_His') . '.pdf';
        $path = 'pdf/attendance/' . $filename;

        \Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Format currency for PDF
     */
    public function formatCurrency(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Format date for PDF
     */
    public function formatDate(?string $date): string
    {
        if (!$date) {
            return '-';
        }

        return \Carbon\Carbon::parse($date)->format('d F Y');
    }
}
