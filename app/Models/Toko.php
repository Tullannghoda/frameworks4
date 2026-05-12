<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $table = 'toko';

    protected $fillable = [
        'barcode',
        'nama_toko',
        'alamat',
        'latitude',
        'longitude',
        'accuracy',
    ];

    public function kunjungans()
    {
        return $this->hasMany(Kunjungan::class);
    }
}
