<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineArea extends Model
{
    protected $fillable = [
        'name',
        'code',
        'department',
        'description',
        'status',
    ];
}