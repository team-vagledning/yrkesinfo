<?php

namespace App\Importers;

class CsvObject extends \stdClass
{
    public function __construct($csv)
    {
        $this->header = $csv->getHeader();
        $this->records = $csv->getRecords();
    }
}