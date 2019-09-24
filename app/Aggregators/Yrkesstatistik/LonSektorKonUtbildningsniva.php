<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Yrkesstatistik;

class LonSektorKonUtbildningsniva extends BaseAggregator implements YrkesstatistikAggregatorInterface
{
    use ScbFormatter;

    public $aggregated = [];

    public static function keys()
    {
        return [
            'SEKTOR' => 0,
            'SSYK' => 1,
            'SEX' => 2,
            'UTBILDNINGSNIVA' => 3,
            'YEAR' => 4,
        ];
    }

    public function run(Yrkesstatistik $yrkesstatistik)
    {
        $data = $yrkesstatistik->statistics['data'];

        foreach ($data as $row) {

            $year = self::getYear($row);
            $sex = self::getSex($row);
            $section = self::getSectionName($row);
            $utbildningsniva = self::getUtbildningsniva($row);

            if (!in_array($section, ['samtliga'])) {
                continue;
            }

            //dd($row);

            $value = data_get($row, 'values.0', 0);
            $value = self::value($value, 'viktat-medelvärde', "anstallda.utbildningsniva.{$utbildningsniva}.{$year}.alla");


            self::incValue($this->aggregated, "lon.utbildningsniva.{$utbildningsniva}.alla.medellon", $value);

            //self::incValue($this->aggregated, "anstallda.total.{$year}.konsfordelning.{$sex}", $value);

            /*
            self::incValue($this->aggregated, "anstallda.regioner.{$region}.{$year}.alla", $value);
            self::incValue($this->aggregated, "anstallda.regioner.{$region}.{$year}.konsfordelning.{$sex}", $value);*/
        }

        self::update($yrkesstatistik, $this->aggregated);
    }
}
