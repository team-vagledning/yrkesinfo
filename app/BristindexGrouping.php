<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BristindexGrouping extends Model
{
    use SoftDeletes;

    protected $table = 'bristindex_groupings';

    protected $guarded = [];

    public function yrkesgrupper()
    {
        return $this->belongsToMany(Yrkesgrupp::class, 'bristindex_groupings_has_yrkesgrupper');
    }

    public static function getByNameSimilarity($term, $similarity = 0.3)
    {
        $ids = collect(DB::select(DB::raw("
            select id, similarity(name_expl, :term) as similarity
            from (
                select id, unnest(string_to_array(name, ' ')) as name_expl
	            from bristindex_groupings
            ) as y
            where similarity(name_expl, :term) >= :similarity
            order by similarity desc
        "), [
            'term' => $term,
            'similarity' => $similarity
        ]));

        // Inject similarity into the results, could possible be made with a sql query
        $bristindexGroupings = self::whereIn('id', $ids->pluck('id'))->get()->each(function ($bristindexGrouping) use ($ids) {
            $bristindexGrouping->similarity = (float) $ids->keyBy('id')[$bristindexGrouping->id]->similarity;
        });

        return $bristindexGroupings;
    }
}
