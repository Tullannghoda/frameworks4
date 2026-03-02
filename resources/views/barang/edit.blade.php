@extends('layouts.master')

@section('content')

<div class="container">
    <h2 class="mb-4">Edit Barang</h2>

    <form action="{{ route('barang.update', $barang->id_barang) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nama Barang</label>
            <input type="text"
                   name="nama_barang"
                   class="form-control"
                   value="{{ $barang->nama_barang }}"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Harga</label>
            <input type="number"
                   name="harga"
                   class="form-control"
                   value="{{ $barang->harga }}"
                   required>
        </div>

        <button type="submit" class="btn btn-primary">
            Update
        </button>

        <a href="{{ route('barang.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </form>
</div>

@endsection