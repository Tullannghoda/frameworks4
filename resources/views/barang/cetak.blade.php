<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            margin-top: 0mm;
            margin-bottom: 0mm;
            margin-left: 0mm;
            margin-right: 0mm;
        }
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        td {
            border: 1px dashed #ccc;
            width: 20%;
            height: 90px;
            text-align: center;
            vertical-align: middle;
            font-size: 11px;
            padding: 4px;
        }

        .barcode-img {
            width: 100%;
            max-width: 130px;
            height: 40px;
        }

        .nama {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 2px;
        }

        .harga {
            font-weight: bold;
            font-size: 12px;
            color: #000;
        }

        .id-text {
            font-size: 9px;
            color: #555;
        }
    </style>
</head>
<body>

@php
    use Picqer\Barcode\BarcodeGeneratorPNG;
    $generator   = new BarcodeGeneratorPNG();
    $startIndex  = ($startY - 1) * 5 + ($startX - 1);
    $barangIndex = 0;
@endphp

<table>

@for($row = 0; $row < 8; $row++)
    <tr>
        @for($col = 0; $col < 5; $col++)
            @php $currentIndex = $row * 5 + $col; @endphp

            @if($currentIndex < $startIndex)
                <td></td>
            @elseif($barangIndex < count($barang))
                @php
                    $b       = $barang[$barangIndex];
                    $png     = $generator->getBarcode((string) $b->id_barang, $generator::TYPE_CODE_128, 2, 40);
                    $base64  = base64_encode($png);
                @endphp
                <td>
                    <div class="nama">{{ $b->nama_barang }}</div>
                    <img class="barcode-img" src="data:image/png;base64,{{ $base64 }}">
                    <div class="id-text">ID: {{ $b->id_barang }}</div>
                    <div class="harga">Rp {{ number_format($b->harga, 0, ',', '.') }}</div>
                </td>
                @php $barangIndex++; @endphp
            @else
                <td></td>
            @endif
        @endfor
    </tr>
@endfor

</table>

</body>
</html>