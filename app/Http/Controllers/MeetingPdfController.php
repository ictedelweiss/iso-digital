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
        try {
            \Illuminate\Support\Facades\Log::info('PDF Generation Started', ['meetingId' => $meetingId]);

            $meeting = Meeting::with('attendees')->findOrFail($meetingId);
            \Illuminate\Support\Facades\Log::info('Meeting Found', ['title' => $meeting->title]);

            $pdf = Pdf::loadView('pdf.meeting-attendance', [
                'meeting' => $meeting,
                'attendees' => $meeting->attendees()->orderBy('created_at')->get(),
            ]);
            \Illuminate\Support\Facades\Log::info('PDF View Loaded');

            // Sanitize filename
            $safeTitle = \Illuminate\Support\Str::slug($meeting->title, '_');
            $filename = 'Absensi_' . $safeTitle . '_' . date('Y-m-d') . '.pdf';

            \Illuminate\Support\Facades\Log::info('PDF Generation Completed, downloading...');
            return $pdf->download($filename);
        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('PDF Generation Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}