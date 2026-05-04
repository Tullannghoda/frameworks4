<?php

use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\KasirController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CustomerDataController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/auth/google/callback', function () {

    $googleUser = Socialite::driver('google')->user();

    $user = User::updateOrCreate(
        ['email' => $googleUser->email],
        [
            'name' => $googleUser->name,
            'id_google' => $googleUser->id,
            'password' => bcrypt('google-login')
        ]
    );

    // Generate OTP
    $otp = rand(100000, 999999);
    $user->otp = $otp;
    $user->save();

   // Kirim OTP ke email
    Mail::send('emails.otp', 
        ['otp' => $otp, 'name' => $user->name], 
        function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Kode Verifikasi Login');
    });
    // Pastikan belum login
    Auth::logout();

    // Simpan id user sementara
    session(['otp_user_id' => $user->id]);

    return redirect()->route('verify.otp');
});

Route::get('/verify-otp', function () {
    return view('auth.verify-otp');
})->name('verify.otp');

Route::post('/verify-otp', function (\Illuminate\Http\Request $request) {

    $user = \App\Models\User::find(session('otp_user_id'));

    if ($user && $user->otp == $request->otp) {

        $user->otp = null;
        $user->save();

        \Illuminate\Support\Facades\Auth::login($user);

        return redirect('/dashboard');
    }

    return back()->with('error', 'OTP salah');
});

Route::get('/test-email', function () {
    Mail::raw('Test email Laravel berhasil', function ($message) {
        $message->to('glennovian@gmail.com')
                ->subject('Test Email');
    });

    return 'Email dikirim!';
});

Route::middleware(['auth'])->group(function () {

    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
    Route::post('/kategori/store', [KategoriController::class, 'store'])->name('kategori.store');
    Route::post('/buku/store', [BukuController::class, 'store'])->name('buku.store');
    Route::get('/pdf-sertifikat', [PDFController::class, 'sertifikat'])->name('pdf.sertifikat');
    Route::get('/pdf-undangan', [PDFController::class, 'undangan'])->name('pdf.undangan');
    Route::post('/barang/cetak', [BarangController::class, 'cetak'])->name('barang.cetak');
    Route::resource('barang', BarangController::class);
    Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    Route::post('/wilayah/regency', [WilayahController::class, 'fetchRegency'])->name('wilayah.regency');
    Route::post('/wilayah/district', [WilayahController::class, 'fetchDistrict'])->name('wilayah.district');
    Route::post('/wilayah/village', [WilayahController::class, 'fetchVillage'])->name('wilayah.village');
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
    Route::post('/kasir/cari-barang', [KasirController::class, 'cariBarang'])->name('kasir.cari');
    Route::post('/kasir/bayar', [KasirController::class, 'bayar'])->name('kasir.bayar');
    Route::post('/kasir/simpan-transaksi', [KasirController::class, 'simpanTransaksi'])->name('kasir.simpan');

        // ── Studi Kasus 3 — Customer & Akses Kamera ──────────────
    Route::prefix('vendor/customers')->name('customerdata.')->group(function () {
        Route::get('/',      [CustomerDataController::class, 'index'])->name('index');
        Route::get('/blob',  [CustomerDataController::class, 'createBlob'])->name('create-blob');
        Route::post('/blob', [CustomerDataController::class, 'storeBlob'])->name('store-blob');
        Route::get('/file',  [CustomerDataController::class, 'createFile'])->name('create-file');
        Route::post('/file', [CustomerDataController::class, 'storeFile'])->name('store-file');
        Route::delete('/{customer}', [CustomerDataController::class, 'destroy'])->name('destroy');
    });
});

/*
| Customer Routes (Tidak perlu login)
*/
Route::prefix('kantin')->name('customer.')->group(function () {
    Route::get('/',              [CustomerController::class, 'index'])->name('index');
    Route::post('/pesan',        [CustomerController::class, 'store'])->name('store');
    Route::get('/status/{idpesanan}',   [CustomerController::class, 'status'])->name('status');

    // AJAX: ambil menu berdasarkan vendor
    Route::get('/menu/{idvendor}', [CustomerController::class, 'getMenuByVendor'])->name('menu.byvendor');
});

/*
| Payment Routes
*/
Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/{idpesanan}',   [PaymentController::class, 'show'])->name('show');
    Route::post('/callback',     [PaymentController::class, 'callback'])->name('callback')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
    Route::get('/finish',        [PaymentController::class, 'finish'])->name('finish');
});

/*
| Vendor Routes
*/
Route::prefix('vendor')->name('vendor.')->group(function () {
    // Auth (tidak perlu middleware)
    Route::get('/login',          [VendorController::class, 'showLogin'])->name('login');
    Route::post('/login',         [VendorController::class, 'login'])->name('login.post');
    Route::get('/register',       [VendorController::class, 'showRegister'])->name('register');
    Route::post('/register',      [VendorController::class, 'register'])->name('register.post');
    Route::post('/logout',        [VendorController::class, 'logout'])->name('logout');

    // Protected routes
    Route::middleware('vendor.auth')->group(function () {
        Route::get('/dashboard',       [VendorController::class, 'dashboard'])->name('dashboard');

        // Menu CRUD
        Route::get('/menu',            [VendorController::class, 'menuIndex'])->name('menu.index');
        Route::get('/menu/create',     [VendorController::class, 'menuCreate'])->name('menu.create');
        Route::post('/menu',           [VendorController::class, 'menuStore'])->name('menu.store');
        Route::get('/menu/{id}/edit',  [VendorController::class, 'menuEdit'])->name('menu.edit');
        Route::put('/menu/{id}',       [VendorController::class, 'menuUpdate'])->name('menu.update');
        Route::delete('/menu/{id}',    [VendorController::class, 'menuDestroy'])->name('menu.destroy');
    });
});

// Redirect root ke halaman kantin
Route::get('/', function () {
    return redirect()->route('customer.index');
});

Route::middleware('vendor.auth')->group(function () {

    // PDF Tag Harga dengan Barcode (SC 1)
    // Contoh: GET /vendor/menu/1/pdf-tag-harga
    Route::get('/vendor/menu/{menu}/pdf-tag-harga', [BarcodeController::class, 'pdfTagHarga'])
        ->name('menu.pdf-tag-harga');

    // Invoice PDF dengan QR Code (SC 2)
    // Contoh: GET /vendor/pesanan/5/invoice
    Route::get('/vendor/pesanan/{pesanan}/invoice', [BarcodeController::class, 'invoiceQr'])
        ->name('pesanan.invoice');

});

require __DIR__.'/auth.php';
