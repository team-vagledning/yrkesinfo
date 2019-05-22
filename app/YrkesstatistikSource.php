<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YrkesstatistikSource extends Model
{
    protected $table = 'yrkesstatistik_sources';

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array'
    ];

    public function yrkesstatistik()
    {
        return $this->hasMany(Yrkesstatistik::class, 'yrkesstatistik_source_id');
    }
}