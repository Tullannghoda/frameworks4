<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Authenticatable
{
    use HasFactory;

    protected $table = 'vendor';
    protected $primaryKey = 'idvendor';

    protected $fillable = [
        'nama_vendor',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function menus()
    {
        return $this->hasMany(Menu::class, 'idvendor', 'idvendor');
    }

    public function pesanans()
    {
        return $this->hasMany(Pesanan::class, 'idvendor', 'idvendor');
    }
}
