<?php

namespace App\Importers;

class CsvObject extends \stdClass
{
    public function __construct($csv = false)
    {
        $this->init();

        if ($csv)  {
            $this->set($csv);
        }
    }

    public function init()
    {
        $this->header = [];
        $this->records = [];
    }

    public function set($csv)
    {
        $this->header = $csv->getHeader();
        $this->records = $csv->getRecords();
    }
}