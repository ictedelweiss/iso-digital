<!DOCTYPE html>
<html>

<head>
    <title>Form Permohonan Cuti</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            color: #000;
            font-size: 11px;
            margin: 0;
            padding: 20px 40px;
            line-height: 1.5;
        }

        /* Header */
        .header {
            width: 100%;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1e40af;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-left {
            width: 30%;
            font-weight: bold;
            font-size: 12px;
            color: #003087;
            vertical-align: middle;
            text-align: left;
        }

        .header-center {
            width: 40%;
            text-align: center;
            vertical-align: middle;
        }

        .header-title {
            color: #1e40af;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-right {
            width: 30%;
            text-align: right;
            vertical-align: middle;
        }

        .header-right img {
            max-height: 50px;
        }

        /* Content Fields with Dotted Lines */
        .field-row {
            margin-bottom: 5px;
            display: flex;
        }

        .field-label {
            width: 130px;
            flex-shrink: 0;
        }

        .field-value {
            flex: 1;
            border-bottom: 1px dotted #000;
            padding-left: 5px;
            min-height: 15px;
        }

        /* Narrative Text */
        .narrative {
            margin: 20px 0;
            line-height: 1.8;
        }

        .narrative-line {
            border-bottom: 1px dotted #000;
            padding: 0 10px;
            display: inline-block;
            min-width: 50px;
            text-align: center;
        }

        .narrative-line-block {
            border-bottom: 1px dotted #000;
            padding: 2px 5px;
            min-height: 20px;
            width: 100%;
            margin-top: 5px;
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
            margin-bottom: 8px;
            text-align: justify;
        }

        /* Calculation Table inside Notes */
        .calc-table {
            margin-left: 40px;
            margin-top: 8px;
            width: calc(100% - 60px);
        }

        .calc-table tr {
            line-height: 1.6;
        }

        .calc-label {
            width: 60%;
            padding-right: 10px;
        }

        .calc-eq {
            width: 30px;
            text-align: center;
        }

        .calc-val {
            width: 80px;
            text-align: left;
            border-bottom: 1px dotted #000;
            padding-left: 5px;
        }

        .calc-unit {
            width: 60px;
            padding-left: 10px;
        }

        /* Signatures Table */
        .sig-container {
            margin-top: 40px;
        }

        .sig-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        .sig-table td {
            border: 1px solid #000;
            text-align: center;
            vertical-align: top;
            width: 33.33%;
            padding: 10px;
        }

        .sig-role {
            margin-bottom: 60px;
            font-size: 11px;
        }

        .sig-img-container {
            height: 50px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            margin-bottom: 5px;
        }

        .sig-img {
            max-height: 50px;
            max-width: 150px;
            display: block;
        }

        .sig-name {
            font-weight: bold;
            font-size: 11px;
            margin-top: 5px;
        }

        .sig-title {
            font-weight: bold;
            font-size: 11px;
            margin-top: 5px;
        }

        /* Footer */
        .footer-id {
            text-align: right;
            font-size: 9px;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <!-- Header -->
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

    <!-- Personal Information Fields -->
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

    <!-- Leave Request Narrative -->
    <div class="narrative">
        Dengan ini mengajukan permohonan cuti selama
        <span class="narrative-line">{{ $leave->duration_days }}</span>
        hari kerja, terhitung mulai tanggal<br>
        <span class="narrative-line"
            style="min-width: 150px;">{{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }}</span>
        s/d
        <span class="narrative-line"
            style="min-width: 150px;">{{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}</span>
        untuk keperluan :<br>
        <div class="narrative-line-block">{{ $leave->purpose }}</div>
    </div>

    <div style="margin-top: 15px;">
        Demikian permohonan cuti ini saya buat, untuk dapat dipertimbangkan sebagaimana mestinya
    </div>

    <!-- Notes Section -->
    <div class="notes">
        <div class="notes-title">Note :</div>
        <ol>
            <li>Surat permohonan cuti kerja harus sudah diajukan 1 (satu) minggu sebelum pelaksanaan cuti.</li>
            <li>Permohonan Cuti yang mendadak harus dilandasi oleh alasan kuat yang berhubungan dengan cuti dimaksud.
            </li>
            <li>
                <table class="calc-table">
                    <tr>
                        <td class="calc-label">Hak Cuti Thn. .......... (sebelumnya)</td>
                        <td class="calc-eq">=</td>
                        <td class="calc-val"></td>
                        <td class="calc-unit">Hari</td>
                    </tr>
                    <tr>
                        <td class="calc-label"><strong>Hak Cuti Thn. .......... (berjalan)</strong></td>
                        <td class="calc-eq">=</td>
                        <td class="calc-val"></td>
                        <td class="calc-unit">+/+</td>
                    </tr>
                    <tr>
                        <td class="calc-label">Total Hak Cuti</td>
                        <td class="calc-eq">=</td>
                        <td class="calc-val"></td>
                        <td class="calc-unit">Hari</td>
                    </tr>
                    <tr>
                        <td class="calc-label"><strong>Cuti yang telah diambil s/d .................. (hari ini
                                )</strong></td>
                        <td class="calc-eq">=</td>
                        <td class="calc-val"></td>
                        <td class="calc-unit">Hari -/-</td>
                    </tr>
                    <tr>
                        <td class="calc-label">Sisa Cuti</td>
                        <td class="calc-eq">=</td>
                        <td class="calc-val"></td>
                        <td class="calc-unit">Hari</td>
                    </tr>
                    <tr>
                        <td class="calc-label">Permohonan Cuti</td>
                        <td class="calc-eq">=</td>
                        <td class="calc-val"></td>
                        <td class="calc-unit">Hari -/-</td>
                    </tr>
                    <tr>
                        <td class="calc-label"><strong>Sisa Cuti Per ............... (tanggal hari ini)</strong></td>
                        <td class="calc-eq">=</td>
                        <td class="calc-val"></td>
                        <td class="calc-unit"><strong>Hari</strong></td>
                    </tr>
                </table>
            </li>
            <li>Bagi Karyawan yang belum memiliki hak cuti atau hak cuti sudah habis (cuti negatif), wajib meminta
                persetujuan Ketua Yayasan untuk pengajuan permohonan cuti dan diserahkan kepada HRD.</li>
        </ol>
    </div>

    <!-- Signatures -->
    <div class="sig-container">
        <table class="sig-table">
            <tr>
                <!-- Column 1: Diajukan Oleh -->
                <td>
                    <div class="sig-role">Diajukan Oleh,</div>
                    <div class="sig-img-container">
                        @if($signaturePemohon)
                            <img src="{{ $signaturePemohon }}" class="sig-img">
                        @endif
                    </div>
                    <div class="sig-name">{{ $leave->name }}</div>
                    <div class="sig-title">Pemohon</div>
                </td>

                <!-- Column 2: Disetujui Oleh -->
                <td>
                    <div class="sig-role">Disetujui Oleh,</div>
                    <div class="sig-img-container">
                        @if($signatures['koordinator']['src'])
                            <img src="{{ $signatures['koordinator']['src'] }}" class="sig-img">
                        @endif
                    </div>
                    <div class="sig-name">{{ $signatures['koordinator']['name'] ?? '................................' }}
                    </div>
                    <div class="sig-title">Direct Superior</div>
                </td>

                <!-- Column 3: Diketahui Oleh -->
                <td>
                    <div class="sig-role">Diketahui Oleh,</div>
                    <div class="sig-img-container">
                        @if($signatures['hrd']['src'])
                            <img src="{{ $signatures['hrd']['src'] }}" class="sig-img">
                        @endif
                    </div>
                    <div class="sig-name">{{ $signatures['hrd']['name'] ?? '................................' }}</div>
                    <div class="sig-title">HRD/Chairman</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-id">
        YSPE-HRD-FM-019<br>
        Rev.02, 17-04-2023
    </div>
</body>

</html>