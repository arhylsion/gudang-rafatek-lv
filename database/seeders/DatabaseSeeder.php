<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Jenis - 1:1 dari native
        DB::table('tbl_jenis')->upsert([
            ['id_jenis' => 1, 'nama_jenis' => 'Minuman'],
            ['id_jenis' => 3, 'nama_jenis' => 'Elektronik'],
            ['id_jenis' => 5, 'nama_jenis' => 'MODEM'],
        ], ['id_jenis']);

        DB::table('tbl_satuan')->upsert([
            ['id_satuan' => 1, 'nama_satuan' => 'pcs'],
            ['id_satuan' => 2, 'nama_satuan' => 'box'],
            ['id_satuan' => 3, 'nama_satuan' => 'Meter'],
            ['id_satuan' => 4, 'nama_satuan' => 'Kg'],
            ['id_satuan' => 5, 'nama_satuan' => 'Cm'],
        ], ['id_satuan']);

        DB::table('tbl_rak')->upsert([
            ['id_rak' => 2, 'kode_rak' => 'R002', 'nama_rak' => 'Rak Utama 2', 'lokasi' => 'Lantai 1', 'kapasitas' => 50, 'keterangan' => ''],
            ['id_rak' => 3, 'kode_rak' => 'R003', 'nama_rak' => 'Rak Cadangan', 'lokasi' => 'Lantai 2', 'kapasitas' => 30, 'keterangan' => ''],
            ['id_rak' => 4, 'kode_rak' => 'RO01', 'nama_rak' => 'Rak Utama 1', 'lokasi' => 'Lantai 1', 'kapasitas' => 100, 'keterangan' => 'test'],
        ], ['id_rak']);

        DB::table('tbl_keranjang')->upsert([
            ['id_keranjang' => 3, 'kode_keranjang' => 'K003', 'nama_keranjang' => 'Keranjang B1', 'id_rak' => 2, 'kapasitas' => 15, 'kondisi' => 'Baik', 'keterangan' => 'Keranjang untuk kabel'],
            ['id_keranjang' => 4, 'kode_keranjang' => 'K004', 'nama_keranjang' => 'Keranjang B2', 'id_rak' => 2, 'kapasitas' => 15, 'kondisi' => 'Baik', 'keterangan' => 'keranjang buat barang'],
        ], ['id_keranjang']);

        // Users - password asli: administrator, admin gudang sesuai username? Native seed memakai bcrypt $2y$12$... untuk password sesuai peran.
        // Kita reset ke password yang jelas 1:1: administrator / admin123 / kepala123 / teknisi123 agar bisa login.
        // Hash bawaan native tetap valid karena Laravel pakai bcrypt juga, tapi kita pakai password yang mudah diingat:
        $users = [
            ['id_user' => 1, 'nama_user' => 'Admin', 'username' => 'administrator', 'email' => 'administrator@gudang.com', 'password' => Hash::make('administrator'), 'hak_akses' => 'Administrator'],
            ['id_user' => 2, 'nama_user' => 'Admin Gudang', 'username' => 'admin gudang', 'email' => 'admin_gudang@gudang.com', 'password' => Hash::make('admin123'), 'hak_akses' => 'Admin Gudang'],
            ['id_user' => 3, 'nama_user' => 'Kepala Gudang', 'username' => 'kepala gudang', 'email' => 'kepala_gudang@gudang.com', 'password' => Hash::make('kepala123'), 'hak_akses' => 'Kepala Gudang'],
            ['id_user' => 4, 'nama_user' => 'teknisi', 'username' => 'teknisi', 'email' => 'teknisi@gudang.com', 'password' => Hash::make('teknisi123'), 'hak_akses' => 'Teknisi'],
        ];
        foreach ($users as $u) {
            DB::table('tbl_user')->updateOrInsert(['id_user' => $u['id_user']], array_merge($u, [
                'created_at' => now(), 'updated_at' => now(),
            ]));
        }
    }
}
