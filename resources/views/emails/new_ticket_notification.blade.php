<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Helpdesk Baru</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="650" cellpadding="0" cellspacing="0"
                    style="background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">

                    <!-- Header -->
                    <tr>
                        <td style="padding: 25px 30px; border-bottom: 3px solid #2563eb;">
                            <h1 style="margin: 0; color: #2563eb; font-size: 22px; font-weight: bold;">Yayasan Sinar
                                Putih Edelweiss</h1>
                            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 13px;">
                                ICT Helpdesk System
                            </p>
                        </td>
                    </tr>

                    <!-- Title Section -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #f1f5f9;">
                            <h2 style="margin: 0; color: #334155; font-size: 18px; font-weight: 600;">
                                🎫 Tiket Helpdesk Baru Diterima
                            </h2>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 30px;">

                            <!-- Greeting -->
                            <p style="margin: 0 0 20px 0; color: #1e293b; font-size: 14px;">
                                Kepada Tim <strong>ICT</strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; color: #475569; font-size: 14px; line-height: 1.6;">
                                Tiket helpdesk baru telah dikirimkan. Berikut detail tiket:
                            </p>

                            <!-- Detail Table -->
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="margin-bottom: 25px; background-color: #fafafa; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 12px 15px; color: #64748b; font-size: 13px; width: 35%;">
                                        No. Tiket</td>
                                    <td style="padding: 12px 15px; color: #1e293b; font-size: 13px; font-weight: 600;">
                                        {{ $ticket->ticket_number }}</td>
                                </tr>
                                <tr style="border-top: 1px solid #e5e7eb;">
                                    <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Pelapor</td>
                                    <td style="padding: 12px 15px; color: #2563eb; font-size: 13px;">
                                        {{ $ticket->reporter->name ?? 'N/A' }}
                                        ({{ $ticket->reporter->email ?? '' }})
                                    </td>
                                </tr>
                                <tr style="border-top: 1px solid #e5e7eb;">
                                    <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Subject</td>
                                    <td style="padding: 12px 15px; color: #1e293b; font-size: 13px; font-weight: 600;">
                                        {{ $ticket->subject }}</td>
                                </tr>
                                <tr style="border-top: 1px solid #e5e7eb;">
                                    <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Kategori</td>
                                    <td style="padding: 12px 15px; color: #1e293b; font-size: 13px;">
                                        <span
                                            style="background-color: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                                            {{ $ticket->category }}
                                        </span>
                                    </td>
                                </tr>
                                <tr style="border-top: 1px solid #e5e7eb;">
                                    <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Prioritas</td>
                                    <td style="padding: 12px 15px; color: #1e293b; font-size: 13px;">
                                        @php
                                        $priorityColors = [
                                        'Low' => ['bg' => '#dcfce7', 'text' => '#166534'],
                                        'Medium' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                                        'High' => ['bg' => '#fed7aa', 'text' => '#9a3412'],
                                        'Critical' => ['bg' => '#fecaca', 'text' => '#991b1b'],
                                        ];
                                        $pc = $priorityColors[$ticket->priority] ?? $priorityColors['Medium'];
                                        @endphp
                                        <span
                                            style="background-color: {{ $pc['bg'] }}; color: {{ $pc['text'] }}; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                            {{ $ticket->priority }}
                                        </span>
                                    </td>
                                </tr>
                                <tr style="border-top: 1px solid #e5e7eb;">
                                    <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Tanggal Laporan
                                    </td>
                                    <td style="padding: 12px 15px; color: #1e293b; font-size: 13px;">
                                        {{ $ticket->created_at->format('d M Y, H:i') }} WIB</td>
                                </tr>
                            </table>

                            <!-- Description -->
                            <p style="margin: 0 0 8px 0; color: #1e293b; font-size: 13px; font-weight: 600;">
                                Deskripsi Masalah:</p>
                            <div
                                style="margin: 0 0 25px 0; padding: 15px; background-color: #f8fafc; border-left: 3px solid #2563eb; color: #475569; font-size: 13px; line-height: 1.6; border-radius: 0 6px 6px 0;">
                                {{ $ticket->description }}
                            </div>

                            @if($ticket->attachment)
                            <p style="margin: 0 0 25px 0; color: #64748b; font-size: 13px;">
                                📎 <strong>Lampiran:</strong> File terlampir pada tiket
                            </p>
                            @endif

                            <!-- CTA Section -->
                            <div style="text-align: center; margin: 30px 0 20px 0;">
                                <p style="margin: 0 0 15px 0; color: #475569; font-size: 13px;">
                                    Silakan kelola tiket ini melalui dashboard admin:
                                </p>
                                <a href="{{ $dashboardUrl }}"
                                    style="display: inline-block; padding: 14px 40px; background-color: #2563eb; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 15px; font-weight: 600; box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);">
                                    Buka Dashboard Helpdesk
                                </a>
                            </div>

                            <!-- Copy Link -->
                            <p style="text-align: center; margin: 15px 0 0 0; color: #94a3b8; font-size: 12px;">
                                Atau copy link ini ke browser:<br>
                                <span style="color: #2563eb; word-break: break-all;">{{ $dashboardUrl }}</span>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #f8fafc; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #94a3b8; font-size: 11px; line-height: 1.6;">
                                ⚙️ Email Otomatis - Mohon tidak membalas email ini
                            </p>
                            <p style="margin: 5px 0 0 0; color: #cbd5e1; font-size: 11px;">
                                ICT Helpdesk System | Yayasan Sinar Putih Edelweiss<br>
                                ISO Digital System | iso.digital@edelweiss.sch.id
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>