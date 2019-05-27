<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Yrkesstatistik;

class AnstalldaSektorKon implements YrkesstatistikAggregatorInterface
{
    use ScbMapper;

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

    public function run(Yrkesstatistik $yrkesstatistik)
    {
        $data = $yrkesstatistik->statistics['data'];

        foreach ($data as $row) {
            $sector = self::getSectionName($row);
            $year = self::getYear($row);
            $sex = self::getSex($row);

            $value = data_get($row, 'values.0', 0);

            // TODO: Maybe add as Offentlig vs Privat sektor
            //data_inc($this->aggregated, "anstallda.{$year}.total", $value);
            //data_inc($this->aggregated, "anstallda.{$year}.konsfordelning.{$sex}", $value);

            data_inc($this->aggregated, "sektor.{$sector}.anstallda.{$year}.total", $value);
            data_inc($this->aggregated, "sektor.{$sector}.anstallda.{$year}.konsfordelning.{$sex}", $value);


        }

        dd($this->aggregated);
    }

}
