<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Jenis;
use App\Models\Keranjang;
use App\Models\Rak;
use App\Models\Satuan;
use App\Models\SerialInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangController extends Controller
{
    /**
     * Port 1:1 _legacy/modules/barang/tampil_data.php
     * List + search (serial_number, nama_barang, nama_satuan, nama_rak).
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $barangs = Barang::with(['jenisRel', 'satuanRel', 'rak', 'keranjang'])
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . $search . '%';
                $q->where(function ($w) use ($like) {
                    $w->where('tbl_barang.serial_number', 'like', $like)
                        ->orWhere('tbl_barang.nama_barang', 'like', $like)
                        ->orWhereHas('satuanRel', fn ($s) => $s->where('nama_satuan', 'like', $like))
                        ->orWhereHas('rak', fn ($r) => $r->where('nama_rak', 'like', $like));
                });
            })
            ->orderByDesc('id_barang')
            ->get();

        return view('barang.index', compact('barangs', 'search'));
    }

    /**
     * Port 1:1 _legacy/modules/barang/tampil_detail.php
     * Detail join barang + jenis + satuan (+ rak/keranjang untuk lokasi).
     */
    public function show(string $id)
    {
        $barang = Barang::with(['jenisRel', 'satuanRel', 'rak', 'keranjang'])
            ->where('id_barang', $id)
            ->firstOrFail();

        return view('barang.show', compact('barang'));
    }

    /**
     * Port 1:1 _legacy/modules/barang/form_entri.php
     */
    public function create()
    {
        $id_barang = self::nextId();
        $jenisList = Jenis::orderBy('nama_jenis')->get();
        $satuanList = Satuan::orderBy('nama_satuan')->get();
        $rakList = Rak::orderBy('nama_rak')->get();

        return view('barang.create', compact('id_barang', 'jenisList', 'satuanList', 'rakList'));
    }

    /**
     * Port 1:1 _legacy/modules/barang/proses_entri.php
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_barang' => 'required|string|max:5|unique:tbl_barang,id_barang',
            'nama_barang' => 'required|string|max:100',
            'jenis' => 'required|integer|exists:tbl_jenis,id_jenis',
            'stok_minimum' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'serial_number' => 'nullable|string|max:100',
            'satuan' => 'required|integer|exists:tbl_satuan,id_satuan',
            'id_rak' => 'nullable|integer|exists:tbl_rak,id_rak',
            'id_keranjang' => 'nullable|integer|exists:tbl_keranjang,id_keranjang',
            // legacy: *.jpg/*.jpeg/*.png maksimal 3 Mb (pesan 6 & 8)
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
        ], [
            'foto.mimes' => 'Tipe file tidak sesuai. Harap unggah file yang memiliki tipe *.jpg atau *.png.',
            'foto.max' => 'Ukuran file lebih dari 3 Mb. Harap unggah file yang memiliki ukuran maksimal 3 Mb.',
            'foto.image' => 'Tipe file tidak sesuai. Harap unggah file yang memiliki tipe *.jpg atau *.png.',
        ]);

        $fotoName = null;
        if ($request->hasFile('foto')) {
            $fotoName = $this->uploadFoto($request->file('foto'));
        }

        $barang = Barang::create([
            'id_barang' => $validated['id_barang'],
            'nama_barang' => trim($validated['nama_barang']),
            'serial_number' => isset($validated['serial_number']) ? trim($validated['serial_number']) : null,
            'jenis' => $validated['jenis'],
            'stok_minimum' => $validated['stok_minimum'],
            'stok' => $validated['stok'] ?? 0,
            'satuan' => $validated['satuan'],
            'id_rak' => $validated['id_rak'] ?? null,
            'id_keranjang' => $validated['id_keranjang'] ?? null,
            'foto' => $fotoName,
        ]);

        $this->syncModemSerials($barang);

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil disimpan.');
    }

    /**
     * Port 1:1 _legacy/modules/barang/form_ubah.php
     */
    public function edit(string $id)
    {
        $barang = Barang::with(['jenisRel', 'satuanRel', 'rak', 'keranjang'])
            ->where('id_barang', $id)
            ->firstOrFail();

        $jenisList = Jenis::orderBy('nama_jenis')->get();
        $satuanList = Satuan::orderBy('nama_satuan')->get();
        $rakList = Rak::orderBy('nama_rak')->get();
        $keranjangList = $barang->id_rak
            ? Keranjang::where('id_rak', $barang->id_rak)->orderBy('nama_keranjang')->get()
            : collect();

        return view('barang.edit', compact('barang', 'jenisList', 'satuanList', 'rakList', 'keranjangList'));
    }

    /**
     * Port 1:1 _legacy/modules/barang/proses_ubah.php
     */
    public function update(Request $request, string $id)
    {
        $barang = Barang::where('id_barang', $id)->firstOrFail();

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:100',
            'jenis' => 'required|integer|exists:tbl_jenis,id_jenis',
            'stok_minimum' => 'required|integer|min:0',
            'satuan' => 'required|integer|exists:tbl_satuan,id_satuan',
            'id_rak' => 'nullable|integer|exists:tbl_rak,id_rak',
            'id_keranjang' => 'nullable|integer|exists:tbl_keranjang,id_keranjang',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
        ], [
            'foto.mimes' => 'Tipe file tidak sesuai. Harap unggah file yang memiliki tipe *.jpg atau *.png.',
            'foto.max' => 'Ukuran file lebih dari 3 Mb. Harap unggah file yang memiliki ukuran maksimal 3 Mb.',
            'foto.image' => 'Tipe file tidak sesuai. Harap unggah file yang memiliki tipe *.jpg atau *.png.',
        ]);

        $data = [
            'nama_barang' => trim($validated['nama_barang']),
            'jenis' => $validated['jenis'],
            'stok_minimum' => $validated['stok_minimum'],
            'satuan' => $validated['satuan'],
            // legacy: rak kosong => id_rak=NULL, id_keranjang=NULL; rak tanpa keranjang => keranjang NULL
            'id_rak' => $validated['id_rak'] ?? null,
            'id_keranjang' => isset($validated['id_rak']) && $validated['id_rak']
                ? ($validated['id_keranjang'] ?? null)
                : null,
        ];

        // foto diubah => unggah baru + hapus foto lama (legacy menimpa nama di DB;
        // file lama dihapus agar tidak menumpuk di public/images)
        if ($request->hasFile('foto')) {
            $data['foto'] = $this->uploadFoto($request->file('foto'));
            $this->deleteFotoFile($barang->foto);
        }

        $barang->update($data);

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diubah.');
    }

    /**
     * Port 1:1 _legacy/modules/barang/proses_hapus.php
     * Blokir bila sudah tercatat di tbl_barang_masuk (pesan=4),
     * hapus file foto, lalu hapus baris.
     */
    public function destroy(string $id)
    {
        $barang = Barang::where('id_barang', $id)->firstOrFail();

        if (BarangMasuk::where('barang', $id)->exists()) {
            return redirect()->route('barang.index')
                ->with('error', 'Data barang tidak bisa dihapus karena sudah tercatat pada Data Transaksi.');
        }

        $this->deleteFotoFile($barang->foto);
        $barang->delete(); // serial inventory ikut terhapus via FK cascadeOnDelete

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil dihapus.');
    }

    /**
     * Port 1:1 _legacy/modules/barang/get_keranjang.php
     * AJAX (?id_rak= / POST id_rak) => keranjang JSON.
     */
    public function getKeranjang(Request $request)
    {
        $idRak = $request->input('id_rak', $request->query('id_rak'));

        if (empty($idRak)) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Parameter id_rak tidak ditemukan',
            ]);
        }

        $list = Keranjang::where('id_rak', $idRak)
            ->orderBy('nama_keranjang')
            ->get(['id_keranjang', 'kode_keranjang', 'nama_keranjang', 'kapasitas', 'kondisi']);

        if ($list->isEmpty()) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Tidak ada keranjang tersedia untuk rak yang dipilih',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $list,
            'message' => 'Data keranjang berhasil dimuat',
        ]);
    }

    /**
     * AJAX untuk modul barang-masuk: kembalikan stok/satuan barang.
     * GET /barang/ajax?id=<id_barang> atau ?id_barang=
     */
    public function getBarangAjax(Request $request)
    {
        $id = $request->input('id', $request->query('id_barang'));

        $barang = Barang::with(['satuanRel', 'rak', 'keranjang'])
            ->where('id_barang', $id)
            ->first();

        if (! $barang) {
            return response()->json([
                'success' => false,
                'message' => 'Data barang tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id_barang' => $barang->id_barang,
                'nama_barang' => $barang->nama_barang,
                'serial_number' => $barang->serial_number,
                'stok' => (int) $barang->stok,
                'stok_minimum' => (int) $barang->stok_minimum,
                'satuan' => $barang->satuan,
                'nama_satuan' => $barang->satuanRel->nama_satuan ?? null,
                'id_rak' => $barang->id_rak,
                'nama_rak' => $barang->rak->nama_rak ?? null,
                'id_keranjang' => $barang->id_keranjang,
                'nama_keranjang' => $barang->keranjang->nama_keranjang ?? null,
            ],
        ]);
    }

    // -----------------------------------------------------------------
    // Helpers (logika port 1:1 dari legacy)
    // -----------------------------------------------------------------

    /**
     * ID gen B0001 increment: 4 digit terakhir id_barang max + 1.
     * ("B" . str_pad(nomor_urut, 4, "0", STR_PAD_LEFT))
     */
    public static function nextId(): string
    {
        $last = Barang::orderByDesc('id_barang')->value('id_barang');
        $num = $last ? ((int) substr($last, -4)) + 1 : 1;

        return 'B' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Upload foto ke public/images dengan nama hash
     * sha1(md5(time + nama_asli)) seperti legacy.
     */
    protected function uploadFoto($file): string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $name = sha1(md5(time() . $file->getClientOriginalName())) . '.' . $ext;

        $dir = public_path('images');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $file->move($dir, $name);

        return $name;
    }

    protected function deleteFotoFile(?string $foto): void
    {
        if (! empty($foto)) {
            $path = public_path('images/' . $foto);
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    /**
     * Port blok modem di proses_entri.php:
     * jika nama_barang mengandung "modem" (case-insensitive),
     * pecah serial_number per koma -> tbl_serial_inventory,
     * banyak SN => sufiks -01, -02, ...; duplikat diabaikan.
     */
    protected function syncModemSerials(Barang $barang): void
    {
        if (empty($barang->serial_number) || stripos($barang->nama_barang, 'modem') === false) {
            return;
        }

        $serials = array_filter(array_map('trim', explode(',', (string) $barang->serial_number)));
        $multiple = count($serials) > 1;

        foreach (array_values($serials) as $i => $sn) {
            $current = $multiple ? $sn . '-' . str_pad($i + 1, 2, '0', STR_PAD_LEFT) : $sn;

            // abaikan duplikat (legacy: skip error 1062 duplicate entry)
            if (SerialInventory::where('serial_number', $current)->exists()) {
                continue;
            }

            SerialInventory::create([
                'id_barang' => $barang->id_barang,
                'serial_number' => $current,
                'status' => 'Available',
            ]);
        }
    }
}
