<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegDistrict extends Model
{
    protected $table = 'reg_districts';
    protected $primaryKey = 'id';
    public $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
}