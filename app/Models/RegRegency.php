<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegRegency extends Model
{
    protected $table = 'reg_regencies';
    protected $primaryKey = 'id';
    public $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
}