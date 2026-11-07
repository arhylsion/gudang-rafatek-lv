<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Keranjang;
use App\Models\Rak;
use App\Models\SerialInventory;
use App\Services\EmailNotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BarangKeluarController extends Controller
{
    /**
     * Port 1:1 _legacy/modules/barang-keluar/tampil_data.php
     * Join barang + satuan + rak + keranjang + creator, order desc.
     * Filter opsional ?status= & ?jenis=.
     */
    public function index(Request $request)
    {
        $query = BarangKeluar::with(['barangRel.satuanRel', 'rak', 'keranjang', 'creator'])
            ->orderByDesc('id_transaksi');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->query('jenis'));
        }

        // Batasi 500 transaksi terbaru agar web app tetap responsif dan bebas buffer di skala 100k+
        $keluar = $query->limit(500)->get();

        return view('barang-keluar.index', [
            'keluar' => $keluar,
            'fStatus' => $request->query('status'),
            'fJenis' => $request->query('jenis'),
        ]);
    }

    /**
     * Port 1:1 _legacy/modules/barang-keluar/form_entri.php
     * (+ form_entri_barang_keluar.php yang hanya switch type).
     * Query ?type=keluar|pinjam (default keluar), generate TK-0000001,
     * list barang dengan stok > 0 + daftar rak.
     */
    public function create(Request $request)
    {
        $type = $request->query('type', 'keluar');
        if (! in_array($type, ['keluar', 'pinjam'])) {
            $type = 'keluar';
        }

        $jenis = $type === 'pinjam' ? 'Pinjam' : 'Keluar';
        $id_transaksi = BarangKeluar::nextId();
        $barangList = Barang::where('stok', '>', 0)
            ->orderBy('id_barang')
            ->get(['id_barang', 'nama_barang', 'serial_number']);
        $rakList = Rak::orderBy('nama_rak')->get();

        return view('barang-keluar.create', compact('type', 'jenis', 'id_transaksi', 'barangList', 'rakList'));
    }

    /**
     * Port 1:1 _legacy/modules/barang-keluar/proses_entri.php
     * Insert status "Menunggu Persetujuan" + reservasi serial modem.
     * Email dikirim setelah commit; kegagalan email tidak menggagalkan transaksi.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_transaksi' => 'required|string|max:20|unique:tbl_barang_keluar,id_transaksi',
            'tanggal' => 'required|date',
            'barang' => 'required|string|exists:tbl_barang,id_barang',
            'jumlah' => 'required|integer|min:1',
            'jenis' => 'required|in:Keluar,Pinjam',
            'serial_number' => 'nullable|string|max:100',
            'id_rak' => 'nullable|integer|exists:tbl_rak,id_rak',
            'id_keranjang' => 'nullable|integer|exists:tbl_keranjang,id_keranjang',
            'catatan' => 'required|string|max:1000',
        ]);

        // Normalisasi d-m-Y (date-picker) -> Y-m-d (legacy: explode('-') dd-mm-yyyy)
        try {
            $tanggalFormatted = Carbon::createFromFormat('d-m-Y', trim($validated['tanggal']))->format('Y-m-d');
        } catch (\Exception $e) {
            $tanggalFormatted = date('Y-m-d', strtotime($validated['tanggal']));
        }

        $barangRow = Barang::where('id_barang', $validated['barang'])->firstOrFail();

        if ((int) $validated['jumlah'] > (int) $barangRow->stok) {
            return back()->withInput()
                ->with('error', 'Stok tidak mencukupi. Stok tersedia: '.$barangRow->stok);
        }

        $serial = isset($validated['serial_number']) ? trim($validated['serial_number']) : null;
        $serial = ($serial === '') ? null : $serial;
        $createdBy = Auth::id();

        try {
            DB::transaction(function () use ($validated, $tanggalFormatted, $serial, $barangRow, $createdBy) {
                BarangKeluar::create([
                    'id_transaksi' => $validated['id_transaksi'],
                    'tanggal' => $tanggalFormatted,
                    'barang' => $validated['barang'],
                    'serial_number' => $serial,
                    'jumlah' => (int) $validated['jumlah'],
                    'jenis' => $validated['jenis'],
                    'status' => BarangKeluar::STATUS_MENUNGGU,
                    'id_rak' => $validated['id_rak'] ?? null,
                    'id_keranjang' => $validated['id_keranjang'] ?? null,
                    'created_by' => $createdBy,
                    'catatan' => trim($validated['catatan']),
                ]);

                // Reservasi serial di tbl_serial_inventory (hanya modem, seperti native)
                if ($serial && stripos($barangRow->nama_barang, 'modem') !== false) {
                    $affected = SerialInventory::where('id_barang', $validated['barang'])
                        ->where('serial_number', $serial)
                        ->where('status', 'Available')
                        ->update([
                            'status' => 'Reserved',
                            'reserved_for_transaction' => $validated['id_transaksi'],
                        ]);

                    if ($affected === 0) {
                        throw new \RuntimeException('Serial number yang dipilih tidak tersedia (sudah dipakai/direservasi).');
                    }
                }
            });
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        // Kirim notifikasi email setelah commit; jangan gagalkan transaksi jika mail gagal
        try {
            $barangMail = Barang::with('satuanRel')->where('id_barang', $validated['barang'])->first();
            $user = Auth::user();

            if ($user && $user->email && $barangMail) {
                EmailNotificationService::sendOutgoingGoodsNotification([
                    'id_transaksi' => $validated['id_transaksi'],
                    'tanggal' => $tanggalFormatted,
                    'nama_barang' => $barangMail->nama_barang,
                    'jumlah' => (int) $validated['jumlah'],
                    'satuan' => $barangMail->satuanRel->nama_satuan ?? '-',
                    'jenis' => $validated['jenis'],
                    'serial_number' => $serial,
                    'user_name' => $user->nama_user,
                    'user_email' => $user->email,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Email notification error: '.$e->getMessage());
        }

        return redirect()->route('barang-keluar.index')
            ->with('success', 'Data barang keluar berhasil disimpan dengan status "Menunggu Persetujuan". ID Transaksi: '.$validated['id_transaksi']);
    }

    /**
     * Port 1:1 _legacy/modules/barang-keluar/form_selesai.php
     * Halaman detail transaksi.
     */
    public function show(string $id)
    {
        $trx = BarangKeluar::with(['barangRel.satuanRel', 'rak', 'keranjang', 'creator'])
            ->where('id_transaksi', $id)
            ->firstOrFail();

        return view('barang-keluar.show', compact('trx'));
    }

    /**
     * Port 1:1 _legacy/modules/barang-keluar/proses_status.php
     * Menangani GET ?id&status=Disetujui/Ditolak (link Setujui/Tolak)
     * dan POST dengan status + catatan/admin_note (form detail).
     * Hanya Administrator & Admin Gudang yang boleh menyetujui/menolak.
     */
    public function updateStatus(Request $request)
    {
        if (! in_array(Auth::user()->hak_akses, ['Administrator', 'Admin Gudang'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk melakukan aksi ini.');
        }

        $idTransaksi = $request->input('id', $request->input('id_transaksi'));
        $statusBaru = $request->input('status');

        if (! $idTransaksi || ! in_array($statusBaru, ['Disetujui', 'Ditolak'])) {
            return redirect()->route('barang-keluar.index')->with('error', 'Status tidak valid!');
        }

        $trx = BarangKeluar::where('id_transaksi', $idTransaksi)->first();

        if (! $trx) {
            return redirect()->route('barang-keluar.index')->with('error', 'Data transaksi tidak ditemukan');
        }

        $statusLama = $trx->status;

        if ($statusLama === $statusBaru) {
            return redirect()->route('barang-keluar.index')->with('error', 'Status sudah '.$statusBaru);
        }

        $catatan = $request->input('catatan', $request->input('admin_note'));

        try {
            DB::transaction(function () use ($trx, $statusLama, $statusBaru, $catatan) {
                $barang = Barang::where('id_barang', $trx->barang)->lockForUpdate()->firstOrFail();

                $attrs = ['status' => $statusBaru];

                if ($statusBaru === 'Disetujui') {
                    $attrs['tanggal_persetujuan'] = now();

                    // Kurangi stok hanya jika belum pernah disetujui sebelumnya
                    if ($statusLama !== BarangKeluar::STATUS_DISETUJUI) {
                        if ((int) $barang->stok < (int) $trx->jumlah) {
                            throw new \RuntimeException('Stok tidak mencukupi. Stok tersedia: '.$barang->stok);
                        }
                        $barang->decrement('stok', (int) $trx->jumlah);
                    }
                } else {
                    // Ditolak
                    $attrs['tanggal_penolakan'] = now();

                    // Kembalikan stok jika sebelumnya sudah disetujui
                    if ($statusLama === BarangKeluar::STATUS_DISETUJUI) {
                        $barang->increment('stok', (int) $trx->jumlah);
                    }
                }

                if ($catatan !== null && trim((string) $catatan) !== '') {
                    $attrs['catatan'] = trim((string) $catatan);
                }

                $trx->update($attrs);

                // Update status serial modem (seperti native)
                if (stripos($barang->nama_barang, 'modem') !== false && ! empty($trx->serial_number)) {
                    if ($statusBaru === 'Disetujui') {
                        SerialInventory::where('id_barang', $trx->barang)
                            ->where('serial_number', $trx->serial_number)
                            ->update([
                                'status' => 'Used',
                                'used_in_transaction' => $trx->id_transaksi,
                                'reserved_for_transaction' => null,
                            ]);
                    } else {
                        SerialInventory::where('id_barang', $trx->barang)
                            ->where('serial_number', $trx->serial_number)
                            ->update([
                                'status' => 'Available',
                                'reserved_for_transaction' => null,
                                'used_in_transaction' => null,
                            ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            return redirect()->route('barang-keluar.index')->with('error', $e->getMessage());
        }

        // Kirim notifikasi update status; jangan gagalkan transaksi jika mail gagal
        try {
            $barangMail = Barang::with('satuanRel')->where('id_barang', $trx->barang)->first();
            $creator = $trx->creator;

            if ($creator && $creator->email && $barangMail) {
                EmailNotificationService::sendStatusUpdateNotification([
                    'id_transaksi' => $trx->id_transaksi,
                    'tanggal' => $trx->tanggal instanceof \DateTimeInterface
                        ? $trx->tanggal->format('Y-m-d')
                        : (string) $trx->tanggal,
                    'nama_barang' => $barangMail->nama_barang,
                    'jumlah' => (int) $trx->jumlah,
                    'satuan' => $barangMail->satuanRel->nama_satuan ?? '-',
                    'jenis' => $trx->jenis,
                    'serial_number' => $trx->serial_number,
                    'user_name' => $creator->nama_user,
                    'user_email' => $creator->email,
                    'status' => $statusBaru,
                    'admin_note' => trim((string) $catatan),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Status update email notification error: '.$e->getMessage());
        }

        $pesanStok = '';
        if ($statusBaru === 'Disetujui' && $statusLama !== 'Disetujui') {
            $pesanStok = ' Stok barang telah dikurangi sebanyak '.$trx->jumlah.' unit.';
        } elseif ($statusBaru === 'Ditolak' && $statusLama === 'Disetujui') {
            $pesanStok = ' Stok barang telah dikembalikan sebanyak '.$trx->jumlah.' unit.';
        }

        return redirect()->route('barang-keluar.index')
            ->with('success', 'Status transaksi '.$idTransaksi.' berhasil diubah ke "'.$statusBaru.'".'.$pesanStok);
    }

    /**
     * Port 1:1 _legacy/modules/barang-keluar/pengembalian_form.php
     * Dropdown transaksi Pinjam + Disetujui + belum dikembalikan.
     * $id opsional untuk preselect.
     */
    public function pengembalianForm(Request $request, ?string $id = null)
    {
        $list = BarangKeluar::with('barangRel')
            ->where('jenis', 'Pinjam')
            ->where('status', BarangKeluar::STATUS_DISETUJUI)
            ->whereNull('tanggal_pengembalian')
            ->orderByDesc('id_transaksi')
            ->get();

        $preselect = $id ?? $request->query('id');

        return view('barang-keluar.pengembalian', [
            'list' => $list,
            'preselect' => $preselect,
        ]);
    }

    /**
     * Port 1:1 _legacy/modules/barang-keluar/proses_pengembalian.php
     * Hanya untuk jenis Pinjam + Disetujui + tanggal_pengembalian NULL.
     * Kondisi Baik -> stok kembali; Rusak/Hilang -> stok tidak kembali.
     */
    public function prosesPengembalian(Request $request, ?string $id = null)
    {
        $idTransaksi = $id ?? $request->input('id_transaksi');

        if (! $idTransaksi) {
            return back()->withInput()->with('error', 'ID Transaksi tidak boleh kosong.');
        }

        $trx = BarangKeluar::where('id_transaksi', $idTransaksi)->first();

        if (! $trx || $trx->jenis !== 'Pinjam' || $trx->status !== BarangKeluar::STATUS_DISETUJUI || $trx->tanggal_pengembalian !== null) {
            return redirect()->route('barang-keluar.index')
                ->with('error', 'Transaksi tidak ditemukan atau sudah dikembalikan!');
        }

        $validated = $request->validate([
            'tanggal_pengembalian' => 'required|date|before_or_equal:today',
            'kondisi' => 'required|in:Baik,Rusak,Hilang',
            'catatan' => 'required_if:kondisi,Rusak,Hilang|nullable|string|max:1000',
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:3072',
        ]);

        $file = $request->file('foto');
        $ext = strtolower($file->getClientOriginalExtension());
        $namaFoto = $idTransaksi.'_'.date('YmdHis').'.'.$ext;
        $dest = public_path('images/pengembalian');

        if (! is_dir($dest)) {
            mkdir($dest, 0777, true);
        }

        $file->move($dest, $namaFoto);

        DB::transaction(function () use ($trx, $validated, $namaFoto) {
            $trx->update([
                'tanggal_pengembalian' => $validated['tanggal_pengembalian'],
                'kondisi' => $validated['kondisi'],
                'catatan' => $validated['catatan'] ?? $trx->catatan,
                'foto_pengembalian' => $namaFoto,
            ]);

            // Hanya kondisi Baik yang mengembalikan stok (Rusak/Hilang tidak kembali)
            if ($validated['kondisi'] === 'Baik') {
                Barang::where('id_barang', $trx->barang)->increment('stok', (int) $trx->jumlah);
            }
        });

        $pesanStok = $validated['kondisi'] === 'Baik'
            ? ' Stok dikembalikan: '.$trx->jumlah.' unit.'
            : ' Stok tidak dikembalikan karena kondisi '.$validated['kondisi'].'.';

        return redirect()->route('barang-keluar.index')
            ->with('success', 'Barang '.$idTransaksi.' berhasil dikembalikan dengan kondisi: '.$validated['kondisi'].'.'.$pesanStok);
    }

    /**
     * Port 1:1 _legacy/modules/barang-keluar/proses_hapus.php
     * Plain delete tanpa restore stok (seperti native).
     */
    public function destroy(string $id)
    {
        $trx = BarangKeluar::where('id_transaksi', $id)->firstOrFail();
        $trx->delete();

        return redirect()->route('barang-keluar.index')
            ->with('success', 'Data barang keluar berhasil dihapus.');
    }

    /**
     * Port 1:1 _legacy/modules/barang-keluar/cetak.php
     * Slip PDF tunggal via Dompdf; foto di-embed base64.
     */
    public function cetak(string $id)
    {
        $trx = BarangKeluar::with(['barangRel.satuanRel', 'rak', 'keranjang', 'creator'])
            ->where('id_transaksi', $id)
            ->firstOrFail();

        $sn = $trx->serial_number
            ?: ($trx->barangRel->serial_number ?? null)
            ?: $trx->barang;

        $fotoBase64 = $this->embedFotoBase64(
            $trx->barangRel->foto ? public_path('images/'.$trx->barangRel->foto) : null
        );

        $fotoKembaliBase64 = $this->embedFotoBase64(
            $trx->foto_pengembalian ? public_path('images/pengembalian/'.$trx->foto_pengembalian) : null
        ) ?? $this->embedFotoBase64(
            $trx->foto_pengembalian ? public_path('images/'.$trx->foto_pengembalian) : null
        );

        $pdf = Pdf::loadView('barang-keluar.pdf.cetak', [
            'trx' => $trx,
            'sn' => $sn,
            'fotoBase64' => $fotoBase64,
            'fotoKembaliBase64' => $fotoKembaliBase64,
        ]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('transaksi_'.$id.'.pdf');
    }

    /**
     * Port 1:1 _legacy/modules/barang-keluar/get_barang.php
     * AJAX GET ?id_barang => JSON stok, serial, satuan.
     */
    public function ajaxBarang(Request $request)
    {
        $id = $request->query('id_barang', $request->input('id_barang'));

        $barang = Barang::with('satuanRel')->where('id_barang', $id)->first();

        if (! $barang) {
            return response()->json([
                'success' => false,
                'message' => 'Data barang tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'stok' => (int) $barang->stok,
            'serial_number' => $barang->serial_number,
            'nama_satuan' => $barang->satuanRel->nama_satuan ?? null,
            'nama_barang' => $barang->nama_barang,
        ]);
    }

    /**
     * Port 1:1 _legacy/modules/barang-keluar/get_serial_numbers.php
     * AJAX GET ?id_barang => daftar serial Available (JSON array).
     */
    public function ajaxSerials(Request $request)
    {
        $idBarang = $request->query('id_barang', $request->input('id_barang'));

        if (! $idBarang) {
            return response()->json(['error' => 'Parameter id_barang tidak ditemukan'], 422);
        }

        $data = SerialInventory::where('id_barang', $idBarang)
            ->where('status', 'Available')
            ->orderBy('serial_number')
            ->get(['serial_number'])
            ->map(fn ($row) => ['serial_number' => $row->serial_number])
            ->values();

        return response()->json($data);
    }

    /**
     * Port 1:1 _legacy/modules/barang-keluar/get_location_from_masuk.php
     * (+ mode ?id_rak= untuk dropdown keranjang, seperti BarangMasukController).
     * AJAX GET ?id_barang => lokasi dari transaksi masuk terbaru.
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

            $lokasi = BarangMasuk::with(['rak', 'keranjang'])
                ->where('barang', $idBarang)
                ->whereNotNull('id_rak')
                ->whereNotNull('id_keranjang')
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

        return 'data:image/'.$mime.';base64,'.base64_encode($bin);
    }
}
