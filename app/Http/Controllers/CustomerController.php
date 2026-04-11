<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Halaman utama pemesanan - pilih vendor
    public function index()
    {
        $vendors = Vendor::all();
        return view('customer.index', compact('vendors'));
    }

    // Ambil menu berdasarkan vendor (AJAX)
    public function getMenuByVendor($idvendor)
    {
        $menus = Menu::where('idvendor', $idvendor)->get();
        return response()->json($menus);
    }

    // Proses simpan pesanan
    public function store(Request $request)
    {
        $request->validate([
            'idvendor' => 'required|exists:vendor,idvendor',
            'items'    => 'required|array|min:1',
        ]);

        // Generate nama guest otomatis: Guest_0000001
        $lastPesanan = Pesanan::orderBy('idpesanan', 'desc')->first();
        $nextNumber  = $lastPesanan ? ($lastPesanan->idpesanan + 1) : 1;
        $namaGuest   = 'Guest_' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);

        // Hitung total
        $total = 0;
        foreach ($request->items as $item) {
            $menu   = Menu::findOrFail($item['idmenu']);
            $total += $menu->harga * $item['jumlah'];
        }

        // Generate order_id unik untuk Midtrans
        $orderId = 'KANTIN-' . time() . '-' . rand(100, 999);

        // Buat pesanan
        $pesanan = Pesanan::create([
            'nama'        => $namaGuest,
            'total'       => $total,
            'status_bayar'=> 0,
            'order_id'    => $orderId,
            'idvendor'    => $request->idvendor,
        ]);

        // Simpan detail pesanan
        foreach ($request->items as $item) {
            $menu = Menu::findOrFail($item['idmenu']);
            DetailPesanan::create([
                'idmenu'   => $menu->idmenu,
                'idpesanan'=> $pesanan->idpesanan,
                'jumlah'   => $item['jumlah'],
                'harga'    => $menu->harga,
                'subtotal' => $menu->harga * $item['jumlah'],
                'catatan'  => $item['catatan'] ?? null,
            ]);
        }

        return redirect()->route('payment.show', $pesanan->idpesanan);
    }

    // Halaman status pesanan
    public function status($idpesanan)
    {
        $pesanan = Pesanan::with('detailPesanans.menu')->findOrFail($idpesanan);
        return view('customer.status', compact('pesanan'));
    }
}
