<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Yrkesstatistik;

class AnstalldaLanKon implements YrkesstatistikAggregatorInterface
{
    use ScbMapper;

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

            data_inc($this->aggregated, "anstallda.{$year}.total", $value);
            data_inc($this->aggregated, "anstallda.{$year}.konsfordelning.{$sex}", $value);

            data_inc($this->aggregated, "regioner.{$region}.anstallda.{$year}.total", $value);
            data_inc($this->aggregated, "regioner.{$region}.anstallda.{$year}.konsfordelning.{$sex}", $value);


        }

        dd($this->aggregated);
    }

}
