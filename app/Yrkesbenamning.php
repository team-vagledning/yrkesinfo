<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Yrkesbenamning extends Model
{
    use SoftDeletes;

    protected $table = 'yrkesbenamningar';

    protected $guarded = [];

    public function yrkesgrupper()
    {
        return $this->belongsToMany(Yrkesgrupp::class, 'yrkesgrupper_has_yrkesbenamningar');
    }

    public static function getByNameSimilarity($term, $similarity = 0.3, $with = [])
    {
        $ids = collect(DB::select(DB::raw("
            select id, similarity(name_expl, :term) as similarity
            from (
                select id, unnest(string_to_array(name, ' ')) as name_expl
	            from yrkesbenamningar
            ) as y
            where similarity(name_expl, :term) >= :similarity
            order by similarity desc
        "), [
            'term' => $term,
            'similarity' => $similarity
        ]));

        // Inject similarity into the results, could possible be made with a sql query
        $yrkesbenamningar = self::whereIn('id', $ids->pluck('id'))->with($with)->get()->each(function ($yrkesbenamningar) use ($ids) {
            $yrkesbenamningar->similarity = (float) $ids->keyBy('id')[$yrkesbenamningar->id]->similarity;
        });

        return $yrkesbenamningar;
    }
}
