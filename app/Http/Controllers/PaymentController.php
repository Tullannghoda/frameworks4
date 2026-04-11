<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    // Halaman pembayaran - generate snap token
    public function show($idpesanan)
    {
        $pesanan = Pesanan::with('detailPesanans.menu')->findOrFail($idpesanan);

        // Jika sudah lunas, redirect ke status
        if ($pesanan->status_bayar == 1) {
            return redirect()->route('customer.status', $idpesanan)
                             ->with('info', 'Pesanan ini sudah dibayar.');
        }

        // Jika belum ada snap token, generate baru
        if (!$pesanan->snap_token) {
            $params = [
                'transaction_details' => [
                    'order_id'     => $pesanan->order_id,
                    'gross_amount' => $pesanan->total,
                ],
                'customer_details' => [
                    'first_name' => $pesanan->nama,
                ],
                'item_details' => $pesanan->detailPesanans->map(function ($detail) {
                    return [
                        'id'       => $detail->idmenu,
                        'price'    => $detail->harga,
                        'quantity' => $detail->jumlah,
                        'name'     => $detail->menu->nama_menu,
                    ];
                })->toArray(),
                // Batasi hanya VA dan QRIS
                'enabled_payments' => [
                    'bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va', 'gopay', 'qris'
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            $pesanan->snap_token = $snapToken;
            $pesanan->save();
        }

        return view('customer.payment', compact('pesanan'));
    }

    // Webhook callback dari Midtrans (Server-to-server notification)
    public function callback(Request $request)
    {
        $notification = new Notification();

        $transactionStatus = $notification->transaction_status;
        $paymentType       = $notification->payment_type;
        $orderId           = $notification->order_id;
        $fraudStatus       = $notification->fraud_status;

        $pesanan = Pesanan::where('order_id', $orderId)->firstOrFail();

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $pesanan->status_bayar = 0;
            } elseif ($fraudStatus == 'accept') {
                $pesanan->status_bayar  = 1;
                $pesanan->metode_bayar  = $paymentType;
            }
        } elseif ($transactionStatus == 'settlement') {
            $pesanan->status_bayar = 1;
            $pesanan->metode_bayar = $paymentType;
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $pesanan->status_bayar = 0;
        } elseif ($transactionStatus == 'pending') {
            $pesanan->status_bayar = 0;
        }

        $pesanan->save();

        return response()->json(['status' => 'ok']);
    }

    // Halaman finish setelah bayar (redirect dari Midtrans)
    public function finish(Request $request)
    {
        $orderId = $request->order_id;
        $pesanan = Pesanan::where('order_id', $orderId)->firstOrFail();

        // Update status jika settlement/capture
        if (in_array($request->transaction_status, ['settlement', 'capture'])) {
            $pesanan->status_bayar = 1;
            $pesanan->metode_bayar = $request->payment_type ?? null;
            $pesanan->save();
        }

        return redirect()->route('customer.status', $pesanan->idpesanan);
    }
    
}
