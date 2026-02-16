<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Daftar Hadir</title>
    <style>
        @page {
            size: A4;
            margin: 12mm;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            color: #111;
            padding: 0mm;
            font-size: 13px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .header-table td {
            border: 1.2px solid #111;
            padding: 12px;
            text-align: center;
            font-weight: 600;
            font-size: 16px;
        }

        .header-left {
            font-size: 15px;
            line-height: 1.4;
            width: 30%;
        }

        .header-middle {
            font-size: 18px;
            width: 40%;
        }

        .header-right {
            width: 30%;
        }

        .header-right img {
            max-height: 48px;
            display: block;
            margin: 0 auto;
        }

        .info-table {
            width: 100%;
            margin-bottom: 16px;
            border-collapse: collapse;
            font-size: 13px;
        }

        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .info-label {
            width: 120px;
            font-weight: 600;
        }

        .agenda-lines {
            flex: 1;
            height: 16px;
            border-bottom: 1px dotted #111;
            display: block;
            width: 100%;
        }

        .agenda-item {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 4px;
        }

        .participant-table {
            width: 100%;
            border-collapse: collapse;
        }

        .participant-table th,
        .participant-table td {
            border: 1.1px solid #111;
            padding: 8px 6px;
            font-size: 13px;
            vertical-align: middle;
        }

        .participant-table td {
            height: 46px;
        }

        .participant-table th {
            background: #fff;
            font-weight: 800;
            text-align: center;
            font-size: 14px;
        }

        .col-no {
            width: 40px;
            text-align: center;
            font-weight: 800;
        }

        .col-name {
            width: 210px;
        }

        .col-role {
            width: 150px;
        }

        .sig-cell {
            width: 190px;
            vertical-align: top;
            text-align: center;
            padding: 2px;
        }

        .sig-cell img {
            max-height: 60px;
            max-width: 100%;
            margin: 0 auto;
        }

        .footer-stamp {
            font-size: 10px;
            text-align: right;
            margin-top: 10px;
        }

        /* Helper for page breaks */
        .page-break {
            page-break-after: always;
        }

        /* Agenda list numbering */
        ol.agenda-list {
            margin: 0;
            padding-left: 20px;
        }

        ol.agenda-list li {
            border-bottom: 1px dotted #111;
            padding-bottom: 2px;
            margin-bottom: 4px;
        }
    </style>
</head>

<body>
    @php
        $logoPath = public_path('logo.png');
        $logoData = '';
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
        }

        // Agenda parsing (mockup, since we don't store agenda in DB yet, use title as first item)
        $agendaLines = [$meeting->title, '', ''];
        $dateStr = \Carbon\Carbon::parse($meeting->created_at)->locale('id')->isoFormat('D MMMM Y');
        $timeStr = \Carbon\Carbon::parse($meeting->created_at)->format('H:i');

        // Pagination logic
        $perPage = 16;
        $totalAttendees = $attendees->count();
        $chunks = $attendees->chunk($perPage);
        $totalPages = $chunks->count();
        if ($totalPages == 0)
            $totalPages = 1;
    @endphp

    @foreach($chunks as $pageIndex => $chunk)
        <table class="header-table">
            <tr>
                <td class="header-left">
                    YAYASAN<br>SINAR PUTIH EDELWEISS
                </td>
                <td class="header-middle">DAFTAR HADIR {{ $pageIndex > 0 ? '(Lanjutan)' : '' }}</td>
                <td class="header-right">
                    @if($logoData)
                        <img src="data:image/png;base64,{{ $logoData }}" alt="Logo">
                    @endif
                </td>
            </tr>
        </table>

        @if($pageIndex == 0)
            <table class="info-table">
                <tr>
                    <td class="info-label">AGENDA RAPAT</td>
                    <td>
                        @foreach($agendaLines as $idx => $line)
                            <div style="margin-bottom: 4px;">
                                {{ $idx + 1 }}). <span
                                    style="border-bottom: 1px dotted #000; width: 80%; display: inline-block;">{{ $line }}</span>
                            </div>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <td class="info-label">TANGGAL</td>
                    <td><span style="border-bottom: 1px dotted #000; display: inline-block; width: 200px;">{{ $dateStr }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="info-label">JAM</td>
                    <td><span style="border-bottom: 1px dotted #000; display: inline-block; width: 100px;">{{ $timeStr }}</span>
                    </td>
                </tr>
            </table>

            <div style="font-weight: 600; margin-bottom: 4px;">PESERTA RAPAT :</div>
        @else
            <div style="font-weight: 600; margin-bottom: 4px; margin-top: 12px;">PESERTA RAPAT (Lanjutan):</div>
        @endif

        <table class="participant-table">
            <thead>
                <tr>
                    <th class="col-no">NO</th>
                    <th class="col-name">NAMA</th>
                    <th class="col-role">JABATAN</th>
                    <th class="sig-cell">TANDA TANGAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chunk as $index => $attendee)
                    <tr>
                        <td class="col-no">{{ ($pageIndex * $perPage) + $index + 1 }}</td>
                        <td class="col-name">{{ $attendee->name }}</td>
                        <td class="col-role">{{ $attendee->division }}</td>
                        <td class="sig-cell">
                            @if($attendee->signature_path)
                                @php
                                    $sigPath = storage_path('app/public/' . $attendee->signature_path);
                                    // Fallback checking because path might differ
                                    if (!file_exists($sigPath)) {
                                        $sigPath = public_path('storage/' . $attendee->signature_path);
                                    }
                                @endphp

                                @if(file_exists($sigPath))
                                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($sigPath)) }}" alt="TTD">
                                @endif
                            @else
                                &nbsp;
                            @endif
                        </td>
                    </tr>
                @empty
                    @if($pageIndex == 0)
                        <tr>
                            <td colspan="4" style="text-align:center; padding: 20px;">Belum ada peserta</td>
                        </tr>
                    @endif
                @endforelse

                {{-- Fill empty rows if needed on last page to maintain look, or just leave as is --}}
                @if($chunk->count() < $perPage && $pageIndex == 0)
                    @for($i = 0; $i < (5 - $chunk->count()); $i++)
                        <tr>
                            <td class="col-no">&nbsp;</td>
                            <td class="col-name">&nbsp;</td>
                            <td class="col-role">&nbsp;</td>
                            <td class="sig-cell">&nbsp;</td>
                        </tr>
                    @endfor
                @endif
            </tbody>
        </table>

        <div class="footer-stamp">YSPE-MGT-FM-007<br>Rev.01 20-01-2022</div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>

</html>