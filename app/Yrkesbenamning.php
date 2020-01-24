<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Yrkesbenamning extends Model
{
    use SoftDeletes;

    protected $table = 'yrkesbenamningar';

    protected $guarded = [];

    public function yrkesgrupper()
    {
        return $this->belongsToMany(Yrkesgrupp::class, 'yrkesgrupper_has_yrkesbenamningar');
    }
}
