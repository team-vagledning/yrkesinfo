<?php

namespace App\Importers\Sunkoder;

use App\BristindexYrkesgrupp;
use App\Sunkod;
use App\Yrkesgrupp;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class KopplingsschemaImport implements ToCollection, WithStartRow
{
    public const SSYK = 1;
    public const SUNKOD = 4;

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $sunkoder = explode(",", $row[self::SUNKOD]);
            $yrkesgrupp = Yrkesgrupp::whereSsykOrAlternativeSsyk($row[self::SSYK])->first();

            if (!$yrkesgrupp) {
                echo "Could not find yrkesgrupp with SSYK: {$row[self::SSYK]}\n";
                continue;
            }

            foreach ($sunkoder as $sunkod) {
                $sunkod = trim($sunkod);

                if (empty($sunkod)) {
                    continue;
                }

                $sunkod = Sunkod::firstOrCreate(['kod' => $sunkod]);
                $sunkod->yrkesgrupper()->syncWithoutDetaching($yrkesgrupp);
            }
        }
    }
}
