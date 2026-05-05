<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Purple Admin')</title>

    {{-- ======================================================= --}}
    {{-- STYLE GLOBAL — berlaku untuk semua halaman               --}}
    {{-- ======================================================= --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />

    {{-- ======================================================= --}}
    {{-- STYLE PAGE — hanya berlaku untuk halaman yang aktif      --}}
    {{-- Diisi di masing-masing view dengan @section('styles')   --}}
    {{-- ======================================================= --}}
    @yield('styles')
</head>
<body>
    <div class="container-scroller">

        {{-- ======================================================= --}}
        {{-- NAVBAR                                                    --}}
        {{-- ======================================================= --}}
        @include('layouts.navbar')

        <div class="container-fluid page-body-wrapper">

            {{-- ======================================================= --}}
            {{-- SIDEBAR                                                   --}}
            {{-- ======================================================= --}}
            @include('layouts.sidebar')

            {{-- ======================================================= --}}
            {{-- CONTENT                                                   --}}
            {{-- ======================================================= --}}
            <div class="main-panel">
                <div class="content-wrapper">
                    @yield('content')
                </div>

                {{-- ======================================================= --}}
                {{-- FOOTER                                                    --}}
                {{-- ======================================================= --}}
                @include('layouts.footer')
            </div>
            {{-- main-panel ends --}}

        </div>
        {{-- page-body-wrapper ends --}}

    </div>
    {{-- container-scroller --}}

    {{-- ======================================================= --}}
    {{-- JAVASCRIPT GLOBAL — berlaku untuk semua halaman          --}}
    {{-- ======================================================= --}}
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/vendors/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    {{-- ======================================================= --}}
    {{-- JAVASCRIPT PAGE — hanya untuk halaman yang aktif         --}}
    {{-- Diisi di masing-masing view dengan @section('scripts')  --}}
    {{-- ======================================================= --}}
    @yield('scripts')

</body>
</html>
