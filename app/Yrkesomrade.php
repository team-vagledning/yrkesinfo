<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Yrkesomrade extends Model
{
    use HasRelationships, SoftDeletes;

    protected $table = 'yrkesomraden';

    protected $guarded = [];

    protected $appends = ['bristindex'];

    protected $casts = [
        'aggregated_statistics' => 'array',
        'extras' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function yrkesgrupper()
    {
        return $this->belongsToMany(Yrkesgrupp::class, 'yrkesomraden_has_yrkesgrupper');
    }

    /**
     * @return \Staudenmeir\EloquentHasManyDeep\HasManyDeep
     */
    public function bristindex()
    {
        return $this->hasManyDeepFromRelations($this->yrkesgrupper(), (new Yrkesgrupp)->bristindex());
    }

    public function bristindexGroupings()
    {
        return $this->hasManyDeepFromRelations($this->yrkesgrupper(), (new Yrkesgrupp)->bristindexGroupings());
    }

    public function texts()
    {
        return $this->morphMany(Text::class, 'ref');
    }

    /**
     * Find Yrkesomrade from Arbetsförmedlingen Taxonomy
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $optionalId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromArbetsformedlingenByExternalId($query, $optionalId)
    {
        return $query->whereSource('Arbetsförmedlingen')->whereExternalId($optionalId);
    }

    public function getBristindexes($regionId = false)
    {
        $cacheKey = "yrkesomrade.bristindex.{$this->id}";

        if (cache()->has($cacheKey)) {
            return cache()->get($cacheKey);
        }

        $res = [];

        /**
         * Fetch all groupings, and only get one yrkesgrupp out of each
         */
        $groupings = $this->bristindexGroupings()->distinct()->with('yrkesgrupper')->get();
        $yrkesgrupper = $groupings->map(function ($grouping) {
            return $grouping->yrkesgrupper()->has('bristindex')->first();
        })->pluck('id');

        $treAr = $this->bristindex()->riket()->treAr()->whereIn('bristindex.yrkesgrupp_id', $yrkesgrupper)->maxArtal()->get();
        $ettAr = $this->bristindex()->riket()->ettAr()->whereIn('bristindex.yrkesgrupp_id', $yrkesgrupper)->maxArtal()->when($regionId, function ($query, $regionId) {
            $query->where('region_id', $regionId);
        })->get();

        $commonTreAr = Bristindex::mostCommonBristindex($treAr);
        $commonEttAr = Bristindex::mostCommonBristindex($ettAr);

        $treArValue = Bristindex::$ranges[$commonTreAr['text']][0];
        $treArValueInverted = Bristindex::$ranges[$commonTreAr['text']][2];

        $ettArValue = Bristindex::$ranges[$commonEttAr['text']][0];
        $ettArValueInverted = Bristindex::$ranges[$commonEttAr['text']][2];

        $treArTextToLower = strtolower($commonTreAr['text']);
        $ettArTextToLower = strtolower($commonEttAr['text']);

        $forklarandeTreAr = "Utifrån {$treAr->count()} yrkesprognoser så har {$commonTreAr['count']} st {$treArTextToLower}";
        $forklarandeEttAr = "Utifrån {$ettAr->count()} yrkesprognoser så har {$commonEttAr['count']} st {$ettArTextToLower}";

        $res = [
            'tre_ar' => [
                'varde' => $treArValue,
                'konkurrensVarde' => $treArValueInverted,
                'text' => $commonTreAr['text'],
                'forklarandeText' => $forklarandeTreAr,
            ],
            'ett_ar' => [
                'varde' => $ettArValue,
                'konkurrensVarde' => $ettArValueInverted,
                'text' => $commonEttAr['text'],
                'forklarandeText' => $forklarandeEttAr,
            ]
        ];

        cache()->set($cacheKey, $res, now()->addDay());

        return $res;
    }

    public function getYrkesprognoser()
    {
        $cacheKey = "yrkesomrade.yrkesprognoser.{$this->id}";

        if (cache()->has($cacheKey)) {
            return cache()->get($cacheKey);
        }

        $res = [];

        /**
         * Fetch all groupings, and only get one yrkesgrupp out of each
         */
        $groupings = $this->bristindexGroupings()->distinct()->with('yrkesgrupper')->get();
        $yrkesgrupper = $groupings->map(function ($grouping) {
            return $grouping->yrkesgrupper()->has('bristindex')->first();
        })->pluck('id');

        $ettAr = $this->bristindex()->riket()->ettAr()->maxArtal()->whereIn('bristindex.yrkesgrupp_id', $yrkesgrupper)->get();
        $treAr = $this->bristindex()->riket()->treAr()->maxArtal()->whereIn('bristindex.yrkesgrupp_id', $yrkesgrupper)->get();

        if (count($ettAr)) {
            $res[] = [
                'omfang' => 1,
                'varde' => (float) $ettAr->countBy('bristindex')->sort()->keys()->last(),
                'artal' => $ettAr->first()->artal,
                'antalPrognoser' => $ettAr->count(),
            ];
        }

        if (count($treAr)) {
            $res[] = [
                'omfang' => 5,
                'varde' => (float) $treAr->countBy('bristindex')->sort()->keys()->last(),
                'artal' => $treAr->first()->artal,
                'antalPrognoser' => $treAr->count(),
            ];
        }

        cache()->set($cacheKey, $res, now()->addDay());

        return $res;
    }
}
