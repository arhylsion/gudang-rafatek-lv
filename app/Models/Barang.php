<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'tbl_barang';
    protected $primaryKey = 'id_barang';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id_barang','nama_barang','serial_number','jenis','stok_minimum','stok','satuan','id_rak','id_keranjang','foto'];

    public function jenisRel() { return $this->belongsTo(Jenis::class, 'jenis', 'id_jenis'); }
    public function satuanRel() { return $this->belongsTo(Satuan::class, 'satuan', 'id_satuan'); }
    public function rak() { return $this->belongsTo(Rak::class, 'id_rak', 'id_rak'); }
    public function keranjang() { return $this->belongsTo(Keranjang::class, 'id_keranjang', 'id_keranjang'); }
    public function serials() { return $this->hasMany(SerialInventory::class, 'id_barang', 'id_barang'); }
    public function masuk() { return $this->hasMany(BarangMasuk::class, 'barang', 'id_barang'); }
    public function keluar() { return $this->hasMany(BarangKeluar::class, 'barang', 'id_barang'); }

    public function getIsModemAttribute(): bool
    {
        return stripos($this->nama_barang, 'modem') !== false;
    }

    public function scopeLowStock($q)
    {
        return $q->whereColumn('stok', '<=', 'stok_minimum');
    }
}
