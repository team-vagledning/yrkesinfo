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
                [$sector, $sex, $year], "Total", $value
            );

            // Sum for both sexes
            $sumSexes = $this->factory->findOrMakeEntry($collection, [
                $sector,
                ScbFormatter::$kon['1+2'],
                $year
            ]);

            // Sum for both sexes and simple sektion
            $sumAll = $this->factory->findOrMakeEntry($collection, [
                self::getSektionName($row, true),
                ScbFormatter::$kon['1+2'],
                $year
            ]);

            // Update the sum
            $sumSexes->setValue($sumSexes->getValue() + $value);
            $sumAll->setValue($sumAll->getValue() + $value);

            $collection->addEntry($entry);
            $collection->addEntry($sumSexes, true);
            $collection->addEntry($sumAll, true);
        }
    }

    public function lastRun(Yrkesstatistik $yrkesstatistik, Collection $collection)
    {
        //$years = $collection->getUniqueKeyValuesByKeys(["Anställda", "Sektor", "Kön", "År"])['År'];

        // TODO: Aggregera till Offentlig och Privat
    }

}
