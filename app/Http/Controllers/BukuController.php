<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Kategori;

class BukuController extends Controller
{
    public function index()
    {
        $buku = Buku::with('kategori')->get();
        $kategori = Kategori::all();

        return view('buku.index', compact('buku', 'kategori'));
    }

    public function store(Request $request)
    {
        Buku::create([
            'kategori_id' => $request->kategori_id,
            'kode' => $request->kode,
            'judul' => $request->judul,
            'pengarang' => $request->pengarang
        ]);

        return redirect()->route('buku.index');
    }
}
