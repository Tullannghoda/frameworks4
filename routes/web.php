<?php

use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\PDFController;

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
    Mail::raw("Kode OTP kamu adalah: $otp", function ($message) use ($user) {
        $message->to($user->email)
                ->subject('Kode OTP Login');
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

});

require __DIR__.'/auth.php';
