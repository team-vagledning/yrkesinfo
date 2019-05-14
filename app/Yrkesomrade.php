<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Yrkesomrade extends Model
{
    protected $table = 'yrkesomraden';

    protected $guarded = [];

    public function yrkesgrupper()
    {
        return $this->belongsToMany(Yrkesgrupp::class, 'yrkesomraden_has_yrkesgrupper');
    }

    public function scopeTaxonomyId($query, $optionalId)
    {
        return $query->whereSource('Arbetsförmedlingen')->whereOptionalId($optionalId);
    }
}