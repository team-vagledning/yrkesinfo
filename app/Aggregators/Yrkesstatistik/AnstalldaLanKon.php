<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Modules\Yrkesstatistik\Collection;
use App\Modules\Yrkesstatistik\EntryFactory;
use App\Yrkesstatistik;

class AnstalldaLanKon extends BaseAggregator implements YrkesstatistikAggregatorInterface
{
    use ScbFormatter;

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
            $value = data_get($row, 'values.0', 0);

            // Make entry from row
            $entry = $this->factory->makeEntry(
                [$region, $sex, $year],
                $value,
                "Total"
            );

            // Make for summing, for the whole country
            $summingEntry = $this->factory->findOrMakeEntry($collection, [
                ScbFormatter::$regioner['00'],
                ScbFormatter::$kon['1+2'],
                $year
            ]);

            // Update the sum
            $summingEntry->setValue($summingEntry->getValue() + $value);

            $collection->addEntry($entry);
            $collection->addEntry($summingEntry, true);
        }
    }

    public function lastRun(Yrkesstatistik $yrkesstatistik, Collection $collection)
    {
        $entries = $collection->findAllByKeysAndKeyValues(["Anställda", "Län", "Kön", "År"], ["?", "?", "?", "2017"]);

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
