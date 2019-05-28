<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Yrkesstatistik;

class AnstalldaLanKon extends BaseAggregator implements YrkesstatistikAggregatorInterface
{
    use ScbFormatter;

    public $aggregated = [];

    public static function keys()
    {
        return [
            'REGION' => 0,
            'SSYK' => 1,
            'SEX' => 2,
            'YEAR' => 3,
        ];
    }

    public function run(Yrkesstatistik $yrkesstatistik)
    {
        $data = $yrkesstatistik->statistics['data'];

        foreach ($data as $row) {
            $region = self::getRegionName($row);
            $year = self::getYear($row);
            $sex = self::getSex($row);

            $value = data_get($row, 'values.0', 0);
            $value = self::value($value, 'summera');

            self::incValue($this->aggregated, "anstallda.total.{$year}.alla", $value);
            self::incValue($this->aggregated, "anstallda.total.{$year}.konsfordelning.{$sex}", $value);

            self::incValue($this->aggregated, "anstallda.regioner.{$region}.{$year}.alla", $value);
            self::incValue($this->aggregated, "anstallda.regioner.{$region}.{$year}.konsfordelning.{$sex}", $value);
        }

        self::update($yrkesstatistik, $this->aggregated);
    }
}
