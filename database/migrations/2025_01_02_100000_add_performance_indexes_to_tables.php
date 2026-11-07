<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optimasi Performa Skala Tinggi (High Concurrency & Big Data Scaling).
 * Menambahkan B-Tree indexes pada kolom filter, sorting, dan rentang tanggal
 * agar waktu respon query tetap <= 10ms pada ratusan ribu hingga jutaan data.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_barang_keluar', function (Blueprint $table) {
            $table->index('status', 'idx_barang_keluar_status');
            $table->index('tanggal', 'idx_barang_keluar_tanggal');
            $table->index(['status', 'jenis'], 'idx_barang_keluar_status_jenis');
            $table->index(['tanggal', 'barang'], 'idx_barang_keluar_tanggal_barang');
        });

        Schema::table('tbl_barang_masuk', function (Blueprint $table) {
            $table->index('tanggal', 'idx_barang_masuk_tanggal');
            $table->index(['tanggal', 'barang'], 'idx_barang_masuk_tanggal_barang');
        });

        Schema::table('tbl_barang', function (Blueprint $table) {
            $table->index('jenis', 'idx_barang_jenis');
            $table->index('satuan', 'idx_barang_satuan');
            $table->index('stok', 'idx_barang_stok');
        });

        Schema::table('tbl_opname', function (Blueprint $table) {
            $table->index('tanggal_opname', 'idx_opname_tanggal');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_barang_keluar', function (Blueprint $table) {
            $table->dropIndex('idx_barang_keluar_status');
            $table->dropIndex('idx_barang_keluar_tanggal');
            $table->dropIndex('idx_barang_keluar_status_jenis');
            $table->dropIndex('idx_barang_keluar_tanggal_barang');
        });

        Schema::table('tbl_barang_masuk', function (Blueprint $table) {
            $table->dropIndex('idx_barang_masuk_tanggal');
            $table->dropIndex('idx_barang_masuk_tanggal_barang');
        });

        Schema::table('tbl_barang', function (Blueprint $table) {
            $table->dropIndex('idx_barang_jenis');
            $table->dropIndex('idx_barang_satuan');
            $table->dropIndex('idx_barang_stok');
        });

        Schema::table('tbl_opname', function (Blueprint $table) {
            $table->dropIndex('idx_opname_tanggal');
        });
    }
};

