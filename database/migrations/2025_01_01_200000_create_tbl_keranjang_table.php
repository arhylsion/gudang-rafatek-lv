<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Master dependen: Keranjang.
 *
 * Layer 20 — bergantung pada tbl_rak.
 * Relasi opsional: keranjang boleh tanpa rak (nullable).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_keranjang', function (Blueprint $table) {
            $table->increments('id_keranjang');
            $table->string('kode_keranjang', 10)->unique();
            $table->string('nama_keranjang', 100);
            $table->unsignedInteger('id_rak')->nullable();
            $table->integer('kapasitas')->default(0);
            $table->string('kondisi', 50)->default('Baik');
            $table->text('keterangan')->nullable();

            $table->foreign('id_rak')
                ->references('id_rak')->on('tbl_rak')
                ->nullOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_keranjang');
    }
};
