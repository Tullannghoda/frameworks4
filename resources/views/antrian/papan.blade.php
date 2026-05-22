<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papan Antrian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            background: #0f0c29;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            color: white;
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        /* ── Header ─────────────────────────────────── */
        .papan-header {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .papan-header .brand {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .live-dot {
            display: inline-block; width: 10px; height: 10px;
            background: #28a745; border-radius: 50%;
            animation: blink 1.2s infinite; margin-right: 6px;
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.2} }

        /* ── Nomor dipanggil ────────────────────────── */
        .current-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px 20px;
        }
        .current-label {
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 4px;
            opacity: 0.7;
            margin-bottom: 10px;
        }
        .current-nomor {
            font-size: clamp(100px, 20vw, 180px);
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, #f6d365, #fda085);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: none;
            transition: all 0.4s;
        }
        .current-nomor.panggil-animasi {
            animation: zoom-in 0.5s ease;
        }
        @keyframes zoom-in {
            0%  { transform: scale(0.7); opacity: 0.3; }
            60% { transform: scale(1.05); }
            100%{ transform: scale(1);   opacity: 1; }
        }
        .current-nama {
            font-size: clamp(24px, 4vw, 40px);
            font-weight: 600;
            margin-top: 8px;
            opacity: 0.95;
        }
        .silakan-text {
            font-size: 18px;
            opacity: 0.6;
            margin-top: 6px;
        }

        /* ── Daftar antrian menunggu ────────────────── */
        .daftar-section {
            padding: 10px 32px 30px;
        }
        .daftar-title {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 3px;
            opacity: 0.5;
            margin-bottom: 12px;
        }
        .daftar-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .nomor-chip {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            padding: 10px 18px;
            font-size: 15px;
            backdrop-filter: blur(6px);
            transition: all 0.3s;
        }
        .nomor-chip .n { font-weight: 700; font-size: 18px; }
        .nomor-chip .nm { opacity: 0.7; font-size: 12px; margin-left: 8px; }
        .nomor-chip.terlambat {
            border-color: rgba(248, 81, 73, 0.4);
            background: rgba(248, 81, 73, 0.15);
        }

        /* ── Tombol aktifkan suara ──────────────────── */
        .btn-audio {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 14px;
            backdrop-filter: blur(10px);
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-audio:hover { background: rgba(255,255,255,0.25); }
        .btn-audio.active { background: rgba(40,167,69,0.3); border-color: #28a745; }

        /* ── Clock ──────────────────────────────────── */
        .clock {
            font-size: 16px;
            opacity: 0.7;
            font-variant-numeric: tabular-nums;
        }

        /* ── Empty state ────────────────────────────── */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            opacity: 0.4;
        }
        .empty-state i { font-size: 60px; display: block; margin-bottom: 12px; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="papan-header">
        <div class="brand">
            <i class="bi bi-display me-2"></i>Papan Antrian
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="clock" id="clock">00:00:00</div>
            <span style="font-size:13px; opacity:0.6">
                <span class="live-dot"></span>Live
            </span>
        </div>
    </div>

    {{-- Nomor yang dipanggil --}}
    <div class="current-section">
        <div class="current-label">Nomor Antrian Dipanggil</div>
        <div class="current-nomor" id="currentNomor">—</div>
        <div class="current-nama" id="currentNama">Belum ada panggilan</div>
        <div class="silakan-text" id="silakanText"></div>
    </div>

    {{-- Daftar antrian --}}
    <div class="daftar-section">
        <div class="daftar-title">Antrian Berikutnya</div>
        <div class="daftar-grid" id="daftarGrid">
            <div class="empty-state w-100">
                <i class="bi bi-hourglass"></i>
                Belum ada antrian
            </div>
        </div>
    </div>

    {{-- Tombol aktifkan suara (wajib interaksi user dulu) --}}
    <button class="btn-audio" id="btnAudio" onclick="toggleAudio()">
        <i class="bi bi-volume-mute-fill me-2"></i>Aktifkan Suara
    </button>

    {{-- Audio ting-tong (opsional — letakkan file dingdong.mp3 di public/audio/) --}}
    <audio id="audioTingTong" src="{{ asset('audio/dingdong.mp3') }}" preload="auto"></audio>

    <script>
    // ── Clock ────────────────────────────────────────
    function updateClock() {
        document.getElementById('clock').textContent =
            new Date().toLocaleTimeString('id-ID');
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ── State ────────────────────────────────────────
    let audioEnabled  = false;
    let lastNomor     = null;

    // ── Toggle suara ─────────────────────────────────
    function toggleAudio() {
        audioEnabled = !audioEnabled;
        const btn = document.getElementById('btnAudio');
        if (audioEnabled) {
            btn.innerHTML = '<i class="bi bi-volume-up-fill me-2"></i>Suara Aktif';
            btn.classList.add('active');
        } else {
            btn.innerHTML = '<i class="bi bi-volume-mute-fill me-2"></i>Aktifkan Suara';
            btn.classList.remove('active');
        }
    }

    // ── Suara panggilan ──────────────────────────────
    function bunyikanPanggilan(nomor, nama) {
        if (!audioEnabled) return;
        if (!('speechSynthesis' in window)) {
            console.warn('Browser tidak mendukung Web Speech API');
            return;
        }

        window.speechSynthesis.cancel();

        const pesan = new SpeechSynthesisUtterance(
            `Nomor antrian ${nomor}. ${nama}, silakan masuk.`
        );
        pesan.lang   = 'id-ID';
        pesan.rate   = 0.85;
        pesan.pitch  = 1.0;
        pesan.volume = 1.0;

        const audio = document.getElementById('audioTingTong');

        // Coba putar audio ting-tong, lalu TTS
        audio.currentTime = 0;
        const playPromise = audio.play();

        if (playPromise !== undefined) {
            playPromise
                .then(() => {
                    audio.onended = function() {
                        window.speechSynthesis.speak(pesan);
                    };
                })
                .catch(() => {
                    // Audio gagal (tidak ada file / autoplay policy), langsung TTS
                    window.speechSynthesis.speak(pesan);
                });
        } else {
            window.speechSynthesis.speak(pesan);
        }
    }

    // ── SSE ──────────────────────────────────────────
    const source = new EventSource('{{ route("antrian.stream") }}');

    source.addEventListener('antrian-update', function(e) {
        const data = JSON.parse(e.data);

        // Render nomor dipanggil
        if (data.current) {
            const nomor = data.current.nomor;
            const nama  = data.current.nama;

            document.getElementById('currentNama').textContent = nama;
            document.getElementById('silakanText').textContent = data.current.terlambat
                ? '⚠️ Panggilan Ulang — Silakan Masuk'
                : 'Silakan masuk';

            if (nomor !== lastNomor) {
                lastNomor = nomor;

                const el = document.getElementById('currentNomor');
                el.textContent = '#' + nomor;
                el.classList.remove('panggil-animasi');
                // Force reflow agar animasi ulang
                void el.offsetWidth;
                el.classList.add('panggil-animasi');

                bunyikanPanggilan(nomor, nama);
            }
        } else {
            document.getElementById('currentNomor').textContent = '—';
            document.getElementById('currentNama').textContent  = 'Belum ada panggilan';
            document.getElementById('silakanText').textContent  = '';
        }

        // Render daftar menunggu & terlambat
        renderDaftar(data.list || []);
    });

    source.onerror = function() {
        console.warn('SSE error, reconnecting...');
    };

    // ── Render daftar ────────────────────────────────
    function renderDaftar(list) {
        const grid = document.getElementById('daftarGrid');
        const menunggu  = list.filter(x => x.status === 'menunggu');
        const terlambat = list.filter(x => x.status === 'terlambat');
        const gabungan  = [...menunggu, ...terlambat];

        if (!gabungan.length) {
            grid.innerHTML = `<div class="empty-state w-100">
                <i class="bi bi-hourglass"></i>Belum ada antrian berikutnya</div>`;
            return;
        }

        grid.innerHTML = gabungan.map(item =>
            `<div class="nomor-chip ${item.status === 'terlambat' ? 'terlambat' : ''}">
                <span class="n">#${item.nomor}</span>
                <span class="nm">${escHtml(item.nama)}${item.status === 'terlambat' ? ' ⚠️' : ''}</span>
            </div>`
        ).join('');
    }

    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }
    </script>
</body>
</html>
