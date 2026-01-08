<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MeetingPdfController extends Controller
{
    /**
     * Generate PDF for meeting attendance
     */
    public function generate(string $meetingId)
    {
        $meeting = Meeting::with('attendees')->findOrFail($meetingId);

        $pdf = Pdf::loadView('pdf.meeting-attendance', [
            'meeting' => $meeting,
            'attendees' => $meeting->attendees()->orderBy('created_at')->get(),
        ]);

        // Sanitize filename
        $safeTitle = \Illuminate\Support\Str::slug($meeting->title, '_');
        $filename = 'Absensi_' . $safeTitle . '_' . date('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
