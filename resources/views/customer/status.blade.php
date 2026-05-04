@extends('layouts.kantin')

@section('title', 'Status Pesanan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card p-4 text-center">

            @if($pesanan->status_bayar == 1)
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size:5rem;"></i>
                </div> class="bi bi-qr-code"></i> Download Invoice (QR Code)
                    </a>
                <h3 class="fw-bold text-success">Pembayaran Berhasil!</h3>
                <p class="text-muted">Pesanan kamu sudah <strong>LUNAS</strong> dan sedang diproses.</p>
                <div class="badge bg-success fs-6 mb-3">LUNAS</div>
                
                  <a href="{{ route('pesanan.invoice', $pesanan->id) }}"
                        target="_blank"
                        class="btn btn-primary">
                        <i
            @else
                <div class="mb-3">
                    <i class="bi bi-clock-history text-warning" style="font-size:5rem;"></i>
                </div>
                <h3 class="fw-bold text-warning">Menunggu Pembayaran</h3>
                <p class="text-muted">Pesanan kamu belum dibayar.</p>
                <div class="badge bg-warning text-dark fs-6 mb-3">BELUM LUNAS</div>
            @endif

            <div class="alert alert-light text-start mt-2">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td><strong>{{ $pesanan->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Order ID</td>
                        <td><code>{{ $pesanan->order_id }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total</td>
                        <td><strong class="text-success">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</strong></td>
                    </tr>
                    @if($pesanan->metode_bayar)
                    <tr>
                        <td class="text-muted">Metode</td>
                        <td>{{ strtoupper($pesanan->metode_bayar) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Waktu</td>
                        <td>{{ $pesanan->created_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            <h6 class="fw-bold text-start mt-2">Detail Pesanan</h6>
            <table class="table table-bordered table-sm text-start">
                <thead class="table-light">
                    <tr><th>Menu</th><th class="text-center">Qty</th><th class="text-end">Subtotal</th></tr>
                </thead>
                <tbody>
                    @foreach($pesanan->detailPesanans as $detail)
                    <tr>
                        <td>{{ $detail->menu->nama_menu }}</td>
                        <td class="text-center">{{ $detail->jumlah }}</td>
                        <td class="text-end">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-grid gap-2 mt-3">
                @if($pesanan->status_bayar == 0)
                    <a href="{{ route('payment.show', $pesanan->idpesanan) }}" class="btn btn-success">
                        <i class="bi bi-wallet2"></i> Bayar Sekarang
                    </a>
                @endif
                <a href="{{ route('customer.index') }}" class="btn btn-outline-success">
                    <i class="bi bi-plus-circle"></i> Pesan Lagi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
