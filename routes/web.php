<?php

use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
    Route::post('/kategori/store', [KategoriController::class, 'store'])->name('kategori.store');
    Route::post('/buku/store', [BukuController::class, 'store'])->name('buku.store');

});

require __DIR__.'/auth.php';
