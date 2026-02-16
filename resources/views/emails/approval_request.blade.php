<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Approval</title>
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
                                @if($record instanceof \App\Models\PurchaseRequisition)
                                    Purchase Requisition System
                                @elseif($record instanceof \App\Models\LeaveRequest)
                                    Leave Request System
                                @elseif($record instanceof \App\Models\HandoverForm)
                                    Handover Form System
                                @else
                                    ISO Digital System
                                @endif
                            </p>
                        </td>
                    </tr>

                    <!-- Title Section -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #f1f5f9;">
                            <h2 style="margin: 0; color: #334155; font-size: 18px; font-weight: 600;">
                                @if($record instanceof \App\Models\PurchaseRequisition)
                                    Permintaan Approval Purchase Requisition
                                @elseif($record instanceof \App\Models\LeaveRequest)
                                    Permintaan Approval Cuti/Izin
                                @elseif($record instanceof \App\Models\HandoverForm)
                                    Permintaan Approval Serah Terima
                                @else
                                    Permintaan Approval
                                @endif
                            </h2>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 30px;">

                            <!-- Greeting -->
                            <p style="margin: 0 0 20px 0; color: #1e293b; font-size: 14px;">
                                Kepada Yth. <strong>{{ $approverName }}</strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; color: #475569; font-size: 14px; line-height: 1.6;">
                                @if($record instanceof \App\Models\PurchaseRequisition)
                                    Anda menerima permintaan approval untuk Purchase Requisition dengan detail sebagai
                                    berikut:
                                @elseif($record instanceof \App\Models\LeaveRequest)
                                    Anda menerima permintaan approval untuk Cuti/Izin dengan detail sebagai berikut:
                                @elseif($record instanceof \App\Models\HandoverForm)
                                    Anda menerima permintaan approval untuk Serah Terima dengan detail sebagai berikut:
                                @else
                                    Anda menerima permintaan approval dengan detail sebagai berikut:
                                @endif
                            </p>

                            <!-- Detail Table -->
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="margin-bottom: 25px; background-color: #fafafa; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden;">
                                @if($record instanceof \App\Models\PurchaseRequisition)
                                    <tr>
                                        <td style="padding: 12px 15px; color: #64748b; font-size: 13px; width: 40%;">Judul
                                        </td>
                                        <td style="padding: 12px 15px; color: #1e293b; font-size: 13px; font-weight: 600;">
                                            {{ $record->pr_number }}</td>
                                    </tr>
                                    <tr style="border-top: 1px solid #e5e7eb;">
                                        <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Pemohon</td>
                                        <td style="padding: 12px 15px; color: #2563eb; font-size: 13px;">
                                            {{ $record->requester ?? ($record->creator ? $record->creator->name : 'N/A') }}
                                        </td>
                                    </tr>
                                    <tr style="border-top: 1px solid #e5e7eb;">
                                        <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Departemen</td>
                                        <td style="padding: 12px 15px; color: #1e293b; font-size: 13px;">
                                            {{ $record->department }}</td>
                                    </tr>
                                    <tr style="border-top: 1px solid #e5e7eb;">
                                        <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Tanggal Permintaan
                                        </td>
                                        <td style="padding: 12px 15px; color: #1e293b; font-size: 13px;">
                                            {{ \Carbon\Carbon::parse($record->created_at)->format('Y-m-d') }}</td>
                                    </tr>
                                    <tr style="border-top: 1px solid #e5e7eb;">
                                        <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Status Anggaran
                                        </td>
                                        <td style="padding: 12px 15px; color: #1e293b; font-size: 13px;">
                                            <span
                                                style="background-color: {{ $record->status === 'Dianggarkan' ? '#dcfce7' : '#fef3c7' }}; color: {{ $record->status === 'Dianggarkan' ? '#166534' : '#92400e' }}; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                                                {{ $record->budget_status ?? 'Tidak Dianggarkan' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr style="border-top: 1px solid #e5e7eb;">
                                        <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Total Estimasi</td>
                                        <td style="padding: 12px 15px; color: #1e293b; font-size: 13px; font-weight: 600;">
                                            Rp {{ number_format($record->total_estimate ?? 0, 0, ',', '.') }}</td>
                                    </tr>

                                @elseif($record instanceof \App\Models\LeaveRequest)
                                    <tr>
                                        <td style="padding: 12px 15px; color: #64748b; font-size: 13px; width: 40%;">Nama
                                        </td>
                                        <td style="padding: 12px 15px; color: #1e293b; font-size: 13px; font-weight: 600;">
                                            {{ $record->name }}</td>
                                    </tr>
                                    <tr style="border-top: 1px solid #e5e7eb;">
                                        <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Departemen</td>
                                        <td style="padding: 12px 15px; color: #1e293b; font-size: 13px;">
                                            {{ $record->department }}</td>
                                    </tr>
                                    <tr style="border-top: 1px solid #e5e7eb;">
                                        <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Jenis Cuti</td>
                                        <td style="padding: 12px 15px; color: #1e293b; font-size: 13px;">
                                            {{ $record->leave_type }}</td>
                                    </tr>
                                    <tr style="border-top: 1px solid #e5e7eb;">
                                        <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Tanggal Mulai</td>
                                        <td style="padding: 12px 15px; color: #1e293b; font-size: 13px;">
                                            {{ \Carbon\Carbon::parse($record->start_date)->format('d M Y') }}</td>
                                    </tr>
                                    <tr style="border-top: 1px solid #e5e7eb;">
                                        <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Tanggal Selesai
                                        </td>
                                        <td style="padding: 12px 15px; color: #1e293b; font-size: 13px;">
                                            {{ \Carbon\Carbon::parse($record->end_date)->format('d M Y') }}</td>
                                    </tr>
                                    <tr style="border-top: 1px solid #e5e7eb;">
                                        <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Total Hari</td>
                                        <td style="padding: 12px 15px; color: #1e293b; font-size: 13px; font-weight: 600;">
                                            {{ $record->days_count }} hari</td>
                                    </tr>

                                @elseif($record instanceof \App\Models\HandoverForm)
                                    <tr>
                                        <td style="padding: 12px 15px; color: #64748b; font-size: 13px; width: 40%;">Nama
                                            Item</td>
                                        <td style="padding: 12px 15px; color: #1e293b; font-size: 13px; font-weight: 600;">
                                            {{ $record->item_name }}</td>
                                    </tr>
                                    <tr style="border-top: 1px solid #e5e7eb;">
                                        <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Penerima</td>
                                        <td style="padding: 12px 15px; color: #2563eb; font-size: 13px;">
                                            {{ $record->recipient_name }}</td>
                                    </tr>
                                    <tr style="border-top: 1px solid #e5e7eb;">
                                        <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Departemen Penerima
                                        </td>
                                        <td style="padding: 12px 15px; color: #1e293b; font-size: 13px;">
                                            {{ $record->recipient_department }}</td>
                                    </tr>
                                    <tr style="border-top: 1px solid #e5e7eb;">
                                        <td style="padding: 12px 15px; color: #64748b; font-size: 13px;">Tanggal Serah
                                            Terima</td>
                                        <td style="padding: 12px 15px; color: #1e293b; font-size: 13px;">
                                            {{ \Carbon\Carbon::parse($record->handover_date)->format('d M Y') }}</td>
                                    </tr>
                                @endif
                            </table>

                            <!-- Items Table (for PR only) -->
                            @if($record instanceof \App\Models\PurchaseRequisition && $record->items && count($record->items) > 0)
                                <p style="margin: 0 0 10px 0; color: #1e293b; font-size: 14px; font-weight: 600;">
                                    Item yang Diminta ({{ count($record->items) }} item):
                                </p>
                                <table width="100%" cellpadding="0" cellspacing="0"
                                    style="margin-bottom: 25px; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden;">
                                    <thead>
                                        <tr style="background-color: #f8fafc;">
                                            <th
                                                style="padding: 12px 10px; text-align: center; color: #475569; font-size: 12px; font-weight: 600; border-bottom: 1px solid #e5e7eb; width: 50px;">
                                                No</th>
                                            <th
                                                style="padding: 12px 15px; text-align: left; color: #475569; font-size: 12px; font-weight: 600; border-bottom: 1px solid #e5e7eb;">
                                                Item</th>
                                            <th
                                                style="padding: 12px 10px; text-align: center; color: #475569; font-size: 12px; font-weight: 600; border-bottom: 1px solid #e5e7eb; width: 60px;">
                                                Qty</th>
                                            <th
                                                style="padding: 12px 10px; text-align: center; color: #475569; font-size: 12px; font-weight: 600; border-bottom: 1px solid #e5e7eb; width: 70px;">
                                                Unit</th>
                                            <th
                                                style="padding: 12px 15px; text-align: right; color: #475569; font-size: 12px; font-weight: 600; border-bottom: 1px solid #e5e7eb; width: 120px;">
                                                Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($record->items as $index => $item)
                                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                                <td style="padding: 10px; text-align: center; color: #64748b; font-size: 12px;">
                                                    {{ $index + 1 }}</td>
                                                <td style="padding: 10px 15px; color: #1e293b; font-size: 12px;">
                                                    {{ $item->item_name }}</td>
                                                <td style="padding: 10px; text-align: center; color: #1e293b; font-size: 12px;">
                                                    {{ $item->quantity }}</td>
                                                <td style="padding: 10px; text-align: center; color: #64748b; font-size: 12px;">
                                                    {{ $item->unit }}</td>
                                                <td
                                                    style="padding: 10px 15px; text-align: right; color: #1e293b; font-size: 12px;">
                                                    Rp {{ number_format($item->unit_price ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            <!-- Notes Section -->
                            @if(!empty($record->notes) || !empty($record->reason))
                                <p style="margin: 0 0 8px 0; color: #64748b; font-size: 13px;">
                                    <strong>Catatan:</strong> {{ $record->notes ?? $record->reason ?? '' }}
                                </p>
                            @endif

                            <!-- CTA Section -->
                            <div style="text-align: center; margin: 30px 0 20px 0;">
                                <p style="margin: 0 0 15px 0; color: #475569; font-size: 13px;">
                                    Silakan lakukan approval dengan mengklik tombol di bawah ini:
                                </p>
                                <a href="{{ $url }}"
                                    style="display: inline-block; padding: 14px 40px; background-color: #2563eb; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 15px; font-weight: 600; box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);">
                                    Buka Halaman Approval
                                </a>
                            </div>

                            <!-- Copy Link -->
                            <p style="text-align: center; margin: 15px 0 0 0; color: #94a3b8; font-size: 12px;">
                                Atau copy link ini ke browser:<br>
                                <span style="color: #2563eb; word-break: break-all;">{{ $url }}</span>
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
                                Diproses dari sistem Purchase Requisition Yayasan Sinar Putih Edelweiss<br>
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