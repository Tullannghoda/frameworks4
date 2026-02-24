<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Undangan</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 14px;
            margin: 40px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
        }

        .right {
            text-align: right;
        }

        .content {
            margin-top: 20px;
            text-align: justify;
        }

        .signature {
            margin-top: 60px;
            text-align: right;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>BATAK FEST 2026</h2>
    <p>IKAMABA UNAIR</p>
    <p>Gubeng Kertajaya IX G no.9 Airlangga | Telp. (031) 5033869</p>
</div>

<div class="right">
    <p>Surabaya, {{ $tanggal }}</p>
</div>

<p>Nomor : {{ $nomor }}</p>
<p>Lampiran : -</p>
<p>Perihal : Undangan Silaturahmi</p>

<br>

<p>Yth. {{ $nama }}</p>
<p>({{ $email }})</p>

<div class="content">
    <p>Dengan hormat,</p>

    <p>
        Dalam rangka melestarikan budaya Batak di Universitas Airlangga,
        kami mengundang Bapak/Ibu untuk hadir pada acara yang akan dilaksanakan pada:
    </p>

    <table>
        <tr>
            <td width="150">Hari, Tanggal</td>
            <td>: {{ $hari }}, {{ $tgl_acara }}</td>
        </tr>
        <tr>
            <td>Waktu</td>
            <td>: {{ $waktu }}</td>
        </tr>
        <tr>
            <td>Tempat</td>
            <td>: {{ $tempat }}</td>
        </tr>
    </table>

    <p style="margin-top:20px;">
        Demikian undangan ini kami sampaikan. Atas perhatian dan kehadirannya,
        kami ucapkan terima kasih.
    </p>
</div>

<div class="signature">
    <p>Humas Batak Fest 2026,</p>
    <br><br>
    <p><strong>Butong</strong></p>
</div>

</body>
</html>