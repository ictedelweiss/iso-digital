@extends('layouts.public')

@section('title', 'Tiket Berhasil Dikirim')

@section('content')
<div
    style="min-height: 100vh; background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%); padding: 40px 16px; display: flex; align-items: center; justify-content: center;">
    <div style="max-width: 560px; width: 100%; text-align: center;">

        {{-- Success Icon --}}
        <div
            style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background: linear-gradient(135deg, #059669, #10b981); border-radius: 50%; margin-bottom: 24px; box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white"
                width="40" height="40">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
        </div>

        <h1
            style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 28px; font-weight: 700; color: #f8fafc; margin: 0 0 12px 0;">
            Tiket Berhasil Dikirim! 🎉
        </h1>
        <p
            style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; color: #94a3b8; margin: 0 0 32px 0; line-height: 1.6;">
            Tim ICT akan segera menindaklanjuti tiket Anda.
        </p>

        {{-- Ticket Summary Card --}}
        <div
            style="background: rgba(30, 41, 59, 0.8); backdrop-filter: blur(16px); border: 1px solid rgba(148, 163, 184, 0.1); border-radius: 16px; padding: 28px; margin-bottom: 32px; text-align: left; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="padding: 10px 0; color: #64748b; font-size: 13px; width: 40%;">No. Tiket</td>
                    <td style="padding: 10px 0; color: #3b82f6; font-size: 15px; font-weight: 700;">{{
                        $ticket->ticket_number }}</td>
                </tr>
                <tr style="border-top: 1px solid rgba(148, 163, 184, 0.1);">
                    <td style="padding: 10px 0; color: #64748b; font-size: 13px;">Subject</td>
                    <td style="padding: 10px 0; color: #e2e8f0; font-size: 13px; font-weight: 500;">{{ $ticket->subject
                        }}</td>
                </tr>
                <tr style="border-top: 1px solid rgba(148, 163, 184, 0.1);">
                    <td style="padding: 10px 0; color: #64748b; font-size: 13px;">Kategori</td>
                    <td style="padding: 10px 0;">
                        <span
                            style="background: rgba(37, 99, 235, 0.15); color: #93c5fd; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                            {{ $ticket->category }}
                        </span>
                    </td>
                </tr>
                <tr style="border-top: 1px solid rgba(148, 163, 184, 0.1);">
                    <td style="padding: 10px 0; color: #64748b; font-size: 13px;">Prioritas</td>
                    <td style="padding: 10px 0;">
                        @php
                        $pColors = [
                        'Low' => ['bg' => 'rgba(34, 197, 94, 0.15)', 'text' => '#86efac'],
                        'Medium' => ['bg' => 'rgba(234, 179, 8, 0.15)', 'text' => '#fde047'],
                        'High' => ['bg' => 'rgba(249, 115, 22, 0.15)', 'text' => '#fdba74'],
                        'Critical' => ['bg' => 'rgba(239, 68, 68, 0.15)', 'text' => '#fca5a5'],
                        ];
                        $pc = $pColors[$ticket->priority] ?? $pColors['Medium'];
                        @endphp
                        <span
                            style="background: {{ $pc['bg'] }}; color: {{ $pc['text'] }}; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                            {{ $ticket->priority }}
                        </span>
                    </td>
                </tr>
                <tr style="border-top: 1px solid rgba(148, 163, 184, 0.1);">
                    <td style="padding: 10px 0; color: #64748b; font-size: 13px;">Status</td>
                    <td style="padding: 10px 0;">
                        <span
                            style="background: rgba(56, 189, 248, 0.15); color: #7dd3fc; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                            {{ $ticket->status }}
                        </span>
                    </td>
                </tr>
                <tr style="border-top: 1px solid rgba(148, 163, 184, 0.1);">
                    <td style="padding: 10px 0; color: #64748b; font-size: 13px;">Tanggal</td>
                    <td style="padding: 10px 0; color: #e2e8f0; font-size: 13px;">{{ $ticket->created_at->format('d M Y,
                        H:i') }} WIB</td>
                </tr>
            </table>
        </div>

        {{-- Actions --}}
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('helpdesk.create') }}"
                style="display: inline-block; padding: 12px 28px; background: linear-gradient(135deg, #2563eb, #3b82f6); color: #fff; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 600; font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4); transition: all 0.3s;"
                onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='translateY(0)'">
                ➕ Buat Tiket Baru
            </a>
            <a href="{{ url('/laravel-app/public/') }}"
                style="display: inline-block; padding: 12px 28px; background: rgba(148, 163, 184, 0.1); border: 1px solid rgba(148, 163, 184, 0.2); color: #94a3b8; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 500; font-family: 'Plus Jakarta Sans', sans-serif; transition: all 0.3s;"
                onmouseover="this.style.borderColor='#3b82f6'; this.style.color='#e2e8f0'"
                onmouseout="this.style.borderColor='rgba(148, 163, 184, 0.2)'; this.style.color='#94a3b8'">
                🏠 Kembali
            </a>
        </div>

        {{-- Footer --}}
        <p style="color: #475569; font-size: 12px; margin-top: 32px;">
            Email notifikasi telah dikirim ke tim ICT.<br>
            Hubungi <a href="mailto:ict@edelweiss.sch.id"
                style="color: #3b82f6; text-decoration: none;">ict@edelweiss.sch.id</a> untuk pertanyaan lebih lanjut.
        </p>
    </div>
</div>
@endsection