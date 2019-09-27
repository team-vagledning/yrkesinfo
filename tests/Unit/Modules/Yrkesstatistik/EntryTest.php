<?php

namespace Tests\Modules\Yrkesstatistik;

use App\Exceptions\NotFoundException;
use App\Modules\Yrkesstatistik\Entry;
use App\Modules\Yrkesstatistik\EntryFactory;
use Tests\TestCase;

class EntryTest extends TestCase
{
    public function testMakeFromFactory()
    {
        $factory = app(EntryFactory::class)->createFactory("Lön", ["Kön", "Utbildningnivå", "År"]);

        $firstEntry = $factory->makeEntry(["Man", "Gymnasieutbildning", "2018"], 10, "Total");
        $secondEntry = $factory->makeEntry(["Kvinna", "Högskola", "2019"], 20, "Total");

        $this->assertEquals("Gymnasieutbildning", $firstEntry->getKeyValue("Utbildningnivå"));
        $this->assertEquals("Högskola", $secondEntry->getKeyValue("Utbildningnivå"));

        // Test exception
        $this->expectException(NotFoundException::class);
        $firstEntry->getKeyValue("Felstavat");
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
