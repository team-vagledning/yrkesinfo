<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sunkod extends Model
{
    protected $table = 'sunkoder';

    protected $guarded = [];

    public function yrkesgrupper()
    {
        return $this->belongsToMany(Yrkesgrupp::class, 'yrkesgrupper_has_sunkoder');
    }
}
