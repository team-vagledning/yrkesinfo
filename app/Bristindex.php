<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bristindex extends Model
{
    protected $table = 'bristindex';

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    public static $ranges = [
        'Saknas' => [0, 1, 0],
        'Hård konkurrens' => [1, 1.99, 3],
        'Liten konkurrens' => [2, 2.9, 2],
        'Mycket liten konkurrens' => [3, 5, 1],
    ];

    public static function bristindexToText(float $bristindex)
    {
        foreach (self::$ranges as $text => $values) {
            if ($bristindex >= $values[0] && $bristindex < $values[1]) {
                return $text;
            }
        }

        return self::$ranges[0];
    }

    public static function mostCommonBristindex(Collection $bristindexes)
    {
        $count = collect(self::$ranges)->mapWithKeys(function ($item, $key) {
            return [$key => 0];
        });

        $bristindexes->each(function ($bristindex) use ($count) {
            $count[self::bristindexToText($bristindex->bristindex)] += 1;
        });

        $mostCommon = $count->sort(function ($a, $b) {
            return $a < $b;
        });

        return [
            'text' => $mostCommon->keys()->first(),
            'count' => $mostCommon->first(),
        ];
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function faRegion()
    {
        return $this->belongsTo(FaRegion::class, 'fa_region_id');
    }

    public function yrkesgrupp()
    {
        return $this->belongsTo(Yrkesgrupp::class, 'yrkesgrupp_id');
    }

    public function scopeFemAr($query)
    {
        return $query->where('omfang', 5);
    }

    public function scopeTreAr($query)
    {
        return $query->where('omfang', 3);
    }

    public function scopeEttAr($query)
    {
        return $query->where('omfang', 1);
    }

    public function scopeRiket($query)
    {
        return $query->where('fa_region_id', null);
    }

    public function scopeMaxArtal($query)
    {
        return $query->where('artal', function($where) {
            $where
                ->select(DB::raw("max(artal)"))
                ->fromRaw("bristindex as j")
                ->whereRaw("j.omfang = bristindex.omfang");
        });
    }
}
