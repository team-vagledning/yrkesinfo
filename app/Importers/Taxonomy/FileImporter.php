<?php

namespace App\Importers\Taxonomy;

use App\Importers\CsvObject;
use App\Importers\ImporterInterface;
use League\Csv\Reader;

const REMOTE_FILE = 'https://raw.githubusercontent.com/JobtechSwe/taxonomy-dump/master/taxonomy.csv';
const DELIMITER = '|';

class FileImporter implements ImporterInterface
{
    public function __invoke()
    {
        $this->run();
    }

    public function run()
    {
        return $this->getCsv(REMOTE_FILE, DELIMITER);
    }

    public function getCsv($url, $delimiter): CsvObject
    {
        $content = file_get_contents($url);
        $csv = Reader::createFromString($content)->setDelimiter($delimiter)->setHeaderOffset(0);
        return new CsvObject($csv);
    }
}
