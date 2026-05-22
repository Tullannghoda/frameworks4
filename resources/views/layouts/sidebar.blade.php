{{-- ============================================================ --}}
{{-- SIDEBAR PARTIAL                                              --}}
{{-- @include('layouts.sidebar') di master.blade.php             --}}
{{-- Mencakup semua menu Modul 1 - 8                             --}}
{{-- ============================================================ --}}
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

        {{-- Profile --}}
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ Auth::user()->name ?? 'Guest' }}</span>
                    <span class="text-secondary text-small">Admin</span>
                </div>
            </a>
        </li>

        {{-- ── Modul 1: Dashboard ── --}}
        <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>

        {{-- ── Modul 1: Kategori ── --}}
        <li class="nav-item {{ request()->is('kategori*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kategori.index') }}">
                <span class="menu-title">Kategori</span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
        </li>

        {{-- ── Modul 1: Buku ── --}}
        <li class="nav-item {{ request()->is('buku*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('buku.index') }}">
                <span class="menu-title">Buku</span>
                <i class="mdi mdi-book-open menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ request()->is('modul4*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#menuModul4"
                aria-expanded="{{ request()->is('modul4*') ? 'true' : 'false' }}">
                <span class="menu-title">Modul 4</span>
                <i class="mdi mdi-code-tags menu-icon"></i>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->is('modul4*') ? 'show' : '' }}" id="menuModul4">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('modul4.datatable') ? 'active' : '' }}"
                            href="{{ route('modul4.datatable') }}">
                            DataTable jQuery
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('modul4.select') ? 'active' : '' }}"
                            href="{{ route('modul4.select') }}">
                            Select &amp; Select2
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        {{-- ── Modul 4: Wilayah ── --}}
        <li class="nav-item {{ request()->is('wilayah*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('wilayah.index') }}">
                <span class="menu-title">Wilayah</span>
                <i class="mdi mdi-map-marker menu-icon"></i>
            </a>
        </li>

        {{-- ── Modul 5: Kasir ── --}}
        <li class="nav-item {{ request()->is('kasir*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kasir.index') }}">
                <span class="menu-title">Kasir</span>
                <i class="mdi mdi-cart menu-icon"></i>
            </a>
        </li>

        {{-- ── Modul 5: Tag Harga (Barang) ── --}}
        <li class="nav-item {{ request()->is('barang*') && !request()->is('barang/scanner*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('barang.index') }}">
                <span class="menu-title">Tag Harga</span>
                <i class="mdi mdi-tag-multiple menu-icon"></i>
            </a>
        </li>

        {{-- ── Modul 7: Customer (Dropdown) ── --}}
        <li class="nav-item {{ request()->is('vendor/customers*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#menuCustomer"
               aria-expanded="{{ request()->is('vendor/customers*') ? 'true' : 'false' }}">
                <span class="menu-title">Customer</span>
                <i class="mdi mdi-account-group menu-icon"></i>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->is('vendor/customers*') ? 'show' : '' }}" id="menuCustomer">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customerdata.index') ? 'active' : '' }}"
                           href="{{ route('customerdata.index') }}">
                            Data Customer
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customerdata.create-blob') ? 'active' : '' }}"
                           href="{{ route('customerdata.create-blob') }}">
                            Tambah Customer 1
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customerdata.create-file') ? 'active' : '' }}"
                           href="{{ route('customerdata.create-file') }}">
                            Tambah Customer 2
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        {{-- ── Modul 8: Scan Barcode ── --}}
        <li class="nav-item {{ request()->is('barang/scanner*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('barang.scanner') }}">
                <span class="menu-title">Scan Barcode</span>
                <i class="mdi mdi-barcode-scan menu-icon"></i>
            </a>
        </li>

        {{-- ── Modul 9: Geolcationn ── --}}
        <li class="nav-item {{ request()->is('toko*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('toko.index') }}">
                <span class="menu-title">Kunjungan Toko</span>
                <i class="mdi mdi-store menu-icon"></i>
            </a>
        </li>
        
        <li class="nav-item {{ request()->is('antrian*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#menuAntrian"
                aria-expanded="{{ request()->is('antrian*') ? 'true' : 'false' }}">
                <span class="menu-title">Sistem Antrian</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->is('antrian*') ? 'show' : '' }}" id="menuAntrian">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('antrian.admin') ? 'active' : '' }}"
                        href="{{ route('antrian.admin') }}">
                            Admin Antrian
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('antrian.guest') }}" target="_blank">
                            Halaman Guest ↗
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('antrian.papan') }}" target="_blank">
                            Papan Antrian ↗
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</nav>
