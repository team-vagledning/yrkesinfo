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
                $statistics = $yrkesgrupp->yrkesstatistikAggregated()->first()->statistics;
                $keys = self::findVardeKeys($statistics);

                foreach ($keys as $key) {
                    $valueObject = data_get($statistics, $key);

                    dd($key, $yrkesgrupp->name);

                    switch ($valueObject['strategi']) {
                        case 'summera':
                            $value = self::value($valueObject['varde'], 'summera');
                            self::incValue($a, $key, $value);
                            break;
                        default:
                            throw new \Exception("No valid strategy, what to do?");
                    }
                }
            }

            dd(data_get($a, 'anstallda.total.2017.alla'));
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