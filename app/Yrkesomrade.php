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
        $femAr = $this->bristindex()->femAr()->get();
        $ettAr = $this->bristindex()->ettAr()->when($regionId, function ($query, $regionId) {
            $query->where('region_id', $regionId);
        })->get();

        $commonFemAr = BristindexYrkesgrupp::mostCommonBristindex($femAr);
        $commonEttAr = BristindexYrkesgrupp::mostCommonBristindex($ettAr);

        $femArValue = BristindexYrkesgrupp::$ranges[$commonFemAr['text']][0];
        $ettArValue = BristindexYrkesgrupp::$ranges[$commonEttAr['text']][0];

        $femArTextToLower = strtolower($commonFemAr['text']);
        $ettArTextToLower = strtolower($commonEttAr['text']);

        $forklarandeFemAr = "Utifrån {$femAr->count()} yrkesprognoser så har {$commonFemAr['count']} st {$femArTextToLower}";
        $forklarandeEttAr = "Utifrån {$ettAr->count()} yrkesprognoser så har {$commonEttAr['count']} st {$ettArTextToLower}";

        return [
            'fem_ar' => [
                'varde' => $femArValue,
                'text' => $commonFemAr['text'],
                'forklarandeText' => $forklarandeFemAr,
            ],
            'ett_ar' => [
                'varde' => $ettArValue,
                'text' => $commonEttAr['text'],
                'forklarandeText' => $forklarandeEttAr,
            ]
        ];
    }
}
