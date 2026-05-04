<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; width: 226px; height: 142px; background: #fff; padding: 8px; }
        .tag-container { border: 2px solid #333; border-radius: 6px; padding: 8px; width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: space-between; }
        .nama-menu { font-size: 13px; font-weight: bold; text-align: center; color: #111; text-transform: uppercase; letter-spacing: 0.5px; }
        .harga { font-size: 18px; font-weight: bold; text-align: center; color: #d9534f; }
        .harga span { font-size: 11px; color: #555; font-weight: normal; }
        .barcode-section { text-align: center; }
        .barcode-section img { max-width: 100%; height: 40px; }
        .id-label { font-size: 8px; color: #888; text-align: center; margin-top: 2px; }
        .divider { border: none; border-top: 1px dashed #ccc; }
    </style>
</head>
<body>
    <div class="tag-container">
        <div class="nama-menu">{{ $menu->nama_menu }}</div>
        <hr class="divider">
        <div class="harga">
            <span>Rp</span> {{ number_format($menu->harga, 0, ',', '.') }}
        </div>
        <hr class="divider">
        <div class="barcode-section">
            <img src="{{ $barcodeBase64 }}" alt="Barcode {{ $menu->idmenu }}">
            <div class="id-label">ID: {{ str_pad($menu->idmenu, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>
</body>
</html>
