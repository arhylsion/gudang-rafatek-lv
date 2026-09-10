<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    /**
     * Port 1:1 _legacy/modules/satuan/tampil_data.php
     */
    public function index()
    {
        $satuan = Satuan::orderByDesc('id_satuan')->get();

        return view('satuan.index', compact('satuan'));
    }

    /**
     * Port 1:1 _legacy/modules/satuan/form_entri.php
     */
    public function create()
    {
        return view('satuan.create');
    }

    /**
     * Port 1:1 _legacy/modules/satuan/proses_entri.php
     * nama_satuan harus unik (legacy pesan=4 bila duplikat).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:30|unique:tbl_satuan,nama_satuan',
        ], [
            'nama_satuan.required' => 'Satuan tidak boleh kosong.',
            'nama_satuan.unique' => 'Satuan ' . $request->input('nama_satuan') . ' sudah ada. Silahkan ganti nama satuan yang Anda masukan.',
        ]);

        Satuan::create(['nama_satuan' => trim($validated['nama_satuan'])]);

        return redirect()->route('satuan.index')->with('success', 'Data satuan berhasil disimpan.');
    }

    /**
     * Port 1:1 _legacy/modules/satuan/form_ubah.php
     */
    public function edit(string $id)
    {
        $satuan = Satuan::where('id_satuan', $id)->firstOrFail();

        return view('satuan.edit', compact('satuan'));
    }

    /**
     * Port 1:1 _legacy/modules/satuan/proses_ubah.php
     */
    public function update(Request $request, string $id)
    {
        $satuan = Satuan::where('id_satuan', $id)->firstOrFail();

        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:30|unique:tbl_satuan,nama_satuan,' . $satuan->id_satuan . ',id_satuan',
        ], [
            'nama_satuan.required' => 'Satuan tidak boleh kosong.',
            'nama_satuan.unique' => 'Satuan ' . $request->input('nama_satuan') . ' sudah ada. Silahkan ganti nama satuan yang Anda masukan.',
        ]);

        $satuan->update(['nama_satuan' => trim($validated['nama_satuan'])]);

        return redirect()->route('satuan.index')->with('success', 'Data satuan berhasil diubah.');
    }

    /**
     * Port 1:1 _legacy/modules/satuan/proses_hapus.php
     * Blokir bila sudah tercatat di tbl_barang (legacy pesan=5).
     */
    public function destroy(string $id)
    {
        $satuan = Satuan::where('id_satuan', $id)->firstOrFail();

        if (Barang::where('satuan', $satuan->id_satuan)->exists()) {
            return redirect()->route('satuan.index')
                ->with('error', 'Data satuan tidak bisa dihapus karena sudah tercatat pada Data Barang.');
        }

        $satuan->delete();

        return redirect()->route('satuan.index')->with('success', 'Data satuan berhasil dihapus.');
    }

    /**
     * Port 1:1 _legacy/modules/satuan/export.php
     * Export Excel via HTML table (header application/vnd-ms-excel).
     */
    public function export()
    {
        $rows = Satuan::orderBy('nama_satuan')->get();

        $html = '<center><h4>DATA SATUAN</h4></center>';
        $html .= '<table border="1">';
        $html .= '<thead><tr style="background-color:#231a98;color:#fff">';
        $html .= '<th height="30" align="center" vertical="center">No.</th>';
        $html .= '<th height="30" align="center" vertical="center">Satuan</th>';
        $html .= '</tr></thead><tbody>';

        $no = 1;
        foreach ($rows as $row) {
            $html .= '<tr>';
            $html .= '<td width="70" align="center">' . $no++ . '</td>';
            $html .= '<td width="500">' . e($row->nama_satuan) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd-ms-excel',
            'Content-Disposition' => 'attachment; filename=Data-Satuan.xls',
        ]);
    }
}
