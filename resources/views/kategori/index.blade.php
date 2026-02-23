@extends('layouts.master')

@section('content')
<h3>Data Kategori</h3>

<form action="{{ route('kategori.store') }}" method="POST" class="mb-3">
    @csrf
    <div class="form-group">
        <input type="text" name="nama_kategori" class="form-control" placeholder="Masukkan nama kategori">
    </div>
    <button type="button" class="btn btn-gradient-primary mb-3"
            data-bs-toggle="modal" data-bs-target="#tambahKategoriModal">
    + Tambah Kategori
    </button>
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
<!-- Modal -->
<div class="modal fade" id="tambahKategoriModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form method="POST" action="{{ route('kategori.store') }}">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title">Tambah Kategori</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label>Nama Kategori</label>
            <input type="text" name="nama_kategori" class="form-control" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-gradient-primary">Simpan</button>
        </div>

      </form>

    </div>
  </div>
</div>
@endsection
