@extends('layouts.app')

@section('title', 'Tambah Menu')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card p-4">
            <h4 class="fw-bold mb-4">
                <i class="bi bi-plus-circle-fill text-success"></i> Tambah Menu Baru
            </h4>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('vendor.menu.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Menu <span class="text-danger">*</span></label>
                    <input type="text" name="nama_menu" class="form-control" value="{{ old('nama_menu') }}"
                           required placeholder="Contoh: Nasi Goreng Spesial">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="harga" class="form-control" value="{{ old('harga') }}"
                               required min="0" placeholder="15000">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Foto Menu (opsional)</label>
                    <input type="file" name="path_gambar" class="form-control" accept="image/*"
                           onchange="previewImage(event)">
                    <div class="mt-2" id="previewContainer" style="display:none;">
                        <img id="imgPreview" src="#" class="img-thumbnail" style="max-height:150px;">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('vendor.menu.index') }}" class="btn btn-secondary flex-fill">
                        <i class="bi bi-x-lg"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-success flex-fill">
                        <i class="bi bi-check-lg"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        document.getElementById('previewContainer').style.display = 'block';
        document.getElementById('imgPreview').src = URL.createObjectURL(file);
    }
}
</script>
@endpush
