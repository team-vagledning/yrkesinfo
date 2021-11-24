<?php

namespace App\Importers\Bristindex\V1;

use App\Bristindex;
use App\Yrkesgrupp;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class FemArImport implements ToCollection
{
    public const SSYK = 1;
    public const BRISTINDEX = 3;
    public const OMFANG = 5;

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $yrkesgrupper = Yrkesgrupp::where('ssyk', 'like', (string) $row[self::SSYK] . '%')->get();

            // Do nothing if bristindex is non numeric
            if (is_numeric($row[self::BRISTINDEX]) === false) {
                continue;
            }

            foreach ($yrkesgrupper as $yrkesgrupp) {
                Bristindex::updateOrCreate([
                    'region_id' => null,
                    'yrkesgrupp_id' => $yrkesgrupp->id,
                    'omfang' => self::OMFANG,
                ], [
                    'bristindex' => (float) str_replace(',', '.', $row[self::BRISTINDEX]),
                ]);
            }
        }
    }
}
