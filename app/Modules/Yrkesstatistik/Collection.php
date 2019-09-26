<?php

namespace App\Modules\Yrkesstatistik;

class Collection
{
    public $collection = [];

    public function addEntry(Entry $entry)
    {
        $this->collection[] = $entry->toArray();
    }
}
