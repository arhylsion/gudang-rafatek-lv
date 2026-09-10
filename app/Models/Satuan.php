<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    protected $table = 'tbl_satuan';
    protected $primaryKey = 'id_satuan';
    public $timestamps = false;
    protected $fillable = ['nama_satuan'];
    public function barangs() { return $this->hasMany(Barang::class, 'satuan', 'id_satuan'); }
}
