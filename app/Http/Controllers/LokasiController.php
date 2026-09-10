<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Rak;

class LokasiController extends Controller
{
    /**
     * Port 1:1 _legacy/modules/lokasi_rak/tampil_data.php
     * Hub overview: 3 kartu ringkasan (COUNT rak, COUNT keranjang,
     * SUM kapasitas) + 5 rak terbaru + seluruh keranjang JOIN rak.
     * Hanya index — tanpa store/update/destroy.
     */
    public function index()
    {
        $totalRak = Rak::count();
        $totalKeranjang = Keranjang::count();
        $totalKapasitas = (int) Rak::sum('kapasitas');

        $recentRak = Rak::orderByDesc('id_rak')->limit(5)->get();
        $keranjangs = Keranjang::with('rak')->orderByDesc('id_keranjang')->get();

        return view('lokasi.index', compact(
            'totalRak',
            'totalKeranjang',
            'totalKapasitas',
            'recentRak',
            'keranjangs'
        ));
    }
}
