<?php

namespace App\Http\Controllers;

use App\Models\IctTicket;
use App\Models\User;
use App\Mail\NewTicketNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class HelpdeskController extends Controller
{
    /**
     * Tampilkan form pembuatan tiket
     */
    public function create()
    {
        $user = Auth::user();
        return view('helpdesk.create', compact('user'));
    }

    /**
     * Simpan tiket baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:200',
            'category' => 'required|in:Hardware,Software,Network,Account',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'description' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
            'reporter_name' => 'nullable|string|max:255',
            'reporter_email' => 'nullable|email|max:255',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('helpdesk-attachments', 'public');
        }

        // Tentukan user_id: logged in user atau cari berdasarkan email
        $userId = null;
        if (Auth::check()) {
            $userId = Auth::id();
        } elseif (!empty($validated['reporter_email'])) {
            $user = User::where('ms_email', $validated['reporter_email'])
                ->orWhere('username', explode('@', $validated['reporter_email'])[0])
                ->first();
            $userId = $user?->id;
        }

        // Fallback ke user ID 1 jika tidak ditemukan
        if (!$userId) {
            $userId = 1;
        }

        $ticket = IctTicket::create([
            'user_id' => $userId,
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'description' => $validated['description'],
            'attachment' => $attachmentPath,
            'status' => 'Open',
        ]);

        // Kirim email notifikasi ke tim ICT
        try {
            Mail::to('ict@edelweiss.sch.id')->send(
                new NewTicketNotification($ticket)
            );
        } catch (\Exception $e) {
            Log::error('Failed to send helpdesk ticket notification: ' . $e->getMessage());
        }

        return redirect()->route('helpdesk.success', $ticket->id);
    }

    /**
     * Halaman sukses setelah tiket dibuat
     */
    public function success($ticketId)
    {
        $ticket = IctTicket::findOrFail($ticketId);
        return view('helpdesk.success', compact('ticket'));
    }
}