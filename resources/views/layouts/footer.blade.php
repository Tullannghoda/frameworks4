{{-- ============================================================ --}}
{{-- FOOTER PARTIAL                                               --}}
{{-- @include('layouts.footer') di master.blade.php              --}}
{{-- ============================================================ --}}
<footer class="footer">
    <div class="d-sm-flex justify-content-center justify-content-sm-between">
        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
            © {{ date('Y') }} <a href="#" target="_blank">Koleksi Buku</a>. All rights reserved.
        </span>
        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center text-muted">
            Framework Laravel {{ app()->version() }}
        </span>
    </div>
</footer>
