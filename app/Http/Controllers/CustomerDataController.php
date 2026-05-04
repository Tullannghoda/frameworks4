<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerDataController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // Data Customer (index)
    // ─────────────────────────────────────────────────────────────────────────

    public function index()
    {
        $customers = Customer::latest()->paginate(10);
        return view('vendor.customer.index', compact('customers'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Tambah Customer 1 — foto disimpan sebagai BLOB (Base64) di database
    // ─────────────────────────────────────────────────────────────────────────

    public function createBlob()
    {
        return view('vendor.customer.create_blob');
    }

    public function storeBlob(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:customers,email',
            'telepon'  => 'nullable|string|max:20',
            'alamat'   => 'nullable|string',
            'foto_blob' => 'required|string', // data URL base64 dari kamera
        ]);

        // Validasi format data URL
        if (!str_starts_with($request->foto_blob, 'data:image/')) {
            return back()->withErrors(['foto_blob' => 'Format foto tidak valid.'])->withInput();
        }

        Customer::create([
            'nama'      => $request->nama,
            'email'     => $request->email,
            'telepon'   => $request->telepon,
            'alamat'    => $request->alamat,
            'foto_blob' => $request->foto_blob,
        ]);

        return redirect()->route('customerdata.index')
            ->with('success', 'Customer berhasil ditambahkan (foto disimpan sebagai BLOB).');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Tambah Customer 2 — foto disimpan sebagai file, path di database
    // ─────────────────────────────────────────────────────────────────────────

    public function createFile()
    {
        return view('vendor.customer.create_file');
    }

    public function storeFile(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'email'     => 'required|email|unique:customers,email',
            'telepon'   => 'nullable|string|max:20',
            'alamat'    => 'nullable|string',
            'foto_base64' => 'required|string', // data URL base64 dari kamera
        ]);

        // Konversi base64 → file dan simpan ke storage/app/public/customers/
        $dataUrl = $request->foto_base64;

        if (!str_starts_with($dataUrl, 'data:image/')) {
            return back()->withErrors(['foto_base64' => 'Format foto tidak valid.'])->withInput();
        }

        // Pisahkan header dan data
        [$header, $base64Data] = explode(',', $dataUrl, 2);
        preg_match('/data:(image\/\w+);base64/', $header, $matches);
        $mimeType  = $matches[1] ?? 'image/jpeg';
        $extension = explode('/', $mimeType)[1]; // jpeg / png / webp

        $filename  = 'customers/' . uniqid('cust_', true) . '.' . $extension;
        $decoded   = base64_decode($base64Data);

        Storage::disk('public')->put($filename, $decoded);

        Customer::create([
            'nama'       => $request->nama,
            'email'      => $request->email,
            'telepon'    => $request->telepon,
            'alamat'     => $request->alamat,
            'foto_path'  => $filename,
        ]);

        return redirect()->route('customerdata.index')
            ->with('success', 'Customer berhasil ditambahkan (foto disimpan sebagai file).');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Delete
    // ─────────────────────────────────────────────────────────────────────────

    public function destroy(Customer $customer)
    {
        // Hapus file jika ada
        if ($customer->foto_path) {
            Storage::disk('public')->delete($customer->foto_path);
        }

        $customer->delete();

        return redirect()->route('customerdata.index')
            ->with('success', 'Customer berhasil dihapus.');
    }
}
