@extends('layouts.master')

@section('title', 'Tambah Customer (File)')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-camera-fill me-2"></i>
                        Tambah Customer — Foto disimpan sebagai File
                    </h5>
                    <small class="opacity-75">Foto disimpan di storage/app/public/customers/ | Path tersimpan di database</small>
                </div>
                <div class="card-body">

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('customerdata.store-file') }}" method="POST" id="formFile">
                        @csrf

                        {{-- Kamera --}}
                        <div class="mb-3 text-center">
                            <label class="form-label fw-bold">Foto Customer</label>

                            <video id="video"
                                   width="320" height="240"
                                   class="rounded border d-block mx-auto"
                                   autoplay
                                   style="background:#000;">
                            </video>

                            <canvas id="canvas" width="320" height="240" class="d-none"></canvas>
                            <img id="preview"
                                 src=""
                                 alt="Preview"
                                 class="rounded border d-none mx-auto"
                                 width="320" height="240"
                                 style="object-fit:cover;">

                            <div class="mt-2 d-flex justify-content-center gap-2">
                                <button type="button" id="btnCapture" class="btn btn-success btn-sm">
                                    <i class="bi bi-camera"></i> Ambil Foto
                                </button>
                                <button type="button" id="btnRetake" class="btn btn-secondary btn-sm d-none">
                                    <i class="bi bi-arrow-repeat"></i> Ulang
                                </button>
                            </div>

                            {{-- Hidden input base64 untuk dikirim ke server --}}
                            <input type="hidden" name="foto_base64" id="foto_base64">

                            @error('foto_base64')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Info penyimpanan --}}
                        <div class="alert alert-info py-2 small">
                            <i class="bi bi-info-circle me-1"></i>
                            Foto akan disimpan ke <code>storage/app/public/customers/</code>
                            dan path-nya disimpan di kolom <code>foto_path</code> database.
                        </div>

                        <hr>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                       value="{{ old('nama') }}" required>
                                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Telepon</label>
                                <input type="text" name="telepon" class="form-control"
                                       value="{{ old('telepon') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Alamat</label>
                                <input type="text" name="alamat" class="form-control"
                                       value="{{ old('alamat') }}">
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('customerdata.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" id="btnSimpan" class="btn btn-success" disabled>
                                <i class="bi bi-save me-1"></i> Simpan Customer
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
(function () {
    const video      = document.getElementById('video');
    const canvas     = document.getElementById('canvas');
    const preview    = document.getElementById('preview');
    const fileInput  = document.getElementById('foto_base64');
    const btnCap     = document.getElementById('btnCapture');
    const btnRet     = document.getElementById('btnRetake');
    const btnSave    = document.getElementById('btnSimpan');

    let stream = null;

    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: 320, height: 240 }
            });
            video.srcObject = stream;
            video.classList.remove('d-none');
            preview.classList.add('d-none');
            btnCap.classList.remove('d-none');
            btnRet.classList.add('d-none');
            btnSave.disabled = true;
            fileInput.value  = '';
        } catch (err) {
            alert('Tidak bisa mengakses kamera: ' + err.message);
        }
    }

    btnCap.addEventListener('click', () => {
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl   = canvas.toDataURL('image/jpeg', 0.85);
        fileInput.value = dataUrl;
        preview.src     = dataUrl;

        if (stream) stream.getTracks().forEach(t => t.stop());

        video.classList.add('d-none');
        preview.classList.remove('d-none');
        btnCap.classList.add('d-none');
        btnRet.classList.remove('d-none');
        btnSave.disabled = false;
    });

    btnRet.addEventListener('click', startCamera);

    document.getElementById('formFile').addEventListener('submit', function (e) {
        if (!fileInput.value) {
            e.preventDefault();
            alert('Silakan ambil foto customer terlebih dahulu.');
        }
    });

    startCamera();
})();
</script>
@endsection
@endsection
