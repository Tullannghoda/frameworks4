<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AntrianController extends Controller
{
    // ─────────────────────────────────────────────
    // HALAMAN
    // ─────────────────────────────────────────────

    /** GET /antrian/guest — form daftar antrian */
    public function guest()
    {
        return view('antrian.guest');
    }

    /** GET /antrian/admin — dashboard admin */
    public function admin()
    {
        return view('antrian.admin');
    }

    /** GET /antrian/papan — papan antrian publik */
    public function papan()
    {
        return view('antrian.papan');
    }

    /** GET /antrian/tiket — tiket nomor antrian (dibuka di tab baru) */
    public function tiket(Request $request)
    {
        $nomor = $request->query('nomor');
        $nama  = $request->query('nama');

        if (!$nomor || !$nama) {
            return redirect()->route('antrian.guest');
        }

        return view('antrian.tiket', compact('nomor', 'nama'));
    }

    // ─────────────────────────────────────────────
    // AKSI
    // ─────────────────────────────────────────────

    /**
     * POST /antrian/daftar
     * Guest mendaftar → dapat nomor antrian
     */
    public function daftar(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:100']);

        // Ambil & increment counter nomor antrian
        $nomor = Cache::increment('antrian_counter');

        // Ambil daftar antrian yang ada
        $list = Cache::get('antrian_list', []);

        // Tambah ke list
        $list[] = [
            'nomor'  => $nomor,
            'nama'   => $request->nama,
            'status' => 'menunggu',   // menunggu | dipanggil | terlambat
        ];

        Cache::put('antrian_list', $list, now()->addHours(8));

        // Redirect ke tiket di tab baru (pakai JS di view)
        return response()->json([
            'success' => true,
            'nomor'   => $nomor,
            'nama'    => $request->nama,
        ]);
    }

    /**
     * POST /antrian/panggil
     * Admin memanggil nomor antrian berikutnya
     */
    public function panggil(Request $request)
    {
        $list = Cache::get('antrian_list', []);

        // Cari antrian pertama yang masih "menunggu"
        $index = null;
        foreach ($list as $i => $item) {
            if ($item['status'] === 'menunggu') {
                $index = $i;
                break;
            }
        }

        if ($index === null) {
            return response()->json(['success' => false, 'message' => 'Tidak ada antrian.'], 422);
        }

        // Ubah status antrian sebelumnya (yang dipanggil) jadi terlambat
        foreach ($list as $i => $item) {
            if ($item['status'] === 'dipanggil') {
                $list[$i]['status'] = 'terlambat';
            }
        }

        // Set antrian terpilih jadi dipanggil
        $list[$index]['status'] = 'dipanggil';

        // Simpan nomor yang sedang dipanggil ke cache terpisah agar mudah dibaca SSE
        Cache::put('antrian_current', [
            'nomor' => $list[$index]['nomor'],
            'nama'  => $list[$index]['nama'],
        ], now()->addHours(8));

        Cache::put('antrian_list', $list, now()->addHours(8));

        return response()->json(['success' => true]);
    }

    /**
     * POST /antrian/panggil-terlambat
     * Admin memanggil ulang nomor yang terlambat
     */
    public function panggilTerlambat(Request $request)
    {
        $request->validate(['nomor' => 'required|integer']);

        $list = Cache::get('antrian_list', []);

        $index = null;
        foreach ($list as $i => $item) {
            if ($item['nomor'] == $request->nomor && $item['status'] === 'terlambat') {
                $index = $i;
                break;
            }
        }

        if ($index === null) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 422);
        }

        // Reset yang sedang dipanggil ke terlambat
        foreach ($list as $i => $item) {
            if ($item['status'] === 'dipanggil') {
                $list[$i]['status'] = 'terlambat';
            }
        }

        $list[$index]['status'] = 'dipanggil';

        Cache::put('antrian_current', [
            'nomor'     => $list[$index]['nomor'],
            'nama'      => $list[$index]['nama'],
            'terlambat' => true,
        ], now()->addHours(8));

        Cache::put('antrian_list', $list, now()->addHours(8));

        return response()->json(['success' => true]);
    }

    /**
     * POST /antrian/reset
     * Admin mereset semua antrian (akhir hari)
     */
    public function reset()
    {
        Cache::forget('antrian_list');
        Cache::forget('antrian_current');
        Cache::forget('antrian_counter');

        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────
    // SSE STREAM
    // ─────────────────────────────────────────────

    /**
     * GET /antrian/stream
     * Server-Sent Events endpoint — mengirim update antrian ke semua client
     */
    public function stream()
    {
        return response()->stream(function () {
            set_time_limit(0); // Cegah PHP timeout

            $lastHash = null;

            while (true) {
                // Baca state terbaru dari cache
                $list    = Cache::get('antrian_list', []);
                $current = Cache::get('antrian_current', null);

                $payload = [
                    'list'    => $list,
                    'current' => $current,
                ];

                $hash = md5(json_encode($payload));

                // Hanya kirim jika ada perubahan data
                if ($hash !== $lastHash) {
                    $lastHash = $hash;

                    echo 'event: antrian-update' . PHP_EOL;
                    echo 'data: ' . json_encode($payload) . PHP_EOL;
                    echo PHP_EOL;

                    ob_flush();
                    flush();
                }

                // Keep-alive komentar tiap siklus
                echo ': ping' . PHP_EOL . PHP_EOL;
                ob_flush();
                flush();

                if (connection_aborted()) {
                    break;
                }

                sleep(1);
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',   // Penting untuk Nginx
            'Connection'        => 'keep-alive',
        ]);
    }
}
