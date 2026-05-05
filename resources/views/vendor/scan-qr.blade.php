@extends('layouts.master')

@section('title', 'Scan QR Code Customer')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white d-flex align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="mdi mdi-qrcode-scan me-2"></i>
                            Scan QR Code Customer
                        </h5>
                        <small class="opacity-75">Arahkan kamera ke QR Code milik customer</small>
                    </div>
                </div>
                <div class="card-body text-center">

                    {{-- Area kamera scanner --}}
                    <div id="reader" style="width:100%; max-width:500px; margin:0 auto;"></div>

                    {{-- Status scanning --}}
                    <div id="statusScanning" class="mt-3">
                        <span class="badge bg-success px-3 py-2 fs-6">
                            <i class="mdi mdi-camera me-1"></i> Menunggu scan...
                        </span>
                    </div>

                    <div id="statusDone" class="mt-3 d-none">
                        <span class="badge bg-primary px-3 py-2 fs-6">
                            <i class="mdi mdi-check-circle me-1"></i> QR Code terdeteksi!
                        </span>
                    </div>

                    {{-- Tombol scan ulang --}}
                    <button id="btnScanUlang" class="btn btn-outline-dark mt-3 d-none" onclick="scanUlang()">
                        <i class="mdi mdi-refresh me-1"></i> Scan Ulang
                    </button>

                </div>
            </div>

            {{-- Hasil: pesanan ditemukan --}}
            <div id="hasilScan" class="d-none">
                <div class="card shadow-sm border-success">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="mdi mdi-receipt me-2"></i>
                            Detail Pesanan
                        </h5>
                        <span id="badgeStatus" class="badge fs-6"></span>
                    </div>
                    <div class="card-body">

                        {{-- Info umum pesanan --}}
                        <table class="table table-borderless mb-3">
                            <tr>
                                <td width="140" class="text-muted fw-bold">Nama Customer</td>
                                <td>: <strong id="res_nama"></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold">ID Pesanan</td>
                                <td>: <strong id="res_id"></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold">Status Bayar</td>
                                <td>: <span id="res_status" class="fw-bold"></span></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold">Metode Bayar</td>
                                <td>: <span id="res_metode">-</span></td>
                            </tr>
                        </table>

                        {{-- Menu yang dipesan (hanya dari vendor ini) --}}
                        <h6 class="fw-bold"><i class="mdi mdi-food me-1"></i> Menu Pesanan (Vendor Anda)</h6>
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Menu</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody id="res_items">
                                {{-- Diisi JavaScript --}}
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold table-success">
                                    <td colspan="2" class="text-end">Total</td>
                                    <td class="text-end" id="res_total"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>

            {{-- Hasil: error / tidak ditemukan --}}
            <div id="hasilError" class="d-none">
                <div class="alert alert-danger">
                    <i class="mdi mdi-alert-circle me-2"></i>
                    <span id="res_error">Pesanan tidak ditemukan.</span>
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
                qrbox: { width: 250, height: 250 },
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
        if (sudahScan) return;
        sudahScan = true;

        // 1. Bunyi beep
        const beep = document.getElementById('beepSound');
        beep.play().catch(() => {});

        // 2. Hentikan scanner
        html5QrCode.stop().then(() => {
            // 3. Update status UI
            document.getElementById('statusScanning').classList.add('d-none');
            document.getElementById('statusDone').classList.remove('d-none');
            document.getElementById('btnScanUlang').classList.remove('d-none');

            // 4. Cari pesanan dari server
            cariPesanan(decodedText);
        });
    }

    // ── Gagal scan (abaikan, dipanggil tiap frame gagal) ──
    function onScanFailure(error) {}

    // ── Lookup pesanan ke server ──
    function cariPesanan(idpesanan) {
        fetch(`/vendor/cari-pesanan?idpesanan=${encodeURIComponent(idpesanan)}`)
            .then(res => res.json())
            .then(data => {
                // Sembunyikan dulu semua hasil
                document.getElementById('hasilScan').classList.add('d-none');
                document.getElementById('hasilError').classList.add('d-none');

                if (data.success) {
                    // Isi info umum
                    document.getElementById('res_id').textContent    = data.idpesanan;
                    document.getElementById('res_nama').textContent  = data.nama;

                    const statusEl  = document.getElementById('res_status');
                    const badgeEl   = document.getElementById('badgeStatus');
                    if (data.status_bayar == 1) {
                        statusEl.textContent      = 'LUNAS ✓';
                        statusEl.className        = 'fw-bold text-success';
                        badgeEl.textContent       = 'LUNAS';
                        badgeEl.className         = 'badge bg-light text-success fs-6';
                    } else {
                        statusEl.textContent      = 'BELUM LUNAS ✗';
                        statusEl.className        = 'fw-bold text-danger';
                        badgeEl.textContent       = 'BELUM LUNAS';
                        badgeEl.className         = 'badge bg-warning text-dark fs-6';
                    }

                    document.getElementById('res_metode').textContent =
                        data.metode_bayar ? data.metode_bayar.toUpperCase() : '-';

                    // Isi tabel menu
                    let totalVendor = 0;
                    const tbody = document.getElementById('res_items');
                    tbody.innerHTML = '';
                    data.items.forEach(item => {
                        totalVendor += item.subtotal;
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${item.nama_menu}</td>
                            <td class="text-center">${item.jumlah}</td>
                            <td class="text-end">Rp ${Number(item.subtotal).toLocaleString('id-ID')}</td>
                            <td class="text-muted small">${item.catatan ?? '-'}</td>
                        `;
                        tbody.appendChild(tr);
                    });

                    document.getElementById('res_total').textContent =
                        'Rp ' + Number(totalVendor).toLocaleString('id-ID');

                    document.getElementById('hasilScan').classList.remove('d-none');
                } else {
                    document.getElementById('res_error').textContent = data.message || 'Pesanan tidak ditemukan.';
                    document.getElementById('hasilError').classList.remove('d-none');
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