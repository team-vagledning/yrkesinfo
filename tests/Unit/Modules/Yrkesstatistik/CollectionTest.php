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
            $entryFactory1->makeEntry(["Man", 2000], 10, "Total"),
            $entryFactory1->makeEntry(["Kvinna", 2001], 10, "Total"),
            $entryFactory2->makeEntry(["Gymnasiet", 2000], 10, "Total"),
        ]);

        $queryKeys = ["Lön", "Kön", "År"];

        $searchedEntries = $collection->findAllByKeys($queryKeys);
        $searchedEntry = $collection->findFirstByKeys($queryKeys);

        $this->assertCount(2, $searchedEntries);
        $this->assertInstanceOf(Entry::class, $searchedEntry);
    }

    public function testFindingByKeyAndKeyValues()
    {
        $collection = app(Collection::class);
        $entryFactory = app(EntryFactory::class)->createFactory("Anställda", ["Sektion", "År"]);

        $collection->addEntries([
            $entryFactory->makeEntry(["Offentligt", "2019"], 1000, "Total"),
            $entryFactory->makeEntry(["Privat", "2019"], 500, "Total"),
            $entryFactory->makeEntry(["Okänt", "2019"], 600, "Total"),
        ]);

        $entry = $collection->findFirstByKeysAndKeyValues(["Anställda", "Sektion", "År"], ["Privat", "2019"]);

        $this->assertEquals(500, $entry->getValue());
    }

    public function testUpdatingEntry()
    {
        $collection = app(Collection::class);
        $entryFactory = app(EntryFactory::class)->createFactory("Anställda", ["Sektion", "År"]);

        $collection->addEntries([
            $entryFactory->makeEntry(["Offentligt", "2019"], 1000, "Total"),
            $entryFactory->makeEntry(["Privat", "2019"], 500, "Total"),
            $entryFactory->makeEntry(["Okänt", "2019"], 600, "Total"),
        ]);

        // Find a specific entry
        $entry = $collection->findFirstByKeysAndKeyValues(["Anställda", "Sektion", "År"], ["Privat", "2019"]);

        // Update the value
        $entry->setValue(1200);

        // Find the same entry, and check value
        $_entry = $collection->findFirstByKeysAndKeyValues(["Anställda", "Sektion", "År"], ["Privat", "2019"]);
        $this->assertEquals(1200, $_entry->getValue());
    }
}
