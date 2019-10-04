<?php

namespace Tests\Modules\Yrkesstatistik;

use App\Exceptions\NotFoundException;
use App\Modules\Yrkesstatistik\Collection;
use App\Modules\Yrkesstatistik\Entry;
use App\Modules\Yrkesstatistik\EntryFactory;
use Tests\TestCase;

class EntryTest extends TestCase
{
    public function testMakeFromFactory()
    {
        $factory = app(EntryFactory::class)->createFactory("Lön", ["Kön", "Utbildningnivå", "År"]);

        $firstEntry = $factory->makeEntry(["Man", "Gymnasieutbildning", "2018"], "Total", 10);
        $secondEntry = $factory->makeEntry(["Kvinna", "Högskola", "2019"], "Total", 20);

        $this->assertEquals("Gymnasieutbildning", $firstEntry->getKeyValue("Utbildningnivå"));
        $this->assertEquals("Högskola", $secondEntry->getKeyValue("Utbildningnivå"));

        // Test exception
        $this->expectException(NotFoundException::class);
        $firstEntry->getKeyValue("Felstavat");
    }

    public function testFindOrMakeFromFactory()
    {
        $collection = new Collection();
        $factory = app(EntryFactory::class)->createFactory("Lön", ["Kön", "Utbildningnivå", "År"]);

        $entry = $factory->findOrMakeEntry($collection, ["Man", "Gymnasieutbildning", "2018"]);
        $collection->addEntry($entry);
        $this->assertEquals(0, $entry->getValue());

        $entry->setValue(10);

        $findAgainEntry = $factory->findOrMakeEntry($collection, ["Man", "Gymnasieutbildning", "2018"]);
        $this->assertEquals(10, $findAgainEntry->getValue());
    }

    public function testInitializeFromArray()
    {
        $entry = (new Entry())->initializeFromArray([
            "keys" => ["Anställda", "Kön"],
            "keyValues" => ["base", "Man"],
            "value" => 1000,
            "valueType" => "Total",
        ]);

        $this->assertEquals(1000, $entry->getValue());
    }
}
