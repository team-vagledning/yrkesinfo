<?php

namespace Tests\Modules\Yrkesstatistik;

use App\Modules\Yrkesstatistik\Collection;
use App\Modules\Yrkesstatistik\Entry;
use App\Modules\Yrkesstatistik\EntryFactory;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testFindByKeys()
    {
        $collection = app(Collection::class);

        $entryFactory1 = app(EntryFactory::class);
        $entryFactory1->createFactory("Lön", ["Kön", "År"]);

        $entryFactory2 = app(EntryFactory::class);
        $entryFactory2->createFactory("Lön", ["Utbildningsnivå", "År"]);

        $collection->addEntries([
            $entryFactory1->makeEntry(["Man", 2000], "Total", 10),
            $entryFactory1->makeEntry(["Kvinna", 2001], "Total", 10),
            $entryFactory2->makeEntry(["Gymnasiet", 2000], "Total", 10),
        ]);

        $queryKeys = ["Lön", "Kön", "År"];

        $searchedEntries = $collection->findAllByKeys($queryKeys);
        $searchedEntry = $collection->findFirstByKeys($queryKeys);

        $this->assertCount(2, $searchedEntries);
        $this->assertInstanceOf(Entry::class, $searchedEntry);
    }

    public function testFindByMultipleKeyValueOptions()
    {
        $collection = app(Collection::class);

        $factory = app(EntryFactory::class)->createFactory("Lön", ["Sektion", "År"]);

        $a1 = $factory->makeEntry(["A", 2000], "Total", 0);
        $b1 = $factory->makeEntry(["B", 2000], "Total", 0);
        $b2 = $factory->makeEntry(["B", 2001], "Total", 0);
        $c1 = $factory->makeEntry(["C", 2001], "Total", 0);
        $d1 = $factory->makeEntry(["D", 2002], "Total", 0);


        $collection->addEntries([$a1, $b1, $b2, $c1, $d1]);

        // One should still work
        $e = $collection->findAllByKeysAndKeyValues(["Lön", "Sektion", "År"], ["A", 2000]);
        $this->assertTrue(in_array($a1, $e));

        // Try two
        $e = $collection->findAllByKeysAndKeyValues(["Lön", "Sektion", "År"], [["A", "B"], 2000]);

        $this->assertTrue(in_array($a1, $e));
        $this->assertTrue(in_array($b1, $e));
    }

    public function testGetUniqueKeyValuesByKeys()
    {
        $collection = app(Collection::class);

        $factory = app(EntryFactory::class)->createFactory("Lön", ["Sektion", "År"]);

        $collection->addEntries([
            $factory->makeEntry(["A", 2000], "Total", 0),
            $factory->makeEntry(["B", 2000], "Total", 0),
            $factory->makeEntry(["B", 2001], "Total", 0),
            $factory->makeEntry(["C", 2001], "Total", 0),
            $factory->makeEntry(["D", 2002], "Total", 0),
        ]);

        $expected = [
            "Lön" => ["base"],
            "Sektion" => ["A", "B", "C", "D"],
            "År" => [2000, 2001, 2002],
        ];

        $this->assertEquals($expected, $collection->getUniqueKeyValuesByKeys(["Lön", "Sektion", "År"]));
    }

    public function testPossibleKeyValuePairs()
    {
        $collection = app(Collection::class);

        $keyValue = [["A", "B"], "2000", ["1", "2", "3"], ["Foo"]];

        $possibleKeyValuePairs = [
            ["A", "2000", "1", "Foo"],
            ["A", "2000", "2", "Foo"],
            ["A", "2000", "3", "Foo"],
            ["B", "2000", "1", "Foo"],
            ["B", "2000", "2", "Foo"],
            ["B", "2000", "3", "Foo"],
        ];

        $results = $collection->possibleKeyValuePairs($keyValue);

        $this->assertEquals($possibleKeyValuePairs, $results);
    }

    public function testFindingByKeyAndKeyValues()
    {
        $collection = app(Collection::class);
        $entryFactory = app(EntryFactory::class)->createFactory("Anställda", ["Sektion", "År"]);

        $collection->addEntries([
            $entryFactory->makeEntry(["Offentligt", "2019"], "Total", 1000),
            $entryFactory->makeEntry(["Privat", "2019"], "Total", 500),
            $entryFactory->makeEntry(["Okänt", "2019"], "Total", 600),
        ]);

        $entry = $collection->findFirstByKeysAndKeyValues(["Anställda", "Sektion", "År"], ["Privat", "2019"]);

        $this->assertEquals(500, $entry->getValue());
    }

    public function testFindingByKeyAndKeyValuesAndValueType()
    {
        $collection = app(Collection::class);
        $entryFactory = app(EntryFactory::class)->createFactory("Anställda", ["Sektion", "År"]);

        $collection->addEntries([
            $entryFactory->makeEntry(["Offentligt", "2019"], "Total", 1000),
            $entryFactory->makeEntry(["Privat", "2019"], "Medel", 500),
            $entryFactory->makeEntry(["Okänt", "2019"], "Total", 600),
        ]);

        $entry = $collection->findFirstByKeysAndKeyValues(["Anställda", "Sektion", "År"], ["?", "?"], "Medel");

        $this->assertEquals(500, $entry->getValue());
    }

    public function testFindingByKeyAndKeyValuesWithUnknowns()
    {
        $collection = app(Collection::class);
        $entryFactory = app(EntryFactory::class)->createFactory("Anställda", ["Sektion", "År"]);

        $collection->addEntries([
            $entryFactory->makeEntry(["Offentligt", "2019"], "Total", 1000),
            $entryFactory->makeEntry(["Offentligt", "2018"], "Total", 900),
            $entryFactory->makeEntry(["Offentligt", "2017"], "Total", 800),
        ]);

        $entries = $collection->findAllByKeysAndKeyValues(["Anställda", "Sektion", "År"], ["?", "2018"]);

        $this->assertCount(1, $entries);
        $this->assertEquals(900, $entries[0]->getValue());
    }

    public function testRemovingUnknownsFromArray()
    {
        $testArray = ["A", "B", "?", "D", "?", "F"];
        $expectedArray = ["A", "B", "D", "F"];

        $this->assertEquals($expectedArray, app(Collection::class)->removeUnknownKeys($testArray));
    }

    public function testUpdatingEntry()
    {
        $collection = app(Collection::class);
        $entryFactory = app(EntryFactory::class)->createFactory("Anställda", ["Sektion", "År"]);

        $collection->addEntries([
            $entryFactory->makeEntry(["Offentligt", "2019"], "Total", 1000),
            $entryFactory->makeEntry(["Privat", "2019"], "Total", 500),
            $entryFactory->makeEntry(["Okänt", "2019"], "Total", 600),
        ]);

        // Find a specific entry
        $entry = $collection->findFirstByKeysAndKeyValues(["Anställda", "Sektion", "År"], ["Privat", "2019"]);

        // Update the value
        $entry->setValue(1200);

        // Find the same entry, and check value
        $_entry = $collection->findFirstByKeysAndKeyValues(["Anställda", "Sektion", "År"], ["Privat", "2019"]);
        $this->assertEquals(1200, $_entry->getValue());
    }

    public function testReplacingEntry()
    {
        $collection = app(Collection::class);
        $entryFactory = app(EntryFactory::class)->createFactory("Anställda", ["Sektion", "År"]);

        $firstEntry = $entryFactory->makeEntry(["Offentligt", "2019"], "Total", 1000);
        $secondEntry = $entryFactory->makeEntry(["Offentligt", "2019"], "Total", 1500);

        $collection->addEntry($firstEntry, true);

        $entry = $collection->findFirstByKeysAndKeyValues(["Anställda", "Sektion", "År"], ["Offentligt", "2019"]);
        $this->assertEquals(1000, $entry->getValue());

        $collection->addEntry($secondEntry, true);

        $entry = $collection->findFirstByKeysAndKeyValues(["Anställda", "Sektion", "År"], ["Offentligt", "2019"]);
        $this->assertEquals(1500, $entry->getValue());
    }

    public function testSumEntries()
    {
        $collection = app(Collection::class);
        $entryFactory = app(EntryFactory::class)->createFactory("Anställda", ["Sektion", "År"]);

        $collection->addEntries([
            $entryFactory->makeEntry(["Offentligt", "2019"], "Total", 1000),
            $entryFactory->makeEntry(["Privat", "2019"], "Total", 500),
            $entryFactory->makeEntry(["Okänt", "2019"], "Total", 600),
        ]);

        $entries = $collection->findAllByKeys(["Anställda", "Sektion", "År"]);

        $this->assertEquals(2100, $collection->sumEntries($entries));
    }

    public function testToArray()
    {
        $collection = app(Collection::class);
        $entryFactory = app(EntryFactory::class)->createFactory("Anställda", ["Sektion", "År"]);

        $collection->addEntries([
            $entryFactory->makeEntry(["Offentligt", "2019"], "Total", 1000),
        ]);

        $this->assertIsArray($collection->toArray());
        $this->arrayHasKey("entries");
        $this->assertCount(1, $collection->toArray()['entries']);
    }

    public function testInitializeFromArray()
    {
        $collection = (new Collection())->initializeFromArray([
            'entries' => [
                [
                    "keys" => ["Anställda", "Kön"],
                    "keyValues" => ["base", "Man"],
                    "value" => 1000,
                    "valueType" => "Total",
                ]
            ]
        ]);

        $entry = $collection->findFirstByKeys(["Anställda", "Kön"]);
        $this->assertEquals(1000, $entry->getValue());
    }
}
