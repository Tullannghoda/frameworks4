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
            border: none;
            width: 20%;
            height: 80px;
            text-align: center;
            vertical-align: middle;
            font-size: 12px;
        }
    </style>
</head>
<body>

@php
    $startIndex = ($startY - 1) * 5 + ($startX - 1);
    $totalSlots = 40;
    $barangIndex = 0;
@endphp

<table>

@for($row = 0; $row < 8; $row++)
    <tr>
        @for($col = 0; $col < 5; $col++)
            @php
                $currentIndex = $row * 5 + $col;
            @endphp

            @if($currentIndex < $startIndex)
                <td></td>
            @elseif($barangIndex < count($barang))
                <td>
                    <strong>{{ $barang[$barangIndex]->nama_barang }}</strong><br>
                    {{ $barang[$barangIndex]->id_barang }} <br>
                    <strong>{{ number_format($barang[$barangIndex]->harga, 0, ',', '.') }}</strong><br>
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