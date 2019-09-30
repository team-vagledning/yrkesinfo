<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Modules\Yrkesstatistik\Collection;
use App\Yrkesstatistik;

class AnstalldaUtbildningsnivaKon extends BaseAggregator implements YrkesstatistikAggregatorInterface
{
    use ScbFormatter;

    public $aggregated = [];

    public static function keys()
    {
        return [
            'SSYK' => 0,
            'UTBILDNINGSNIVA' => 1,
            'AGE' => 2,
            'SEX' => 3,
            'YEAR' => 4,
        ];
    }

    public function firstRun(Yrkesstatistik $yrkesstatistik, Collection $collection)
    {
        // TODO: Implement firstRun() method.
    }

    public function lastRun(Yrkesstatistik $yrkesstatistik, Collection $collection)
    {
        // TODO: Implement lastRun() method.
    }

    public function run(Yrkesstatistik $yrkesstatistik)
    {

        $data = $yrkesstatistik->statistics['data'];

        foreach ($data as $row) {
            $year = self::getAr($row);
            $sex = self::getKon($row);
            $utbildningsniva = self::getUtbildningsniva($row);

            $value = data_get($row, 'values.0', 0);
            $value = self::value($value, 'summera');

            self::incValue($this->aggregated, "anstallda.utbildningsniva.{$utbildningsniva}.{$year}.alla", $value);
            self::incValue($this->aggregated, "anstallda.utbildningsniva.{$utbildningsniva}.{$year}.konsfordelning.{$sex}", $value);

            /*self::incValue($this->aggregated, "anstallda.total.{$year}.konsfordelning.{$sex}", $value);

            self::incValue($this->aggregated, "anstallda.regioner.{$region}.{$year}.alla", $value);
            self::incValue($this->aggregated, "anstallda.regioner.{$region}.{$year}.konsfordelning.{$sex}", $value);*/
        }

        self::update($yrkesstatistik, $this->aggregated);
    }
}
