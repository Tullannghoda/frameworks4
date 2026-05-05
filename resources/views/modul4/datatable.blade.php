@extends('layouts.master')

@section('content')

<h3>Input Barang</h3>

<div class="mb-3">
    <label>Nama Barang</label>
    <input type="text" id="namaBarang" class="form-control mb-2">

    <label>Harga Barang</label>
    <input type="number" id="hargaBarang" class="form-control mb-2">

    <button type="button" id="btnSubmit" class="btn btn-primary">
        Submit
    </button>
</div>

<h3>Daftar Barang</h3>

<table id="tabelBarang" class="display table table-bordered">
    <thead>
        <tr>
            <th>ID Barang</th>
            <th>Nama</th>
            <th>Harga</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal Edit -->
<div class="modal fade" id="modalBarang">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <label>ID Barang</label>
                <input type="text" id="editId" class="form-control mb-2" readonly>

                <label>Nama Barang</label>
                <input type="text" id="editNama" class="form-control mb-2">

                <label>Harga Barang</label>
                <input type="number" id="editHarga" class="form-control mb-2">
            </div>

            <div class="modal-footer">
                <button class="btn btn-danger" id="btnHapus">Hapus</button>
                <button class="btn btn-primary" id="btnUbah">Ubah</button>
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function () {

    let idBarang = 1;
    let selectedRow;

    let table = $("#tabelBarang").DataTable();

    // Klik baris → buka modal edit
    $("#tabelBarang tbody").on("click", "tr", function () {
        selectedRow = table.row(this);
        let data = selectedRow.data();

        $("#editId").val(data[0]);
        $("#editNama").val(data[1]);
        $("#editHarga").val(data[2]);

        let modal = new bootstrap.Modal(document.getElementById("modalBarang"));
        modal.show();
    });

    // Ubah data baris
    $("#btnUbah").click(function () {
        selectedRow.data([
            $("#editId").val(),
            $("#editNama").val(),
            $("#editHarga").val()
        ]).draw();

        bootstrap.Modal.getInstance(document.getElementById("modalBarang")).hide();
    });

    // Hapus baris
    $("#btnHapus").click(function () {
        selectedRow.remove().draw();
        bootstrap.Modal.getInstance(document.getElementById("modalBarang")).hide();
    });

    // Submit tambah barang
    $("#btnSubmit").click(function () {
        let nama  = $("#namaBarang").val().trim();
        let harga = $("#hargaBarang").val().trim();

        if (!nama || !harga) {
            alert("Nama dan Harga tidak boleh kosong!");
            return;
        }

        $("#btnSubmit").html(`<span class="spinner-border spinner-border-sm"></span> Loading...`);
        $("#btnSubmit").prop("disabled", true);

        setTimeout(function () {
            table.row.add([idBarang, nama, harga]).draw();
            idBarang++;

            $("#namaBarang").val("");
            $("#hargaBarang").val("");

            $("#btnSubmit").html("Submit");
            $("#btnSubmit").prop("disabled", false);
        }, 1000);
    });

});
</script>
@endsection
