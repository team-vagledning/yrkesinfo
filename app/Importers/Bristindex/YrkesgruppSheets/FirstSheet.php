<?php

namespace App\Importers\Bristindex\YrkesgruppSheets;

use App\BristindexYrkesgrupp;
use App\Region;
use App\Yrkesgrupp;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FirstSheet implements WithHeadingRow, ToCollection
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $region = Region::where('external_id', ((int) $row['lan']))->first();
            $yrkesgrupper = Yrkesgrupp::where('ssyk', 'like', (string) $row['ssyk'] . '%')->get();

            dd($row);

            if (is_numeric($row['18h_om_1_ar']) === false) {
                continue;
            }

            foreach ($yrkesgrupper as $yrkesgrupp) {
                BristindexYrkesgrupp::updateOrCreate([
                    'region_id' => $region->id,
                    'yrkesgrupp_id' => $yrkesgrupp->id
                ], [
                    'bristindex' => (float) str_replace(',', '.', $row['18h_om_1_ar'])
                ]);
            }

            print $row['18h_om_1_ar'];
            print PHP_EOL;
        }
    }
}
