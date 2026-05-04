<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Color\Color;

class BarcodeController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // Helper: Generate Barcode base64 PNG
    // ─────────────────────────────────────────────────────────────────────────

    public static function generateBarcodeBase64(string $text): string
    {
        $generator = new BarcodeGeneratorPNG();
        $png = $generator->getBarcode($text, BarcodeGeneratorPNG::TYPE_CODE_128, 2, 50);
        return 'data:image/png;base64,' . base64_encode($png);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper: Generate QR Code base64 PNG
    // ─────────────────────────────────────────────────────────────────────────

    public static function generateQrBase64(string $text, int $size = 200): string
    {
        $qrCode = new QrCode(
            data: $text,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: $size,
            margin: 10,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255),
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return 'data:image/png;base64,' . base64_encode($result->getString());
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Studi Kasus 1: PDF Tag Harga + Barcode
    // Route: GET /vendor/menu/{menu}/pdf-tag-harga
    // Kolom: idmenu, nama_menu, harga
    // ─────────────────────────────────────────────────────────────────────────

    public function pdfTagHarga(Menu $menu)
    {
        // Barcode berisi idmenu sebagai string
        $barcodeBase64 = self::generateBarcodeBase64((string) $menu->idmenu);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.tag_harga', [
            'menu'          => $menu,
            'barcodeBase64' => $barcodeBase64,
        ]);

        $pdf->setPaper([0, 0, 226.77, 141.73]); // 8cm x 5cm
        return $pdf->stream("tag_harga_{$menu->idmenu}.pdf");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Studi Kasus 2: Invoice PDF + QR Code
    // Route: GET /vendor/pesanan/{pesanan}/invoice
    // Kolom: idpesanan, nama, total, order_id, metode_bayar, status_bayar,
    //        timestamp, detailPesanans → jumlah, harga, subtotal, catatan
    //        detail->menu → nama_menu
    // ─────────────────────────────────────────────────────────────────────────

    public function invoiceQr(Pesanan $pesanan)
    {
        // Load relasi agar tidak N+1
        $pesanan->load('detailPesanans.menu');

        // QR berisi idpesanan
        $qrBase64 = self::generateQrBase64((string) $pesanan->idpesanan, 250);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', [
            'pesanan'  => $pesanan,
            'qrBase64' => $qrBase64,
        ]);

        $pdf->setPaper('a4');
        return $pdf->stream("invoice_{$pesanan->idpesanan}.pdf");
    }
}
