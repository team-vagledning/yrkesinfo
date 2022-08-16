<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kommun extends Model
{
    protected $table = 'kommuner';

    protected $guarded = [];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function faRegion()
    {
        return $this->belongsTo(FaRegion::class);
    }
}
