@extends('layouts.kantin')

@section('title', 'Pembayaran - Kantin Online')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card p-4">
            <div class="text-center mb-4">
                <i class="bi bi-credit-card-2-front-fill text-success" style="font-size:3rem;"></i>
                <h3 class="fw-bold mt-2">Pembayaran</h3>
                <p class="text-muted">Selesaikan pembayaran untuk pesanan kamu</p>
            </div>

            <div class="alert alert-info">
                <strong><i class="bi bi-person-circle"></i> {{ $pesanan->nama }}</strong><br>
                Order ID: <code>{{ $pesanan->order_id }}</code>
            </div>

            {{-- Detail Pesanan --}}
            <h6 class="fw-bold mb-2"><i class="bi bi-list-ul"></i> Detail Pesanan</h6>
            <table class="table table-sm table-bordered mb-3">
                <thead class="table-light">
                    <tr>
                        <th>Menu</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pesanan->detailPesanans as $detail)
                    <tr>
                        <td>{{ $detail->menu->nama_menu }}
                            @if($detail->catatan)
                                <br><small class="text-muted">{{ $detail->catatan }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $detail->jumlah }}</td>
                        <td class="text-end">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold table-success">
                        <td colspan="2" class="text-end">Total</td>
                        <td class="text-end">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            @if($pesanan->status_bayar == 1)
                <div class="alert alert-success text-center">
                    <i class="bi bi-check-circle-fill fs-3"></i>
                    <p class="mb-0 mt-1 fw-bold">Pesanan ini sudah <strong>LUNAS</strong>!</p>
                </div>
                <a href="{{ route('customer.status', $pesanan->idpesanan) }}" class="btn btn-success w-100">
                    Lihat Status Pesanan
                </a>
            @else
                <div class="d-grid">
                    <button id="btnBayar" class="btn btn-success btn-lg">
                        <i class="bi bi-wallet2"></i> Bayar Sekarang
                        <span class="fw-bold">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
                    </button>
                </div>
                <p class="text-center text-muted mt-2" style="font-size:0.85rem;">
                    <i class="bi bi-shield-lock"></i> Pembayaran aman via Midtrans (Virtual Account / QRIS)
                </p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($pesanan->status_bayar == 0)
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    document.getElementById('btnBayar').addEventListener('click', function () {
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memuat pembayaran...';

        snap.pay('{{ $pesanan->snap_token }}', {
            onSuccess: function (result) {
                window.location.href = '/payment/finish?order_id=' + result.order_id
                    + '&transaction_status=' + result.transaction_status
                    + '&payment_type=' + result.payment_type;
            },
            onPending: function (result) {
                alert('Pembayaran pending. Silahkan selesaikan pembayaran.');
                window.location.href = '/payment/finish?order_id=' + result.order_id
                    + '&transaction_status=pending';
            },
            onError: function (result) {
                alert('Pembayaran gagal!');
                document.getElementById('btnBayar').disabled = false;
                document.getElementById('btnBayar').innerHTML = '<i class="bi bi-wallet2"></i> Bayar Sekarang';
            },
            onClose: function () {
                document.getElementById('btnBayar').disabled = false;
                document.getElementById('btnBayar').innerHTML = '<i class="bi bi-wallet2"></i> Bayar Sekarang <span class="fw-bold">Rp {{ number_format($pesanan->total, 0, ",", ".") }}</span>';
            }
        });
    });
</script>
@endif
@endpush
