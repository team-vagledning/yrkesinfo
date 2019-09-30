<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Modules\Yrkesstatistik\Collection;
use App\Modules\Yrkesstatistik\EntryFactory;
use App\Yrkesstatistik;

class AnstalldaLanKon extends BaseAggregator implements YrkesstatistikAggregatorInterface
{
    use ScbFormatter;

    public $aggregated = [];
    public $factory;

    public function __construct(EntryFactory $entryFactory)
    {
        $this->factory = $entryFactory->createFactory("Anställda", ["Län", "Kön", "År"]);
    }

    public static function keys()
    {
        return [
            'REGION' => 0,
            'SSYK' => 1,
            'SEX' => 2,
            'YEAR' => 3,
        ];
    }

    public function firstRun(Yrkesstatistik $yrkesstatistik, Collection $collection)
    {
        $data = $yrkesstatistik->statistics['data'];

        foreach ($data as $row) {
            $region = self::getRegionName($row);
            $year = self::getAr($row);
            $sex = self::getKon($row);

            self::incValue($this->aggregated, "anstallda.total.{$year}.alla", $value);

            self::incValue($this->aggregated, "anstallda.total.{$year}.konsfordelning.{$sex}", $value);

            self::incValue($this->aggregated, "anstallda.regioner.{$region}.{$year}.alla", $value);
            self::incValue($this->aggregated, "anstallda.regioner.{$region}.{$year}.konsfordelning.{$sex}", $value);

            $entry = $this->factory->makeEntry(
                [$region, $sex, $year],
                data_get($row, 'values.0', 0),
                "Total"
            );

            $collection->addEntry($entry);
        }
    }

    public function lastRun(Yrkesstatistik $yrkesstatistik, Collection $collection)
    {
        // TODO: Implement lastRun() method.

        $collection->findAllByKeys()
    }

    public function run(Yrkesstatistik $yrkesstatistik)
    {
        $data = $yrkesstatistik->statistics['data'];

        foreach ($data as $row) {
            $region = self::getRegionName($row);
            $year = self::getAr($row);
            $sex = self::getKon($row);

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
