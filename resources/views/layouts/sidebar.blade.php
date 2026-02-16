<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

        <li class="nav-item {{ request()->is('kategori*') ? 'active' : '' }}">
            <a class="nav-link" href="/kategori">
                <span class="menu-title">Kategori</span>
            </a>
        </li>

        <li class="nav-item {{ request()->is('buku*') ? 'active' : '' }}">
            <a class="nav-link" href="/buku">
                <span class="menu-title">Buku</span>
            </a>
        </li>

        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="nav-link btn btn-link">Logout</button>
            </form>
        </li>

    </ul>
</nav>
