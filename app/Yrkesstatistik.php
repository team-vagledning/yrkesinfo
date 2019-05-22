<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Yrkesstatistik extends Model
{
    protected $table = 'yrkesstatistik';

    protected $guarded = [];

    protected $casts = [
        'statistics' => 'array'
    ];

    public function source()
    {
        return $this->belongsTo(YrkesstatistikSource::class, 'yrkesstatistik_source_id');
    }

    public function yrkesgrupp()
    {
        return $this->belongsTo(Yrkesgrupp::class, 'yrkesgrupp_id');
    }
}