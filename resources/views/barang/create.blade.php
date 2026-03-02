@extends('layouts.master')

@section('content')

<div class="container">
    <h2 class="mb-4">Tambah Barang</h2>

    <form action="{{ route('barang.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nama Barang</label>
            <input type="text"
                   name="nama_barang"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Harga</label>
            <input type="number"
                   name="harga"
                   class="form-control"
                   required>
        </div>

        <button type="submit" class="btn btn-success">
            Simpan
        </button>

        <a href="{{ route('barang.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </form>
</div>

@endsection