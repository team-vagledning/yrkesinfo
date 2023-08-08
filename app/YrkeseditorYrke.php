<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class YrkeseditorYrke extends Model
{
    protected $table = 'yrkeseditor_yrken';

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
    ];

    public function scopeWhereSsyk($query, $ssyk)
    {
        return $query->whereRaw("data->'yrkesgrupperOchBenamningar' @> ?", ['[{"ssyk": "' . $ssyk . '"}]']);
    }

    public static function getByNameSimilarity($term, $similarity = 0.3, $with = [])
    {
        $ids = collect(DB::select(DB::raw("
            select id, similarity(lower(name_expl), lower(:term)) as similarity
            from (
                select id, jsonb_array_elements(data->'yrkessynonymer' || jsonb_build_array(data->>'namn'))::text AS name_expl
	            from yrkeseditor_yrken
            ) as y
            where similarity(name_expl, :term) >= :similarity
            order by similarity desc
        "), [
            'term' => $term,
            'similarity' => $similarity
        ]));

        // Inject similarity into the results, could possibly be made with a sql query
        $yrkeseditorYrke = self::whereIn('id', $ids->pluck('id'))->with($with)->get()->each(function ($yrkeseditorYrke) use ($ids) {
            // Sort and get the biggest similarity by id
            $similarity = (float) $ids->sortBy('similarity')->keyBy('id')[$yrkeseditorYrke->id]->similarity;
            $yrkeseditorYrke->similarity = $similarity;
        });

        return $yrkeseditorYrke;
    }

    public static function getByFormagor($term)
    {
        return self::whereExists(function ($query) use ($term) {
            $query->select(DB::raw(1))
                ->from(DB::raw("jsonb_array_elements(data->'formagor'->'detaljer') AS detalj"))
                ->whereRaw("detalj->>'kategori' = ?", [ucfirst($term)]);
        })->get();
    }
}
