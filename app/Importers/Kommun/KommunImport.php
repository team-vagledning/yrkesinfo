<?php

namespace App\Importers\Kommun;

use App\Bristindex;
use App\FaRegion;
use App\Kommun;
use App\Region;
use App\Sunkod;
use App\Yrkesgrupp;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class KommunImport implements ToCollection, WithStartRow
{
    public const FA_REGION = 0;
    public const KOMMUN_KOD = 2;
    public const KOMMUN_NAMN = 3;
    public const REGION = 4;


    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $faRegionNo = ltrim($row[self::FA_REGION], 0);
            $faRegion = FaRegion::where('external_id', $faRegionNo)->firstOrFail();
            $region = Region::where('name', $row[self::REGION])->firstOrFail();

            Kommun::updateOrCreate([
                'external_id' => $row[self::KOMMUN_KOD],
                'region_id' => $region->id,
                'fa_region_id' => $faRegion->id,
                'name' => $row[self::KOMMUN_NAMN],
            ], [
                'external_id' => $row[self::KOMMUN_KOD],
            ]);
        }
    }
}
