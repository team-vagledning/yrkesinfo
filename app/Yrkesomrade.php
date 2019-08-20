<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Yrkesomrade extends Model
{
    use HasRelationships;

    protected $table = 'yrkesomraden';

    protected $guarded = [];

    protected $appends = ['bristindex'];

    protected $casts = [
        'aggregated_statistics' => 'array'
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

    public function getBristindexAttribute()
    {
        $femAr = round_number($this->bristindex()->femAr()->avg('bristindex'));
        $ettAr = round_number($this->bristindex()->ettAr()->avg('bristindex'));

        return [
            'fem_ar' => [
                'varde' => $femAr,
                'text' => BristindexYrkesgrupp::bristindexToText($femAr),
            ],
            'ett_ar' => [
                'varde' => $ettAr,
                'text' => BristindexYrkesgrupp::bristindexToText($ettAr),
            ]
        ];
    }
}
