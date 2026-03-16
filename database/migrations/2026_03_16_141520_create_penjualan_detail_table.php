<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->increments('idpenjualan_detail');
            $table->integer('id_penjualan')->unsigned();
            $table->string('id_barang', 8);
            $table->smallInteger('jumlah');
            $table->integer('subtotal');

            $table->foreign('id_penjualan')
                  ->references('id_penjualan')
                  ->on('penjualan');

            $table->foreign('id_barang')
                  ->references('id_barang')
                  ->on('barang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_detail');
    }
};
