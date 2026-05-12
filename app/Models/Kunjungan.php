<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    protected $table = 'kunjungan';

    protected $fillable = [
        'toko_id',
        'nama_sales',
        'latitude_sales',
        'longitude_sales',
        'accuracy_sales',
        'jarak_meter',
        'threshold_efektif',
        'diterima',
        'threshold',
    ];

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }
}