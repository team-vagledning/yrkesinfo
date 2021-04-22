<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Yrkesgrupp extends Model
{
    use SoftDeletes;

    protected $table = 'yrkesgrupper';

    protected $guarded = [];

    protected $casts = [
        'alternative_ssyk' => 'array',
        'aggregated_statistics' => 'array'
    ];

    public function yrkesomraden()
    {
        return $this->belongsToMany(Yrkesomrade::class, 'yrkesomraden_has_yrkesgrupper');
    }

    public function yrkesbenamningar()
    {
        return $this->belongsToMany(Yrkesbenamning::class, 'yrkesgrupper_has_yrkesbenamningar');
    }

    public function yrkesstatistik()
    {
        return $this->hasMany(Yrkesstatistik::class, 'yrkesgrupp_id');
    }

    public function yrkesstatistikAggregated()
    {
        return $this->hasOne(YrkesstatistikAggregated::class, 'yrkesgrupp_id');
    }

    public function bristindex()
    {
        return $this->hasMany(BristindexYrkesgrupp::class, 'yrkesgrupp_id');
    }

    public function alternativeSsykOrOriginal()
    {
        if (empty($this->alternative_ssyk) === false) {
            return $this->alternative_ssyk;
        }

        return $this->ssyk;
    }

    public static function getByNameSimilarity($term, $similarity = 0.3)
    {
        $ids = collect(DB::select(DB::raw("
            select id, similarity(name_expl, :term) as similarity
            from (
                select id, unnest(string_to_array(name, ' ')) as name_expl
	            from yrkesgrupper
            ) as y
            where similarity(name_expl, :term) >= :similarity
            order by similarity desc
        "), [
            'term' => $term,
            'similarity' => $similarity
        ]));

        // Inject similarity into the results, could possible be made with a sql query
        $yrkesgrupper = self::whereIn('id', $ids->pluck('id'))->get()->each(function ($yrkesgrupp) use ($ids) {
            $yrkesgrupp->similarity = (float) $ids->keyBy('id')[$yrkesgrupp->id]->similarity;
        });

        return $yrkesgrupper;
    }
}
