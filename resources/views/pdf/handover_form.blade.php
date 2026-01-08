<!DOCTYPE html>
<html>

<head>
    <title>Form Serah Terima Perangkat ICT</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            color: #000;
            font-size: 11px;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }

        /* Header Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000;
        }

        .header-table td {
            border: 1px solid #000;
            padding: 10px;
            vertical-align: middle;
            text-align: center;
        }

        .header-left {
            width: 25%;
            font-weight: bold;
            font-size: 12px;
        }

        .header-center {
            width: 50%;
            font-weight: bold;
            font-size: 14px;
        }

        .header-right {
            width: 25%;
        }

        .header-right img {
            max-width: 120px;
            max-height: 60px;
        }

        /* General Info */
        .info-section {
            margin-bottom: 15px;
            padding-left: 5px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }

        .info-label {
            display: table-cell;
            width: 180px;
        }

        .info-sep {
            display: table-cell;
            width: 10px;
        }

        .info-val {
            display: table-cell;
        }

        /* Usage Guide */
        .guide-section {
            margin-bottom: 15px;
            padding-left: 5px;
        }

        .guide-title {
            text-decoration: underline;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .guide-list {
            margin: 0;
            padding-left: 20px;
        }

        .guide-list li {
            margin-bottom: 2px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }

        .items-table th {
            font-weight: bold;
            background-color: #fff;
            height: 40px;
        }

        .items-table td {
            height: 25px;
        }

        /* Footer Text */
        .confirmation-text {
            text-align: center;
            margin: 15px 0 40px 0;
        }

        /* Signatures */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-top: 20px;
        }

        .signature-table td {
            border: none;
            text-align: center;
            vertical-align: top;
            width: 25%;
            padding: 0 5px;
        }

        .sig-role {
            margin-bottom: 40px;
        }

        .sig-img-container {
            height: 60px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            margin-bottom: 5px;
        }

        .sig-img {
            max-height: 55px;
            max-width: 100px;
            display: block;
            margin: 0 auto;
        }

        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Actually image names are typically in parentheses in old one, but user image shows name in bold parentheses */
        .sig-title {
            font-weight: bold;
            margin-top: 2px;
            font-size: 10px;
        }

        /* Doc ID Footer */
        .doc-id {
            position: fixed;
            bottom: 20px;
            right: 0;
            text-align: right;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <table class="header-table">
        <tr>
            <td class="header-left">
                YAYASAN<br>SINAR PUTIH<br>EDELWEISS
            </td>
            <td class="header-center">
                FORM SERAH TERIMA<br>PERANGKAT ICT
            </td>
            <td class="header-right">
                <img src="{{ $logoSrc }}" alt="Edelweiss School">
            </td>
        </tr>
    </table>

    <!-- Info -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Dari</div>
            <div class="info-sep">:</div>
            <div class="info-val">{{ $form->creator->name ?? 'ICT' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Kepada (Nama Peminjam)</div>
            <div class="info-sep">:</div>
            <div class="info-val">{{ $form->recipient_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Perihal</div>
            <div class="info-sep">:</div>
            <div class="info-val">{{ $form->item_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Hari & Tanggal</div>
            <div class="info-sep">:</div>
            <div class="info-val">{{ \Carbon\Carbon::parse($form->handover_date)->isoFormat('D MMMM Y') }}</div>
        </div>
    </div>

    <!-- Guide -->
    <div class="guide-section">
        <div class="guide-title">Panduan Penggunaan</div>
        <ol class="guide-list">
            <li>Untuk digunakan dalam menjalankan pekerjaan yang berkaitan dengan pekerjaan Yayasan/Sekolah.</li>
            <li>Penggunaan harus sesuai dengan ketentuan Perusahaan dan kode etik yang berlaku terkait ITE dan media
                sosial.</li>
            <li>ICT meminta tandatangan di bagian kolom penyerahan pada karyawan yang menerima perangkat.</li>
            <li>ICT meminta tandatangan di bagian kolom pengembalian pada karyawan setelah proses pengembalian
                perangkat.</li>
            <li>Wajib menjaga laptop secara baik dan aman.</li>
            <li>Wajib melaporkan ke Yayasan jika terdapat kerusakan dan atau kehilangan.</li>
            <li>Peminjam wajib mengganti jika karena kecerobohan dan kelalaian menyebabkan laptop rusak dan hilang,
                nilai penggantian akan diputuskan oleh Pengurus Yayasan pada saat kejadian.</li>
        </ol>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Barang</th>
                <th width="15%">Spesifikasi</th>
                <th width="10%">Jumlah</th>
                <th width="15%">Masa<br>Peminjaman</th>
                <th width="10%">Penyerahan<br>(Paraf)</th>
                <th width="10%">Pengembalian<br>(Paraf)</th>
                <th width="15%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ $form->item_name }}</td>
                <td>{{ $form->specification }}</td>
                <td>{{ $form->quantity }}</td>
                <td>{{ $form->loan_period }}</td>
                <td>
                    <!-- Paraf Penyerahan (Recipient small sig or tick?) - Leaving blank for manual paraf as per typically usage, or could put checkmark if approved -->
                </td>
                <td>
                    <!-- Paraf Pengembalian - Empty for future -->
                </td>
                <td>{{ $form->notes }}</td>
            </tr>
            <!-- Empty rows to match the look of a form -->
            @for($i = 2; $i <= 5; $i++)
                <tr>
                    <td>{{ $i }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="confirmation-text">
        Mohon dilakukan pengecekan bersama dan ditandatangani apabila sudah diterima dengan baik.
    </div>

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <!-- 1. Diketahui Oleh (Koordinator) -->
            <td>
                <div class="sig-role">Diketahui Oleh,</div>
                <div class="sig-img-container">
                    @if($signatures['coordinator']['src'])
                        <img src="{{ $signatures['coordinator']['src'] }}" class="sig-img">
                    @endif
                </div>
                <div class="sig-name">({{ $signatures['coordinator']['name'] ?? '................' }})</div>
                <div class="sig-title">Koordinator</div>
            </td>

            <!-- 2. Disetujui Oleh (HRD) -->
            <td>
                <div class="sig-role">Disetujui Oleh,</div>
                <div class="sig-img-container">
                    @if($signatures['hrd']['src'])
                        <img src="{{ $signatures['hrd']['src'] }}" class="sig-img">
                    @endif
                </div>
                <div class="sig-name">({{ $signatures['hrd']['name'] ?? '................' }})</div>
                <div class="sig-title">HRD</div>
            </td>

            <!-- 3. Diserahkan Oleh (ICT) -->
            <td>
                <div class="sig-role">Diserahkan Oleh,</div>
                <div class="sig-img-container">
                    @if($signatureICT)
                        <img src="{{ $signatureICT }}" class="sig-img">
                    @endif
                </div>
                <div class="sig-name">({{ $form->creator->name ?? 'Aris Setyawan' }})</div>
                <div class="sig-title">ICT</div>
            </td>

            <!-- 4. Diterima Oleh (Recipient) -->
            <td>
                <div class="sig-role">Diterima Oleh,</div>
                <div class="sig-img-container">
                    @if($signatures['recipient']['src'])
                        <img src="{{ $signatures['recipient']['src'] }}" class="sig-img">
                    @endif
                </div>
                <div class="sig-name">({{ $signatures['recipient']['name'] ?? '................' }})</div>
                <div class="sig-title">{{ $form->recipient_name }}</div>
                <!-- Assuming Title is name repeated or job title? Image has title below name. Using recipient name for now as title isn't stored separately -->
            </td>
        </tr>
    </table>

    <div class="doc-id">
        YSPE-ICT-FM-002<br>
        Rev.04, 10-12-2024
    </div>
</body>

</html>