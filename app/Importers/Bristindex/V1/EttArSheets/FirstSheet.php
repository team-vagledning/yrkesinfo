<?php

namespace App\Importers\Bristindex\V1\EttArSheets;

use App\Bristindex;
use App\Region;
use App\Yrkesgrupp;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class FirstSheet implements ToCollection, WithStartRow
{
    public const LAN = 0;
    public const SSYK = 2;
    public const BRISTINDEX = 4;
    public const OMFANG = 1;

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $regionName = substr($row[self::LAN], stripos($row[self::LAN], ' ') + 1);

            $region = Region::where('name', $regionName)->first();
            $yrkesgrupper = Yrkesgrupp::where('ssyk', 'like', (string) $row[self::SSYK] . '%')->get();

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
