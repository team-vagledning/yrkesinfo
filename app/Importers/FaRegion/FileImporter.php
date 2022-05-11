<?php

namespace App\Importers\FaRegion;

use App\FaRegion;
use App\Importers\ImporterInterface;
use Maatwebsite\Excel\Facades\Excel;

class FileImporter implements ImporterInterface
{
    public function run()
    {
        FaRegion::truncate();

        $xmlData = simplexml_load_file(storage_path('imports/fa_regioner/fa_regioner_tillvaxtverket.kml'));

        $regioner = [];

        foreach ($xmlData->Document->Folder->Placemark as $region) {


            $faId = (string) (int) (string) $region->ExtendedData->SchemaData->SimpleData[1][0];

            $toInsert =  [
                'external_id' => $faId,
                'name' => self::names()[$faId],
            ];

            $coordsToInsert = [];

            if ($region->MultiGeometry) {
                foreach ($region->MultiGeometry->Polygon as $polygon) {
                    $coords = (string) $polygon->outerBoundaryIs->LinearRing->coordinates;
                    $coords = trim($coords);
                    $coords = explode(" ", $coords);

                    $coords = array_map(function ($coordRow) {
                        [$x, $y] = explode(",", $coordRow);
                        return  [
                            'x' => $x,
                            'y' => $y
                        ];
                    }, $coords);

                    $coordsToInsert[]['koordinater'] = $coords;
                }
            }

            if ($region->Polygon) {
                $coords = (string) $region->Polygon->outerBoundaryIs->LinearRing->coordinates;
                $coords = trim($coords);
                $coords = explode(" ", $coords);

                $coords = array_map(function ($coordRow) {
                    [$x, $y] = explode(",", $coordRow);
                    return  [
                        'x' => $x,
                        'y' => $y
                    ];
                }, $coords);

                $coordsToInsert[]['koordinater'] = $coords;
            }


            $toInsert['grans'] = json_encode([
                'polygoner' => $coordsToInsert
            ]);

            FaRegion::insert($toInsert);

        }
    }

    public static function names() {
        return [
            1 => "Malmö-Lund",
            2 => "Kristianstad-Hässleholm",
            3 => "Karlskrona",
            4 => "Älmhult-Osby",
            5 => "Ljungby",
            6 => "Halmstad",
            7 => "Värnamo",
            8 => "Växjö",
            9 => "Kalmar",
            10 => "Oskarshamn",
            11 => "Västervik",
            12 => "Vimmerby",
            13 => "Jönköping",
            14 => "Borås",
            15 => "Göteborg",
            16 => "Trollhättan-Vänersborg",
            17 => "Lidköping-Götene",
            18 => "Skövde-Skara",
            19 => "Linköping-Norrköping",
            20 => "Gotland",
            21 => "Nyköping-Oxelösund",
            22 => "Eskilstuna",
            23 => "Stockholm",
            24 => "Västerås",
            25 => "Örebro",
            26 => "Karlskoga",
            27 => "Karlstad",
            28 => "Västlandet",
            29 => "Torsby",
            30 => "Malung-Sälen",
            31 => "Vansbro",
            32 => "Ludvika",
            33 => "Avesta-Hedemora",
            34 => "Falun-Borlänge",
            35 => "Mora",
            36 => "Gävle",
            37 => "Bollnäs-Ovanåker",
            38 => "Hudiksvall",
            39 => "Ljusdal",
            40 => "Härjedalen",
            41 => "Östersund",
            42 => "Sundsvall",
            43 => "Kramfors",
            44 => "Örnsköldsvik",
            45 => "Sollefteå",
            46 => "Strömsund",
            47 => "Åsele",
            48 => "Umeå",
            49 => "Lycksele",
            50 => "Vilhelmina",
            51 => "Storuman",
            52 => "Skellefteå",
            53 => "Arvidsjaur",
            54 => "Arjeplog",
            55 => "Luleå",
            56 => "Haparanda",
            57 => "Överkalix",
            58 => "Jokkmokk",
            59 => "Gällivare",
            60 => "Kiruna",
        ];
    }
}
