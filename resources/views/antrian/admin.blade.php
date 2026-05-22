@extends('layouts.master')

@section('title', 'Admin Antrian')

@section('styles')
{{-- Bootstrap Icons (tidak ada di master layout Purple Admin) --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .antrian-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .antrian-card .card-header {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-radius: 16px 16px 0 0 !important;
        padding: 16px 22px;
        font-weight: 700;
        font-size: 15px;
    }
    .btn-panggil {
        background: linear-gradient(135deg, #11998e, #38ef7d);
        border: none; color: #fff; font-weight: 600;
        padding: 12px 24px; border-radius: 12px; font-size: 15px;
        transition: all .3s; width: 100%;
    }
    .btn-panggil:hover { opacity: .9; transform: translateY(-1px); color:#fff; }
    .btn-panggil:disabled { opacity: .5; cursor: not-allowed; }

    .live-dot {
        display: inline-block; width: 10px; height: 10px;
        background: #28a745; border-radius: 50%; margin-right: 6px;
        animation: blink 1.2s infinite;
    }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.2} }

    .badge-menunggu  { background:#fff3cd; color:#856404; border-radius:50px; padding:5px 14px; font-size:12px; }
    .badge-dipanggil { background:#d1e7dd; color:#0f5132; border-radius:50px; padding:5px 14px; font-size:12px; }
    .badge-terlambat { background:#f8d7da; color:#842029; border-radius:50px; padding:5px 14px; font-size:12px; }

    .antrian-row.dipanggil td { background:#f0fff4 !important; }
    .antrian-row.terlambat td { background:#fff5f5 !important; }

    .nomor-badge {
        display:inline-flex; align-items:center; justify-content:center;
        width:40px; height:40px; border-radius:50%;
        background:#667eea; color:#fff; font-weight:700; font-size:14px;
    }
    .current-display {
        background: linear-gradient(135deg, #11998e, #38ef7d);
        color:#fff; border-radius:14px; padding:18px 22px;
        display:flex; align-items:center; gap:18px;
    }
    .current-display .num { font-size:48px; font-weight:900; line-height:1; }

    .stat-box {
        background:#fff; border-radius:12px; padding:14px;
        text-align:center; box-shadow:0 2px 10px rgba(0,0,0,.06);
    }
    .stat-box .num { font-size:30px; font-weight:700; }
</style>
@endsection

@section('content')
{{-- Judul --}}
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="mdi mdi-clipboard-pulse-outline me-2 text-primary"></i>Dashboard Admin Antrian
            </h4>
            <p class="text-muted mb-0" style="font-size:13px">
                <span class="live-dot"></span>Real-time via Server-Sent Events
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('antrian.guest') }}" target="_blank" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-person-plus me-1"></i>Halaman Guest
            </a>
            <a href="{{ route('antrian.papan') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-display me-1"></i>Papan Antrian
            </a>
        </div>
    </div>
</div>

{{-- Statistik --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="stat-box">
            <div class="num text-primary" id="statTotal">0</div>
            <small class="text-muted">Total Antrian</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-box">
            <div class="num text-warning" id="statMenunggu">0</div>
            <small class="text-muted">Menunggu</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-box">
            <div class="num text-success" id="statDipanggil">0</div>
            <small class="text-muted">Dipanggil</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-box">
            <div class="num text-danger" id="statTerlambat">0</div>
            <small class="text-muted">Terlambat</small>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Panel Kiri --}}
    <div class="col-lg-4">
        {{-- Nomor aktif --}}
        <div class="antrian-card card mb-3">
            <div class="card-header">
                <i class="bi bi-megaphone-fill me-2"></i>Sedang Dipanggil
            </div>
            <div class="card-body p-3">
                <div class="current-display">
                    <div class="num" id="currentNomor">—</div>
                    <div>
                        <div class="fw-bold" style="font-size:18px" id="currentNama">Belum ada</div>
                        <small style="opacity:.75">Nomor aktif saat ini</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol aksi --}}
        <div class="antrian-card card mb-3">
            <div class="card-header">
                <i class="bi bi-sliders me-2"></i>Kontrol Antrian
            </div>
            <div class="card-body p-3">
                <button class="btn-panggil mb-2" id="btnPanggil" onclick="panggilBerikutnya()">
                    <i class="bi bi-megaphone me-2"></i>Panggil Berikutnya
                </button>
                <button class="btn btn-outline-danger btn-sm w-100" onclick="resetAntrian()">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Semua Antrian
                </button>
            </div>
        </div>

        {{-- Tips --}}
        <div class="alert alert-info" style="border-radius:12px; font-size:13px">
            <i class="bi bi-info-circle-fill me-2"></i>
            Klik tombol <strong><i class="bi bi-megaphone"></i></strong> di baris <em>Terlambat</em>
            untuk memanggil ulang pasien yang tidak hadir.
        </div>
    </div>

    {{-- Panel Kanan: Tabel --}}
    <div class="col-lg-8">
        <div class="antrian-card card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-ol me-2"></i>Daftar Antrian</span>
                <span id="updateTime" style="opacity:.7; font-size:12px; font-weight:400"></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width:60px">No.</th>
                                <th>Nama</th>
                                <th>Status</th>
                                <th class="text-center" style="width:100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="antrianBody">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <span class="spinner-border spinner-border-sm me-2"></span>Memuat data...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const antrianBody  = document.getElementById('antrianBody');
const currentNomor = document.getElementById('currentNomor');
const currentNama  = document.getElementById('currentNama');
const updateTime   = document.getElementById('updateTime');
const csrfToken    = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ── SSE ──────────────────────────────────────────────
const source = new EventSource('{{ route("antrian.stream") }}');

source.addEventListener('antrian-update', function(e) {
    const data = JSON.parse(e.data);
    renderTable(data.list  || []);
    renderCurrent(data.current);
    renderStats(data.list  || []);
    updateTime.textContent = 'Update: ' + new Date().toLocaleTimeString('id-ID');
});

source.onerror = function() {
    console.warn('SSE disconnect, reconnecting...');
};

// ── Render ────────────────────────────────────────────
function renderTable(list) {
    if (!list.length) {
        antrianBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">
            <i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada antrian</td></tr>`;
        return;
    }
    antrianBody.innerHTML = list.map(item => {
        const badge = {
            menunggu:  `<span class="badge-menunggu"><i class="bi bi-hourglass-split me-1"></i>Menunggu</span>`,
            dipanggil: `<span class="badge-dipanggil"><i class="bi bi-megaphone-fill me-1"></i>Dipanggil</span>`,
            terlambat: `<span class="badge-terlambat"><i class="bi bi-clock-history me-1"></i>Terlambat</span>`,
        }[item.status] || item.status;

        const aksi = item.status === 'terlambat'
            ? `<button class="btn btn-sm btn-warning" title="Panggil Ulang"
                    onclick="panggilTerlambat(${item.nomor})">
                    <i class="bi bi-megaphone"></i>
               </button>` : '';

        return `<tr class="antrian-row ${item.status}">
            <td class="ps-3"><div class="nomor-badge">${item.nomor}</div></td>
            <td class="fw-semibold">${escHtml(item.nama)}</td>
            <td>${badge}</td>
            <td class="text-center">${aksi}</td>
        </tr>`;
    }).join('');
}

function renderCurrent(current) {
    currentNomor.textContent = current ? '#' + current.nomor : '—';
    currentNama.textContent  = current ? current.nama : 'Belum ada';
}

function renderStats(list) {
    document.getElementById('statTotal').textContent     = list.length;
    document.getElementById('statMenunggu').textContent  = list.filter(x => x.status === 'menunggu').length;
    document.getElementById('statDipanggil').textContent = list.filter(x => x.status === 'dipanggil').length;
    document.getElementById('statTerlambat').textContent = list.filter(x => x.status === 'terlambat').length;
}

// ── Aksi POST ─────────────────────────────────────────
function postJson(url, body) {
    return fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: body ? JSON.stringify(body) : null,
    }).then(r => r.json());
}

function panggilBerikutnya() {
    const btn = document.getElementById('btnPanggil');
    btn.disabled = true;
    postJson('{{ route("antrian.panggil") }}')
        .then(d => { if (!d.success) alert(d.message || 'Tidak ada antrian menunggu.'); })
        .catch(() => alert('Koneksi bermasalah.'))
        .finally(() => setTimeout(() => btn.disabled = false, 800));
}

function panggilTerlambat(nomor) {
    if (!confirm('Panggil ulang nomor #' + nomor + '?')) return;
    postJson('{{ route("antrian.panggil-terlambat") }}', { nomor })
        .then(d => { if (!d.success) alert(d.message || 'Gagal.'); })
        .catch(() => alert('Koneksi bermasalah.'));
}

function resetAntrian() {
    if (!confirm('Reset semua antrian? Tindakan ini tidak bisa dibatalkan.')) return;
    postJson('{{ route("antrian.reset") }}')
        .then(d => { if (!d.success) alert('Gagal reset.'); })
        .catch(() => alert('Koneksi bermasalah.'));
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endsection
