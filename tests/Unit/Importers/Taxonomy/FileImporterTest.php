<?php

namespace Tests\Importers\Taxonomy;

use App\Importers\Taxonomy\FileImporter;
use Tests\TestCase;

class FileImporterTest extends TestCase
{
    public function testHeader()
    {
        $csv = app(FileImporter::class)->run();
        $this->assertEquals('occupationFieldId', $csv->header[0]);
    }
}