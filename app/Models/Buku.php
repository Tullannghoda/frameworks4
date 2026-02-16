<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kategori;

class Buku extends Model
{
    protected $fillable = [
        'kategori_id',
        'kode',
        'judul',
        'pengarang'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}
