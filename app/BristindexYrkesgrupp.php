<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BristindexYrkesgrupp extends Model
{
    protected $table = 'bristindex_yrkesgrupp';

    protected $guarded = [];

    public static $ranges = [
        'Saknas' => [0, 1],
        'Väldigt hård konkurrens' => [1, 1.99],
        'Hård konkurrens' => [1.99, 2.9],
        'Måttlig konkurrens' => [2.9, 3.3],
        'Liten konkurrens' => [3.3, 4],
        'Väldigt liten konkurrens' => [4, 5.1],
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

    public function yrkesgrupp()
    {
        return $this->belongsTo(Yrkesgrupp::class, 'yrkesgrupp_id');
    }

    public function scopeFemAr($query)
    {
        return $query->where('omfang', 5);
    }

    public function scopeEttAr($query)
    {
        return $query->where('omfang', 1);
    }
}
