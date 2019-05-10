<?php

namespace Tests\Importers\Taxonomy;

use App\Importers\Taxonomy\FileImporter;
use Tests\TestCase;

class FileImporterTest extends TestCase
{
    public function testHeader()
    {
        $header = app(FileImporter::class)->parseFile(FileImporter::REMOTE_FILE)->getHeader();
        $this->assertEquals('occupationFieldId', $header[0]);
    }

    public function testTransform()
    {
        $importer = app(FileImporter::class);

        $records = [
            [
                'ssyk' => 1,
                'ssykTerm' => 'Foo name',
                'ssykDescription' => 'Foo description',
                'occupationNameTerm' => 'Foo worker',
            ],
            [
                'ssyk' => 1,
                'ssykTerm' => 'Foo name',
                'ssykDescription' => 'Foo description',
                'occupationNameTerm' => 'Foo chief',
            ]
        ];

        $expected = [
            'ssyk' => 1,
            'name' => 'Foo name',
            'description' => 'Foo description',
            'yrkesbenamningar' => ['Foo worker', 'Foo chief'],
        ];

        $importer->setRecords($records)->transformRecords();
        $this->assertEquals($importer->getRecords()[0], $expected);
    }
}