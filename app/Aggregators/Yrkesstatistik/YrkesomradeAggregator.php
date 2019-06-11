<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Yrkesomrade;
use App\YrkesstatistikAggregated;
use Arr;
use Str;

class YrkesomradeAggregator extends BaseAggregator
{
    public function run()
    {
        //
        // Hur lösa medelvärden och viktade medelvärden?
        //

        $yrkesomraden = Yrkesomrade::take(1)->get();

        foreach ($yrkesomraden as $yrkesomrade) {
            $a = [];
            foreach ($yrkesomrade->yrkesgrupper as $yrkesgrupp) {
                $statistics = $yrkesgrupp->yrkesstatistikAggregated()->first();

                $keys = self::findVardeKeys($statistics->statistics);

                foreach ($keys as $key) {
                    $valueObject = data_get($statistics->statistics, $key);

                    if (is_null($valueObject)) {
                        continue;
                    }

                    switch ($valueObject['strategi']) {
                        case 'summera':
                            $value = self::value($valueObject['varde'], 'summera');
                            self::incValue($a, $key, $value);
                            break;
                        default:
                            throw new \Exception("No valid strategy, what to do? Strategy: {$valueObject['strategi']}");
                    }
                }
            }
            
        }

    }

    public static function findVardeKeys($input)
    {
        return collect(Arr::dot($input))->filter(function ($value, $key) {
            return $value === 'värde' && Str::endsWith($key, 'typ');
        })->keys()->map(function ($key) {
            // Remove .typ from the key
            return substr($key, 0, -4);
        });
    }
}
