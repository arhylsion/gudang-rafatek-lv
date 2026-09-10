# Gudang RAFATEK — Laravel 11 (Migrasi 1:1 dari PHP Native)

Aplikasi Persediaan Barang Gudang Material, dimigrasi 1:1 dari PHP Native (MySQLi, `main.php?module=xxx`) ke **Laravel 11**, dengan **frontend & desain lama tetap dipakai** (Atlantis Bootstrap 4 di `public/assets`).

Kode native dibackup di `_legacy/` (aman, tidak dihapus).

## Fitur (semua terintegrasi 1:1)

- Auth: login username **atau** email + password (bcrypt cost 12), logout, proteksi `auth`
- Hak akses: `Administrator`, `Admin Gudang`, `Kepala Gudang`, `Teknisi` (+ `User`) via middleware `role` / `role_not`
- Dashboard: 6 kartu statistik, grafik 12 bulan (Chart.js), grafik per jenis, tabel stok tersedia
- Barang: CRUD + detail, ID `B0001` auto, upload foto `public/images` (jpg/jpeg/png ≤3MB), logic modem → `tbl_serial_inventory`, cegah hapus jika dipakai di barang masuk, AJAX keranjang
- Barang Masuk: list + form (`TM-0000001` auto, datepicker, AJAX barang/lokasi), stok `+=jumlah` (pengganti trigger DB), hapus kurangi stok, validasi tanggal
- Barang Keluar: list + form Keluar/Pinjam (`TK-0000001` auto), transaksi + cek stok + reservasi serial modem, email notifikasi, approve/reject (stok -/+ , serial Used/Available, email status), pengembalian (foto `public/images/pengembalian`, Baik tambah stok), hapus, cetak PDF (Dompdf)
- Stock Opname: form + riwayat + filter tanggal, update stok jika selisih, hapus, export PDF/Excel, AJAX stok
- Master: Jenis, Satuan (CRUD + export Excel, cegah hapus jika dipakai), Rak (kode unik, cegah hapus jika ada keranjang), Keranjang (FK rak nullable), Lokasi Rak (hub: 3 kartu + tabel)
- User (khusus Administrator): CRUD + validasi unik
- Profil: edit nama/username/email/foto (`public/assets/img/profiles`) + password opsional, admin bisa edit user lain via `?id=`
- Password: ubah dengan verifikasi lama
- Laporan Stok / Masuk / Keluar: filter (stok minimum, tanggal, status/jenis), cetak PDF, export Excel (HTML table)
- Email: SMTP Gmail (dari `config/email.php` lama → `.env` `MAIL_*`), Mailable + `EmailNotificationService`, CC admin
- Helper: `tanggal_indo()`, `hari_indo()` di `app/Helpers/tanggal_indo.php` (autoload composer)

## Struktur

- `app/Models/`: `User (tbl_user)`, `Jenis`, `Satuan`, `Rak`, `Keranjang`, `Barang`, `BarangMasuk`, `BarangKeluar`, `SerialInventory`, `Opname` — semua pakai nama tabel `tbl_*` asli agar DB lama kompatibel
- `database/migrations/`: modular per tabel, 1 file = 1 tabel (urutan via prefix layer: `10xxxx` master independen, `20xxxx` master dependen, `30xxxx` transaksi, `90xxxx` support framework; tiap file punya docblock dependensi + `down()` simetris)
- `app/Http/Controllers/`: `Auth`, `Dashboard`, `Barang`, `BarangMasuk`, `BarangKeluar`, `Opname`, `Rak`, `Keranjang`, `Lokasi`, `Jenis`, `Satuan`, `User`, `Profil`, `Password`, `Laporan`
- `resources/views/`: `layouts/app.blade.php` (port `main.php`), `auth/login.blade.php` (port `login.php`), `partials/sidebar.blade.php` (port `sidebar_menu.php`), + semua modul, `emails/`, `laporan/pdf`, `laporan/export`
- `public/assets/`, `public/images/`: disalin dari native agar desain 1:1
- `routes/web.php`: 86 routes, `/` → `/login`, `/dashboard`, grup `role_not:Kepala Gudang`, `role:Administrator`, `role:Administrator,Admin Gudang`

## Instalasi (Laragon, PHP 8.2+, MySQL)

```bash
cd C:\laragon\www\gudangrafatek
composer install
cp .env.example .env   # sudah terisi default Laragon
php artisan key:generate
# Buat DB `gudang` (kosong, root tanpa password di Laragon) atau sesuaikan .env:
# DB_CONNECTION=mysql, DB_HOST=127.0.0.1, DB_PORT=3306, DB_DATABASE=gudang, DB_USERNAME=root, DB_PASSWORD=
php artisan migrate --force
php artisan db:seed --force
php artisan serve
# buka http://127.0.0.1:8000/login
```

Mail (opsional, default SMTP Gmail dari config lama):
`MAIL_MAILER=smtp, MAIL_HOST=smtp.gmail.com, MAIL_PORT=587, MAIL_USERNAME=gudang.rafatek@gmail.com, MAIL_PASSWORD=cnlgjhfevkvzudgb, MAIL_ENCRYPTION=tls`

## Akun default (seeder)

| Username | Password | Hak akses | Email |
|---|---|---|---|
| administrator | administrator | Administrator | administrator@gudang.com |
| admin gudang | admin123 | Admin Gudang | admin_gudang@gudang.com |
| kepala gudang | kepala123 | Kepala Gudang | kepala_gudang@gudang.com |
| teknisi | teknisi123 | Teknisi | teknisi@gudang.com |

## Verifikasi

```bash
php artisan route:list
php artisan view:cache
php artisan test   # 7 passed: login, auth, dashboard+16 modul, role protection
```

## Catatan migrasi

- Trigger MySQL `stok_masuk`/`hapus_stok_masuk` diganti Eloquent `increment/decrement` dalam `DB::transaction` (hasil sama, lebih aman)
- `libs/barcode.php` (GD) tidak diport — serial ditampilkan sebagai teks monospace (fallback sama seperti native saat barcode gagal)
- `modules/peminjaman/*` yang dirujuk `content.php` tapi file tidak ada di native, tidak diport (dead route)
- `.htaccess` native yang berisi URL absolut usang diganti bawaan Laravel `public/.htaccess`
- Foto barang lama tetap di `public/images/`, foto profil di `public/assets/img/profiles/` agar path lama tetap jalan
