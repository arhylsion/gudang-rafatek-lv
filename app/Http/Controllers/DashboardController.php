<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Jenis;
use App\Models\Satuan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahBarang = Barang::count();
        $jumlahMasuk = BarangMasuk::count();
        $jumlahKeluar = BarangKeluar::count();
        $jumlahJenis = Jenis::count();
        $jumlahSatuan = Satuan::count();
        $jumlahUser = User::count();

        // 12 bulan terakhir
        $months = [];
        $dataMasuk = [];
        $dataKeluar = [];
        $totalMasuk = [];
        $totalKeluar = [];
        $keys = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = Carbon::now()->subMonths($i);
            $key = $d->format('Y-m');
            $keys[] = $key;
            $months[] = $d->translatedFormat('M Y');
            $dataMasuk[] = BarangMasuk::whereRaw("DATE_FORMAT(tanggal,'%Y-%m')=?", [$key])->count();
            $totalMasuk[] = (int) BarangMasuk::whereRaw("DATE_FORMAT(tanggal,'%Y-%m')=?", [$key])->sum('jumlah');
            $dataKeluar[] = BarangKeluar::whereRaw("DATE_FORMAT(tanggal,'%Y-%m')=?", [$key])->count();
            $totalKeluar[] = (int) BarangKeluar::whereRaw("DATE_FORMAT(tanggal,'%Y-%m')=?", [$key])->sum('jumlah');
        }

        $jenisList = Jenis::orderBy('nama_jenis')->get();
        $categoryData = [];
        foreach ($jenisList as $j) {
            $masuk = [];
            $keluar = [];
            foreach ($keys as $key) {
                $masuk[] = (int) BarangMasuk::join('tbl_barang as b', 'b.id_barang', '=', 'tbl_barang_masuk.barang')
                    ->where('b.jenis', $j->id_jenis)
                    ->whereRaw("DATE_FORMAT(tbl_barang_masuk.tanggal,'%Y-%m')=?", [$key])->sum('tbl_barang_masuk.jumlah');
                $keluar[] = (int) BarangKeluar::join('tbl_barang as b', 'b.id_barang', '=', 'tbl_barang_keluar.barang')
                    ->where('b.jenis', $j->id_jenis)
                    ->whereRaw("DATE_FORMAT(tbl_barang_keluar.tanggal,'%Y-%m')=?", [$key])->sum('tbl_barang_keluar.jumlah');
            }
            $categoryData[$j->nama_jenis] = ['masuk' => $masuk, 'keluar' => $keluar];
        }

        $stokTersedia = Barang::with(['jenisRel', 'satuanRel'])
            ->whereColumn('stok', '>', 'stok_minimum')
            ->orderByDesc('stok')->orderBy('id_barang')->get();

        return view('dashboard.index', compact(
            'jumlahBarang', 'jumlahMasuk', 'jumlahKeluar', 'jumlahJenis', 'jumlahSatuan', 'jumlahUser',
            'months', 'dataMasuk', 'dataKeluar', 'totalMasuk', 'totalKeluar',
            'jenisList', 'categoryData', 'stokTersedia'
        ));
    }
}
