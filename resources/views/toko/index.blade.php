@extends('layouts.master')

@section('title', 'Kunjungan Toko')

@section('content')
<div class="container-fluid py-3">
    <h4 class="fw-bold mb-4"><i class="mdi mdi-store me-2"></i>Kunjungan Toko</h4>

    {{-- SECTION 1: List Toko + Tambah Toko                           --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="mdi mdi-format-list-bulleted me-2"></i>List Toko</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahToko">
                        <i class="mdi mdi-plus me-1"></i> Tambah Toko
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tableToko">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Barcode</th>
                                    <th>Nama Toko</th>
                                    <th>Alamat</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Akurasi (m)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tokos as $i => $toko)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><code>{{ $toko->barcode }}</code></td>
                                    <td>{{ $toko->nama_toko }}</td>
                                    <td>{{ $toko->alamat ?? '-' }}</td>
                                    <td>{{ $toko->latitude }}</td>
                                    <td>{{ $toko->longitude }}</td>
                                    <td>{{ $toko->accuracy }}m</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('toko.cetak-barcode', $toko->id) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Cetak Barcode">
                                                <i class="mdi mdi-barcode"></i> Cetak
                                            </a>
                                            <button class="btn btn-sm btn-danger btnHapusToko"
                                                    data-id="{{ $toko->id }}"
                                                    title="Hapus">
                                                <i class="mdi mdi-delete"></i> Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-3">Belum ada data toko.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: Input Titik Awal Toko (GPS)                       --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="mdi mdi-crosshairs-gps me-2"></i>Input Titik Awal Toko</h5>
                    <small class="opacity-75">Ambil koordinat GPS saat berada di lokasi toko</small>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Toko <span class="text-danger">*</span></label>
                        <input type="text" id="inputNamaToko" class="form-control" placeholder="Nama toko..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input type="text" id="inputAlamat" class="form-control" placeholder="Alamat toko...">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Latitude</label>
                            <input type="text" id="inputLat" class="form-control" readonly placeholder="-">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Longitude</label>
                            <input type="text" id="inputLng" class="form-control" readonly placeholder="-">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Akurasi</label>
                            <input type="text" id="inputAcc" class="form-control" readonly placeholder="-">
                        </div>
                    </div>

                    <div id="statusGPS" class="alert alert-secondary py-2 small mb-3">
                        <i class="mdi mdi-satellite-variant me-1"></i> Belum mengambil lokasi.
                    </div>

                    <div class="d-flex gap-2">
                        <button id="btnAmbilTitik" class="btn btn-info text-white" onclick="ambilTitikToko()">
                            <i class="mdi mdi-crosshairs-gps me-1"></i> Ambil Titik Lokasi
                        </button>
                        <button id="btnSimpanToko" class="btn btn-success" disabled onclick="simpanToko()">
                            <i class="mdi mdi-content-save me-1"></i> Simpan Toko
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 3: Titik Kunjungan Sales (Scan QR + GPS)             --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="mdi mdi-qrcode-scan me-2"></i>Titik Kunjungan Sales</h5>
                    <small class="opacity-75">Scan barcode toko lalu ambil posisi GPS sales</small>
                </div>
                <div class="card-body">

                    {{-- Scanner --}}
                    <div id="readerKunjungan" style="width:100%; max-width:400px; margin:0 auto;" class="mb-3"></div>

                    <div id="statusScanner" class="mb-2">
                        <span class="badge bg-success px-3 py-2">
                            <i class="mdi mdi-camera me-1"></i> Menunggu scan barcode toko...
                        </span>
                    </div>

                    {{-- Info toko hasil scan --}}
                    <div id="infoTokoScan" class="alert alert-info d-none py-2 small mb-3">
                        <strong>Toko:</strong> <span id="scanNamaToko">-</span><br>
                        <strong>Alamat:</strong> <span id="scanAlamat">-</span><br>
                        <strong>Koordinat:</strong> <span id="scanKoord">-</span><br>
                        <strong>Akurasi toko:</strong> <span id="scanAccToko">-</span>
                        <input type="hidden" id="scanTokoId">
                    </div>

                    {{-- Nama sales --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Sales</label>
                        <input type="text" id="inputNamaSales" class="form-control" placeholder="Nama sales...">
                    </div>

                    {{-- Threshold --}}
                    <div class="mb-3">
                        <label class="form-label">Radius Maksimal (meter)</label>
                        <input type="number" id="inputThreshold" class="form-control" value="300" min="1">
                    </div>

                    {{-- GPS Sales --}}
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Lat Sales</label>
                            <input type="text" id="salesLat" class="form-control" readonly placeholder="-">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Lng Sales</label>
                            <input type="text" id="salesLng" class="form-control" readonly placeholder="-">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Akurasi</label>
                            <input type="text" id="salesAcc" class="form-control" readonly placeholder="-">
                        </div>
                    </div>

                    <div id="statusGPSSales" class="alert alert-secondary py-2 small mb-3">
                        <i class="mdi mdi-satellite-variant me-1"></i> Belum mengambil posisi sales.
                    </div>

                    <div class="d-flex gap-2">
                        <button id="btnScanUlang" class="btn btn-outline-dark btn-sm d-none" onclick="scanUlang()">
                            <i class="mdi mdi-refresh me-1"></i> Scan Ulang
                        </button>
                        <button id="btnAmbilGPSSales" class="btn btn-secondary" disabled onclick="ambilGPSSales()">
                            <i class="mdi mdi-crosshairs-gps me-1"></i> Ambil GPS Sales
                        </button>
                        <button id="btnKirimKunjungan" class="btn btn-primary" disabled onclick="kirimKunjungan()">
                            <i class="mdi mdi-send me-1"></i> Kirim Laporan
                        </button>
                    </div>

                    {{-- Hasil kunjungan --}}
                    <div id="hasilKunjungan" class="mt-3 d-none"></div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Toko --}}
<div class="modal fade" id="modalTambahToko" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Toko Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Masukkan koordinat manual dari Google Maps atau sumber lain.</p>
                <div class="mb-2">
                    <label class="form-label">Nama Toko <span class="text-danger">*</span></label>
                    <input type="text" id="modalNamaToko" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Alamat</label>
                    <input type="text" id="modalAlamat" class="form-control">
                </div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Latitude <span class="text-danger">*</span></label>
                        <input type="number" step="any" id="modalLat" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Longitude <span class="text-danger">*</span></label>
                        <input type="number" step="any" id="modalLng" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Akurasi (m)</label>
                        <input type="number" id="modalAcc" class="form-control" value="10">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanModal" onclick="simpanTokoModal()">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<audio id="beepSound" src="https://www.soundjay.com/buttons/sounds/beep-07a.mp3" preload="auto"></audio>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
// ── State ──
let titikToko      = null; // { lat, lng, acc }
let tokoScanned    = null; // data toko dari scan
let titikSales     = null; // { lat, lng, acc }
let html5QrCode    = null;
let sudahScan      = false;

// 1. AMBIL TITIK LOKASI TOKO (GPS Akurat)
async function ambilTitikToko() {
    const btn    = document.getElementById('btnAmbilTitik');
    const status = document.getElementById('statusGPS');

    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Mengambil lokasi...`;
    status.className = 'alert alert-warning py-2 small mb-3';
    status.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Menunggu sinyal GPS terbaik (maks 20 detik)...';

    try {
        const pos = await getAccuratePosition(50, 20000);

        titikToko = {
            lat: pos.coords.latitude,
            lng: pos.coords.longitude,
            acc: pos.coords.accuracy,
        };

        document.getElementById('inputLat').value = titikToko.lat.toFixed(7);
        document.getElementById('inputLng').value = titikToko.lng.toFixed(7);
        document.getElementById('inputAcc').value = titikToko.acc.toFixed(1) + 'm';

        status.className = 'alert alert-success py-2 small mb-3';
        status.innerHTML = `✅ Lokasi berhasil diambil — Akurasi: ${titikToko.acc.toFixed(1)}m`;

        document.getElementById('btnSimpanToko').disabled = false;
    } catch (err) {
        status.className = 'alert alert-danger py-2 small mb-3';
        status.innerHTML = `❌ Gagal ambil lokasi: ${err.message}`;
    }

    btn.disabled = false;
    btn.innerHTML = `<i class="mdi mdi-crosshairs-gps me-1"></i> Ambil Titik Lokasi`;
}

// ── Simpan toko dengan GPS ──
function simpanToko() {
    const nama = document.getElementById('inputNamaToko').value.trim();
    if (!nama) { alert('Nama toko wajib diisi!'); return; }
    if (!titikToko) { alert('Ambil titik lokasi terlebih dahulu!'); return; }

    kirimToko({
        nama_toko : nama,
        alamat    : document.getElementById('inputAlamat').value,
        latitude  : titikToko.lat,
        longitude : titikToko.lng,
        accuracy  : titikToko.acc,
    });
}

// ── Simpan toko via modal (manual) ──
function simpanTokoModal() {
    const nama = document.getElementById('modalNamaToko').value.trim();
    const lat  = parseFloat(document.getElementById('modalLat').value);
    const lng  = parseFloat(document.getElementById('modalLng').value);
    const acc  = parseFloat(document.getElementById('modalAcc').value) || 10;

    if (!nama || isNaN(lat) || isNaN(lng)) {
        alert('Nama, Latitude, dan Longitude wajib diisi!');
        return;
    }

    kirimToko({ nama_toko: nama, alamat: document.getElementById('modalAlamat').value, latitude: lat, longitude: lng, accuracy: acc });
}

function kirimToko(data) {
    axios.post('{{ route("toko.store") }}', data, {
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(res => {
        if (res.data.success) {
            alert('Toko berhasil disimpan!');
            location.reload();
        }
    }).catch(() => alert('Gagal menyimpan toko.'));
}

// ── Hapus toko ──
document.querySelectorAll('.btnHapusToko').forEach(btn => {
    btn.addEventListener('click', function () {
        if (!confirm('Hapus toko ini?')) return;
        axios.delete(`/toko/${this.dataset.id}`, {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => location.reload());
    });
});

// 2. SCAN BARCODE TOKO
function mulaiScanner() {
    sudahScan   = false;
    html5QrCode = new Html5Qrcode("readerKunjungan");

    html5QrCode.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: { width: 300, height: 150 },
            formatsToSupport: [
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.QR_CODE,
            ],
        },
        onScanSuccess,
        () => {}
    ).catch(err => alert('Kamera tidak bisa diakses: ' + err));
}

function onScanSuccess(decoded) {
    if (sudahScan) return;
    sudahScan = true;

    document.getElementById('beepSound').play().catch(() => {});

    html5QrCode.stop().then(() => {
        document.getElementById('statusScanner').innerHTML =
            `<span class="badge bg-primary px-3 py-2"><i class="mdi mdi-check-circle me-1"></i> Barcode: ${decoded}</span>`;
        document.getElementById('btnScanUlang').classList.remove('d-none');

        // Cari toko berdasarkan barcode
        axios.get(`{{ route('toko.cari-barcode') }}?barcode=${encodeURIComponent(decoded)}`)
            .then(res => {
                if (res.data.success) {
                    tokoScanned = res.data.toko;

                    document.getElementById('scanTokoId').value    = tokoScanned.id;
                    document.getElementById('scanNamaToko').textContent = tokoScanned.nama_toko;
                    document.getElementById('scanAlamat').textContent   = tokoScanned.alamat || '-';
                    document.getElementById('scanKoord').textContent    = `${tokoScanned.latitude}, ${tokoScanned.longitude}`;
                    document.getElementById('scanAccToko').textContent  = `${tokoScanned.accuracy}m`;

                    document.getElementById('infoTokoScan').classList.remove('d-none');
                    document.getElementById('btnAmbilGPSSales').disabled = false;
                } else {
                    alert('Toko tidak ditemukan: ' + decoded);
                }
            });
    });
}

function scanUlang() {
    document.getElementById('infoTokoScan').classList.add('d-none');
    document.getElementById('btnAmbilGPSSales').disabled = true;
    document.getElementById('btnKirimKunjungan').disabled = true;
    document.getElementById('btnScanUlang').classList.add('d-none');
    document.getElementById('hasilKunjungan').classList.add('d-none');
    document.getElementById('statusScanner').innerHTML =
        `<span class="badge bg-success px-3 py-2"><i class="mdi mdi-camera me-1"></i> Menunggu scan barcode toko...</span>`;
    titikSales  = null;
    tokoScanned = null;
    mulaiScanner();
}

// 3. AMBIL GPS SALES
async function ambilGPSSales() {
    const btn    = document.getElementById('btnAmbilGPSSales');
    const status = document.getElementById('statusGPSSales');

    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Mengambil...`;
    status.className = 'alert alert-warning py-2 small mb-3';
    status.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Menunggu sinyal GPS terbaik (maks 20 detik)...';

    try {
        const pos = await getAccuratePosition(50, 20000);

        titikSales = {
            lat: pos.coords.latitude,
            lng: pos.coords.longitude,
            acc: pos.coords.accuracy,
        };

        document.getElementById('salesLat').value = titikSales.lat.toFixed(7);
        document.getElementById('salesLng').value = titikSales.lng.toFixed(7);
        document.getElementById('salesAcc').value = titikSales.acc.toFixed(1) + 'm';

        status.className = 'alert alert-success py-2 small mb-3';
        status.innerHTML = `✅ Posisi sales diambil — Akurasi: ${titikSales.acc.toFixed(1)}m`;

        document.getElementById('btnKirimKunjungan').disabled = false;
    } catch (err) {
        status.className = 'alert alert-danger py-2 small mb-3';
        status.innerHTML = `❌ Gagal: ${err.message}`;
    }

    btn.disabled  = false;
    btn.innerHTML = `<i class="mdi mdi-crosshairs-gps me-1"></i> Ambil GPS Sales`;
}

// 4. KIRIM LAPORAN KUNJUNGAN
function kirimKunjungan() {
    if (!tokoScanned || !titikSales) {
        alert('Scan barcode toko dan ambil GPS sales terlebih dahulu!');
        return;
    }

    const btn = document.getElementById('btnKirimKunjungan');
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Memproses...`;

    axios.post('{{ route("toko.simpan-kunjungan") }}', {
        toko_id        : document.getElementById('scanTokoId').value,
        nama_sales     : document.getElementById('inputNamaSales').value,
        latitude_sales : titikSales.lat,
        longitude_sales: titikSales.lng,
        accuracy_sales : titikSales.acc,
        threshold      : document.getElementById('inputThreshold').value,
    }, {
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(res => {
        const d       = res.data;
        const warna   = d.diterima ? 'success' : 'danger';
        const icon    = d.diterima ? '✅' : '❌';
        const hasil   = document.getElementById('hasilKunjungan');

        hasil.className = `alert alert-${warna} mt-3`;
        hasil.innerHTML = `
            <h6 class="fw-bold">${icon} ${d.diterima ? 'KUNJUNGAN DITERIMA' : 'KUNJUNGAN DITOLAK'}</h6>
            <table class="table table-sm mb-0">
                <tr><td>Jarak Aktual</td><td>: <strong>${d.jarak_meter}m</strong></td></tr>
                <tr><td>Threshold Efektif</td><td>: <strong>${d.threshold_efektif}m</strong></td></tr>
            </table>
        `;
        hasil.classList.remove('d-none');
    })
    .catch(() => alert('Gagal mengirim laporan.'))
    .finally(() => {
        btn.disabled  = false;
        btn.innerHTML = `<i class="mdi mdi-send me-1"></i> Kirim Laporan`;
    });
}

// HELPER: getAccuratePosition (Lampiran 1 dari modul)
function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
    return new Promise((resolve, reject) => {
        let bestResult = null;
        const startTime = Date.now();

        const watchId = navigator.geolocation.watchPosition(
            (position) => {
                const acc = position.coords.accuracy;

                if (!bestResult || acc < bestResult.coords.accuracy) {
                    bestResult = position;
                }

                if (acc <= targetAccuracy) {
                    navigator.geolocation.clearWatch(watchId);
                    resolve(bestResult);
                }

                if (Date.now() - startTime >= maxWait) {
                    navigator.geolocation.clearWatch(watchId);
                    if (bestResult) resolve(bestResult);
                    else reject(new Error("Timeout, tidak dapat posisi"));
                }
            },
            (error) => reject(error),
            { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait }
        );
    });
}

// ── Start scanner saat halaman load ──
window.addEventListener('load', mulaiScanner);
</script>
@endsection
