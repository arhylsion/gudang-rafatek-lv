<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Master dependen: Barang.
 *
 * Layer 20 — bergantung pada tbl_rak dan tbl_keranjang.
 *
 * Catatan tipe FK: PK rak/keranjang adalah increments (unsigned INT),
 * jadi kolom FK memakai unsignedInteger (bukan foreignId/bigint)
 * agar tipe cocok dan FK valid di MySQL.
 *
 * Kolom jenis/satuan sengaja tanpa FK (sesuai native):
 * relasi logis ke tbl_jenis/tbl_satuan, dijaga di level aplikasi
 * agar lookup bisa diubah tanpa migrasi baru.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_barang', function (Blueprint $table) {
            $table->string('id_barang', 5)->primary();
            $table->string('nama_barang', 100);
            $table->string('serial_number', 100)->nullable();
            $table->unsignedInteger('jenis');
            $table->integer('stok_minimum');
            $table->integer('stok')->default(0);
            $table->unsignedInteger('satuan');
            $table->unsignedInteger('id_rak')->nullable();
            $table->unsignedInteger('id_keranjang')->nullable();
            $table->string('foto', 100)->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('tbl_barang');
    }
};
