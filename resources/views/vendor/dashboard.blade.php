@extends('layouts.kantin')

@section('title', 'Dashboard Vendor')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-speedometer2 text-success"></i> Dashboard Vendor</h3>
        <p class="text-muted mb-0">Selamat datang, <strong>{{ $vendor->nama_vendor }}</strong>!</p>
    </div>

    {{-- tambahan untuk modul 7: tombol menu customer --}}
    <div class="d-flex gap-2">
        <a href="{{ route('vendor.menu.index') }}" class="btn btn-success">
            <i class="bi bi-grid"></i> Kelola Menu
        </a>
            <a href="{{ route('vendor.scan') }}" class="btn btn-dark">
            <i class="bi bi-qr-code-scan"></i> Scan QR
        </a>
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-people-fill"></i> Data Customer
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('customerdata.index') }}">
                        <i class="bi bi-list-ul me-2"></i> Semua Customer
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('customerdata.create-blob') }}">
                        <i class="bi bi-camera me-2"></i> Tambah Customer (BLOB)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('customerdata.create-file') }}">
                        <i class="bi bi-camera-fill me-2"></i> Tambah Customer (File)
                    </a>
                </li>
            </ul>
        </div>
    </div>
    {{-- donne --}}

</div>

{{-- Statistik --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center p-3 border-0 bg-success text-white">
            <i class="bi bi-bag-check-fill fs-2"></i>
            <h2 class="fw-bold mt-1">{{ $pesanans->count() }}</h2>
            <p class="mb-0">Total Pesanan Lunas</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3 border-0 bg-primary text-white">
            <i class="bi bi-cash-stack fs-2"></i>
            <h2 class="fw-bold mt-1">Rp {{ number_format($pesanans->sum('total'), 0, ',', '.') }}</h2>
            <p class="mb-0">Total Pendapatan</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3 border-0 bg-warning text-dark">
            <i class="bi bi-menu-button-wide fs-2"></i>
            <h2 class="fw-bold mt-1">{{ \App\Models\Menu::where('idvendor', $vendor->idvendor)->count() }}</h2>
            <p class="mb-0">Total Menu</p>
        </div>
    </div>
</div>

{{-- Daftar Pesanan Lunas --}}
<div class="card p-3">
    <h5 class="fw-bold mb-3"><i class="bi bi-receipt-cutoff text-success"></i> Pesanan Lunas</h5>

    @if($pesanans->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1"></i>
            <p class="mt-2">Belum ada pesanan yang lunas.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Nama Guest</th>
                        <th>Waktu</th>
                        <th>Total</th>
                        <th>Metode Bayar</th>
                        <th>Detail</th>
                        <th>Invoice</th>{{--  TAMBAHAN MODUL 7 --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($pesanans as $i => $pesanan)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><span class="badge bg-secondary">{{ $pesanan->nama }}</span></td>
                        <td>{{ $pesanan->created_at->format('d M Y H:i') }}</td>
                        <td class="fw-bold text-success">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</td>
                        <td>
                            @if($pesanan->metode_bayar)
                                <span class="badge bg-info text-dark">{{ strtoupper($pesanan->metode_bayar) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-success" type="button"
                                data-bs-toggle="collapse" data-bs-target="#detail{{ $pesanan->idpesanan }}">
                                <i class="bi bi-eye"></i> Detail
                            </button>
                        </td>
                        {{--  TAMBAHAN MODUL 7: tombol invoice QR --}}
                        <td>
                            <a href="{{ route('vendor.pesanan.invoice', $pesanan->idpesanan) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-qr-code"></i> Invoice
                            </a>
                        </td>
                        {{--  AKHIR TAMBAHAN --}}
                    </tr>
                    <tr class="collapse" id="detail{{ $pesanan->idpesanan }}">
                        <td colspan="7" class="bg-light">
                            <table class="table table-sm table-bordered mb-0">
                                <thead><tr><th>Menu</th><th>Qty</th><th>Subtotal</th><th>Catatan</th></tr></thead>
                                <tbody>
                                    @foreach($pesanan->detailPesanans as $detail)
                                    <tr>
                                        <td>{{ $detail->menu->nama_menu }}</td>
                                        <td>{{ $detail->jumlah }}</td>
                                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                        <td>{{ $detail->catatan ?: '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection