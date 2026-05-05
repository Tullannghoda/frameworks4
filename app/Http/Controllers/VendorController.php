<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    // ===================== AUTH =====================

    public function showLogin()
    {
        return view('vendor.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $vendor = Vendor::where('email', $request->email)->first();

        if ($vendor && Hash::check($request->password, $vendor->password)) {
            session(['vendor' => $vendor]);
            return redirect()->route('vendor.dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    public function logout()
    {
        session()->forget('vendor');
        return redirect()->route('vendor.login');
    }

    public function showRegister()
    {
        return view('vendor.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'email'       => 'required|email|unique:vendor,email',
            'password'    => 'required|min:6|confirmed',
        ]);

        Vendor::create([
            'nama_vendor' => $request->nama_vendor,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
        ]);

        return redirect()->route('vendor.login')->with('success', 'Registrasi berhasil! Silahkan login.');
    }

    // ===================== DASHBOARD =====================

    public function dashboard()
    {
        $vendor   = session('vendor');
        $pesanans = Pesanan::where('idvendor', $vendor->idvendor)
                           ->where('status_bayar', 1)
                           ->with('detailPesanans.menu')
                           ->orderBy('created_at', 'desc')
                           ->get();

        return view('vendor.dashboard', compact('pesanans', 'vendor'));
    }

    // ===================== MENU =====================

    public function menuIndex()
    {
        $vendor = session('vendor');
        $menus  = Menu::where('idvendor', $vendor->idvendor)->get();
        return view('vendor.menu.index', compact('menus', 'vendor'));
    }

    public function menuCreate()
    {
        return view('vendor.menu.create');
    }

    public function menuStore(Request $request)
    {
        $request->validate([
            'nama_menu'   => 'required|string|max:255',
            'harga'       => 'required|integer|min:0',
            'path_gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $vendor    = session('vendor');
        $imagePath = null;

        if ($request->hasFile('path_gambar')) {
            $imagePath = $request->file('path_gambar')->store('menu', 'public');
        }

        Menu::create([
            'nama_menu'   => $request->nama_menu,
            'harga'       => $request->harga,
            'path_gambar' => $imagePath,
            'idvendor'    => $vendor->idvendor,
        ]);

        return redirect()->route('vendor.menu.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function menuEdit($id)
    {
        $vendor = session('vendor');
        $menu   = Menu::where('idmenu', $id)->where('idvendor', $vendor->idvendor)->firstOrFail();
        return view('vendor.menu.edit', compact('menu'));
    }

    public function menuUpdate(Request $request, $id)
    {
        $request->validate([
            'nama_menu'   => 'required|string|max:255',
            'harga'       => 'required|integer|min:0',
            'path_gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $vendor = session('vendor');
        $menu   = Menu::where('idmenu', $id)->where('idvendor', $vendor->idvendor)->firstOrFail();

        if ($request->hasFile('path_gambar')) {
            if ($menu->path_gambar) {
                Storage::disk('public')->delete($menu->path_gambar);
            }
            $menu->path_gambar = $request->file('path_gambar')->store('menu', 'public');
        }

        $menu->nama_menu = $request->nama_menu;
        $menu->harga     = $request->harga;
        $menu->save();

        return redirect()->route('vendor.menu.index')->with('success', 'Menu berhasil diupdate!');
    }

    public function menuDestroy($id)
    {
        $vendor = session('vendor');
        $menu   = Menu::where('idmenu', $id)->where('idvendor', $vendor->idvendor)->firstOrFail();

        if ($menu->path_gambar) {
            Storage::disk('public')->delete($menu->path_gambar);
        }

        $menu->delete();

        return redirect()->route('vendor.menu.index')->with('success', 'Menu berhasil dihapus!');
    }
    // ===================== MODUL 8 P2: QR SCANNER =====================
 
    /**
     * Halaman scan QR code customer
     */
    public function scanQr()
    {
        $vendor = session('vendor');
        return view('vendor.scan-qr', compact('vendor'));
    }
 
    /**
     * API: Ambil detail pesanan berdasarkan idpesanan dari QR code.
     * Hanya tampilkan menu milik vendor yang sedang login.
     */
    public function cariPesanan(Request $request)
    {
        $idpesanan = $request->query('idpesanan');
        $vendor    = session('vendor');
 
        $pesanan = Pesanan::with(['detailPesanans.menu'])
                          ->find($idpesanan);
 
        if (!$pesanan) {
            return response()->json([
                'success' => false,
                'message' => "Pesanan dengan ID '$idpesanan' tidak ditemukan.",
            ]);
        }
 
        // Filter detail hanya untuk menu milik vendor ini
        $detailVendor = $pesanan->detailPesanans->filter(function ($detail) use ($vendor) {
            return $detail->menu && $detail->menu->idvendor == $vendor->idvendor;
        })->values();
 
        if ($detailVendor->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "Tidak ada pesanan untuk vendor Anda pada QR ini.",
            ]);
        }
 
        return response()->json([
            'success'      => true,
            'idpesanan'    => $pesanan->idpesanan,
            'nama'         => $pesanan->nama,
            'status_bayar' => $pesanan->status_bayar,
            'metode_bayar' => $pesanan->metode_bayar,
            'total'        => $pesanan->total,
            'items'        => $detailVendor->map(function ($detail) {
                return [
                    'nama_menu' => $detail->menu->nama_menu,
                    'jumlah'    => $detail->jumlah,
                    'harga'     => $detail->harga,
                    'subtotal'  => $detail->subtotal,
                    'catatan'   => $detail->catatan,
                ];
            }),
        ]);
    }
}
