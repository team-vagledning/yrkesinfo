<?php

namespace App\Importers\Bristindex;

use App\Importers\Bristindex\YrkesgruppSheets\FirstSheet;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class YrkesgruppImport extends DefaultValueBinder implements WithMultipleSheets, WithCustomValueBinder
{
    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value) || gettype($value) === 'double') {

            if (gettype($value) === 'double') {
                //dd($value);
            }

            $cell->setValueExplicit('hej', DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function sheets(): array
    {
        return [
            0 => new FirstSheet()
        ];
    }
}
