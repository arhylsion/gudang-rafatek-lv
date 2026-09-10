<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Rak extends Model
{
    protected $table = 'tbl_rak';
    protected $primaryKey = 'id_rak';
    public $timestamps = false;
    protected $fillable = ['kode_rak','nama_rak','lokasi','kapasitas','keterangan'];
    public function keranjangs() { return $this->hasMany(Keranjang::class, 'id_rak', 'id_rak'); }
    public function barangs() { return $this->hasMany(Barang::class, 'id_rak', 'id_rak'); }
}
