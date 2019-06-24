<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Yrkesomrade extends Model
{
    use HasRelationships;

    protected $table = 'yrkesomraden';

    protected $guarded = [];

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

    public function bristindex()
    {
        return $this->hasManyDeep(BristindexYrkesgrupp::class, ['yrkesomraden_has_yrkesgrupper', Yrkesgrupp::class]);
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
}