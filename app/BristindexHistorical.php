<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BristindexHistorical extends Model
{
    protected $table = 'bristindex_historical';

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
    ];

    public function yrkesgrupp()
    {
        return $this->belongsTo(Yrkesgrupp::class, 'yrkesgrupp_id');
    }
}
