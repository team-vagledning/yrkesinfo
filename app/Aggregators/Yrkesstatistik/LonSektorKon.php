<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Modules\Yrkesstatistik\Collection;
use App\Modules\Yrkesstatistik\EntryFactory;
use App\Yrkesstatistik;

class LonSektorKon extends BaseAggregator implements YrkesstatistikAggregatorInterface
{
    use ScbFormatter;

    const AVERAGE = 0;
    const PERCENTILE_10 = 1;
    const PERCENTILE_90 = 2;

    public $factory;

    public function __construct(EntryFactory $entryFactory)
    {
        $this->factory = $entryFactory->createFactory("Lön", ["Sektor", "Kön", "År"]);
    }

    public static function keys()
    {
        return [
            'SEKTOR' => 0,
            'SSYK' => 1,
            'SEX' => 2,
            'YEAR' => 3,
        ];
    }

    public function firstRun(Yrkesstatistik $yrkesstatistik, Collection $collection)
    {
        $data = $yrkesstatistik->statistics['data'];

        foreach ($data as $row) {
            $sector = self::getSektionName($row);
            $year = self::getAr($row);
            $sex = self::getKon($row);
            $value = data_get($row, 'values.' . self::AVERAGE, 0);
            $valuePercentile10 = data_get($row, 'values.' . self::PERCENTILE_10, 0);
            $valuePercentile90 = data_get($row, 'values.' . self::PERCENTILE_90, 0);

            // Make entries
            $entries = $this->factory->makeEntries([
                [[$sector, $sex, $year], $value, "Medel"],
                [[$sector, $sex, $year], $valuePercentile10, "MedelPercentile10"],
                [[$sector, $sex, $year], $valuePercentile90, "MedelPercentile90"],
            ]);

            $collection->addEntries($entries);
        }
    }

    public function lastRun(Yrkesstatistik $yrkesstatistik, Collection $collection)
    {
        // TODO: Kolla hur datan kommer från SCB... de verkar redan räknat ut totalen etc
    }

    public function run(Yrkesstatistik $yrkesstatistik)
    {
        $data = $yrkesstatistik->statistics['data'];

        foreach ($data as $row) {
            $sector = self::getSektionName($row);
            $year = self::getAr($row);
            $sex = self::getKon($row);

            // lon.total.2017.alla.medel
            // lon.total.2017.alla.10-percentilen
            // {
            //      typ: "värde",
            //      varde: 1000
            //      strategi: "viktat-medelvärde"
            //      mot: "anstallda.total.2017.alla"
            // }

            $avarageSalary = data_get($row, 'values.' . self::AVERAGE, 0);
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
