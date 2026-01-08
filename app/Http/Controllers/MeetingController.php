<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Attendee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MeetingController extends Controller
{
    /**
     * Show meeting attendance form
     */
    public function attend(string $meetingId)
    {
        $meeting = Meeting::findOrFail($meetingId);

        return view('meeting.attend', [
            'meeting' => $meeting,
        ]);
    }

    /**
     * Submit attendance with signature
     */
    public function submitAttendance(Request $request, string $meetingId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'division' => 'required|string|max:255',
            'signature' => 'required|string',
        ]);

        $meeting = Meeting::findOrFail($meetingId);

        // Process base64 signature
        $signatureData = $request->input('signature');
        $signaturePath = null;

        if ($signatureData && str_starts_with($signatureData, 'data:image')) {
            // Extract base64 data
            $signatureData = preg_replace('/^data:image\/\w+;base64,/', '', $signatureData);
            $signatureData = base64_decode($signatureData);

            // Generate unique filename
            $filename = 'signatures/meeting_' . $meetingId . '_' . time() . '_' . uniqid() . '.png';

            // Store in public disk
            Storage::disk('public')->put($filename, $signatureData);
            $signaturePath = $filename;
        }

        // Create attendee record
        $attendee = Attendee::create([
            'meeting_id' => $meetingId,
            'name' => $request->input('name'),
            'division' => $request->input('division'),
            'signature_path' => $signaturePath,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil disimpan',
            'attendee' => $attendee,
        ]);
    }

    /**
     * Get meeting attendees list
     */
    public function getAttendees(string $meetingId)
    {
        $meeting = Meeting::with('attendees')->findOrFail($meetingId);

        return response()->json([
            'meeting' => $meeting,
            'attendees' => $meeting->attendees,
            'total' => $meeting->attendees->count(),
        ]);
    }
}
