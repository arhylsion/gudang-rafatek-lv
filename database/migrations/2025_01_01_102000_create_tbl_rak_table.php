<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Master: Rak penyimpanan.
 *
 * Layer 10 — independen, tanpa FK.
 * Direferensikan oleh tbl_keranjang, tbl_barang,
 * tbl_barang_masuk, dan tbl_barang_keluar (SET NULL saat dihapus).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_rak', function (Blueprint $table) {
            $table->increments('id_rak');
            $table->string('kode_rak', 10)->unique();
            $table->string('nama_rak', 100);
            $table->string('lokasi', 200)->nullable();
            $table->integer('kapasitas')->default(0);
            $table->text('keterangan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_rak');
    }
};
