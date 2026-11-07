<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    protected $table = 'tbl_barang_masuk';
    protected $primaryKey = 'id_transaksi';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id_transaksi','tanggal','barang','serial_number','jumlah','id_rak','id_keranjang'];
    protected $casts = ['tanggal' => 'date'];

    public function barangRel() { return $this->belongsTo(Barang::class, 'barang', 'id_barang'); }
    public function rak() { return $this->belongsTo(Rak::class, 'id_rak', 'id_rak'); }
    public function keranjang() { return $this->belongsTo(Keranjang::class, 'id_keranjang', 'id_keranjang'); }

    public static function nextId(): string
    {
        $last = static::orderBy('id_transaksi', 'desc')->value('id_transaksi');
        $num = $last ? ((int) substr($last, 3)) + 1 : 1;
        return 'TM-'.str_pad($num, 7, '0', STR_PAD_LEFT);
    }

    protected static function booted(): void
    {
        static::saved(fn () => \Illuminate\Support\Facades\Cache::forget('dashboard_analytics_12m'));
        static::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('dashboard_analytics_12m'));
    }
}
