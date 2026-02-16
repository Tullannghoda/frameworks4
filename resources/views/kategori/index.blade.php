@extends('layouts.master')

@section('content')
<h3>Data Kategori</h3>

<form action="{{ route('kategori.store') }}" method="POST" class="mb-3">
    @csrf
    <div class="form-group">
        <input type="text" name="nama_kategori" class="form-control" placeholder="Masukkan nama kategori">
    </div>
    <button type="submit" class="btn btn-primary mt-2">Tambah</button>
</form>

<table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>Nama Kategori</th>
    </tr>

    @foreach($kategori as $key => $item)
    <tr>
        <td>{{ $key + 1 }}</td>
        <td>{{ $item->nama_kategori }}</td>
    </tr>
    @endforeach
</table>
@endsection
