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

    public function _scopeLatestPerSourceAndYrkesgrupp($query)
    {
        return $query->whereIn('id', function ($query) {
            $query
                ->select(\DB::raw('max(id)'))
                ->from($this->table)
                ->groupBy('yrkesstatistik_source_id', 'yrkesgrupp_id');
        });
    }

    public function scopeLatestPerSourceAndYrkesgrupp($query, Yrkesgrupp $yrkesgrupp)
    {
        return $query->whereIn('id', function ($query) use ($yrkesgrupp) {
            $query
                ->select(\DB::raw('max(id)'))
                ->from($this->table)
                ->where('yrkesgrupp_id', $yrkesgrupp->id)
                ->groupBy('yrkesstatistik_source_id');
        });
    }
}
