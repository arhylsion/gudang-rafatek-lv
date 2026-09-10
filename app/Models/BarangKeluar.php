<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    protected $table = 'tbl_barang_keluar';
    protected $primaryKey = 'id';
    protected $fillable = ['id_transaksi','tanggal','tanggal_pengembalian','barang','serial_number','jumlah','pemohon','jenis','id_rak','id_keranjang','status','tanggal_persetujuan','tanggal_penolakan','kondisi','catatan','created_by','foto_pengembalian'];
    protected $casts = [
        'tanggal' => 'date',
        'tanggal_pengembalian' => 'date',
        'tanggal_persetujuan' => 'datetime',
        'tanggal_penolakan' => 'datetime',
    ];

    public const STATUS_MENUNGGU = 'Menunggu Persetujuan';
    public const STATUS_DISETUJUI = 'Disetujui';
    public const STATUS_DITOLAK = 'Ditolak';

    public function barangRel() { return $this->belongsTo(Barang::class, 'barang', 'id_barang'); }
    public function rak() { return $this->belongsTo(Rak::class, 'id_rak', 'id_rak'); }
    public function keranjang() { return $this->belongsTo(Keranjang::class, 'id_keranjang', 'id_keranjang'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by', 'id_user'); }

    public static function nextId(): string
    {
        $last = static::orderBy('id_transaksi', 'desc')->value('id_transaksi');
        $num = $last ? ((int) substr($last, 3)) + 1 : 1;
        return 'TK-'.str_pad($num, 7, '0', STR_PAD_LEFT);
    }
}
