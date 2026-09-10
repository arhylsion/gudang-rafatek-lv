<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Opname;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OpnameController extends Controller
{
    /**
     * Port 1:1 _legacy/modules/stock-opname/tampil_data.php + riwayat_opname.php
     * Filter tanggal_awal/akhir + list join opname+barang+petugas order desc
     * + list barang untuk form (dalam satu halaman).
     */
    public function index(Request $request)
    {
        $fAwal = $request->query('tanggal_awal', $request->input('tanggal_awal'));
        $fAkhir = $request->query('tanggal_akhir', $request->input('tanggal_akhir'));

        $query = Opname::with(['barang', 'petugas'])->orderByDesc('tanggal_opname');

        if ($fAwal && $fAkhir) {
            $query->whereBetween(DB::raw('DATE(tanggal_opname)'), [
                $this->parseTanggal($fAwal),
                $this->parseTanggal($fAkhir),
            ]);
        }

        $riwayat = $query->get();
        $barangList = Barang::orderBy('nama_barang')->get(['id_barang', 'nama_barang']);

        return view('opname.index', compact('riwayat', 'barangList', 'fAwal', 'fAkhir'));
    }

    /**
     * Port 1:1 _legacy/modules/stock-opname/proses_opname.php
     * Insert tbl_opname + bila berbeda update tbl_barang.stok.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_barang' => 'required|string|exists:tbl_barang,id_barang',
            'stok_fisik' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'id_barang.required' => 'Barang tidak boleh kosong.',
            'stok_fisik.required' => 'Stok fisik tidak boleh kosong.',
        ]);

        $barang = Barang::where('id_barang', $validated['id_barang'])->firstOrFail();
        $stokSistem = (int) $barang->stok;
        $stokFisik = (int) $validated['stok_fisik'];

        DB::transaction(function () use ($validated, $stokSistem, $stokFisik) {
            Opname::create([
                'id_barang' => $validated['id_barang'],
                'stok_sistem' => $stokSistem,
                'stok_fisik' => $stokFisik,
                'id_petugas' => Auth::id(),
                'keterangan' => isset($validated['keterangan']) ? trim($validated['keterangan']) : null,
            ]);

            if ($stokFisik !== $stokSistem) {
                Barang::where('id_barang', $validated['id_barang'])->update(['stok' => $stokFisik]);
            }
        });

        return redirect()->route('opname.index')->with('success', 'Stock opname berhasil disimpan.');
    }

    /**
     * Port 1:1 _legacy/modules/stock-opname/hapus_opname.php
     */
    public function destroy(string $id)
    {
        $opname = Opname::where('id_opname', $id)->first();

        if (! $opname) {
            return redirect()->route('opname.index')->with('error', 'Gagal menghapus data atau data tidak ditemukan.');
        }

        $opname->delete();

        return redirect()->route('opname.index')->with('success', 'Data stock opname berhasil dihapus.');
    }

    /**
     * Port 1:1 _legacy/modules/stock-opname/get_stok_barang.php
     * AJAX GET ?id_barang => JSON stok.
     */
    public function getStok(Request $request)
    {
        $id = $request->query('id_barang', $request->input('id_barang'));

        $stok = Barang::where('id_barang', $id)->value('stok');

        return response()->json(['stok' => $stok !== null ? (int) $stok : 0]);
    }

    /**
     * Port 1:1 _legacy/modules/stock-opname/export_opname.php (Dompdf PDF)
     */
    public function exportPdf(Request $request)
    {
        $fAwalRaw = $request->query('tanggal_awal', $request->input('tanggal_awal'));
        $fAkhirRaw = $request->query('tanggal_akhir', $request->input('tanggal_akhir'));

        $rows = $this->exportQuery($fAwalRaw, $fAkhirRaw)->get();

        foreach ($rows as $row) {
            $foto = $row->barang->foto ?? null;
            $row->foto_base64 = $this->embedFotoBase64(
                $foto ? public_path('images/' . $foto) : null
            );
        }

        $pdf = Pdf::loadView('opname.pdf', [
            'rows' => $rows,
            'fAwal' => $fAwalRaw,
            'fAkhir' => $fAkhirRaw,
        ]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan_Stock_Opname_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Port 1:1 _legacy/modules/stock-opname/export_csv.php (Excel .xls via HTML table)
     */
    public function exportCsv(Request $request)
    {
        $fAwalRaw = $request->query('tanggal_awal', $request->input('tanggal_awal'));
        $fAkhirRaw = $request->query('tanggal_akhir', $request->input('tanggal_akhir'));

        $rows = $this->exportQuery($fAwalRaw, $fAkhirRaw)->get();

        $suffix = 'Seluruh_Periode';
        if ($fAwalRaw && $fAkhirRaw) {
            $suffix = $this->parseTanggal($fAwalRaw) . '_s.d._' . $this->parseTanggal($fAkhirRaw);
        } elseif ($fAwalRaw) {
            $suffix = 'Mulai_' . $this->parseTanggal($fAwalRaw);
        } elseif ($fAkhirRaw) {
            $suffix = 'Sampai_' . $this->parseTanggal($fAkhirRaw);
        }

        return response()->view('opname.export', [
            'rows' => $rows,
            'fAwal' => $fAwalRaw,
            'fAkhir' => $fAkhirRaw,
        ], 200, [
            'Content-Type' => 'application/vnd-ms-excel',
            'Content-Disposition' => 'attachment; filename="Laporan_Stock_Opname_' . str_replace(' ', '_', $suffix) . '.xls"',
        ]);
    }

    protected function exportQuery(?string $fAwalRaw, ?string $fAkhirRaw)
    {
        $query = Opname::with(['barang', 'petugas'])->orderByDesc('tanggal_opname');

        if ($fAwalRaw && $fAkhirRaw) {
            $query->whereBetween(DB::raw('DATE(tanggal_opname)'), [
                $this->parseTanggal($fAwalRaw),
                $this->parseTanggal($fAkhirRaw),
            ]);
        } elseif ($fAwalRaw) {
            $query->where(DB::raw('DATE(tanggal_opname)'), '>=', $this->parseTanggal($fAwalRaw));
        } elseif ($fAkhirRaw) {
            $query->where(DB::raw('DATE(tanggal_opname)'), '<=', $this->parseTanggal($fAkhirRaw));
        }

        return $query;
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
