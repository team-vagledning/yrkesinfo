<?php

namespace App\Importers\Bristindex\V3;

use App\Bristindex;
use App\BristindexGrouping;
use App\FaRegion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CurrentData implements ToCollection, WithStartRow, WithCustomCsvSettings
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

    public const PIL_2022_TILLTRADE = 11;
    public const PIL_2026_TILLTRADE = 12;
    public const PIL_2022_PENSION = 13;
    public const PIL_2026_PENSION = 14;
    public const PIL_2022_EFTERFRAGA = 15;
    public const PIL_2026_EFTERFRAGA = 16;
    public const INDEX = 17;
    public const SVARARE = 18;
    public const LATTARE = 19;

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
        // Clear bristindex, we don't care as Analysteamet can't deliver
        Bristindex::truncate();

        foreach ($collection as $row) {
            $faRegion = null;
            if ($row[self::FA_REGION] > 0) {
                $faRegion = FaRegion::where('external_id', $row[self::FA_REGION])->first();
            }

            $grouping = BristindexGrouping::where('external_id', '=', (string) $row[self::CONCEPT_ID])
                ->with('yrkesgrupper')->first();

            $oneYearBristindex = $row[self::BRISTINDEX_1_YEAR];
            $threeYearBristindex = $row[self::BRISTINDEX_5_YEAR];

            $svarareFaRegion = null;
            if (is_numeric($row[self::SVARARE])) {
                $svarareFaRegion = FaRegion::where('external_id', $row[self::SVARARE])->first();
            }

            $lattareFaRegion = null;
            if (is_numeric($row[self::SVARARE])) {
                $lattareFaRegion = FaRegion::where('external_id', $row[self::LATTARE])->first();
            }


            $meta = [
                'yrkesverksamma' => $row[self::NUM_YRKESVERKSAMMA],
                'andel_kvinnor' => $row[self::NUM_KVINNOR],
                'andel_man' => $row[self::NUM_MAN],
                'special' => $row[self::SPECIAL],
                'tilltrade' => $row[self::TILLTRADE],
                'pension' => $row[self::PENSION],
                'efterfraga' => $row[self::EFTERFRAGA],

                'pil_2022_tilltrade' => floatval(round_number(str_replace(',', '.', $row[self::PIL_2022_TILLTRADE]))),
                'pil_2026_tilltrade' => floatval(round_number(str_replace(',', '.', $row[self::PIL_2026_TILLTRADE]))),

                'pil_2022_pension' => floatval(round_number(str_replace(',', '.', $row[self::PIL_2022_PENSION]))),
                'pil_2026_pension' => floatval(round_number(str_replace(',', '.', $row[self::PIL_2026_PENSION]))),

                'pil_2022_efterfraga' => floatval(round_number(str_replace(',', '.', $row[self::PIL_2022_EFTERFRAGA]))),
                'pil_2026_efterfraga' => floatval(round_number(str_replace(',', '.', $row[self::PIL_2026_EFTERFRAGA]))),

                'index' => $row[self::INDEX],
                'svarare_fa_region_id' => $svarareFaRegion ? $svarareFaRegion->id : null,
                'lattare_fa_region_id' => $lattareFaRegion ? $lattareFaRegion->id : null,
            ];

            foreach ($grouping->yrkesgrupper as $yrkesgrupp) {
                // Copy from 0 fa region
                if ($faRegion !== null) {
                    $parentOneYear = $yrkesgrupp->bristindex()->riket()->ettAr()->first();
                    $parentThreeYear = $yrkesgrupp->bristindex()->riket()->treAr()->first();

                    $oneYearBristindex = $parentOneYear->bristindex;
                    $threeYearBristindex = $parentThreeYear->bristindex;

                    $meta = array_merge($meta, [
                        'yrkesverksamma' => $parentOneYear->meta['yrkesverksamma'],
                        'andel_kvinnor' => $parentOneYear->meta['andel_kvinnor'],
                        'andel_man' => $parentOneYear->meta['andel_man'],
                        'special' => $parentOneYear->meta['special'],
                        'tilltrade' => $parentOneYear->meta['tilltrade'],
                        'pension' => $parentOneYear->meta['pension'],
                        'efterfraga' => $parentOneYear->meta['efterfraga'],
                    ]);
                }

                $bristindexInsert = [
                    'fa_region_id' => $faRegion ? $faRegion->id : null,
                    'yrkesgrupp_id'=> $yrkesgrupp->id,
                    'meta' => json_encode($meta),
                ];

                $oneYear = self::makeYear(
                    $bristindexInsert,
                    1,
                    '2023',
                    $oneYearBristindex
                );

                $threeYear = self::makeYear(
                    $bristindexInsert,
                    3,
                    '2026',
                    $threeYearBristindex
                );

                Bristindex::insert([$oneYear, $threeYear]);
            }
        }
    }

    private static function makeYear($toInsert, $omfang, $artal, $bristindex)
    {
        return array_merge($toInsert, [
            'omfang' => $omfang,
            'artal' => $artal,
            'bristindex' => $bristindex,
        ]);
    }

}
