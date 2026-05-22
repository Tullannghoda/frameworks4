<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Antrian #{{ $nomor }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex; align-items: center; justify-content: center;
        }
        .tiket-card {
            background: white;
            border-radius: 20px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        .tiket-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 30px 20px 20px;
            text-align: center;
        }
        .tiket-body {
            padding: 30px 20px;
            text-align: center;
        }
        .nomor-besar {
            font-size: 100px;
            font-weight: 800;
            color: #667eea;
            line-height: 1;
            letter-spacing: -4px;
        }
        .separator {
            border: none;
            border-top: 2px dashed #dee2e6;
            margin: 20px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
        }
        .status-menunggu {
            background: #fff3cd;
            color: #856404;
        }
        .status-dipanggil {
            background: #d1e7dd;
            color: #0f5132;
            animation: pulse 1s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        .live-dot {
            display: inline-block;
            width: 10px; height: 10px;
            background: #28a745;
            border-radius: 50%;
            margin-right: 5px;
            animation: blink 1.2s infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.2; }
        }
        .current-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="tiket-card mx-auto">
            <div class="tiket-header">
                <i class="bi bi-ticket-perforated-fill fs-1 mb-2 d-block"></i>
                <h5 class="mb-0 fw-bold">Tiket Antrian Anda</h5>
                <small class="opacity-75">Simpan halaman ini dan tunggu giliran Anda</small>
            </div>

            <div class="tiket-body">
                {{-- Nomor antrian --}}
                <p class="text-muted mb-0">Nomor Antrian</p>
                <div class="nomor-besar">#{{ $nomor }}</div>

                <hr class="separator">

                {{-- Nama --}}
                <div class="mb-3">
                    <p class="text-muted small mb-1">Nama</p>
                    <h5 class="fw-bold mb-0">{{ $nama }}</h5>
                </div>

                {{-- Status badge --}}
                <div id="statusBadge" class="status-badge status-menunggu mb-3">
                    <i class="bi bi-hourglass-split me-1"></i> Menunggu Dipanggil
                </div>

                {{-- Nomor yang sedang dipanggil --}}
                <div class="current-info">
                    <small class="text-muted d-block mb-1">
                        <span class="live-dot"></span>Sedang dipanggil
                    </small>
                    <div id="currentNomor" class="fw-bold fs-5 text-primary">—</div>
                    <small id="currentNama" class="text-muted">Belum ada panggilan</small>
                </div>

                <hr class="separator">
                <small class="text-muted">
                    <i class="bi bi-arrow-repeat me-1"></i>
                    Status diperbarui otomatis secara real-time
                </small>
            </div>
        </div>
    </div>

    <script>
        const myNomor = {{ $nomor }};

        const statusBadge  = document.getElementById('statusBadge');
        const currentNomor = document.getElementById('currentNomor');
        const currentNama  = document.getElementById('currentNama');

        const source = new EventSource('{{ route("antrian.stream") }}');

        source.addEventListener('antrian-update', function(e) {
            const data = JSON.parse(e.data);

            // Update nomor yang sedang dipanggil
            if (data.current) {
                currentNomor.textContent = '#' + data.current.nomor;
                currentNama.textContent  = data.current.nama;
            }

            // Cari status tiket saya
            const myEntry = (data.list || []).find(item => item.nomor === myNomor);
            if (!myEntry) return;

            if (myEntry.status === 'dipanggil') {
                statusBadge.className = 'status-badge status-dipanggil mb-3';
                statusBadge.innerHTML = '<i class="bi bi-megaphone-fill me-1"></i> Anda Dipanggil!';
                document.title = '🔔 DIPANGGIL! Nomor #' + myNomor;
            } else if (myEntry.status === 'terlambat') {
                statusBadge.className = 'status-badge mb-3';
                statusBadge.style.background = '#f8d7da';
                statusBadge.style.color = '#842029';
                statusBadge.innerHTML = '<i class="bi bi-clock-history me-1"></i> Terlewat — Tunggu Panggilan Ulang';
                document.title = 'Terlewat — Antrian #' + myNomor;
            } else {
                statusBadge.className = 'status-badge status-menunggu mb-3';
                statusBadge.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Menunggu Dipanggil';
                document.title = 'Tiket Antrian #' + myNomor;
            }
        });

        source.onerror = function() {
            console.warn('SSE terputus, mencoba reconnect...');
        };
    </script>
</body>
</html>
