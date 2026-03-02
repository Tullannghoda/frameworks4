<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode Verifikasi</title>
</head>
<body style="font-family: Arial, sans-serif;">

    <p>Halo {{ $name }},</p>

    <p>Kode Verifikasi Anda adalah:</p>

    <h2 style="letter-spacing:5px;">{{ $otp }}</h2>

    <p>Kode ini berlaku selama 5 menit.</p>

    <p>Jangan berikan kode ini kepada siapapun.</p>

</body>
</html>