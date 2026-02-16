<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Asset Labels</title>
    <style>
        /* Print-specific styles */
        @media print {
            @page {
                size: A4;
                margin: 10mm;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .page-break {
                page-break-after: always;
            }
        }

        /* General styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 10mm;
        }

        /* Label grid - 3 columns, 60x40mm labels */
        .labels-grid {
            display: grid;
            grid-template-columns: repeat(3, 60mm);
            gap: 5mm;
            margin: 20px 0;
        }

        /* Individual label */
        .label {
            width: 60mm;
            height: 40mm;
            border: 1px solid #ddd;
            padding: 2mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: white;
            position: relative;
            break-inside: avoid;
        }

        .label-header {
            width: 100%;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }

        .label-header .school-name {
            font-size: 8pt;
            font-weight: bold;
            color: #333;
        }

        .label-header .iso-standard {
            font-size: 6pt;
            color: #666;
        }

        .label-qr {
            width: 25mm;
            height: 25mm;
            margin: 1mm 0;
        }

        .label-qr img {
            width: 100%;
            height: 100%;
            display: block;
        }

        .label-code {
            font-size: 9pt;
            font-weight: bold;
            color: #000;
            margin: 1mm 0;
            text-align: center;
            font-family: 'Courier New', monospace;
        }

        .label-info {
            width: 100%;
            text-align: center;
        }

        .label-name {
            font-size: 7pt;
            font-weight: 600;
            color: #333;
            margin: 0.5mm 0;
            line-height: 1.2;
            max-height: 8mm;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .label-meta {
            font-size: 6pt;
            color: #666;
            margin-top: 0.5mm;
            line-height: 1.3;
        }

        .label-meta div {
            margin: 0.3mm 0;
        }

        /* Print button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .print-button:hover {
            background: #45a049;
        }

        .header-actions {
            margin-bottom: 20px;
            padding: 15px;
            background: #e3f2fd;
            border-radius: 4px;
        }

        .header-actions h1 {
            font-size: 18px;
            margin-bottom: 5px;
            color: #1976d2;
        }

        .header-actions p {
            font-size: 14px;
            color: #666;
        }
    </style>
</head>

<body>
    <button onclick="window.print()" class="print-button no-print">🖨️ Print Labels</button>

    <div class="container">
        <div class="header-actions no-print">
            <h1>Print Asset Labels</h1>
            <p>Total: {{ count($assets) }} label(s) | Format: 60mm x 40mm | ISO 21001:2018 Compliant</p>
        </div>

        <div class="labels-grid">
            @foreach($assets as $asset)
                <div class="label">
                    <div class="label-header">
                        <div class="school-name">EDELWEISS SCHOOL</div>
                        <div class="iso-standard">ISO 21001:2018</div>
                    </div>

                    <div class="label-qr">
                        <img src="data:image/svg+xml;base64,{{ $asset->qr_code }}" alt="QR Code">
                    </div>

                    <div class="label-code">{{ $asset->asset_code }}</div>

                    <div class="label-info">
                        <div class="label-name">{{ Str::limit($asset->name, 40) }}</div>
                        <div class="label-meta">
                            <div><strong>Cat:</strong> {{ $asset->category->name }}</div>
                            <div><strong>Loc:</strong> {{ $asset->location->code }}</div>
                            @if($asset->purchase_date)
                                <div><strong>Date:</strong> {{ $asset->purchase_date->format('m/Y') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>

</html>