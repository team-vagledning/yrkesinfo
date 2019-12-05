<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Modules\Yrkesstatistik\Collection;
use App\Modules\Yrkesstatistik\Entry;
use App\Modules\Yrkesstatistik\EntryFactory;
use App\Yrkesstatistik;

class LonSektorKonUtbildningsniva extends BaseAggregator implements YrkesstatistikAggregatorInterface
{
    use ScbFormatter;

    public $factory;
    public $weightedFactory;

    public function __construct()
    {
        $this->factory = (new EntryFactory())->createFactory("Lön", ["Sektor", "Kön", "Utbildningsnivå", "År"]);
        $this->weightedFactory = (new EntryFactory())->createFactory("Lön", ["Utbildningsnivå", "Viktat", "År"]);
    }

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

    public function firstRun(Yrkesstatistik $yrkesstatistik, Collection $collection)
    {
        $data = $yrkesstatistik->statistics['data'];

        foreach ($data as $row) {
            $year = self::getAr($row);
            $sex = self::getKon($row);
            $section = self::getSektionName($row);
            $utbildningsniva = self::getUtbildningsniva($row);

            $entry = $this->factory->makeEntry(
                [$section, $sex, $utbildningsniva, $year],
                "Total",
                data_get($row, 'values.0', 0)
            );

            $collection->addEntry($entry);
        }

        return true;
    }

    public function lastRun(Yrkesstatistik $yrkesstatistik, Collection $collection)
    {
        $entries = Collection::filterEntriesWithValidValue($collection->findAllByKeysAndKeyValues(
            ["Lön", "Sektor", "Kön", "Utbildningsnivå", "År"],
            ["Samtliga", "Alla", "?", "?"]
        ));

        foreach ($entries as $e) {
            $anstallda = $collection->findAllByKeysAndKeyValues(
                ["Anställda", "Utbildningsnivå", "Ålder", "Kön", "År"],
                [$e->getKeyValue("Utbildningsnivå"), "?", "?", $e->getKeyValue("År")]
            );

            $simpleUtbildningsniva = $this->getSimpleUtbildningnivaFromUtbildningsniva(
                $e->getKeyValue('Utbildningsnivå')
            );

            $this->setWeighted(
                [$simpleUtbildningsniva, $e->getKeyValue('År')],
                Collection::sumEntries($anstallda),
                $e->getValue()
            );

        }

        foreach ($this->getAllWeighted() as $weighted) {
            [$utbildningsniva, $ar] = $weighted['keys'];
            $weightedValue = round_number($weighted['weighted_value'], 0);

            $entry = $this->weightedFactory->makeEntry(
                [$utbildningsniva, "Ja", $ar],
                "Total",
                $weightedValue
            );

            $collection->addEntry($entry);
        }
    }
}
