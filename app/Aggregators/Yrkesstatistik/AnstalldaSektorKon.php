<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Modules\Yrkesstatistik\Collection;
use App\Yrkesstatistik;

class AnstalldaSektorKon extends BaseAggregator implements YrkesstatistikAggregatorInterface
{
    use ScbFormatter;

    public $aggregated = [];

    public static function keys()
    {
        return [
            'SSYK' => 0,
            'SEKTOR' => 1,
            'SEX' => 2,
            'YEAR' => 3,
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
            $sector = self::getSektionName($row);
            $year = self::getAr($row);
            $sex = self::getKon($row);

            $value = data_get($row, 'values.0', 0);
            $value = self::value($value, 'summera');

            self::incValue($this->aggregated, "anstallda.sektor.{$sector}.{$year}.alla", $value);
            self::incValue($this->aggregated, "anstallda.sektor.{$sector}.{$year}.konsfordelning.{$sex}", $value);
        }

        self::update($yrkesstatistik, $this->aggregated);
    }

}
