@extends('layouts.app')

@section('title', 'Kelola Menu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-grid-fill text-success"></i> Kelola Menu</h3>
        <p class="text-muted mb-0">Daftar menu milik <strong>{{ $vendor->nama_vendor }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Dashboard
        </a>
        <a href="{{ route('vendor.menu.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Tambah Menu
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($menus->isEmpty())
    <div class="card p-5 text-center text-muted">
        <i class="bi bi-inbox fs-1"></i>
        <p class="mt-2">Belum ada menu. Tambahkan menu pertamamu!</p>
        <a href="{{ route('vendor.menu.create') }}" class="btn btn-success mt-2 mx-auto" style="width:fit-content;">
            <i class="bi bi-plus-lg"></i> Tambah Menu
        </a>
    </div>
@else
    <div class="row g-3">
        @foreach($menus as $menu)
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100">
                @if($menu->path_gambar)
                    <img src="{{ asset('storage/' . $menu->path_gambar) }}"
                         class="card-img-top" style="height:160px;object-fit:cover;" alt="{{ $menu->nama_menu }}">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:160px;">
                        <i class="bi bi-image text-secondary" style="font-size:2.5rem;"></i>
                    </div>
                @endif
                <div class="card-body">
                    <h6 class="fw-bold">{{ $menu->nama_menu }}</h6>
                    <p class="text-success fw-bold mb-3">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('vendor.menu.edit', $menu->idmenu) }}" class="btn btn-warning btn-sm flex-fill">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('vendor.menu.destroy', $menu->idmenu) }}" method="POST"
                              onsubmit="return confirm('Yakin hapus menu ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
