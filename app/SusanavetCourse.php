<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SusanavetCourse extends Model
{
    use SoftDeletes;

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
