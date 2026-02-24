<!DOCTYPE html>
<html>
<head>
    <title>Sertifikat</title>
<style>
    @page { 
        margin: 0; 
    }
    body { 
        font-family: 'Times New Roman', serif; 
        margin: 0; 
        padding: 0; 
        width: 100%;
        height: 100%;
    }
    #background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1000;
        }
    .container {
        position: absolute;
        top: 10mm;
        left: 10mm;
        right: 10mm;
        bottom: 10mm;
        
        
        padding: 40px;
        text-align: center;
    }
    h1 { font-size: 60px; margin: 10px 0; color: #333; letter-spacing: 5px; }
    .nomor { font-size: 16px; margin-bottom: 30px; }
    .nama { 
        font-size: 45px; 
        color: #000000ff; 
        font-weight: bold; 
        margin: 15px 0; 
        border-bottom: 2px solid #000000ff; 
        display: inline-block; 
        padding: 0 50px; 
    }
    .teks { font-size: 16px; line-height: 1.5; width: 85%; margin: 0 auto; }
</style>
</head>
<body>
    <div id="background">
        <img src="{{ public_path('images/background.png') }}" width="100%">
    </div>
    <div class="container">
        <h1>SERTIFIKAT</h1>
        <div class="nomor">Nomor: {{ $nomor }}</div>
        
        <p style="font-size: 20px;">Diberikan kepada :</p>
        <div class="nama">{{ $nama }}</div>
        
        <p style="font-size: 20px;">Atas Partisipasinya Sebagai :</p>
        <h2 style="font-size: 30px; color: #000000ff;">{{ $peran }}</h2>
        
        <p class="teks">Dalam acara Seminar dengan tema: "Hilirisasi Kelapa Sawit"</p>
        <p style="margin-top: 40px; font-size: 12px;">
            Diberikan pada tanggal {{ $tanggal }}
        </p>
    </div>
</body>