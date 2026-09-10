<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Transaksi: Stock Opname.
 *
 * Layer 30 — bergantung pada tbl_barang dan tbl_user (petugas).
 * Selisih = stok_fisik - stok_sistem (accessor di model Opname),
 * tidak disimpan agar tidak redundan (green code).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_opname', function (Blueprint $table) {
            $table->increments('id_opname');
            $table->string('id_barang', 5);
            $table->integer('stok_sistem');
            $table->integer('stok_fisik');
            $table->unsignedInteger('id_petugas')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status', 50)->nullable();
            $table->timestamp('tanggal_opname')->useCurrent();
            $table->timestamps();

            $table->foreign('id_barang')
                ->references('id_barang')->on('tbl_barang')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreign('id_petugas')
                ->references('id_user')->on('tbl_user')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_opname');
    }
};
