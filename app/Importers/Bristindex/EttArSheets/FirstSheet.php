<?php

namespace App\Importers\Bristindex\EttArSheets;

use App\BristindexYrkesgrupp;
use App\Region;
use App\Yrkesgrupp;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FirstSheet implements ToCollection
{
    public const LAN = 0;
    public const SSYK = 2;
    public const BRISTINDEX = 4;
    public const OMFANG = 1;

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $region = Region::where('external_id', ((int) $row[self::LAN]))->first();
            $yrkesgrupper = Yrkesgrupp::where('ssyk', 'like', (string) $row[self::SSYK] . '%')->get();

            // Do nothing if bristindex is non numeric
            if (is_numeric($row[self::BRISTINDEX]) === false) {
                continue;
            }

            foreach ($yrkesgrupper as $yrkesgrupp) {
                BristindexYrkesgrupp::updateOrCreate([
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
