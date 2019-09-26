<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Modules\Yrkesstatistik\Entry;
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

    public function firstRun(Yrkesstatistik $yrkesstatistik)
    {
        $data = $yrkesstatistik->statistics['data'];

        $this->aggregated = ['entries' => []];

        foreach ($data as $row) {

            $year = self::getYear($row);
            $sex = self::getSex($row);
            $section = self::getSectionName($row);
            $utbildningsniva = self::getUtbildningsniva($row);

            $entry = new Entry('Lön');

            $entry->addKey('Sektor', $section);
            $entry->addKey('Kön', $sex);
            $entry->addKey('Utbilningsnivå', $utbildningsniva);
            $entry->addKey('År', $year);
            $entry->addValue(data_get($row, 'values.0', 0));

            $this->aggregated['entries'][] = $entry->toArray();

        }

        self::update($yrkesstatistik, $this->aggregated);
        return $this->aggregated;
    }

    public function lastRun(Yrkesstatistik $yrkesstatistik)
    {
        dd("Andra");
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
