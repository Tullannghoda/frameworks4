<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'email',
        'telepon',
        'alamat',
        'foto_blob',
        'foto_path',
    ];

    /**
     * Ambil URL foto: prioritaskan foto_path, fallback ke blob, fallback ke placeholder
     */
    public function getFotoUrlAttribute(): string
    {
        if ($this->foto_path) {
            return asset('storage/' . $this->foto_path);
        }

        if ($this->foto_blob) {
            return $this->foto_blob; // sudah berupa data URL base64
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama) . '&background=random';
    }
}
