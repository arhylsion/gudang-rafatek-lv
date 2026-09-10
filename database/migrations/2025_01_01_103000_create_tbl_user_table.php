<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Master: User + Auth.
 *
 * Layer 10 — independen, tanpa FK.
 * Model Auth (config/auth.php) memakai tabel ini.
 * Direferensikan oleh tbl_barang_keluar.created_by
 * dan tbl_opname.id_petugas (SET NULL saat user dihapus).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_user', function (Blueprint $table) {
            $table->increments('id_user');
            $table->string('nama_user', 30);
            $table->string('username', 30)->unique();
            $table->string('email', 100)->unique()->default('');
            $table->string('password', 255);
            $table->string('foto_profil', 255)->nullable();
            $table->enum('hak_akses', ['Administrator', 'Admin Gudang', 'Kepala Gudang', 'User', 'Teknisi']);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_user');
    }
};
