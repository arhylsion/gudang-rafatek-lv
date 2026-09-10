<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Rak;
use Illuminate\Http\Request;

class RakController extends Controller
{
    /**
     * Port 1:1 _legacy/modules/rak/tampil_data.php
     */
    public function index()
    {
        $rak = Rak::orderByDesc('id_rak')->get();

        return view('rak.index', compact('rak'));
    }

    /**
     * Port 1:1 _legacy/modules/rak/form_entri.php
     */
    public function create()
    {
        return view('rak.create');
    }

    /**
     * Port 1:1 _legacy/modules/rak/proses_entri.php
     * kode_rak harus unik (legacy pesan=4 bila duplikat).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_rak' => 'required|string|max:10|unique:tbl_rak,kode_rak',
            'nama_rak' => 'required|string|max:100',
            'lokasi' => 'nullable|string|max:200',
            'kapasitas' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ], [
            'kode_rak.required' => 'Kode rak tidak boleh kosong.',
            'kode_rak.unique' => 'Kode rak ' . $request->input('kode_rak') . ' sudah ada. Silahkan ganti kode rak yang Anda masukan.',
            'nama_rak.required' => 'Nama rak tidak boleh kosong.',
        ]);

        Rak::create([
            'kode_rak' => trim($validated['kode_rak']),
            'nama_rak' => trim($validated['nama_rak']),
            'lokasi' => isset($validated['lokasi']) ? trim($validated['lokasi']) : null,
            'kapasitas' => (int) ($validated['kapasitas'] ?? 0),
            'keterangan' => isset($validated['keterangan']) ? trim($validated['keterangan']) : null,
        ]);

        return redirect()->route('rak.index')->with('success', 'Data rak berhasil disimpan.');
    }

    /**
     * Port 1:1 _legacy/modules/rak/form_ubah.php
     */
    public function edit(string $id)
    {
        $rak = Rak::where('id_rak', $id)->firstOrFail();

        return view('rak.edit', compact('rak'));
    }

    /**
     * Port 1:1 _legacy/modules/rak/proses_ubah.php
     */
    public function update(Request $request, string $id)
    {
        $rak = Rak::where('id_rak', $id)->firstOrFail();

        $validated = $request->validate([
            'kode_rak' => 'required|string|max:10|unique:tbl_rak,kode_rak,' . $rak->id_rak . ',id_rak',
            'nama_rak' => 'required|string|max:100',
            'lokasi' => 'nullable|string|max:200',
            'kapasitas' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ], [
            'kode_rak.required' => 'Kode rak tidak boleh kosong.',
            'kode_rak.unique' => 'Kode rak ' . $request->input('kode_rak') . ' sudah ada. Silahkan ganti kode rak yang Anda masukan.',
            'nama_rak.required' => 'Nama rak tidak boleh kosong.',
        ]);

        $rak->update([
            'kode_rak' => trim($validated['kode_rak']),
            'nama_rak' => trim($validated['nama_rak']),
            'lokasi' => isset($validated['lokasi']) ? trim($validated['lokasi']) : null,
            'kapasitas' => (int) ($validated['kapasitas'] ?? 0),
            'keterangan' => isset($validated['keterangan']) ? trim($validated['keterangan']) : null,
        ]);

        return redirect()->route('rak.index')->with('success', 'Data rak berhasil diubah.');
    }

    /**
     * Port 1:1 _legacy/modules/rak/proses_hapus.php
     * Blokir bila rak masih memiliki keranjang terkait (legacy pesan=5).
     */
    public function destroy(string $id)
    {
        $rak = Rak::where('id_rak', $id)->firstOrFail();

        if (Keranjang::where('id_rak', $rak->id_rak)->exists()) {
            return redirect()->route('rak.index')
                ->with('error', 'Data rak tidak bisa dihapus karena masih memiliki keranjang yang terkait.');
        }

        $rak->delete();

        return redirect()->route('rak.index')->with('success', 'Data rak berhasil dihapus.');
    }
}
