<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'tbl_user';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama_user',
        'username',
        'email',
        'password',
        'foto_profil',
        'hak_akses',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Agar Auth bisa login pakai username ATAU email (1:1 dengan proses_login.php native)
    public function getAuthIdentifierName()
    {
        return 'id_user';
    }

    public function isRole(...$roles): bool
    {
        return in_array($this->hak_akses, $roles);
    }
}
