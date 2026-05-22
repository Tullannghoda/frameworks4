<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Antrian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            max-width: 460px;
            width: 100%;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px 20px 0 0 !important;
            padding: 30px;
            text-align: center;
            color: white;
        }
        .card-header .icon-wrap {
            width: 70px; height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 15px;
            font-size: 32px;
        }
        .btn-daftar {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-daftar:hover { opacity: 0.9; transform: translateY(-1px); color: white; }
        .btn-daftar:disabled { opacity: 0.6; cursor: not-allowed; }
        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            font-size: 16px;
        }
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102,126,234,0.2); }
        .link-papan {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 13px;
        }
        .link-papan:hover { color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card mx-auto">
            <div class="card-header">
                <div class="icon-wrap">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h4 class="mb-1 fw-bold">Daftar Antrian</h4>
                <p class="mb-0 opacity-75">Masukkan nama Anda untuk mendapatkan nomor antrian</p>
                <a href="{{ route('antrian.papan') }}" class="link-papan mt-2 d-inline-block" target="_blank">
                    <i class="bi bi-display"></i> Lihat Papan Antrian
                </a>
            </div>
            <div class="card-body p-4">
                <div id="alertBox" class="d-none"></div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" id="inputNama" class="form-control"
                           placeholder="Contoh: Budi Santoso"
                           maxlength="100" autocomplete="off">
                    <div id="namaError" class="text-danger small mt-1 d-none">Nama tidak boleh kosong.</div>
                </div>

                <button class="btn-daftar" id="btnDaftar" onclick="daftarAntrian()">
                    <i class="bi bi-ticket-perforated me-2"></i>Ambil Nomor Antrian
                </button>

                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Tiket antrian akan terbuka di tab baru secara otomatis
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script>
        const inputNama = document.getElementById('inputNama');
        const btnDaftar = document.getElementById('btnDaftar');
        const alertBox  = document.getElementById('alertBox');
        const namaError = document.getElementById('namaError');

        // Enter key support
        inputNama.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') daftarAntrian();
        });

        function daftarAntrian() {
            const nama = inputNama.value.trim();

            // Validasi
            if (!nama) {
                namaError.classList.remove('d-none');
                inputNama.focus();
                return;
            }
            namaError.classList.add('d-none');

            // Disable tombol saat loading
            btnDaftar.disabled = true;
            btnDaftar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mendaftarkan...';

            fetch('{{ route("antrian.daftar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ nama }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // Buka tiket di tab baru
                    const url = '{{ route("antrian.tiket") }}?nomor=' + data.nomor + '&nama=' + encodeURIComponent(data.nama);
                    window.open(url, '_blank');

                    // Reset form
                    inputNama.value = '';
                    showAlert('success', `<i class="bi bi-check-circle-fill me-2"></i>Berhasil! Nomor antrian <strong>#${data.nomor}</strong> untuk <strong>${data.nama}</strong> sudah dibuka di tab baru.`);
                } else {
                    showAlert('danger', '<i class="bi bi-x-circle-fill me-2"></i>Gagal mendaftar. Coba lagi.');
                }
            })
            .catch(() => {
                showAlert('danger', '<i class="bi bi-x-circle-fill me-2"></i>Koneksi bermasalah. Coba lagi.');
            })
            .finally(() => {
                btnDaftar.disabled = false;
                btnDaftar.innerHTML = '<i class="bi bi-ticket-perforated me-2"></i>Ambil Nomor Antrian';
            });
        }

        function showAlert(type, msg) {
            alertBox.className = `alert alert-${type} rounded-3`;
            alertBox.innerHTML = msg;
            alertBox.classList.remove('d-none');
            setTimeout(() => alertBox.classList.add('d-none'), 6000);
        }
    </script>
</body>
</html>
