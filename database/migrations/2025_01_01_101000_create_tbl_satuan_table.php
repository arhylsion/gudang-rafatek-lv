<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Master: Satuan (lookup).
 *
 * Layer 10 — independen, tanpa FK.
 * Dipakai oleh tbl_barang.satuan (logis, tanpa FK agar fleksibel).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_satuan', function (Blueprint $table) {
            $table->increments('id_satuan');
            $table->string('nama_satuan', 30)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_satuan');
    }
};
