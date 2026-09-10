<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SerialInventory extends Model
{
    protected $table = 'tbl_serial_inventory';
    protected $fillable = ['id_barang','serial_number','status','reserved_for_transaction','used_in_transaction'];
    public function barang() { return $this->belongsTo(Barang::class, 'id_barang', 'id_barang'); }
}
