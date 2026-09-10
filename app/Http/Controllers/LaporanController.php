<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    // ------------------------------------------------------------------
    // LAPORAN STOK (port _legacy/modules/laporan-stok/*)
    // Filter ?stok=Seluruh|Minimum, join barang+jenis+satuan.
    // ------------------------------------------------------------------
    public function stok(Request $request)
    {
        $fStok = $request->query('stok', $request->input('stok'));

        $rows = collect();
        if ($fStok) {
            $query = Barang::with(['jenisRel', 'satuanRel'])->orderBy('id_barang');
            if ($fStok === 'Minimum') {
                $query->whereColumn('stok', '<=', 'stok_minimum');
            }
            $rows = $query->get();
        }

        return view('laporan.stok', compact('rows', 'fStok'));
    }

    public function stokCetak(Request $request)
    {
        $fStok = $request->query('stok', $request->input('stok', 'Seluruh'));

        $query = Barang::with(['jenisRel', 'satuanRel'])->orderBy('id_barang');
        if ($fStok === 'Minimum') {
            $query->whereColumn('stok', '<=', 'stok_minimum');
        }
        $rows = $query->get();

        foreach ($rows as $row) {
            $row->foto_base64 = $this->embedFotoBase64(
                $row->foto ? public_path('images/' . $row->foto) : null
            );
        }

        $pdf = Pdf::loadView('laporan.pdf.stok', compact('rows', 'fStok'));
        $pdf->setPaper('A4', 'landscape');

        $nama = $fStok === 'Minimum' ? 'Laporan Stok Barang Minimum.pdf' : 'Laporan Stok Barang Seluruh.pdf';

        return $pdf->stream($nama);
    }

    public function stokExport(Request $request)
    {
        $fStok = $request->query('stok', $request->input('stok', 'Seluruh'));

        $query = Barang::with(['jenisRel', 'satuanRel'])->orderBy('id_barang');
        if ($fStok === 'Minimum') {
            $query->whereColumn('stok', '<=', 'stok_minimum');
        }
        $rows = $query->get();

        $nama = $fStok === 'Minimum' ? 'Laporan Stok Barang Minimum.xls' : 'Laporan Stok Seluruh Barang.xls';

        return response()->view('laporan.export.stok', compact('rows', 'fStok'), 200, [
            'Content-Type' => 'application/vnd-ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $nama . '"',
        ]);
    }

    // ------------------------------------------------------------------
    // LAPORAN BARANG MASUK (port _legacy/modules/laporan-barang-masuk/*)
    // Filter tanggal_awal/akhir BETWEEN, join barang+rak+keranjang+satuan.
    // ------------------------------------------------------------------
    public function masuk(Request $request)
    {
        $fAwal = $request->query('tanggal_awal', $request->input('tanggal_awal'));
        $fAkhir = $request->query('tanggal_akhir', $request->input('tanggal_akhir'));

        $rows = collect();
        if ($fAwal && $fAkhir) {
            $tglAwal = $this->parseTanggal($fAwal);
            $tglAkhir = $this->parseTanggal($fAkhir);
            $rows = BarangMasuk::with(['barangRel.satuanRel', 'barangRel.jenisRel', 'rak', 'keranjang'])
                ->whereBetween('tanggal', [$tglAwal, $tglAkhir])
                ->orderBy('id_transaksi')
                ->get();
        }

        return view('laporan.masuk', compact('rows', 'fAwal', 'fAkhir'));
    }

    public function masukCetak(Request $request)
    {
        $tglAwal = $this->parseTanggal($request->query('tanggal_awal', $request->input('tanggal_awal')));
        $tglAkhir = $this->parseTanggal($request->query('tanggal_akhir', $request->input('tanggal_akhir')));

        $rows = BarangMasuk::with(['barangRel.satuanRel', 'barangRel.jenisRel', 'rak', 'keranjang'])
            ->whereBetween('tanggal', [$tglAwal, $tglAkhir])
            ->orderBy('id_transaksi')
            ->get();

        foreach ($rows as $row) {
            $foto = $row->barangRel->foto ?? null;
            $row->foto_base64 = $this->embedFotoBase64(
                $foto ? public_path('images/' . $foto) : null
            );
        }

        $pdf = Pdf::loadView('laporan.pdf.masuk', [
            'rows' => $rows,
            'fAwal' => $request->query('tanggal_awal', $request->input('tanggal_awal')),
            'fAkhir' => $request->query('tanggal_akhir', $request->input('tanggal_akhir')),
        ]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan_Data_Barang_Masuk.pdf');
    }

    public function masukExport(Request $request)
    {
        $fAwalRaw = $request->query('tanggal_awal', $request->input('tanggal_awal'));
        $fAkhirRaw = $request->query('tanggal_akhir', $request->input('tanggal_akhir'));
        $tglAwal = $this->parseTanggal($fAwalRaw);
        $tglAkhir = $this->parseTanggal($fAkhirRaw);

        $rows = BarangMasuk::with(['barangRel.satuanRel', 'barangRel.jenisRel', 'rak', 'keranjang'])
            ->whereBetween('tanggal', [$tglAwal, $tglAkhir])
            ->orderBy('id_transaksi')
            ->get();

        return response()->view('laporan.export.masuk', [
            'rows' => $rows,
            'fAwal' => $fAwalRaw,
            'fAkhir' => $fAkhirRaw,
        ], 200, [
            'Content-Type' => 'application/vnd-ms-excel',
            'Content-Disposition' => 'attachment; filename="Laporan Data Barang Masuk.xls"',
        ]);
    }

    // ------------------------------------------------------------------
    // LAPORAN BARANG KELUAR (port _legacy/modules/laporan-barang-keluar/*)
    // Filter tanggal + status + jenis.
    // ------------------------------------------------------------------
    public function keluar(Request $request)
    {
        $fAwal = $request->query('tanggal_awal', $request->input('tanggal_awal'));
        $fAkhir = $request->query('tanggal_akhir', $request->input('tanggal_akhir'));
        $fStatus = $request->query('status', $request->input('status'));
        $fJenis = $request->query('jenis', $request->input('jenis'));

        $rows = collect();
        if ($fAwal && $fAkhir) {
            $rows = $this->keluarQuery(
                $this->parseTanggal($fAwal),
                $this->parseTanggal($fAkhir),
                $fStatus,
                $fJenis
            )->get();
        }

        return view('laporan.keluar', compact('rows', 'fAwal', 'fAkhir', 'fStatus', 'fJenis'));
    }

    public function keluarCetak(Request $request)
    {
        $fAwalRaw = $request->query('tanggal_awal', $request->input('tanggal_awal'));
        $fAkhirRaw = $request->query('tanggal_akhir', $request->input('tanggal_akhir'));
        $fStatus = $request->query('status', $request->input('status'));
        $fJenis = $request->query('jenis', $request->input('jenis'));

        $rows = $this->keluarQuery(
            $this->parseTanggal($fAwalRaw),
            $this->parseTanggal($fAkhirRaw),
            $fStatus,
            $fJenis
        )->get();

        foreach ($rows as $row) {
            $foto = $row->barangRel->foto ?? null;
            $row->foto_base64 = $this->embedFotoBase64(
                $foto ? public_path('images/' . $foto) : null
            );
        }

        $pdf = Pdf::loadView('laporan.pdf.keluar', [
            'rows' => $rows,
            'fAwal' => $fAwalRaw,
            'fAkhir' => $fAkhirRaw,
        ]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan_Data_Barang_Keluar.pdf');
    }

    public function keluarExport(Request $request)
    {
        $fAwalRaw = $request->query('tanggal_awal', $request->input('tanggal_awal'));
        $fAkhirRaw = $request->query('tanggal_akhir', $request->input('tanggal_akhir'));
        $fStatus = $request->query('status', $request->input('status'));
        $fJenis = $request->query('jenis', $request->input('jenis'));

        $rows = $this->keluarQuery(
            $this->parseTanggal($fAwalRaw),
            $this->parseTanggal($fAkhirRaw),
            $fStatus,
            $fJenis
        )->get();

        return response()->view('laporan.export.keluar', [
            'rows' => $rows,
            'fAwal' => $fAwalRaw,
            'fAkhir' => $fAkhirRaw,
        ], 200, [
            'Content-Type' => 'application/vnd-ms-excel',
            'Content-Disposition' => 'attachment; filename="Laporan Data Barang Keluar.xls"',
        ]);
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------
    protected function keluarQuery(string $tglAwal, string $tglAkhir, ?string $status = null, ?string $jenis = null)
    {
        $query = BarangKeluar::with(['barangRel.satuanRel', 'barangRel.jenisRel', 'rak', 'keranjang'])
            ->whereBetween('tanggal', [$tglAwal, $tglAkhir]);

        if (! empty($status)) {
            $query->where('status', $status);
        }

        if (! empty($jenis)) {
            $query->where('jenis', $jenis);
        }

        return $query->orderBy('id_transaksi');
    }

    protected function parseTanggal(?string $value): string
    {
        if (empty($value)) {
            return date('Y-m-d');
        }

        try {
            return Carbon::createFromFormat('d-m-Y', trim($value))->format('Y-m-d');
        } catch (\Throwable $e) {
            return date('Y-m-d', strtotime($value));
        }
    }

    protected function embedFotoBase64(?string $absolutePath): ?string
    {
        if (! $absolutePath || ! file_exists($absolutePath)) {
            return null;
        }

        $bin = @file_get_contents($absolutePath);

        if ($bin === false) {
            return null;
        }

        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        $mime = $ext === 'jpg' ? 'jpeg' : $ext;

        return 'data:image/' . $mime . ';base64,' . base64_encode($bin);
    }
}
