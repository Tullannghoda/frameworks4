@extends('layouts.master')
@section('title', 'Halaman Kasir')

@section('content')

<h2>Halaman Kasir</h2>

<div>
    <button id="btn-jquery" onclick="setMode('jquery')"><strong>Ajax jQuery</strong></button>
    <button id="btn-axios" onclick="setMode('axios')">Axios</button>
</div>
<p id="mode-label">Mode aktif: <strong>Ajax jQuery</strong></p>

<br>

<label>Kode Barang</label><br>
<input type="text" id="input-kode" placeholder="Ketik kode barang lalu Enter">

<br><br>

<label>Nama Barang</label><br>
<input type="text" id="input-nama" readonly>

<br>

<label>Harga Barang</label><br>
<input type="text" id="input-harga" readonly>

<br>

<label>Jumlah</label><br>
<input type="number" id="input-jumlah" min="1" value="1">

<br><br>

<button id="btn-tambah" onclick="tambahBarang()" disabled>Tambahkan</button>

<br><br>

<table border="1" id="tabel-kasir">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th>Subtotal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody id="tbody-kasir">
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" align="right"><strong>Total</strong></td>
            <td id="total-harga">0</td>
            <td></td>
        </tr>
    </tfoot>
</table>

<br>

<button id="btn-bayar" onclick="bayar()" disabled>Bayar</button>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

var activeMode = 'jquery';
var totalHarga = 0;

function setMode(mode) {
    activeMode = mode;
    kosongkanForm();
    $('#mode-label').html(
        'Mode aktif: <strong>' + (mode === 'jquery' ? 'Ajax jQuery' : 'Axios') + '</strong>'
    );
    $('#btn-jquery').css('font-weight', mode === 'jquery' ? 'bold' : 'normal');
    $('#btn-axios').css('font-weight',  mode === 'axios'  ? 'bold' : 'normal');
}

// ===== AJAX JQUERY =====
function ajaxCariBarang(kode) {
    $.ajax({
        url: "{{ route('kasir.cari') }}",
        type: "POST",
        data: { _token: "{{ csrf_token() }}", id_barang: kode },
        success: function(res) {

            if (res.result) {
                isiForm(res.result);
            } else {
                kosongkanForm();
                Swal.fire('Tidak Ditemukan', res.message, 'error');
            }

        },
        error: function(xhr) { console.log(xhr); }
    });
}

function ajaxBayar(items) {
    $.ajax({
        url: "{{ route('kasir.bayar') }}",
        type: "POST",
        data: { _token: "{{ csrf_token() }}", total: totalHarga, items: items },
        success: function(res) {
            if (res.status === 'success') {
                Swal.fire('Berhasil!', res.message, 'success')
                .then(function() { resetHalaman(); });
            }
        },
        error: function(xhr) { console.log(xhr); }
    });
}

// ===== AXIOS =====
function axiosCariBarang(kode) {
    axios.post("{{ route('kasir.cari') }}", {
        _token: "{{ csrf_token() }}", id_barang: kode
    })
    .then(function(res) {

        if (res.data.result) {
            isiForm(res.data.result);
        } else {
            kosongkanForm();
            Swal.fire('Tidak Ditemukan', res.data.message, 'error');
        }

    })
    .catch(function(e) { console.log(e); });
}

function axiosBayar(items) {
    axios.post("{{ route('kasir.bayar') }}", {
        _token: "{{ csrf_token() }}", total: totalHarga, items: items
    })
    .then(function(res) {
        if (res.data.result) {
            Swal.fire('Berhasil!', res.data.message, 'success')
            .then(function() { resetHalaman(); });
        }
    })
    .catch(function(e) { console.log(e); });
}

function tambahBarang() {
    var kode   = $('#input-kode').val().trim();
    var nama   = $('#input-nama').val();
    var harga  = parseInt($('#input-harga').val());
    var jumlah = parseInt($('#input-jumlah').val());

    if (jumlah <= 0 || isNaN(jumlah)) {
        Swal.fire('Perhatian', 'Jumlah harus lebih dari 0', 'warning');
        return;
    }

    var subtotal = harga * jumlah;
    var barisAda = $('#tbody-kasir tr[data-kode="' + kode + '"]');

    if (barisAda.length > 0) {
        var jumlahLama   = parseInt(barisAda.find('.kolom-jumlah').val());
        var jumlahBaru   = jumlahLama + jumlah;
        var subtotalBaru = harga * jumlahBaru;
        barisAda.find('.kolom-jumlah').val(jumlahBaru);
        barisAda.find('.kolom-subtotal').text(subtotalBaru);
        barisAda.attr('data-subtotal', subtotalBaru);
    } else {
        var baris =
            '<tr data-kode="' + kode + '" data-harga="' + harga + '" data-subtotal="' + subtotal + '">' +
            '<td>' + kode + '</td>' +
            '<td>' + nama + '</td>' +
            '<td>' + harga + '</td>' +
            '<td><input type="number" class="kolom-jumlah" value="' + jumlah + '" min="1" style="width:60px" onchange="updateJumlah(this)"></td>' +
            '<td class="kolom-subtotal">' + subtotal + '</td>' +
            '<td><button onclick="hapusBaris(this)">Hapus</button></td>' +
            '</tr>';
        $('#tbody-kasir').append(baris);
    }

    hitungTotal();
    kosongkanForm();
}

function updateJumlah(input) {
    var baris    = $(input).closest('tr');
    var harga    = parseInt(baris.attr('data-harga'));
    var jumlah   = parseInt($(input).val());
    if (jumlah <= 0 || isNaN(jumlah)) { $(input).val(1); jumlah = 1; }
    var subtotal = harga * jumlah;
    baris.find('.kolom-subtotal').text(subtotal);
    baris.attr('data-subtotal', subtotal);
    hitungTotal();
}

function hapusBaris(btn) {
    $(btn).closest('tr').remove();
    hitungTotal();
}

function hitungTotal() {
    totalHarga = 0;
    $('#tbody-kasir tr').each(function() {
        totalHarga += parseInt($(this).attr('data-subtotal'));
    });
    $('#total-harga').text(totalHarga);
    $('#btn-bayar').prop('disabled', $('#tbody-kasir tr').length === 0);
}

function isiForm(data) {
    $('#input-nama').val(data.nama_barang);
    $('#input-harga').val(data.harga);
    $('#input-jumlah').val(1);
    $('#btn-tambah').prop('disabled', false);
}

function kosongkanForm() {
    $('#input-kode').val('').focus();
    $('#input-nama').val('');
    $('#input-harga').val('');
    $('#input-jumlah').val('');
    $('#btn-tambah').prop('disabled', true);
}

function bayar() {
    var items = [];
    $('#tbody-kasir tr').each(function() {
        items.push({
            id_barang : $(this).attr('data-kode'),
            jumlah    : parseInt($(this).find('.kolom-jumlah').val()),
            subtotal  : parseInt($(this).attr('data-subtotal'))
        });
    });
    if (activeMode === 'jquery') {
        ajaxBayar(items);
    } else {
        axiosBayar(items);
    }
}

function resetHalaman() {
    $('#tbody-kasir').html('');
    totalHarga = 0;
    $('#total-harga').text(0);
    $('#btn-bayar').prop('disabled', true);
    kosongkanForm();
}

$(document).ready(function() {

    $('#input-kode').keypress(function(e) {
        if (e.which == 13) {
            var kode = $(this).val().trim();
            if (kode == '') return;
            console.log('Mode aktif:', activeMode);
            if (activeMode === 'jquery') {
                ajaxCariBarang(kode);
            } else {
                axiosCariBarang(kode);
            }
        }
    });

});

</script>
@endsection
