@extends('layouts.master')

@section('content')
<h3>Data Buku</h3>

<form action="{{ route('buku.store') }}" method="POST">
    @csrf

    <div class="form-group">
        <label>Kategori</label>
        <select name="kategori_id" class="form-control">
            @foreach($kategori as $k)
                <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
            @endforeach
        </select>
    </div>

    <input type="text" name="kode" class="form-control mt-2" placeholder="Kode">
    <input type="text" name="judul" class="form-control mt-2" placeholder="Judul">
    <input type="text" name="pengarang" class="form-control mt-2" placeholder="Pengarang">

    <button class="btn btn-primary mt-3">Tambah Buku</button>
</form>

<hr>

<table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>Kategori</th>
        <th>Kode</th>
        <th>Judul</th>
        <th>Pengarang</th>
    </tr>

    @foreach($buku as $key => $item)
    <tr>
        <td>{{ $key + 1 }}</td>
        <td>{{ $item->kategori->nama_kategori }}</td>
        <td>{{ $item->kode }}</td>
        <td>{{ $item->judul }}</td>
        <td>{{ $item->pengarang }}</td>
    </tr>
    @endforeach
</table>
@endsection
