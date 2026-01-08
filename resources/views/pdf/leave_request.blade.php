<!DOCTYPE html>
<html>

<head>
    <title>Form Permohonan Cuti</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            /* Image looks like serif or slab serif, sticking to standard professional font, maybe bold headers */
            /* Checking image again, "YAYASAN SINAR" looks like serif (Times). Content looks like serif. */
            font-family: Arial, sans-serif;
            /* Actually image looks like Sans Serif (Arial/Calibri) upon closer inspection of "Nama : Aris". Let's stick to Arial/Helvetica for clean look, or Segoe UI. The previous Handover was Arial. */
            color: #000;
            font-size: 11px;
            margin: 0;
            padding: 20px 40px;
            line-height: 1.5;
        }

        /* Header */
        .header {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: double 3px #000;
            /* Maybe not double, just header separation. Image doesn't show border. */
            /* Image has no visible border line under header? Hard to tell. 
               Handover had a box. This one has "YAYASAN..." top left, Title Center, Logo Right.
               Let's assume clean header. */
            padding-bottom: 10px;
        }

        .header-table {
            width: 100%;
        }

        .header-left {
            width: 30%;
            font-weight: bold;
            font-size: 12px;
            color: #555;
            vertical-align: top;
        }

        .header-center {
            width: 40%;
            text-align: center;
            vertical-align: top;
        }

        .header-title {
            color: #1e40af;
            /* Blue title */
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            /* Image doesn't show underline? "FORM PERMOHONAN CUTI" is blue. */
            text-decoration: none;
        }

        .header-right {
            width: 30%;
            text-align: right;
            vertical-align: top;
        }

        .header-right img {
            max-height: 50px;
        }

        /* Content Fields with Dotted Lines */
        .field-row {
            margin-bottom: 8px;
            position: relative;
        }

        .field-label {
            display: inline-block;
            width: 130px;
        }

        .field-value {
            display: inline-block;
            width: calc(100% - 135px);
            border-bottom: 1px dotted #000;
            padding-left: 5px;
        }

        /* Narrative Text with Dotted Lines */
        .narrative {
            margin: 20px 0;
            text-align: justify;
        }

        .narrative-line {
            display: inline-block;
            border-bottom: 1px dotted #000;
            text-align: center;
        }

        /* Notes Section */
        .notes {
            margin-top: 20px;
            font-size: 11px;
        }

        .notes-title {
            text-decoration: underline;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .notes ol {
            padding-left: 20px;
            margin: 0;
        }

        .notes li {
            margin-bottom: 5px;
        }

        /* Calculation Section inside Notes */
        .calc-table {
            margin-left: 20px;
            width: 80%;
            border-collapse: collapse;
        }

        .calc-table td {
            padding: 2px 5px;
        }

        .calc-label {
            width: 60%;
        }

        .calc-eq {
            width: 10px;
            text-align: center;
        }

        .calc-val {
            width: 15%;
            text-align: left;
            font-weight: bold;
        }

        .calc-unit {
            width: 15%;
            text-align: right;
            font-weight: bold;
        }

        /* Signatures */
        .sig-table {
            width: 100%;
            margin-top: 40px;
            border-top: 1px solid #000;
            padding-top: 20px;
        }

        /* Image has a line separating signatures? "Aris Setyawan" ... line below? No. 
           It looks like a horizontal line above signatures? "4. Bagi Karyawan..." -> Line -> Signatures. 
           Wait, there is a line separating the names from the titles? "Diajukan Oleh," then space, then Sig, then Name. 
           Line is above "Aris Setyawan"? No, the line is bottom of the page in image? 
           The image shows a line separating the note list item 4 from the signature area? 
           Or maybe footer line?
           Let's just use standard spacing. */
        .sig-table {
            width: 100%;
            margin-top: 30px;
            border-spacing: 0;
        }

        .sig-td {
            width: 33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }

        .sig-role {
            margin-bottom: 40px;
        }

        .sig-img {
            height: 50px;
            display: block;
            margin: 0 auto;
        }

        .sig-name {
            font-weight: bold;
            margin-top: 5px;
            border-top: 1px solid #000;
            display: inline-block;
            padding-top: 2px;
            width: 80%;
        }

        /* Footer */
        .footer-id {
            text-align: right;
            font-size: 9px;
            color: #888;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-left">
                    YAYASAN SINAR<br>PUTIH EDELWEISS
                </td>
                <td class="header-center">
                    <span class="header-title">FORM PERMOHONAN CUTI</span>
                </td>
                <td class="header-right">
                    <img src="{{ $logoSrc }}" alt="Edelweiss School">
                </td>
            </tr>
        </table>
    </div>

    <!-- Fields -->
    <div class="field-row">
        <span class="field-label">Nama :</span>
        <span class="field-value">{{ $leave->name }}</span>
    </div>
    <div class="field-row">
        <span class="field-label">Jabatan :</span>
        <span class="field-value">{{ $leave->position }}</span>
    </div>
    <div class="field-row">
        <span class="field-label">Departemen :</span>
        <span class="field-value">{{ $leave->department }}</span>
    </div>

    <!-- Narrative -->
    <div class="narrative">
        Dengan ini mengajukan permohonan cuti selama
        <span class="narrative-line" style="width: 50px;">{{ $leave->duration_days }}</span>
        hari kerja, terhitung mulai tanggal <br>
        <span class="narrative-line" style="width: 150px;">{{ $leave->start_date->format('Y-m-d') }}</span>
        s/d
        <span class="narrative-line" style="width: 150px;">{{ $leave->end_date->format('Y-m-d') }}</span>
        untuk keperluan :<br>
        <div class="field-value" style="width: 100%; margin-top: 5px;">{{ $leave->purpose }}</div>
    </div>

    <div style="margin-top: 20px;">
        Demikian permohonan cuti ini saya buat, untuk dapat dipertimbangkan sebagaimana mestinya
    </div>

    <!-- Notes -->
    <div class="notes">
        <div class="notes-title">Note :</div>
        <ol>
            <li>Surat permohonan cuti kerja harus sudah diajukan 1 (satu) minggu sebelum pelaksanaan cuti.</li>
            <li>Permohonan Cuti yang mendadak harus dilandasi oleh alasan kuat yang berhubungan dengan cuti dimaksud.
            </li>
            <li>
                Hak Cuti Thn. .......... (sebelumnya)

                <table class="calc-table">
                    <tr>
                        <td class="calc-label"><strong>Hak Cuti Thn. .......... (berjalan)</strong></td>
                        <td class="calc-eq">=</td>
                        <td class="calc-val">{{ $leave->hak_curr + 0 }}</td>
                        <!-- +0 to remove trailing zeros if decimal -->
                        <td class="calc-unit">Hari</td>
                    </tr>
                    <tr>
                        <td class="calc-label">Total Hak Cuti</td>
                        <td class="calc-eq">=</td>
                        <td class="calc-val">{{ $leave->total_hak + 0 }}</td>
                        <td class="calc-unit">Hari</td>
                    </tr>
                    <tr>
                        <td class="calc-label"><strong>Cuti yang telah diambil s/d ................ (hari ini)</strong>
                        </td>
                        <td class="calc-eq">=</td>
                        <td class="calc-val">{{ $leave->taken_until + 0 }}</td>
                        <td class="calc-unit">Hari</td>
                    </tr>
                    <tr>
                        <td class="calc-label"><strong>Sisa Cuti</strong></td>
                        <td class="calc-eq">=</td>
                        <td class="calc-val">{{ $leave->sisa_curr + 0 }}</td>
                        <td class="calc-unit">Hari</td>
                    </tr>
                    <tr>
                        <td class="calc-label"><strong>Permohonan Cuti</strong></td>
                        <td class="calc-eq">=</td>
                        <td class="calc-val">{{ $leave->request_days + 0 }}</td>
                        <td class="calc-unit">Hari</td>
                    </tr>
                    <tr>
                        <td class="calc-label"><strong>Sisa Cuti Per .......... (tanggal hari ini)</strong></td>
                        <td class="calc-eq">=</td>
                        <td class="calc-val">{{ $leave->sisa_after + 0 }}</td>
                        <td class="calc-unit">Hari</td>
                    </tr>
                </table>
            </li>
            <li style="margin-top: 10px;">Bagi Karyawan yang belum memiliki hak cuti atau hak cuti sudah habis (cuti
                negatif), wajib meminta persetujuan Ketua Yayasan untuk pengajuan permohonan cuti dan diserahkan kepada
                HRD.</li>
        </ol>
    </div>

    <!-- Signatures -->
    <table class="sig-table">
        <tr>
            <td class="sig-td">
                <div class="sig-role">Diajukan Oleh,</div>
                @if($signaturePemohon)
                    <img src="{{ $signaturePemohon }}" class="sig-img">
                @else
                    <div style="height: 50px;"></div>
                @endif
                <div class="sig-name">{{ $leave->name }}</div>
            </td>

            <td class="sig-td">
                <div class="sig-role">Disetujui Oleh,</div>
                @if($signatures['koordinator']['src'])
                    <img src="{{ $signatures['koordinator']['src'] }}" class="sig-img">
                @else
                    <div style="height: 50px;"></div>
                @endif
                <!-- Image shows "Juarsa Oemardikarta" in middle. Coordinator for ICT is Juarsa. -->
                <div class="sig-name">{{ $signatures['koordinator']['name'] ?? '................' }}</div>
            </td>

            <td class="sig-td">
                <div class="sig-role">Diketahui Oleh,</div>
                @if($signatures['hrd']['src'])
                    <img src="{{ $signatures['hrd']['src'] }}" class="sig-img">
                @else
                    <div style="height: 50px;"></div>
                @endif
                <div class="sig-name">{{ $signatures['hrd']['name'] ?? '................' }}</div>
            </td>
        </tr>
    </table>

    <div class="footer-id">
        YSPE-HRD-FM-019<br>
        Rev.02, 17-04-2023
    </div>
</body>

</html>