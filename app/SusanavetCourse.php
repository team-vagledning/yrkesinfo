<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SusanavetCourse extends Model
{
    protected $table = 'susanavet_courses';

    protected $casts = [
        'data' => 'array',
    ];

    protected $guarded = [];

    public function yrkesgrupper()
    {
        return $this->belongsTo(Yrkesgrupp::class);
    }
}
