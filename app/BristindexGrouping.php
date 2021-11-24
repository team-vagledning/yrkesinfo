<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BristindexGrouping extends Model
{
    use SoftDeletes;

    protected $table = 'bristindex_groupings';

    protected $guarded = [];

    public function yrkesgrupper()
    {
        return $this->belongsToMany(Yrkesgrupp::class, 'bristindex_groupings_has_yrkesgrupper');
    }
}
