<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi OTP</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>

<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                <div class="col-lg-4 mx-auto">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>