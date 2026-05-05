@extends('layouts.master')

@section('content')

{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

<h3 class="mb-4">Modul 4 - Select Kota</h3>

<div class="row">

    {{-- CARD 1: Select Biasa --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Select (Biasa)</h5>
            </div>
            <div class="card-body pt-3">

                <div class="mb-3">
                    <label>Nama Kota:</label>
                    <input type="text" id="kotaInput1" class="form-control" placeholder="Masukkan nama kota">
                </div>

                <div class="mb-3">
                    <button class="btn btn-success" id="btnTambah1">Tambahkan</button>
                </div>

                <div class="mb-3">
                    <label>Pilih Kota:</label>
                    <select id="selectKota1" class="form-control">
                        <option value="">-- Pilih Kota --</option>
                    </select>
                </div>

                <div>
                    <label>Kota Terpilih:</label>
                    <p id="hasilKota1" class="fw-bold text-primary">-</p>
                </div>

            </div>
        </div>
    </div>

    {{-- CARD 2: Select2 --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Select2</h5>
            </div>
            <div class="card-body pt-3">

                <div class="mb-3">
                    <label>Nama Kota:</label>
                    <input type="text" id="kotaInput2" class="form-control" placeholder="Masukkan nama kota">
                </div>

                <div class="mb-3">
                    <button class="btn btn-success" id="btnTambah2">Tambahkan</button>
                </div>

                <div class="mb-3">
                    <label>Pilih Kota:</label>
                    <select id="selectKota2" class="form-control" style="width:100%">
                        <option value="">-- Pilih Kota --</option>
                    </select>
                </div>

                <div>
                    <label>Kota Terpilih:</label>
                    <p id="hasilKota2" class="fw-bold text-success">-</p>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
{{-- Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {

    // Init Select2 pada card kedua
    $("#selectKota2").select2();

    // --- Card 1: Select Biasa ---
    $("#btnTambah1").click(function () {
        let kota = $("#kotaInput1").val().trim();
        if (kota !== "") {
            $("#selectKota1").append(`<option value="${kota}">${kota}</option>`);
            $("#kotaInput1").val("");
        }
    });

    $("#selectKota1").change(function () {
        $("#hasilKota1").text($(this).val() || "-");
    });

    // --- Card 2: Select2 ---
    $("#btnTambah2").click(function () {
        let kota = $("#kotaInput2").val().trim();
        if (kota !== "") {
            let newOption = new Option(kota, kota, false, false);
            $("#selectKota2").append(newOption).trigger("change");
            $("#kotaInput2").val("");
        }
    });

    $("#selectKota2").on("change", function () {
        $("#hasilKota2").text($(this).val() || "-");
    });

});
</script>
@endsection
