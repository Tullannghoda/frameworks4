@extends('layouts.master')

@section('content')
<h3>Data Buku</h3>

<hr>

<button type="button" class="btn btn-gradient-primary mb-3"
        data-bs-toggle="modal" data-bs-target="#tambahBukuModal">
    + Tambah Buku
</button>  

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
<!-- Modal Tambah Buku -->
<div class="modal fade" id="tambahBukuModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form action="{{ route('buku.store') }}" method="POST">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title">Tambah Buku</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="form-group mb-3">
            <label>Kategori</label>
            <select name="kategori_id" class="form-control" required>
              @foreach($kategori as $k)
                <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group mb-3">
            <label>Kode</label>
            <input type="text" name="kode" class="form-control" required>
          </div>

          <div class="form-group mb-3">
            <label>Judul</label>
            <input type="text" name="judul" class="form-control" required>
          </div>

          <div class="form-group mb-3">
            <label>Pengarang</label>
            <input type="text" name="pengarang" class="form-control" required>
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