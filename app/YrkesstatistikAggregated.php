<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YrkesstatistikAggregated extends Model
{
    protected $table = 'yrkesstatistik_aggregated';

    protected $guarded = [];

    protected $casts = [
        'statistics' => 'array'
    ];

    public function yrkesgrupp()
    {
        return $this->belongsTo(Yrkesgrupp::class, 'yrkesgrupp_id');
    }
}
