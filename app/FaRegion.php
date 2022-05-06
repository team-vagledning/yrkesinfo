<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaRegion extends Model
{
    protected $table = 'fa_regioner';

    protected $guarded = [];

    protected $casts = [
        'grans' => 'array',
    ];
}
