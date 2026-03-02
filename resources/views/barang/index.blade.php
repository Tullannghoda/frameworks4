@extends('layouts.master')

@section('content')

<h2>Data Barang</h2>

<a href="{{ route('barang.create') }}" class="btn btn-gradient-primary mb-3">
    <i class="mdi mdi-plus"></i> Tambah Barang
</a>

<form action="{{ route('barang.cetak') }}" method="POST">
    @csrf

    <div style="margin-bottom:15px;">
        <label>Koordinat X (Kolom):</label>
        <input type="number" name="start_x" min="1" max="5" required>

        <label style="margin-left:10px;">Koordinat Y (Baris):</label>
        <input type="number" name="start_y" min="1" max="8" required>
    </div>

    <div style="margin-bottom:10px;">
        <button type="button" onclick="checkAll()" class="btn btn-success btn-sm">
            Check All
        </button>

        <button type="button" onclick="uncheckAll()" class="btn btn-warning btn-sm">
            Uncheck All
        </button>
    </div>

    <table id="tableBarang" class="table table-bordered">
        <thead>
            <tr>
                <th>Pilih</th>
                <th>ID</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach($barang as $b)
            <tr>
                <td>
                    <input type="checkbox" name="barang_id[]" value="{{ $b->id_barang }}">
                </td>
                <td>{{ $b->id_barang }}</td>
                <td>{{ $b->nama_barang }}</td>
                <td>Rp {{ number_format($b->harga, 0, ',', '.') }}</td>
                <td>
                    <a href="{{ route('barang.edit', $b->id_barang) }}" class="btn btn-sm btn-info">
                        Edit
                    </a>

                    <button type="button"
                        class="btn btn-sm btn-danger"
                        onclick="deleteBarang('{{ $b->id_barang }}')">
                        Delete
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <button type="submit" class="btn btn-primary mb-3">
        Cetak PDF
    </button>
</form>

<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection


@section('scripts')

<script>
document.addEventListener("DOMContentLoaded", function () {

    // Init DataTables
    if (typeof $ !== 'undefined') {
        $('#tableBarang').DataTable({
            paging: false,
            info: false
        });
    }

    // Delete function
    window.deleteBarang = function(id) {
        if(confirm("Yakin hapus?")) {
            let form = document.getElementById('deleteForm');
            form.action = "/barang/" + id;
            form.submit();
        }
    }

    // Check all
    window.checkAll = function() {
        let table = $('#tableBarang').DataTable();
        table.rows().nodes().to$()
            .find('input[name="barang_id[]"]')
            .prop('checked', true);
    }

    // Uncheck all
    window.uncheckAll = function() {
        let table = $('#tableBarang').DataTable();
        table.rows().nodes().to$()
            .find('input[name="barang_id[]"]')
            .prop('checked', false);
    }

});
</script>

@endsection