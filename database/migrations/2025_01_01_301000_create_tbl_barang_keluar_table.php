<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Transaksi: Barang Keluar (Keluar | Pinjam).
 *
 * Layer 30 — bergantung pada tbl_barang, tbl_rak,
 * tbl_keranjang, dan tbl_user (created_by).
 * State machine status ditangani di BarangKeluarController
 * (Menunggu Persetujuan → Disetujui / Ditolak).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_barang_keluar', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_transaksi', 10)->unique();
            $table->date('tanggal');
            $table->date('tanggal_pengembalian')->nullable();
            $table->string('barang', 5);
            $table->string('serial_number', 100)->nullable();
            $table->integer('jumlah')->nullable();
            $table->string('pemohon', 100)->nullable();
            $table->string('jenis', 50)->nullable()->comment('Keluar|Pinjam');
            $table->unsignedInteger('id_rak')->nullable();
            $table->unsignedInteger('id_keranjang')->nullable();
            $table->enum('status', ['Menunggu Persetujuan', 'Disetujui', 'Ditolak'])
                ->default('Menunggu Persetujuan');
            $table->dateTime('tanggal_persetujuan')->nullable();
            $table->dateTime('tanggal_penolakan')->nullable();
            $table->string('kondisi', 50)->nullable();
            $table->text('catatan')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->string('foto_pengembalian', 255)->nullable();
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

            $table->foreign('created_by')
                ->references('id_user')->on('tbl_user')
                ->nullOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_barang_keluar');
    }
};
