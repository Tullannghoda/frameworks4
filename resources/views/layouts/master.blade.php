<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Koleksi Buku</title>

    {{-- STYLE GLOBAL --}}
    <link rel="stylesheet" href="{{ asset('template/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('template/css/style.css') }}">

    {{-- STYLE PAGE --}}
    @yield('style-page')
</head>
<body>
<div class="container-scroller">

    {{-- NAVBAR --}}
    @include('layouts.navbar')

    <div class="container-fluid page-body-wrapper">

        {{-- SIDEBAR --}}
        @include('layouts.sidebar')

        {{-- CONTENT --}}
        <div class="main-panel">
            <div class="content-wrapper">
                @yield('content')
            </div>

            {{-- FOOTER --}}
            @include('layouts.footer')
        </div>

    </div>
</div>

{{-- JS GLOBAL --}}
<script src="{{ asset('template/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('template/js/off-canvas.js') }}"></script>
<script src="{{ asset('template/js/hoverable-collapse.js') }}"></script>
<script src="{{ asset('template/js/template.js') }}"></script>

{{-- JS PAGE --}}
@yield('js-page')

</body>
</html>
