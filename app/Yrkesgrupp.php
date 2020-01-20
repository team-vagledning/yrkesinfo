<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Yrkesgrupp extends Model
{
    protected $table = 'yrkesgrupper';

    protected $guarded = [];

    protected $casts = [
        'alternative_ssyk' => 'array',
        'aggregated_statistics' => 'array'
    ];

    public function yrkesomraden()
    {
        return $this->belongsToMany(Yrkesomrade::class, 'yrkesomraden_has_yrkesgrupper');
    }

    public function yrkesbenamningar()
    {
        return $this->belongsToMany(Yrkesbenamning::class, 'yrkesgrupper_has_yrkesbenamningar');
    }

    public function yrkesstatistik()
    {
        return $this->hasMany(Yrkesstatistik::class, 'yrkesgrupp_id');
    }

    public function yrkesstatistikAggregated()
    {
        return $this->hasOne(YrkesstatistikAggregated::class, 'yrkesgrupp_id');
    }

    public function bristindex()
    {
        return $this->hasMany(BristindexYrkesgrupp::class, 'yrkesgrupp_id');
    }

    public function alternativeSsykOrOriginal()
    {
        if (empty($this->alternative_ssyk) === false) {
            return $this->alternative_ssyk;
        }

        return $this->ssyk;
    }
}
