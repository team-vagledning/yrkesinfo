<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KeywordedYrkesgruppSearch extends Model
{
    protected $table = 'keyworded_yrkesgrupp_searches';

    protected $guarded = [];

    public function yrkesgrupp()
    {
        return $this->belongsTo(Yrkesgrupp::class);
    }
}
