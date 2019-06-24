<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BristindexYrkesgrupp extends Model

{
    protected $table = 'bristindex_yrkesgrupp';

    protected $guarded = [];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function yrkesgrupp()
    {
        return $this->belongsTo(Yrkesgrupp::class, 'yrkesgrupp_id');
    }

    public function scopeFemAr($query)
    {
        return $query->where('omfang', 5);
    }

    public function scopeEttAr($query)
    {
        return $query->where('omfang', 1);
    }
}