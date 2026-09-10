<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Opname extends Model
{
    protected $table = 'tbl_opname';
    protected $primaryKey = 'id_opname';
    protected $fillable = ['id_barang','stok_sistem','stok_fisik','id_petugas','keterangan','status','tanggal_opname'];
    protected $casts = ['tanggal_opname' => 'datetime'];
    public function barang() { return $this->belongsTo(Barang::class, 'id_barang', 'id_barang'); }
    public function petugas() { return $this->belongsTo(User::class, 'id_petugas', 'id_user'); }
    public function getSelisihAttribute() { return $this->stok_fisik - $this->stok_sistem; }
}
