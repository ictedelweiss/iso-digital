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
    /**
     * Show meeting attendance form
     */
    public function attend(string $meetingId)
    {
        $meeting = Meeting::findOrFail($meetingId);
        $user = auth()->user();

        // Check if already attended (for both staff and guests)
        $alreadyAttended = false;
        if ($user) {
            $alreadyAttended = Attendee::where('meeting_id', $meetingId)
                ->where('user_id', $user->id)
                ->exists();
        }

        if ($alreadyAttended) {
            return view('meeting.already_attended', ['meeting' => $meeting]);
        }

        return view('meeting.attend', [
            'meeting' => $meeting,
            'user' => $user,
            'isLoggedIn' => auth()->check(),
            'hasSignature' => $user ? !empty($user->signature_path) : false,
        ]);
    }

    /**
     * Submit attendance with signature
     */
    public function submitAttendance(Request $request, string $meetingId)
    {
        $meeting = Meeting::findOrFail($meetingId);
        $user = auth()->user();

        // Validation based on meeting type
        if ($meeting->type === 'internal') {
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            // Enforce 1 sign per person
            $exists = Attendee::where('meeting_id', $meetingId)
                ->where('user_id', $user->id)
                ->exists();

            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absensi.'], 400);
            }

            $request->validate([
                'signature' => empty($user->signature_path) ? 'required|string' : 'nullable|string',
            ]);
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
                'division' => 'required|string|max:255',
                'signature' => 'required|string',
            ]);
        }

        $signaturePath = null;

        if ($user) {
            // Internal User Logic
            if (!empty($user->signature_path)) {
                // Reuse signature
                $signaturePath = $user->signature_path;
            } else {
                // New signature logic
                $signatureData = $request->input('signature');
                if ($signatureData && str_starts_with($signatureData, 'data:image')) {
                    $signaturePath = $this->storeSignature($signatureData, 'user_' . $user->id);

                    // Save to user profile for future use
                    $user->signature_path = $signaturePath;
                    $user->save();
                }
            }
        } else {
            // Guest/External Logic
            $signatureData = $request->input('signature');
            if ($signatureData) {
                $signaturePath = $this->storeSignature($signatureData, 'guest_' . uniqid());
            }
        }

        // Create attendee record
        $attendee = Attendee::create([
            'meeting_id' => $meetingId,
            'user_id' => $user?->id,
            'name' => $user ? $user->display_name ?? $user->username : $request->input('name'),
            'division' => $user ? ($user->division ?: 'Internal') : $request->input('division'),
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
     * Helper to store signature
     */
    private function storeSignature($data, $prefix)
    {
        $data = preg_replace('/^data:image\/\w+;base64,/', '', $data);
        $data = base64_decode($data);
        $filename = 'signatures/' . $prefix . '_' . time() . '.png';
        Storage::disk('public')->put($filename, $data);
        return $filename;
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