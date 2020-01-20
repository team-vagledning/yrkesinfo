<?php

namespace Tests\Importers\Taxonomy;

use App\Importers\Taxonomy\ApiImporter;
use App\Importers\Taxonomy\Mappings\YrkesomradeMapper;
use Tests\TestCase;

class ApiImporterTest extends TestCase
{
    public function createYrkesomradeResponse($id, $name, $description)
    {
        $yrkesomrade = new YrkesomradeMapper();
        $yrkesomrade->LocaleFieldID = $id;
        $yrkesomrade->Term = $name;
        $yrkesomrade->Description = $description;

        return $yrkesomrade;
    }

    /**
     * TODO
     */
    public function _testInsertingYrkesomrade()
    {
        $yrkesomrade = $this->createYrkesomradeResponse(1, "Foo", "My Foo");

        $importer = app(ApiImporter::class);
        $importer->yrkesomraden = collect([$yrkesomrade]);

    }
}
