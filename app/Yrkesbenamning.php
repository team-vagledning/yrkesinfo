<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Yrkesbenamning extends Model
{
    protected $table = 'yrkesbenamningar';

    protected $guarded = [];

    public function yrkesgrupper()
    {
        return $this->belongsToMany(Yrkesgrupp::class, 'yrkesgrupper_has_yrkesbenamningar');
    }
}
