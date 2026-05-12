<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toko', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->unique()->comment('Barcode/QR identifier toko');
            $table->string('nama_toko');
            $table->string('alamat')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('accuracy', 8, 2)->default(50)->comment('Akurasi GPS saat input titik toko (meter)');
            $table->timestamps();
        });

        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_id')->constrained('toko')->onDelete('cascade');
            $table->string('nama_sales')->nullable();
            $table->decimal('latitude_sales', 10, 7);
            $table->decimal('longitude_sales', 10, 7);
            $table->decimal('accuracy_sales', 8, 2);
            $table->decimal('jarak_meter', 10, 2)->comment('Jarak aktual pusat ke pusat (meter)');
            $table->decimal('threshold_efektif', 10, 2)->comment('Jarak maks + akurasi toko + akurasi sales');
            $table->boolean('diterima')->default(false)->comment('true jika jarak <= threshold_efektif');
            $table->integer('threshold')->default(300)->comment('Radius maksimal kunjungan (meter)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan');
        Schema::dropIfExists('toko');
    }
};
