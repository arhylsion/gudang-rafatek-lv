<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Jenis;
use Illuminate\Http\Request;

class JenisController extends Controller
{
    /**
     * Port 1:1 _legacy/modules/jenis/tampil_data.php
     */
    public function index()
    {
        $jenis = Jenis::orderByDesc('id_jenis')->get();

        return view('jenis.index', compact('jenis'));
    }

    /**
     * Port 1:1 _legacy/modules/jenis/form_entri.php
     */
    public function create()
    {
        return view('jenis.create');
    }

    /**
     * Port 1:1 _legacy/modules/jenis/proses_entri.php
     * nama_jenis harus unik (legacy pesan=4 bila duplikat).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jenis' => 'required|string|max:50|unique:tbl_jenis,nama_jenis',
        ], [
            'nama_jenis.required' => 'Jenis barang tidak boleh kosong.',
            'nama_jenis.unique' => 'Jenis barang ' . $request->input('nama_jenis') . ' sudah ada. Silahkan ganti nama jenis barang yang Anda masukan.',
        ]);

        Jenis::create(['nama_jenis' => trim($validated['nama_jenis'])]);

        return redirect()->route('jenis.index')->with('success', 'Data jenis barang berhasil disimpan.');
    }

    /**
     * Port 1:1 _legacy/modules/jenis/form_ubah.php
     */
    public function edit(string $id)
    {
        $jenis = Jenis::where('id_jenis', $id)->firstOrFail();

        return view('jenis.edit', compact('jenis'));
    }

    /**
     * Port 1:1 _legacy/modules/jenis/proses_ubah.php
     */
    public function update(Request $request, string $id)
    {
        $jenis = Jenis::where('id_jenis', $id)->firstOrFail();

        $validated = $request->validate([
            'nama_jenis' => 'required|string|max:50|unique:tbl_jenis,nama_jenis,' . $jenis->id_jenis . ',id_jenis',
        ], [
            'nama_jenis.required' => 'Jenis barang tidak boleh kosong.',
            'nama_jenis.unique' => 'Jenis barang ' . $request->input('nama_jenis') . ' sudah ada. Silahkan ganti nama jenis barang yang Anda masukan.',
        ]);

        $jenis->update(['nama_jenis' => trim($validated['nama_jenis'])]);

        return redirect()->route('jenis.index')->with('success', 'Data jenis barang berhasil diubah.');
    }

    /**
     * Port 1:1 _legacy/modules/jenis/proses_hapus.php
     * Blokir bila sudah tercatat di tbl_barang (legacy pesan=5).
     */
    public function destroy(string $id)
    {
        $jenis = Jenis::where('id_jenis', $id)->firstOrFail();

        if (Barang::where('jenis', $jenis->id_jenis)->exists()) {
            return redirect()->route('jenis.index')
                ->with('error', 'Data jenis barang tidak bisa dihapus karena sudah tercatat pada Data Barang.');
        }

        $jenis->delete();

        return redirect()->route('jenis.index')->with('success', 'Data jenis barang berhasil dihapus.');
    }

    /**
     * Port 1:1 _legacy/modules/jenis/export.php
     * Export Excel via HTML table (header application/vnd-ms-excel).
     */
    public function export()
    {
        $rows = Jenis::orderBy('nama_jenis')->get();

        $html = '<center><h4>DATA JENIS BARANG</h4></center>';
        $html .= '<table border="1">';
        $html .= '<thead><tr style="background-color:#231a98;color:#fff">';
        $html .= '<th height="30" align="center" vertical="center">No.</th>';
        $html .= '<th height="30" align="center" vertical="center">Jenis Barang</th>';
        $html .= '</tr></thead><tbody>';

        $no = 1;
        foreach ($rows as $row) {
            $html .= '<tr>';
            $html .= '<td width="70" align="center">' . $no++ . '</td>';
            $html .= '<td width="500">' . e($row->nama_jenis) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd-ms-excel',
            'Content-Disposition' => 'attachment; filename=Data-Jenis-Barang.xls',
        ]);
    }
}
