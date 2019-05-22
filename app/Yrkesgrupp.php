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

    public function yrkesomraden()
    {
        return $this->belongsToMany(Yrkesomrade::class, 'yrkesomraden_has_yrkesgrupper');
    }

    public function yrkesstatistik()
    {
        return $this->hasMany(Yrkesstatistik::class, 'yrkesgrupp_id');
    }
}