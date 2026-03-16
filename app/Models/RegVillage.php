<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegVillage extends Model
{
    protected $table = 'reg_villages';
    protected $primaryKey = 'id';
    public $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
}