<?php

namespace App\Importers\Bristindex\V1;

use App\Importers\Bristindex\EttArSheets\FirstSheet;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class EttArImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            0 => new FirstSheet()
        ];
    }
}
