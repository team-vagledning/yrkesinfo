<?php

namespace App\Importers\Bristindex\V3;

use App\Bristindex;
use App\BristindexGrouping;
use App\BristindexHistorical;
use App\FaRegion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithStartRow;

class HistoricData implements ToCollection, WithStartRow, WithCustomCsvSettings
{
    public const ARTAL = 0;

    public const EFTERFRAGA = 1;
    public const PENSION = 2;
    public const TILLTRADE = 3;

    public const EFTERFRAGA_PROGNOS = 4;
    public const PENSION_PROGNOS = 5;
    public const TILLTRADE_PROGNOS = 6;

    public const CONCEPT_ID = 7;


    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {

            if (empty($row[self::ARTAL])) {
                continue;
            }

            $grouping = BristindexGrouping::where('external_id', '=', (string) $row[self::CONCEPT_ID])
                ->with('yrkesgrupper')->first();

            foreach ($grouping->yrkesgrupper as $yrkesgrupp) {

                $data = [
                    'efterfraga' => floatval(round_number(str_replace(',', '.', $row[self::EFTERFRAGA]))),
                    'pension' => floatval(round_number(str_replace(',', '.', $row[self::PENSION]))),
                    'tilltrade' => floatval(round_number(str_replace(',', '.', $row[self::TILLTRADE]))),

                    'efterfraga_prognos' => floatval(round_number(str_replace(',', '.', $row[self::EFTERFRAGA_PROGNOS]))),
                    'pension_prognos' => floatval(round_number(str_replace(',', '.', $row[self::PENSION_PROGNOS]))),
                    'tilltrade_prognos' => floatval(round_number(str_replace(',', '.', $row[self::TILLTRADE_PROGNOS]))),
                ];

                BristindexHistorical::updateOrCreate([
                    'yrkesgrupp_id' => $yrkesgrupp->id,
                    'artal' => $row[self::ARTAL],
                ], [
                    'data' => json_encode($data)
                ]);
            }
        }
    }

}
