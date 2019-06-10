<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Yrkesstatistik;

class LonSektorKon extends BaseAggregator implements YrkesstatistikAggregatorInterface
{
    use ScbFormatter;

    const AVARAGE = 0;
    const PERCENTILE_10 = 1;
    const PERCENTILE_90 = 2;


    public $aggregated = [];

    public static function keys()
    {
        return [
            'SEKTOR' => 0,
            'SSYK' => 1,
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


            // lon.total.2017.alla.medel
            // lon.total.2017.alla.10-percentilen
            // {
            //      typ: "värde",
            //      varde: 1000
            //      strategi: "viktat-medelvärde"
            //      mot: "anstallda.total.2017.alla"
            // }

            $value = data_get($row, 'values.' . self::AVARAGE, 0);
            $value = self::value($value, 'viktat-medelvärde', 'test');

            dd($value);

            self::incValue($this->aggregated, "anstallda.sektor.{$sector}.{$year}.alla", $value);
            self::incValue($this->aggregated, "anstallda.sektor.{$sector}.{$year}.konsfordelning.{$sex}", $value);
        }

        self::update($yrkesstatistik, $this->aggregated);
    }

}
