<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Yrkesstatistik;

abstract class BaseAggregator
{
    public static function update(Yrkesstatistik $yrkesstatistik, $aggregation)
    {
        $aggregated = $yrkesstatistik->yrkesgrupp->yrkesstatistikAggregated()->firstOrCreate([], [
            'statistics' => []
        ]);

        $aggregated->update([
            'statistics' => array_replace_recursive($aggregated->statistics, $aggregation)
        ]);
    }

    public static function value($value, $strategy, $against = '')
    {
        return [
            'typ' => 'värde',
            'varde' => is_numeric($value) ? $value : 0,
            'strategi' => $strategy,
            'mot' => $against,
        ];
    }

    public static function incValue(&$target, $key, $value)
    {
        if (data_get($target, $key, false) === false) {
            data_set($target, $key, $value);
            data_set($target, "{$key}.varde", 0);
        }

        data_inc($target, "{$key}.varde", $value['varde']);
    }
}