<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Master: Jenis Barang (lookup).
 *
 * Layer 10 — independen, tanpa FK.
 * Dipakai oleh tbl_barang.jenis (logis, tanpa FK agar fleksibel).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_jenis', function (Blueprint $table) {
            $table->increments('id_jenis');
            $table->string('nama_jenis', 50)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_jenis');
    }
};
