<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Keranjang;
use App\Models\Rak;
use App\Models\SerialInventory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    /**
     * Port 1:1 _legacy/modules/barang-masuk/tampil_data.php
     * Join barang + satuan + rak + keranjang, order desc.
     */
    public function index()
    {
        // Batasi 500 transaksi terbaru agar web app tetap responsif dan bebas buffer di skala 100k+
        $masuk = BarangMasuk::with(['barangRel.satuanRel', 'rak', 'keranjang'])
            ->orderByDesc('id_transaksi')
            ->limit(500)
            ->get();

        return view('barang-masuk.index', compact('masuk'));
    }

    /**
     * Port 1:1 _legacy/modules/barang-masuk/form_entri.php
     * Generate TM-0000001 via max+1, list barang + rak.
     */
    public function create()
    {
        $id_transaksi = BarangMasuk::nextId();
        $barangList = Barang::orderBy('id_barang')->get(['id_barang', 'nama_barang', 'serial_number']);
        $rakList = Rak::orderBy('nama_rak')->get();

        return view('barang-masuk.create', compact('id_transaksi', 'barangList', 'rakList'));
    }

    /**
     * Port 1:1 _legacy/modules/barang-masuk/proses_entri.php
     * Insert + stok += jumlah (pengganti trigger 'stok_masuk')
     * + serial modem (split koma, sufiks -01, abaikan duplikat).
     */
    public function store(Request $request)
    {
        $maxDate = Carbon::now()->addYear()->format('Y-m-d');

        $validated = $request->validate([
            'id_transaksi' => 'required|string|max:10|unique:tbl_barang_masuk,id_transaksi',
            // date-picker mengirim d-m-Y; strtotime mengenali format tsb (legacy memakai cara yang sama)
            'tanggal' => ['required', 'date', 'after_or_equal:2020-01-01', "before_or_equal:{$maxDate}"],
            'barang' => 'required|string|exists:tbl_barang,id_barang',
            'jumlah' => 'required|integer|min:1',
            'serial_number' => 'nullable|string|max:100',
            'id_rak' => 'nullable|integer|exists:tbl_rak,id_rak',
            'id_keranjang' => 'nullable|integer|exists:tbl_keranjang,id_keranjang',
        ]);

        // Normalisasi d-m-Y (date-picker) -> Y-m-d sebelum disimpan (legacy: date('Y-m-d', strtotime(...)))
        try {
            $tanggalMasuk = Carbon::createFromFormat('d-m-Y', trim($validated['tanggal']))->format('Y-m-d');
        } catch (\Exception $e) {
            $tanggalMasuk = date('Y-m-d', strtotime($validated['tanggal']));
        }

        DB::transaction(function () use ($validated, $tanggalMasuk) {
            BarangMasuk::create([
                'id_transaksi' => $validated['id_transaksi'],
                'tanggal' => $tanggalMasuk,
                'barang' => $validated['barang'],
                'serial_number' => isset($validated['serial_number']) ? trim($validated['serial_number']) : null,
                'jumlah' => $validated['jumlah'],
                'id_rak' => $validated['id_rak'] ?? null,
                'id_keranjang' => $validated['id_keranjang'] ?? null,
            ]);

            // Pengganti trigger DB 'stok_masuk': stok bertambah sejumlah barang masuk
            Barang::where('id_barang', $validated['barang'])->increment('stok', (int) $validated['jumlah']);

            $this->syncModemSerials($validated['barang'], $validated['serial_number'] ?? null);
        });

        return redirect()->route('barang-masuk.index')->with('success', 'Data barang masuk berhasil disimpan.');
    }

    /**
     * Port 1:1 _legacy/modules/barang-masuk/proses_hapus.php
     * Delete; stok berkurang otomatis (pengganti trigger 'hapus_stok_masuk'),
     * tidak pernah negatif.
     */
    public function destroy(string $id)
    {
        $masuk = BarangMasuk::where('id_transaksi', $id)->firstOrFail();

        DB::transaction(function () use ($masuk) {
            $barang = Barang::where('id_barang', $masuk->barang)->first();
            if ($barang) {
                $barang->stok = max(0, (int) $barang->stok - (int) $masuk->jumlah);
                $barang->save();
            }

            $masuk->delete();
        });

        return redirect()->route('barang-masuk.index')->with('success', 'Data barang masuk berhasil dihapus.');
    }

    /**
     * Port 1:1 _legacy/modules/barang-masuk/get_barang.php
     * AJAX GET ?id_barang => JSON stok, satuan, nama (+ flag modem & lokasi).
     */
    public function ajaxBarang(Request $request)
    {
        $id = $request->query('id_barang', $request->input('id_barang'));

        $barang = Barang::with(['satuanRel', 'jenisRel'])
            ->where('id_barang', $id)
            ->first();

        if (! $barang) {
            return response()->json([
                'success' => false,
                'message' => 'Data barang tidak ditemukan',
            ], 404);
        }

        $isModem = isset($barang->jenisRel->nama_jenis)
            && strtoupper($barang->jenisRel->nama_jenis) === 'MODEM';

        return response()->json([
            'stok' => (int) $barang->stok,
            'nama_barang' => $barang->nama_barang,
            'serial_number' => $barang->serial_number,
            'nama_satuan' => $barang->satuanRel->nama_satuan ?? null,
            'nama_jenis' => $barang->jenisRel->nama_jenis ?? null,
            'is_modem' => $isModem,
            'needs_serial' => $isModem || ! empty($barang->serial_number),
            'id_rak' => $barang->id_rak,
            'id_keranjang' => $barang->id_keranjang,
        ]);
    }

    /**
     * Port 1:1 _legacy/modules/barang-masuk/get_location_from_masuk.php
     * (+ daftar keranjang per rak untuk onchange #id_rak).
     * AJAX GET ?id_barang => lokasi terakhir (fallback lokasi default barang).
     * AJAX GET ?id_rak => daftar keranjang dalam rak tsb.
     */
    public function ajaxLocation(Request $request)
    {
        // Mode daftar keranjang: ?id_rak=
        if ($request->filled('id_rak')) {
            $list = Keranjang::where('id_rak', $request->query('id_rak'))
                ->orderBy('nama_keranjang')
                ->get(['id_keranjang', 'kode_keranjang', 'nama_keranjang', 'kondisi']);

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

        // Mode lokasi barang: ?id_barang= (legacy get_location_from_masuk.php)
        if ($request->filled('id_barang')) {
            $idBarang = $request->query('id_barang');

            // Lokasi dari transaksi barang masuk terbaru yang memiliki rak
            $lokasi = BarangMasuk::with(['rak', 'keranjang'])
                ->where('barang', $idBarang)
                ->whereNotNull('id_rak')
                ->orderByDesc('tanggal')
                ->orderByDesc('id_transaksi')
                ->first();

            if ($lokasi) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id_rak' => $lokasi->id_rak,
                        'id_keranjang' => $lokasi->id_keranjang,
                        'nama_rak' => $lokasi->rak->nama_rak ?? null,
                        'lokasi_rak' => $lokasi->rak->lokasi ?? null,
                        'nama_keranjang' => $lokasi->keranjang->nama_keranjang ?? null,
                        'kondisi_keranjang' => $lokasi->keranjang->kondisi ?? null,
                    ],
                ]);
            }

            // Fallback: lokasi default dari tabel barang
            $barang = Barang::with(['rak', 'keranjang'])
                ->where('id_barang', $idBarang)
                ->whereNotNull('id_rak')
                ->first();

            if ($barang) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id_rak' => $barang->id_rak,
                        'id_keranjang' => $barang->id_keranjang,
                        'nama_rak' => $barang->rak->nama_rak ?? null,
                        'lokasi_rak' => $barang->rak->lokasi ?? null,
                        'nama_keranjang' => $barang->keranjang->nama_keranjang ?? null,
                        'kondisi_keranjang' => $barang->keranjang->kondisi ?? null,
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data lokasi ditemukan untuk barang ini',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Parameter id_barang / id_rak tidak ditemukan',
        ]);
    }

    /**
     * Port blok modem di proses_entri.php:
     * jika nama_barang mengandung "modem" (case-insensitive),
     * pecah serial_number per koma -> tbl_serial_inventory,
     * banyak SN => sufiks -01, -02, ...; duplikat diabaikan (legacy: skip error 1062).
     */
    protected function syncModemSerials(string $idBarang, ?string $serialNumber): void
    {
        if (empty($serialNumber)) {
            return;
        }

        $nama = Barang::where('id_barang', $idBarang)->value('nama_barang');
        if ($nama === null || stripos($nama, 'modem') === false) {
            return;
        }

        $serials = array_values(array_filter(array_map('trim', explode(',', (string) $serialNumber))));
        $multiple = count($serials) > 1;

        foreach ($serials as $i => $sn) {
            $current = $multiple ? $sn . '-' . str_pad($i + 1, 2, '0', STR_PAD_LEFT) : $sn;

            if (SerialInventory::where('serial_number', $current)->exists()) {
                continue;
            }

            SerialInventory::create([
                'id_barang' => $idBarang,
                'serial_number' => $current,
                'status' => 'Available',
            ]);
        }
    }
}
