<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Purchase Requisition {{ $pr->pr_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            color: #111;
            font-size: 13px;
            margin: 0;
            padding: 6mm;
            line-height: 1.4;
        }

        /* Header Section */
        .top {
            display: -webkit-box;
            /* dompdf flex support workaround */
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .box-left {
            border: 1px solid #111;
            padding: 10px 14px;
            font-weight: 700;
            font-size: 15px;
            text-align: center;
            line-height: 1.2;
            width: 30%;
            /* Fixed width for float layout if flex fails */
            float: left;
        }

        .title {
            text-align: center;
            color: #1f4b99;
            font-size: 20px;
            font-weight: 700;
            margin: 6px 0 8px;
            width: 40%;
            float: left;
            padding-top: 10px;
        }

        .logo-right {
            width: 30%;
            float: right;
            text-align: right;
        }

        .logo-right img {
            max-height: 50px;
            max-width: 130px;
        }

        /* Clear floats */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* Info Section */
        .meta {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 10px;
            margin-top: 60px;
            /* Space for floated header */
        }

        .meta td {
            padding: 2px 4px;
        }

        .meta td strong {
            font-weight: 700;
        }

        .chk {
            display: inline-block;
            margin-right: 12px;
            font-weight: 700;
        }

        .chk-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #111;
            text-align: center;
            line-height: 12px;
            font-size: 11px;
            font-weight: 700;
            margin-right: 4px;
        }

        /* Table */
        .table-pr {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin: 8px 0;
        }

        .table-pr th {
            border: 1px solid #111;
            padding: 6px 6px;
            text-align: center;
            font-weight: 700;
            background: #9fb7d6;
        }

        .table-pr td {
            border: 1px solid #111;
            padding: 6px 6px;
            text-align: left;
        }

        .table-pr td.num {
            text-align: center;
            width: 32px;
        }

        .table-pr td.desc {
            text-align: left;
        }

        .table-pr td.qty {
            text-align: center;
            width: 60px;
        }

        .table-pr td.unit {
            text-align: center;
            width: 70px;
        }

        .table-pr td.price {
            text-align: right;
            width: 90px;
        }

        .table-pr td.total {
            text-align: right;
            width: 110px;
        }

        .table-pr .grand {
            background: #9fb7d6;
            font-weight: 700;
        }

        /* Note Box */
        .note {
            border: 1px solid #111;
            padding: 8px;
            margin-top: 8px;
            font-size: 12px;
        }

        /* Approval Section */
        .sign {
            width: 100%;
            margin-top: 14px;
            font-size: 12px;
            text-align: center;
            border-collapse: separate;
            border-spacing: 12px 0;
        }

        .sign td {
            vertical-align: top;
            padding: 0;
            width: 25%;
        }

        .box {
            padding: 12px 6px;
            min-height: 90px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: none;
            /* Layout only */
        }

        .box-inner {
            display: block;
            min-height: 100px;
        }

        .sign strong {
            font-weight: 700;
            display: block;
            margin-bottom: 5px;
        }

        .muted {
            color: #444;
            font-size: 11px;
            margin-top: 5px;
            display: block;
        }

        .approval-img {
            height: 50px;
            display: block;
            margin: 5px auto;
        }

        .approval-img img {
            max-height: 45px;
            max-width: 95%;
        }

        /* Footer */
        .footer {
            font-size: 10px;
            text-align: right;
            margin-top: 6px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="clearfix">
        <div class="box-left">YAYASAN<br>SINAR PUTIH<br>EDELWEISS</div>
        <div class="title">Purchase Requisition</div>
        <div class="logo-right">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" alt="Logo">
            @endif
        </div>
    </div>

    <!-- Info -->
    <table class="meta">
        <tr>
            <td width="20%">Nama Pemohon</td>
            <td width="30%">: <strong>{{ $pr->requester }}</strong></td>
            <td width="15%">Tanggal</td>
            <td width="35%">: {{ $pr->needed_date->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td>Departemen</td>
            <td>: {{ $pr->department }}</td>
            <td>Nomor</td>
            <td>: {{ $pr->pr_number }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td colspan="2" style="padding-top: 8px;">
                @php
                    $isDianggarkan = in_array(strtolower($pr->budget_status), ['tersedia', 'terbatas', 'dianggarkan']);
                    $isBelum = in_array(strtolower($pr->budget_status), ['tidak tersedia', 'belum dianggarkan']);
                @endphp
                <div class="chk">
                    <span class="chk-box">{{ $isDianggarkan ? '✓' : '' }}</span><strong>Dianggarkan</strong>
                </div>
                <div class="chk">
                    <span class="chk-box">{{ $isBelum ? '✓' : '' }}</span><strong>Belum dianggarkan</strong>
                </div>
            </td>
        </tr>
    </table>

    <!-- Table -->
    <table class="table-pr">
        <thead>
            <tr>
                <th>No</th>
                <th>Deskripsi</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
                <tr>
                    <td class="num">{{ $index + 1 }}</td>
                    <td class="desc">{{ $item['item_name'] ?? '' }}</td>
                    @if($item)
                        <td class="qty">{{ number_format($item['qty'], 0) }}</td>
                        <td class="unit">{{ $item['unit'] }}</td>
                        <td class="price">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                        <td class="total">Rp {{ number_format($item['qty'] * $item['price'], 0, ',', '.') }}</td>
                    @else
                        <td class="qty"></td>
                        <td class="unit"></td>
                        <td class="price"></td>
                        <td class="total"></td>
                    @endif
                </tr>
            @endforeach

            <tr class="grand">
                <td colspan="4" style="text-align:right;">Grand Total</td>
                <td style="text-align:center;">Rp</td>
                <td class="total">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Note -->
    <div class="note"><strong>Catatan:</strong> {{ $pr->notes }}</div>

    <!-- Approval -->
    <table class="sign">
        <tr>
            <td>
                <div class="box-inner">
                    <div>Dibuat oleh,<br><strong>Pemohon</strong></div>
                    <div class="approval-img">
                        @if($signaturePemohon)
                            <img src="{{ $signaturePemohon }}" alt="TTD">
                        @else
                            <br><br>
                        @endif
                    </div>
                    <div class="muted">( {{ $pr->requester }} )</div>
                </div>
            </td>
            <td>
                <div class="box-inner">
                    <div>Disetujui oleh,<br><strong>Koordinator</strong></div>
                    <div class="approval-img">
                        @if($signatures['koordinator']['src'])
                            <img src="{{ $signatures['koordinator']['src'] }}" alt="TTD">
                        @else
                            <br><br>
                        @endif
                    </div>
                    <div class="muted">( {{ $signatures['koordinator']['name'] }} )</div>
                </div>
            </td>
            <td>
                <div class="box-inner">
                    <div>Diverifikasi oleh,<br><strong>Accounting</strong></div>
                    <div class="approval-img">
                        @if($signatures['accounting']['src'])
                            <img src="{{ $signatures['accounting']['src'] }}" alt="TTD">
                        @else
                            <br><br>
                        @endif
                    </div>
                    <div class="muted">( {{ $signatures['accounting']['name'] }} )</div>
                </div>
            </td>
            <td>
                <div class="box-inner">
                    <div>Disetujui oleh,<br><strong>Ketua Yayasan</strong></div>
                    <div class="approval-img">
                        @if($signatures['ketua_yayasan']['src'])
                            <img src="{{ $signatures['ketua_yayasan']['src'] }}" alt="TTD">
                        @else
                            <br><br>
                        @endif
                    </div>
                    <div class="muted">( {{ $signatures['ketua_yayasan']['name'] }} )</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        YSPE-FNA-FM-001<br>
        Rev.03, 22-10-2024
    </div>
</body>

</html>