<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    protected $table = 'tbl_jenis';
    protected $primaryKey = 'id_jenis';
    public $timestamps = false;
    protected $fillable = ['nama_jenis'];
    public function barangs() { return $this->hasMany(Barang::class, 'jenis', 'id_jenis'); }
}
