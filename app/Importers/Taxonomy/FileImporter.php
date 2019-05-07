<?php

namespace App\Importers\Taxonomy;

use App\Exceptions\RemoteException;
use App\Importers\ImporterInterface;

use Exception;

const REMOTE_FILE = 'https://raw.githubusercontent.com/JobtechSwe/taxonomy-dump/master/taxonomy.cv';

class FileImporter implements ImporterInterface
{
    public function __invoke()
    {
        $this->fetchFile();
    }

    private function fetchFile()
    {
        $fileContent = file_get_contents(REMOTE_FILE);

        return $fileContent;
    }
}
