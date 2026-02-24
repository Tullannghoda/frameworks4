<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PDFController extends Controller
{
    public function __construct()
    {
        // Pastikan hanya user login yang bisa akses
        $this->middleware('auth');
    }

    public function sertifikat()
    {
        $user = Auth::user();

        // Generate nomor sertifikat otomatis
        $nomor = 'CERT/' . now()->format('YmdHis');

        $data = [
            'nomor' => $nomor,
            'nama'  => $user->name,
            'peran' => 'PESERTA TERBAIK',
            'tanggal' => Carbon::now()->translatedFormat('d F Y'),
        ];

        $pdf = Pdf::loadView('pdf.sertifikat', $data)
                  ->setPaper('a4', 'landscape');

        return $pdf->download('sertifikat-' . str_replace(' ', '-', $user->name) . '.pdf');
    }

    public function undangan()
    {
        $user = Auth::user();
        $now  = \Carbon\Carbon::now();

        $data = [
            'nomor'     => '3353/IV/ARK.ENF/PM.03/' . $now->format('Y'),
            'tanggal'   => $now->translatedFormat('d F Y'),
            'nama'      => $user->name,
            'email'     => $user->email,
            'hari'      => 'Selasa',
            'tgl_acara' => '20 Mareti 2026',
            'waktu'     => '10.00 – 13.00 WIB',
            'tempat'    => 'Plaza Airlangga, Lantai 5',
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.undangan', $data)
                ->setPaper('a4', 'portrait');

        return $pdf->download('surat-undangan-'.$user->name.'.pdf');
    }
}