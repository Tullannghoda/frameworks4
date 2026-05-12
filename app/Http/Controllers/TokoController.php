<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\Kunjungan;
use Illuminate\Http\Request;

class TokoController extends Controller
{
    // ── Halaman utama kunjungan toko ──
    public function index()
    {
        $tokos = Toko::latest()->get();
        return view('toko.index', compact('tokos'));
    }

    // ── Simpan toko baru + titik awal ──
    public function store(Request $request)
    {
        $request->validate([
            'nama_toko'  => 'required|string|max:255',
            'alamat'     => 'nullable|string',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
            'accuracy'   => 'required|numeric',
        ]);

        // Generate barcode unik: TK-001, TK-002, dst
        $last    = Toko::orderByDesc('id')->first();
        $nextNum = $last ? ($last->id + 1) : 1;
        $barcode = 'TK-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        Toko::create([
            'barcode'   => $barcode,
            'nama_toko' => $request->nama_toko,
            'alamat'    => $request->alamat,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy'  => $request->accuracy,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Toko berhasil ditambahkan.',
        ]);
    }

    // ── Hapus toko ──
    public function destroy(Toko $toko)
    {
        $toko->delete();
        return response()->json(['success' => true]);
    }

    // ── Ambil detail toko berdasarkan barcode (untuk scanner) ──
    public function cariBarcode(Request $request)
    {
        $toko = Toko::where('barcode', $request->query('barcode'))->first();

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan.',
            ]);
        }

        return response()->json([
            'success' => true,
            'toko'    => $toko,
        ]);
    }

    // ── Simpan laporan kunjungan sales ──
    public function simpanKunjungan(Request $request)
    {
        $request->validate([
            'toko_id'        => 'required|exists:toko,id',
            'nama_sales'     => 'nullable|string|max:255',
            'latitude_sales' => 'required|numeric',
            'longitude_sales'=> 'required|numeric',
            'accuracy_sales' => 'required|numeric',
            'threshold'      => 'nullable|integer|min:0',
        ]);

        $toko      = Toko::findOrFail($request->toko_id);
        $threshold = $request->threshold ?? 300;

        // Hitung jarak dengan formula Haversine
        $jarak = $this->haversine(
            $toko->latitude, $toko->longitude,
            $request->latitude_sales, $request->longitude_sales
        );

        // Threshold efektif = threshold + akurasi toko + akurasi sales
        $thresholdEfektif = $threshold + $toko->accuracy + $request->accuracy_sales;
        $diterima         = $jarak <= $thresholdEfektif;

        $kunjungan = Kunjungan::create([
            'toko_id'           => $toko->id,
            'nama_sales'        => $request->nama_sales,
            'latitude_sales'    => $request->latitude_sales,
            'longitude_sales'   => $request->longitude_sales,
            'accuracy_sales'    => $request->accuracy_sales,
            'jarak_meter'       => round($jarak, 2),
            'threshold_efektif' => round($thresholdEfektif, 2),
            'diterima'          => $diterima,
            'threshold'         => $threshold,
        ]);

        return response()->json([
            'success'           => true,
            'diterima'          => $diterima,
            'jarak_meter'       => round($jarak, 2),
            'threshold_efektif' => round($thresholdEfektif, 2),
            'message'           => $diterima
                ? "✅ Kunjungan DITERIMA — jarak {$jarak}m ≤ threshold {$thresholdEfektif}m"
                : "❌ Kunjungan DITOLAK — jarak {$jarak}m > threshold {$thresholdEfektif}m",
        ]);
    }

    // ── PDF Barcode toko ──
    public function cetakBarcode(Toko $toko)
    {
        $barcodeBase64 = \App\Http\Controllers\BarcodeController::generateBarcodeBase64($toko->barcode);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('toko.barcode_pdf', [
            'toko'          => $toko,
            'barcodeBase64' => $barcodeBase64,
        ]);

        $pdf->setPaper([0, 0, 226.77, 141.73]); // 8cm x 5cm
        return $pdf->stream("barcode_toko_{$toko->id}.pdf");
    }

    // ── Formula Haversine (menghitung jarak dua koordinat dalam meter) ──
    private function haversine($lat1, $lng1, $lat2, $lng2): float
    {
        $R    = 6371000; // radius bumi dalam meter
        $dLat = ($lat2 - $lat1) * M_PI / 180;
        $dLng = ($lng2 - $lng1) * M_PI / 180;
        $a    = sin($dLat / 2) ** 2
              + cos($lat1 * M_PI / 180) * cos($lat2 * M_PI / 180)
              * sin($dLng / 2) ** 2;
        $c    = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }
}
