<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    protected $table = 'tbl_keranjang';
    protected $primaryKey = 'id_keranjang';
    public $timestamps = false;
    protected $fillable = ['kode_keranjang','nama_keranjang','id_rak','kapasitas','kondisi','keterangan'];
    public function rak() { return $this->belongsTo(Rak::class, 'id_rak', 'id_rak'); }
    public function barangs() { return $this->hasMany(Barang::class, 'id_keranjang', 'id_keranjang'); }
}
