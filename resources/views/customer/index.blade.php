@extends('layouts.kantin')

@section('title', 'Pesan Makanan - Kantin Online')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-success"><i class="bi bi-basket2-fill"></i> Pemesanan Kantin</h2>
            <p class="text-muted">Pilih vendor dan menu favoritmu!</p>
        </div>

        <div class="card p-4">
            <form id="formPesan" action="{{ route('customer.store') }}" method="POST">
                @csrf

                {{-- STEP 1: Pilih Vendor --}}
                <div class="mb-4">
                    <label class="form-label fw-bold fs-5">
                        <span class="badge bg-success me-2">1</span> Pilih Vendor
                    </label>
                    <select class="form-select form-select-lg" id="selectVendor" name="idvendor" required>
                        <option value="">-- Pilih Vendor --</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->idvendor }}">{{ $vendor->nama_vendor }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- STEP 2: Pilih Menu --}}
                <div class="mb-4" id="sectionMenu" style="display:none;">
                    <label class="form-label fw-bold fs-5">
                        <span class="badge bg-success me-2">2</span> Pilih Menu
                    </label>
                    <div id="loadingMenu" class="text-center py-3" style="display:none;">
                        <div class="spinner-border text-success" role="status"></div>
                        <p class="mt-2 text-muted">Memuat menu...</p>
                    </div>
                    <div id="daftarMenu" class="row g-3"></div>
                </div>

                {{-- STEP 3: Ringkasan Pesanan --}}
                <div class="mb-4" id="sectionRingkasan" style="display:none;">
                    <label class="form-label fw-bold fs-5">
                        <span class="badge bg-success me-2">3</span> Ringkasan Pesanan
                    </label>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-success">
                                <tr>
                                    <th>Menu</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Subtotal</th>
                                    <th>Catatan</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="tabelRingkasan"></tbody>
                            <tfoot>
                                <tr class="table-light fw-bold">
                                    <td colspan="3" class="text-end">Total</td>
                                    <td class="text-center text-success fs-5" id="totalHarga">Rp 0</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="hiddenItems"></div>
                </div>

                <div class="d-grid" id="sectionBayar" style="display:none !important;">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-credit-card-fill"></i> Lanjut ke Pembayaran
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- Modal Tambah ke Keranjang --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-cart-plus"></i> Tambah ke Keranjang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalIdMenu">
                <input type="hidden" id="modalHarga">
                <p class="fw-bold fs-5" id="modalNamaMenu"></p>
                <p class="text-muted" id="modalHargaText"></p>
                <div class="mb-3">
                    <label class="form-label">Jumlah</label>
                    <div class="input-group">
                        <button class="btn btn-outline-secondary" type="button" id="btnKurang">-</button>
                        <input type="number" class="form-control text-center" id="inputJumlah" value="1" min="1">
                        <button class="btn btn-outline-secondary" type="button" id="btnTambah">+</button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan (opsional)</label>
                    <input type="text" class="form-control" id="inputCatatan" placeholder="Contoh: tidak pedas, tanpa bawang...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnSimpanKeranjang">
                    <i class="bi bi-cart-check"></i> Tambah
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .menu-card { cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; }
    .menu-card:hover { transform: translateY(-4px); box-shadow: 0 6px 20px rgba(0,0,0,0.12); }
    .menu-img { width: 100%; height: 150px; object-fit: cover; border-radius: 8px 8px 0 0; }
    .menu-img-placeholder { width: 100%; height: 150px; background: #e9ecef; display: flex; align-items: center; justify-content: center; border-radius: 8px 8px 0 0; font-size: 3rem; color: #adb5bd; }
</style>
@endpush

@push('scripts')
<script>
    let keranjang = []; // [{idmenu, nama_menu, harga, jumlah, catatan}]

    // Event: Pilih Vendor
    document.getElementById('selectVendor').addEventListener('change', function () {
        const idvendor = this.value;
        if (!idvendor) {
            document.getElementById('sectionMenu').style.display = 'none';
            document.getElementById('sectionRingkasan').style.display = 'none';
            document.getElementById('sectionBayar').style.display = 'none';
            keranjang = [];
            return;
        }

        document.getElementById('sectionMenu').style.display = 'block';
        document.getElementById('loadingMenu').style.display = 'block';
        document.getElementById('daftarMenu').innerHTML = '';
        keranjang = [];
        updateRingkasan();

        fetch(`/kantin/menu/${idvendor}`)
            .then(res => res.json())
            .then(menus => {
                document.getElementById('loadingMenu').style.display = 'none';
                renderMenu(menus);
            })
            .catch(() => {
                document.getElementById('loadingMenu').style.display = 'none';
                document.getElementById('daftarMenu').innerHTML = '<p class="text-danger">Gagal memuat menu.</p>';
            });
    });

    function renderMenu(menus) {
        const container = document.getElementById('daftarMenu');
        if (menus.length === 0) {
            container.innerHTML = '<div class="col-12"><div class="alert alert-warning">Vendor ini belum memiliki menu.</div></div>';
            return;
        }
        menus.forEach(menu => {
            const imgHtml = menu.path_gambar
                ? `<img src="/storage/${menu.path_gambar}" class="menu-img" alt="${menu.nama_menu}">`
                : `<div class="menu-img-placeholder"><i class="bi bi-image"></i></div>`;

            container.innerHTML += `
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card menu-card h-100" onclick="bukaModalTambah(${menu.idmenu}, '${menu.nama_menu.replace(/'/g, "\\'")}', ${menu.harga})">
                        ${imgHtml}
                        <div class="card-body p-2 text-center">
                            <p class="mb-1 fw-semibold">${menu.nama_menu}</p>
                            <p class="text-success fw-bold mb-0">Rp ${Number(menu.harga).toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                </div>`;
        });
    }

    // Modal tambah ke keranjang
    function bukaModalTambah(idmenu, nama, harga) {
        document.getElementById('modalIdMenu').value   = idmenu;
        document.getElementById('modalHarga').value    = harga;
        document.getElementById('modalNamaMenu').textContent  = nama;
        document.getElementById('modalHargaText').textContent = 'Harga: Rp ' + Number(harga).toLocaleString('id-ID');
        document.getElementById('inputJumlah').value   = 1;
        document.getElementById('inputCatatan').value  = '';
        new bootstrap.Modal(document.getElementById('modalTambah')).show();
    }

    document.getElementById('btnKurang').addEventListener('click', () => {
        const el = document.getElementById('inputJumlah');
        if (parseInt(el.value) > 1) el.value = parseInt(el.value) - 1;
    });
    document.getElementById('btnTambah').addEventListener('click', () => {
        const el = document.getElementById('inputJumlah');
        el.value = parseInt(el.value) + 1;
    });

    document.getElementById('btnSimpanKeranjang').addEventListener('click', () => {
        const idmenu  = parseInt(document.getElementById('modalIdMenu').value);
        const harga   = parseInt(document.getElementById('modalHarga').value);
        const nama    = document.getElementById('modalNamaMenu').textContent;
        const jumlah  = parseInt(document.getElementById('inputJumlah').value);
        const catatan = document.getElementById('inputCatatan').value;

        // Cek apakah sudah ada di keranjang
        const existing = keranjang.find(k => k.idmenu === idmenu);
        if (existing) {
            existing.jumlah  += jumlah;
            existing.catatan  = catatan || existing.catatan;
        } else {
            keranjang.push({ idmenu, nama_menu: nama, harga, jumlah, catatan });
        }

        bootstrap.Modal.getInstance(document.getElementById('modalTambah')).hide();
        updateRingkasan();
    });

    function updateRingkasan() {
        const tbody   = document.getElementById('tabelRingkasan');
        const hidden  = document.getElementById('hiddenItems');
        const section = document.getElementById('sectionRingkasan');
        const btnBayar = document.getElementById('sectionBayar');

        if (keranjang.length === 0) {
            section.style.display  = 'none';
            btnBayar.style.display = 'none';
            tbody.innerHTML        = '';
            hidden.innerHTML       = '';
            document.getElementById('totalHarga').textContent = 'Rp 0';
            return;
        }

        section.style.display  = 'block';
        btnBayar.style.removeProperty('display');

        let total = 0;
        tbody.innerHTML  = '';
        hidden.innerHTML = '';

        keranjang.forEach((item, idx) => {
            const subtotal = item.harga * item.jumlah;
            total += subtotal;

            tbody.innerHTML += `
                <tr>
                    <td>${item.nama_menu}</td>
                    <td class="text-center">Rp ${Number(item.harga).toLocaleString('id-ID')}</td>
                    <td class="text-center">${item.jumlah}</td>
                    <td class="text-center">Rp ${Number(subtotal).toLocaleString('id-ID')}</td>
                    <td>${item.catatan || '-'}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm" onclick="hapusItem(${idx})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;

            hidden.innerHTML += `
                <input type="hidden" name="items[${idx}][idmenu]"   value="${item.idmenu}">
                <input type="hidden" name="items[${idx}][jumlah]"   value="${item.jumlah}">
                <input type="hidden" name="items[${idx}][catatan]"  value="${item.catatan}">`;
        });

        document.getElementById('totalHarga').textContent = 'Rp ' + Number(total).toLocaleString('id-ID');
    }

    function hapusItem(idx) {
        keranjang.splice(idx, 1);
        updateRingkasan();
    }
</script>
@endpush
