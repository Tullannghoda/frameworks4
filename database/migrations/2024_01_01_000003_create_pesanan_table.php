<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('idpesanan');
            $table->string('nama', 255);             // nama guest, contoh: Guest_0000001
            $table->timestamp('timestamp')->useCurrent();
            $table->integer('total');
            $table->string('metode_bayar', 50)->nullable(); // VA atau QRIS
            $table->smallInteger('status_bayar')->default(0); // 0=belum, 1=lunas
            // Kolom tambahan untuk Midtrans
            $table->string('order_id', 255)->unique()->nullable();
            $table->string('snap_token', 500)->nullable();
            $table->unsignedBigInteger('idvendor')->nullable();
            $table->timestamps();

            $table->foreign('idvendor')->references('idvendor')->on('vendor')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
