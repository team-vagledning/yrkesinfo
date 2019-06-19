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

            $avarageSalary = data_get($row, 'values.' . self::AVARAGE, 0);
            $avarageSalary = self::value($avarageSalary, 'viktat-medelvärde', "anstallda.total.{$year}");

            $avarageSalaryPercentile10 = data_get($row, 'values.' . self::PERCENTILE_10, 0);
            $avarageSalaryPercentile10 = self::value($avarageSalaryPercentile10, 'viktat-medelvärde', "anstallda.total.{$year}");


            $avarageSalaryPercentile90 = data_get($row, 'values.' . self::PERCENTILE_90, 0);
            $avarageSalaryPercentile90 = self::value($avarageSalaryPercentile90, 'viktat-medelvärde', "anstallda.total.{$year}");


            if ($sex === "bada") {
                self::incValue($this->aggregated, "lon.sektor.{$sector}.{$year}.alla.medellon", $avarageSalary);
                self::incValue($this->aggregated, "lon.sektor.{$sector}.{$year}.alla.percentil10", $avarageSalaryPercentile10);
                self::incValue($this->aggregated, "lon.sektor.{$sector}.{$year}.alla.percentil90", $avarageSalaryPercentile90);
            } else {
                self::incValue($this->aggregated, "lon.sektor.{$sector}.{$year}.konsfordelning.{$sex}.medellon", $avarageSalary);
                self::incValue($this->aggregated, "lon.sektor.{$sector}.{$year}.konsfordelning.{$sex}.percentil10", $avarageSalaryPercentile10);
                self::incValue($this->aggregated, "lon.sektor.{$sector}.{$year}.konsfordelning.{$sex}.percentil90", $avarageSalaryPercentile90);
            }
        }

        self::update($yrkesstatistik, $this->aggregated);
    }

}
