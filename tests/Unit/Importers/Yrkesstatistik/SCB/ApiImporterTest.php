<?php

namespace Tests\Importers\Yrkesstatisik\SCB;

use App\Importers\Yrkesstatistik\SCB\ApiImporter;
use Tests\TestCase;

class ApiImporterTest extends TestCase
{
    public function testGetQueryKey()
    {
        $queries = [
            'toFind' => [
                'code' => 'MyKey'
            ],
            'bad' => [
                'code' => 'FooBar'
            ]
        ];

        $key = ApiImporter::getQueryKey($queries, 'MyKey');

        $this->assertEquals('query.toFind.selection.values', $key);
    }

    public function testValidStatistics()
    {
        $valid = [
            'columns' => [
                ['code' => 'Yrke2012'],
                ['code' => 'FooBar']
            ]
        ];

        $invalid = [
            'columns' => [
                ['code' => 'ZooBar'],
                ['code' => 'FooBar']
            ]
        ];

        $this->assertTrue(ApiImporter::validStatistics($valid));
        $this->assertFalse(ApiImporter::validStatistics($invalid));
    }
}