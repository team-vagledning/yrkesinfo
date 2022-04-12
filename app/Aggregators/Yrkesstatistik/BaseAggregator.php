<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Yrkesstatistik;
use Arr;

abstract class BaseAggregator
{
    protected $weighted = [];

    private function initWeighted(array $keys)
    {
        $keyName = implode('.', $keys);

        if (! array_key_exists($keyName, $this->weighted)) {
            $this->weighted[$keyName] = [
                'keys' => $keys,
                'count' => 0,
                'value' => 0,
                'weighted_value' => 0,
            ];
        }

        return $keyName;
    }

    public function setWeighted(array $keys, $count, $value)
    {
        $keyName = $this->initWeighted($keys);

        $this->weighted[$keyName]['count'] += $count;
        $this->weighted[$keyName]['value'] += $value * $count;

        try {
            if ($count > 0) {
                $this->weighted[$keyName]['weighted_value'] =
                    $this->weighted[$keyName]['value'] / $this->weighted[$keyName]['count'];
            }
        } catch (\ErrorException $e) {
            // Should we do something?
        }
    }

    public function getAllWeighted()
    {
        return collect($this->weighted)->map(function ($weighted) {
            return Arr::only($weighted, ['keys', 'count', 'weighted_value']);
        })->toArray();
    }

    public static function update(Yrkesstatistik $yrkesstatistik, $aggregation)
    {
        $aggregated = $yrkesstatistik->yrkesgrupp->yrkesstatistikAggregated()->firstOrCreate([], [
            'statistics' => []
        ]);

        $aggregated->update([
            'statistics' => array_replace_recursive($aggregated->statistics, $aggregation)
        ]);
    }

    public static function value($value, $strategy, $against = null, $type = 'värde')
    {
        return [
            'typ' => $type,
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
