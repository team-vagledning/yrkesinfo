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

        $xmlData = simplexml_load_file(storage_path('imports/fa_regioner/fa_regioner.kml'));

        $regioner = [];

        foreach ($xmlData->Document->Folder->Placemark as $region) {

            $toInsert =  [
                'external_id' => (string) $region->ExtendedData->SchemaData->SimpleData[0],
                'name' => (string) $region->ExtendedData->SchemaData->SimpleData[1],
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
}
