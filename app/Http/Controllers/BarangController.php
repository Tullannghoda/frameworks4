<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $barang = Barang::all();
        return view('barang.index', compact('barang'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('barang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Barang::create([
            'nama_barang' => $request->nama_barang,
            'harga' => $request->harga
        ]);

        return redirect()->route('barang.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        return view('barang.edit', compact('barang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $barang->update([
            'nama_barang' => $request->nama_barang,
            'harga' => $request->harga
        ]);

        return redirect()->route('barang.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->route('barang.index');
    }

    public function cetak(Request $request)
    {
        $selected = $request->barang_id;
        $startX = $request->start_x;
        $startY = $request->start_y;

        if (!$selected) {
            return redirect()->back()->with('error', 'Pilih minimal 1 barang!');
        }

        $barang = Barang::whereIn('id_barang', $selected)->get();

        $pdf = Pdf::loadView('barang.cetak', compact('barang', 'startX', 'startY'));

        return $pdf->stream('tag-harga.pdf');
        
    }
    public function cariBarcode(Request $request)
    {
        $id = $request->query('id');

        $barang = \App\Models\Barang::where('id_barang', $id)->first();

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => "Barang dengan ID '$id' tidak ditemukan.",
            ]);
        }

        return response()->json([
            'success' => true,
            'barang'  => [
                'id_barang'   => $barang->id_barang,
                'nama_barang' => $barang->nama_barang,
                'harga'       => $barang->harga,
            ],
        ]);
    }
    public function scanner(){
        return view('barang.scanner');
    }
}
