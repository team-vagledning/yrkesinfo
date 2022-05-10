<?php

namespace App\Importers\Bristindex\V3\Sheets;

use App\Bristindex;
use App\BristindexGrouping;
use App\FaRegion;
use App\Region;
use App\Yrkesgrupp;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class FirstSheet implements ToCollection, WithStartRow
{
    public const CONCEPT_ID = 0;
    public const FA_REGION = 1;
    public const BRISTINDEX_1_YEAR = 2;
    public const BRISTINDEX_5_YEAR = 3;
    public const NUM_YRKESVERKSAMMA = 4;
    public const NUM_KVINNOR = 5;
    public const NUM_MAN = 6;
    public const SPECIAL = 7;
    public const TILLTRADE = 8;
    public const PENSION = 9;
    public const EFTERFRAGA = 10;

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $region = FaRegion::where('external_id', $row[self::FA_REGION])->first();
            $groupings = BristindexGrouping::where('external_id', '=', (string) $row[self::CONCEPT_ID])->get();

            dd($groupings);

            // Do nothing if bristindex is non numeric
            if (is_numeric($row[self::BRISTINDEX]) === false) {
                continue;
            }

            foreach ($yrkesgrupper as $yrkesgrupp) {
                Bristindex::updateOrCreate([
                    'region_id' => $region->id,
                    'yrkesgrupp_id' => $yrkesgrupp->id,
                    'omfang' => self::OMFANG,
                ], [
                    'bristindex' => (float) str_replace(',', '.', $row[self::BRISTINDEX]),
                ]);
            }
        }
    }
}
