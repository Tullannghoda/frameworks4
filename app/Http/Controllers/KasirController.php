<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function index()
    {
        return view('kasir.index');
    }

    public function cariBarang(Request $request)
    {
        $item = Barang::where('id_barang', $request->id_barang)->first();

        if ($item) {
            return response()->json([
                'message' => 'Barang ditemukan',
                'result'  => $item
            ]);
        }

        return response()->json([
            'message' => 'Barang tidak ditemukan'
        ]);
    }

    public function simpanTransaksi(Request $request)
    {
        $transaksi = Penjualan::create([
            'timestamp' => now(),
            'total'     => $request->total
        ]);

        foreach ($request->items as $row) {

            PenjualanDetail::create([
                'id_penjualan' => $transaksi->id_penjualan,
                'id_barang'    => $row['id_barang'],
                'jumlah'       => $row['jumlah'],
                'subtotal'     => $row['subtotal']
            ]);

        }

        return response()->json([
            'message' => 'Transaksi berhasil disimpan'
        ]);
    }
}