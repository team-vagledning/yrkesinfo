<?php

namespace App\Importers\Kommun;

use App\Importers\ImporterInterface;
use Maatwebsite\Excel\Facades\Excel;

class FileImporter implements ImporterInterface
{
    public function run()
    {
        Excel::import(new KommunImport, storage_path('imports/kommuner/kommuner.xlsx'));
    }
}
