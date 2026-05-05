@extends('layouts.master')

@section('title', 'Edit Menu')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card p-4">
            <h4 class="fw-bold mb-4">
                <i class="bi bi-pencil-square text-warning"></i> Edit Menu
            </h4>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('vendor.menu.update', $menu->idmenu) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Menu <span class="text-danger">*</span></label>
                    <input type="text" name="nama_menu" class="form-control"
                           value="{{ old('nama_menu', $menu->nama_menu) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="harga" class="form-control"
                               value="{{ old('harga', $menu->harga) }}" required min="0">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Foto Menu</label>
                    @if($menu->path_gambar)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $menu->path_gambar) }}"
                                 class="img-thumbnail" style="max-height:120px;" id="imgPreview">
                        </div>
                    @else
                        <img id="imgPreview" src="#" class="img-thumbnail mb-2" style="max-height:120px; display:none;">
                    @endif
                    <input type="file" name="path_gambar" class="form-control" accept="image/*"
                           onchange="previewImage(event)">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('vendor.menu.index') }}" class="btn btn-secondary flex-fill">
                        <i class="bi bi-x-lg"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-warning flex-fill">
                        <i class="bi bi-check-lg"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const img = document.getElementById('imgPreview');
        img.src = URL.createObjectURL(file);
        img.style.display = 'block';
    }
}
</script>
@endsection
