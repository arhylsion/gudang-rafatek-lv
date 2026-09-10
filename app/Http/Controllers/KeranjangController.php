<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Rak;
use Illuminate\Http\Request;

class KeranjangController extends Controller
{
    /**
     * Port 1:1 _legacy/modules/keranjang/tampil_data.php
     * Index join keranjang + rak (LEFT JOIN tbl_rak).
     */
    public function index()
    {
        $keranjang = Keranjang::with('rak')->orderByDesc('id_keranjang')->get();

        return view('keranjang.index', compact('keranjang'));
    }

    /**
     * Port 1:1 _legacy/modules/keranjang/form_entri.php
     */
    public function create()
    {
        $rakList = Rak::orderBy('nama_rak')->get();

        return view('keranjang.create', compact('rakList'));
    }

    /**
     * Port 1:1 _legacy/modules/keranjang/proses_entri.php
     * kode_keranjang harus unik (legacy pesan=4 bila duplikat).
     * id_rak nullable; kondisi default "Baik".
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_keranjang' => 'required|string|max:10|unique:tbl_keranjang,kode_keranjang',
            'nama_keranjang' => 'required|string|max:100',
            'id_rak' => 'nullable|integer|exists:tbl_rak,id_rak',
            'kapasitas' => 'nullable|integer|min:0',
            'kondisi' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
        ], [
            'kode_keranjang.required' => 'Kode keranjang tidak boleh kosong.',
            'kode_keranjang.unique' => 'Kode keranjang ' . $request->input('kode_keranjang') . ' sudah ada. Silahkan ganti kode keranjang yang Anda masukan.',
            'nama_keranjang.required' => 'Nama keranjang tidak boleh kosong.',
        ]);

        Keranjang::create([
            'kode_keranjang' => trim($validated['kode_keranjang']),
            'nama_keranjang' => trim($validated['nama_keranjang']),
            'id_rak' => $validated['id_rak'] ?? null,
            'kapasitas' => (int) ($validated['kapasitas'] ?? 0),
            'kondisi' => isset($validated['kondisi']) && trim($validated['kondisi']) !== ''
                ? trim($validated['kondisi'])
                : 'Baik',
            'keterangan' => isset($validated['keterangan']) ? trim($validated['keterangan']) : null,
        ]);

        return redirect()->route('keranjang.index')->with('success', 'Data keranjang berhasil disimpan.');
    }

    /**
     * Port 1:1 _legacy/modules/keranjang/form_ubah.php
     */
    public function edit(string $id)
    {
        $keranjang = Keranjang::where('id_keranjang', $id)->firstOrFail();
        $rakList = Rak::orderBy('nama_rak')->get();

        return view('keranjang.edit', compact('keranjang', 'rakList'));
    }

    /**
     * Port 1:1 _legacy/modules/keranjang/proses_ubah.php
     */
    public function update(Request $request, string $id)
    {
        $keranjang = Keranjang::where('id_keranjang', $id)->firstOrFail();

        $validated = $request->validate([
            'kode_keranjang' => 'required|string|max:10|unique:tbl_keranjang,kode_keranjang,' . $keranjang->id_keranjang . ',id_keranjang',
            'nama_keranjang' => 'required|string|max:100',
            'id_rak' => 'nullable|integer|exists:tbl_rak,id_rak',
            'kapasitas' => 'nullable|integer|min:0',
            'kondisi' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
        ], [
            'kode_keranjang.required' => 'Kode keranjang tidak boleh kosong.',
            'kode_keranjang.unique' => 'Kode keranjang ' . $request->input('kode_keranjang') . ' sudah ada. Silahkan ganti kode keranjang yang Anda masukan.',
            'nama_keranjang.required' => 'Nama keranjang tidak boleh kosong.',
        ]);

        $keranjang->update([
            'kode_keranjang' => trim($validated['kode_keranjang']),
            'nama_keranjang' => trim($validated['nama_keranjang']),
            'id_rak' => $validated['id_rak'] ?? null,
            'kapasitas' => (int) ($validated['kapasitas'] ?? 0),
            'kondisi' => isset($validated['kondisi']) && trim($validated['kondisi']) !== ''
                ? trim($validated['kondisi'])
                : 'Baik',
            'keterangan' => isset($validated['keterangan']) ? trim($validated['keterangan']) : null,
        ]);

        return redirect()->route('keranjang.index')->with('success', 'Data keranjang berhasil diubah.');
    }

    /**
     * Port 1:1 _legacy/modules/keranjang/proses_hapus.php
     * Legacy: hapus langsung tanpa pengecekan relasi.
     */
    public function destroy(string $id)
    {
        $keranjang = Keranjang::where('id_keranjang', $id)->firstOrFail();
        $keranjang->delete();

        return redirect()->route('keranjang.index')->with('success', 'Data keranjang berhasil dihapus.');
    }
}
