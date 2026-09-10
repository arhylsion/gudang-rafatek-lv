<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Transaksi: Barang Masuk.
 *
 * Layer 30 — bergantung pada tbl_barang, tbl_rak, tbl_keranjang.
 * Stok dihitung di aplikasi (increment dalam DB::transaction),
 * bukan via trigger DB seperti native, agar portable dan testable.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_barang_masuk', function (Blueprint $table) {
            $table->string('id_transaksi', 10)->primary();
            $table->date('tanggal');
            $table->string('barang', 5);
            $table->string('serial_number', 100)->nullable();
            $table->integer('jumlah')->nullable();
            $table->unsignedInteger('id_rak')->nullable();
            $table->unsignedInteger('id_keranjang')->nullable();
            $table->timestamps();

            $table->foreign('barang')
                ->references('id_barang')->on('tbl_barang')
                ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('id_rak')
                ->references('id_rak')->on('tbl_rak')
                ->nullOnDelete()->cascadeOnUpdate();

            $table->foreign('id_keranjang')
                ->references('id_keranjang')->on('tbl_keranjang')
                ->nullOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_barang_masuk');
    }
};
