<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';
    protected $primaryKey = 'id_penjualan';
    public $timestamps = false;
    protected $fillable = [
        'timestamp',
        'total'
    ];
}