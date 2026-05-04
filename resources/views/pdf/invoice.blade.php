<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; padding: 30px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; font-weight: bold; color: #111; }
        .header p { font-size: 11px; color: #666; }
        .invoice-meta { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .invoice-meta table td { padding: 2px 6px 2px 0; }
        .invoice-meta .label { color: #666; font-size: 11px; }
        .invoice-meta .value { font-weight: bold; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background: #333; color: #fff; padding: 8px; text-align: left; font-size: 11px; }
        .items-table td { padding: 7px 8px; border-bottom: 1px solid #eee; font-size: 11px; }
        .items-table tr:nth-child(even) td { background: #f9f9f9; }
        .total-section { text-align: right; margin-bottom: 25px; }
        .total-section table { margin-left: auto; }
        .total-section td { padding: 4px 8px; }
        .total-section .grand-total { font-size: 15px; font-weight: bold; color: #d9534f; border-top: 2px solid #333; }
        .footer { display: flex; justify-content: space-between; align-items: flex-end; border-top: 1px dashed #ccc; padding-top: 15px; }
        .footer .status-badge { display: inline-block; background: #5cb85c; color: #fff; padding: 5px 14px; border-radius: 4px; font-weight: bold; font-size: 13px; }
        .qr-section { text-align: center; }
        .qr-section img { width: 100px; height: 100px; }
        .qr-section .qr-label { font-size: 9px; color: #888; margin-top: 3px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>🍽️ Kantin Cerdas</h1>
        <p>Jl. Kampus No. 1 | Telp: (031) 000-0000</p>
        <p>INVOICE PEMBAYARAN</p>
    </div>

    <div class="invoice-meta">
        <table>
            <tr>
                <td class="label">No. Pesanan</td>
                <td class="value">: #{{ str_pad($pesanan->idpesanan, 6, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal</td>
                <td class="value">: {{ \Carbon\Carbon::parse($pesanan->timestamp)->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td class="value">: {{ $pesanan->status_bayar == 1 ? 'LUNAS' : 'BELUM LUNAS' }}</td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="label">Nama Pelanggan</td>
                <td class="value">: {{ $pesanan->nama }}</td>
            </tr>
            <tr>
                <td class="label">Order ID</td>
                <td class="value">: {{ $pesanan->order_id ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Metode Bayar</td>
                <td class="value">: {{ $pesanan->metode_bayar ? strtoupper($pesanan->metode_bayar) : '-' }}</td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Menu</th>
                <th>Harga Satuan</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pesanan->detailPesanans as $i => $detail)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $detail->menu->nama_menu ?? '-' }}</td>
                <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                <td>{{ $detail->jumlah }}</td>
                <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <table>
            <tr>
                <td>Subtotal</td>
                <td>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Biaya Layanan</td>
                <td>Rp 0</td>
            </tr>
            <tr class="grand-total">
                <td><strong>TOTAL BAYAR</strong></td>
                <td><strong>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <div>
            <p><strong>Status Pembayaran:</strong></p>
            <span class="status-badge">✓ LUNAS</span>
            <p style="margin-top:10px; font-size:10px; color:#888;">
                Terima kasih telah berbelanja!<br>
                Dokumen ini dicetak secara otomatis.
            </p>
        </div>

        <div class="qr-section">
            <img src="{{ $qrBase64 }}" alt="QR Pesanan">
            <div class="qr-label">Scan untuk verifikasi</div>
            <div class="qr-label">ID: {{ $pesanan->idpesanan }}</div>
        </div>
    </div>

</body>
</html>
