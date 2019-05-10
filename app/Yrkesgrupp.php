<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Yrkesgrupp extends Model
{
    protected $table = 'yrkesgrupper';

    protected $guarded = [];

    protected $casts = [
        'yrkesbenamningar' => 'array'
    ];
}