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

    public function __construct(EntryFactory $entryFactory)
    {
        $this->factory = $entryFactory->createFactory("Lön", ["Sektor", "Kön", "Utbildningsnivå", "År"]);
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
            ["Samtliga", "?", "Alla", "?"]
        ));

        dd($entries);

        $e = $entries[6];

        $anstallda = $collection->findAllByKeysAndKeyValues(
            ["Anställda", "Utbildningsnivå", "Ålder", "Kön", "År"],
            [$e->getKeyValue("Utbildningsnivå"), "?", "?", $e->getKeyValue("År")]
        );

        dd(Collection::filterEntriesWithValidValue($entries), $withValues);

        dd($e, Collection::sumEntries($entries), $e->getKeyValue("Utbildningsnivå"), $e->getKeyValue("År"));
    }

    public function run(Yrkesstatistik $yrkesstatistik)
    {
        $data = $yrkesstatistik->statistics['data'];

        foreach ($data as $row) {

            $year = self::getAr($row);
            $sex = self::getKon($row);
            $section = self::getSektionName($row);
            $utbildningsniva = self::getUtbildningsniva($row);

            if (!in_array($section, ['samtliga'])) {
                continue;
            }

            //dd($row);


            $value = data_get($row, 'values.0', 0);
            $value = self::value($value, 'viktat-medelvärde', "anstallda.utbildningsniva.{$utbildningsniva}.{$year}.alla");

            if ($sex === "bada" && $section === "samtliga") {
                self::incValue($this->aggregated, "lon.utbildningsniva.{$utbildningsniva}.{$year}.alla.medellon", $value);
            }

            if ($yrkesstatistik->yrkesgrupp_id == 398 && $year == '2018') {
                if ($sex == "bada" && $section == "samtliga") {
                    print "UTB: " . $utbildningsniva . "\n";
                    print "Sektion: " . $section . "\n";
                    print "År: " . $year . "\n";
                    print "Värde: " . $value['varde'] . "\n";
                    print "Aktuellt värde: " . \Arr::get($this->aggregated, "lon.utbildningsniva.{$utbildningsniva}.{$year}.alla.medellon.varde");
                    print "\n\n";
                    print_r($row);
                    print "-------\n\n";
                }
            } else {
                continue;
            }

            //self::incValue($this->aggregated, "anstallda.total.{$year}.konsfordelning.{$sex}", $value);

            /*
            self::incValue($this->aggregated, "anstallda.regioner.{$region}.{$year}.alla", $value);
            self::incValue($this->aggregated, "anstallda.regioner.{$region}.{$year}.konsfordelning.{$sex}", $value);*/
        }

        self::update($yrkesstatistik, $this->aggregated);
    }
}