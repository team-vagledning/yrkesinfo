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

        $femAr = $this->bristindex()->femAr()->maxArtal()->get();
        $ettAr = $this->bristindex()->ettAr()->maxArtal()->when($regionId, function ($query, $regionId) {
            $query->where('region_id', $regionId);
        })->get();

        $commonFemAr = BristindexYrkesgrupp::mostCommonBristindex($femAr);
        $commonEttAr = BristindexYrkesgrupp::mostCommonBristindex($ettAr);

        $femArValue = BristindexYrkesgrupp::$ranges[$commonFemAr['text']][0];
        $femArValueInverted = BristindexYrkesgrupp::$ranges[$commonFemAr['text']][2];

        $ettArValue = BristindexYrkesgrupp::$ranges[$commonEttAr['text']][0];
        $ettArValueInverted = BristindexYrkesgrupp::$ranges[$commonEttAr['text']][2];

        $femArTextToLower = strtolower($commonFemAr['text']);
        $ettArTextToLower = strtolower($commonEttAr['text']);

        $forklarandeFemAr = "Utifrån {$femAr->count()} yrkesprognoser så har {$commonFemAr['count']} st {$femArTextToLower}";
        $forklarandeEttAr = "Utifrån {$ettAr->count()} yrkesprognoser så har {$commonEttAr['count']} st {$ettArTextToLower}";

        $res = [
            'fem_ar' => [
                'varde' => $femArValue,
                'konkurrensVarde' => $femArValueInverted,
                'text' => $commonFemAr['text'],
                'forklarandeText' => $forklarandeFemAr,
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

        $ettAr = $this->bristindex()->ettAr()->maxArtal()->get();
        $femAr = $this->bristindex()->femAr()->maxArtal()->get();

        if (count($ettAr)) {
            $res[] = [
                'omfang' => 1,
                'varde' => (float) $ettAr->countBy('bristindex')->sort()->keys()->last(),
                'artal' => $ettAr->first()->artal,
                'antalPrognoser' => $ettAr->count(),
            ];
        }

        if (count($femAr)) {
            $res[] = [
                'omfang' => 5,
                'varde' => (float) $femAr->countBy('bristindex')->sort()->keys()->last(),
                'artal' => $femAr->first()->artal,
                'antalPrognoser' => $femAr->count(),
            ];
        }

        cache()->set($cacheKey, $res, now()->addDay());

        return $res;
    }
}
