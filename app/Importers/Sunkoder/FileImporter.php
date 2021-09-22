<?php

namespace App\Importers\Sunkoder;

use App\Importers\ImporterInterface;
use Maatwebsite\Excel\Facades\Excel;

class FileImporter implements ImporterInterface
{
    public function run()
    {
        Excel::import(new KopplingsschemaImport, storage_path('imports/sunkoder/kopplingsschema.xlsx'));
    }
}
