@extends('layouts.master')

@section('title', 'Scan Barcode Tag Harga')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="mdi mdi-barcode-scan me-2"></i>
                        Scanner Barcode Tag Harga
                    </h5>
                    <small class="opacity-75">Arahkan kamera ke barcode pada kertas label</small>
                </div>
                <div class="card-body text-center">

                    {{-- Area kamera scanner --}}
                    <div id="reader" style="width:100%; max-width:500px; margin:0 auto;"></div>

                    {{-- Status --}}
                    <div id="statusScanning" class="mt-3">
                        <span class="badge bg-success px-3 py-2 fs-6">
                            <i class="mdi mdi-camera me-1"></i> Menunggu scan...
                        </span>
                    </div>

                    <div id="statusDone" class="mt-3 d-none">
                        <span class="badge bg-primary px-3 py-2 fs-6">
                            <i class="mdi mdi-check-circle me-1"></i> Barcode terdeteksi!
                        </span>
                    </div>

                    {{-- Tombol scan ulang --}}
                    <button id="btnScanUlang" class="btn btn-outline-dark mt-3 d-none" onclick="scanUlang()">
                        <i class="mdi mdi-refresh me-1"></i> Scan Ulang
                    </button>

                </div>
            </div>

            {{-- Hasil scan --}}
            <div id="hasilScan" class="d-none">
                <div class="card shadow-sm border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="mdi mdi-package-variant me-2"></i>
                            Data Barang
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td width="140" class="text-muted fw-bold">ID Barang</td>
                                <td>: <strong id="res_id" class="text-dark"></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold">Nama Barang</td>
                                <td>: <strong id="res_nama" class="text-dark"></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold">Harga</td>
                                <td>: <strong id="res_harga" class="text-success fs-5"></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Error --}}
            <div id="hasilError" class="d-none">
                <div class="alert alert-danger">
                    <i class="mdi mdi-alert-circle me-2"></i>
                    <span id="res_error">Barang tidak ditemukan.</span>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Audio beep --}}
<audio id="beepSound" src="https://www.soundjay.com/buttons/sounds/beep-07a.mp3" preload="auto"></audio>

@section('scripts')
{{-- html5-qrcode library --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    let html5QrCode = null;
    let sudahScan   = false;

    // ── Inisialisasi scanner ──
    function mulaiScanner() {
        sudahScan   = false;
        html5QrCode = new Html5Qrcode("reader");

        html5QrCode.start(
            { facingMode: "environment" }, // kamera belakang
            {
                fps: 10,
                qrbox: { width: 300, height: 150 }, // box lebar untuk barcode 1D
                formatsToSupport: [
                    Html5QrcodeSupportedFormats.CODE_128,
                    Html5QrcodeSupportedFormats.CODE_39,
                    Html5QrcodeSupportedFormats.EAN_13,
                    Html5QrcodeSupportedFormats.EAN_8,
                ],
            },
            onScanSuccess,
            onScanFailure
        ).catch(err => {
            console.error("Gagal mulai scanner:", err);
            alert("Tidak bisa mengakses kamera: " + err);
        });
    }

    // ── Berhasil scan ──
    function onScanSuccess(decodedText) {
        if (sudahScan) return; // cegah scan ganda
        sudahScan = true;

        // 1. Bunyi beep
        const beep = document.getElementById('beepSound');
        beep.play().catch(() => {}); // catch jika autoplay diblokir

        // 2. Hentikan scanner
        html5QrCode.stop().then(() => {
            // 3. Update status UI
            document.getElementById('statusScanning').classList.add('d-none');
            document.getElementById('statusDone').classList.remove('d-none');
            document.getElementById('btnScanUlang').classList.remove('d-none');

            // 4. Cari data barang ke server
            cariBarang(decodedText);
        });
    }

    // ── Gagal scan (abaikan, ini dipanggil tiap frame gagal) ──
    function onScanFailure(error) {
        // tidak perlu ditampilkan ke user
    }

    // ── Lookup barang ke server ──
    function cariBarang(idBarang) {
        fetch(`/barang/cari-barcode?id=${encodeURIComponent(idBarang)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('res_id').textContent    = data.barang.id_barang;
                    document.getElementById('res_nama').textContent  = data.barang.nama_barang;
                    document.getElementById('res_harga').textContent = 'Rp ' + Number(data.barang.harga).toLocaleString('id-ID');

                    document.getElementById('hasilScan').classList.remove('d-none');
                    document.getElementById('hasilError').classList.add('d-none');
                } else {
                    document.getElementById('res_error').textContent = data.message || 'Barang tidak ditemukan.';
                    document.getElementById('hasilError').classList.remove('d-none');
                    document.getElementById('hasilScan').classList.add('d-none');
                }
            })
            .catch(() => {
                document.getElementById('res_error').textContent = 'Gagal menghubungi server.';
                document.getElementById('hasilError').classList.remove('d-none');
            });
    }

    // ── Scan ulang ──
    function scanUlang() {
        document.getElementById('hasilScan').classList.add('d-none');
        document.getElementById('hasilError').classList.add('d-none');
        document.getElementById('statusScanning').classList.remove('d-none');
        document.getElementById('statusDone').classList.add('d-none');
        document.getElementById('btnScanUlang').classList.add('d-none');

        mulaiScanner();
    }

    // ── Mulai saat halaman load ──
    window.addEventListener('load', mulaiScanner);
</script>
@endsection
@endsection
