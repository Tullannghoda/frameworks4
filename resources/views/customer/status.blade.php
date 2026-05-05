@extends('layouts.kantin')

@section('title', 'Status Pesanan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card p-4 text-center">

            @if($pesanan->status_bayar == 1)
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size:5rem;"></i>
                </div>
                <h3 class="fw-bold text-success">Pembayaran Berhasil!</h3>
                <p class="text-muted">Pesanan kamu sudah <strong>LUNAS</strong> dan sedang diproses.</p>
                <div class="badge bg-success fs-6 mb-3">LUNAS</div>

                {{-- ── QR Code Pesanan (Modul 8 P2) ── --}}
                <div class="card border-success mt-3 mb-3">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-qr-code me-2"></i>
                        <strong>QR Code Pesanan Kamu</strong>
                    </div>
                    <div class="card-body text-center py-4">
                        <p class="text-muted small mb-3">
                            Tunjukkan QR Code ini kepada vendor untuk mengambil pesananmu.
                        </p>
                        {{-- QR Code di-generate di sisi client menggunakan ID pesanan --}}
                        <div id="qrcode" class="d-flex justify-content-center"></div>
                        <p class="mt-3 text-muted small">
                            ID Pesanan: <code class="fw-bold">{{ $pesanan->idpesanan }}</code>
                        </p>
                        <p class="text-info small">
                            <i class="bi bi-info-circle"></i>
                            Kamu bisa kembali ke halaman ini kapan saja melalui link:<br>
                            <a href="{{ route('customer.status', $pesanan->idpesanan) }}" class="fw-bold">
                                {{ url(route('customer.status', $pesanan->idpesanan)) }}
                            </a>
                        </p>
                    </div>
                </div>

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

@push('scripts')
@if($pesanan->status_bayar == 1)
{{-- QRCode.js untuk generate QR code di sisi client --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // Generate QR Code berisi idpesanan
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ $pesanan->idpesanan }}",
        width: 200,
        height: 200,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
</script>
@endif
@endpush