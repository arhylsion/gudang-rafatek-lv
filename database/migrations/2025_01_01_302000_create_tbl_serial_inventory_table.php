<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Transaksi pendukung: Serial Inventory (khusus modem).
 *
 * Layer 30 — bergantung pada tbl_barang (cascade delete:
 * hapus barang ikut hapus serialnya).
 * Index pada status untuk query stok Available yang sering.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_serial_inventory', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_barang', 5);
            $table->string('serial_number', 100)->unique();
            $table->enum('status', ['Available', 'Reserved', 'Used', 'Returned'])
                ->default('Available');
            $table->string('reserved_for_transaction', 10)->nullable();
            $table->string('used_in_transaction', 10)->nullable();
            $table->timestamps();

            $table->foreign('id_barang')
                ->references('id_barang')->on('tbl_barang')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_serial_inventory');
    }
};
