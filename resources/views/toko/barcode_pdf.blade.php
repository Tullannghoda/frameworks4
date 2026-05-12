<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; width: 226px; height: 142px; background: #fff; padding: 8px; }
        .tag { border: 2px solid #333; border-radius: 6px; padding: 8px; width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: space-between; }
        .nama { font-size: 12px; font-weight: bold; text-align: center; text-transform: uppercase; }
        .alamat { font-size: 9px; color: #555; text-align: center; }
        .barcode-section { text-align: center; }
        .barcode-section img { max-width: 100%; height: 40px; }
        .id-label { font-size: 8px; color: #888; text-align: center; margin-top: 2px; }
        .divider { border: none; border-top: 1px dashed #ccc; }
    </style>
</head>
<body>
    <div class="tag">
        <div class="nama">{{ $toko->nama_toko }}</div>
        @if($toko->alamat)
        <div class="alamat">{{ Str::limit($toko->alamat, 50) }}</div>
        @endif
        <hr class="divider">
        <div class="barcode-section">
            <img src="{{ $barcodeBase64 }}" alt="{{ $toko->barcode }}">
            <div class="id-label">{{ $toko->barcode }}</div>
        </div>
    </div>
</body>
</html>
