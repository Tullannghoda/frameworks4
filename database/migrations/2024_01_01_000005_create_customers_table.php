<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('telepon')->nullable();
            $table->string('alamat')->nullable();
            // Studi Kasus 3A: simpan foto sebagai BLOB
            $table->longText('foto_blob')->nullable()->comment('Base64 encoded image');
            // Studi Kasus 3B: simpan path file foto
            $table->string('foto_path')->nullable()->comment('Path file di storage');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
