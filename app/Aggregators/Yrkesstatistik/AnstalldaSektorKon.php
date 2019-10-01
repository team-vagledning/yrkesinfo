<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Modules\Yrkesstatistik\Collection;
use App\Modules\Yrkesstatistik\EntryFactory;
use App\Yrkesstatistik;

class AnstalldaSektorKon extends BaseAggregator implements YrkesstatistikAggregatorInterface
{
    use ScbFormatter;

    public $factory;

    public function __construct(EntryFactory $entryFactory)
    {
        $this->factory = $entryFactory->createFactory("Anställda", ["Sektor", "Kön", "År"]);
    }

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
        $data = $yrkesstatistik->statistics['data'];

        foreach ($data as $row) {
            $sector = self::getSektionName($row);
            $year = self::getAr($row);
            $sex = self::getKon($row);
            $value = data_get($row, 'values.0', 0);

            $entry = $this->factory->makeEntry(
                [$sector, $sex, $year],
                $value,
                "Total"
            );

            $collection->addEntry($entry);
        }
    }

    public function lastRun(Yrkesstatistik $yrkesstatistik, Collection $collection)
    {
        $entries = $collection->findAllByKeysAndKeyValues(["Anställda", "Sektor", "Kön", "År"], ["?", "?", "?", "2017"]);

        $years = $collection->getUniqueKeyValuesByKeys(["Anställda", "Sektor", "Kön", "År"])['År'];

        dd($years);

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
