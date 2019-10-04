<?php

namespace App\Modules\Yrkesstatistik;

class EntryFactory
{
    public $keys;

    public function createFactory($base, array $keys)
    {
        array_unshift($keys, $base);

        $this->keys = $keys;

        return $this;
    }

    public function makeEntry(array $keyValues, $dataValueType, $dataValue) : Entry
    {
        // Make room for the base, should always be the first key
        array_unshift($keyValues, "base");

        return (new Entry())->initialize($this->keys, $keyValues, $dataValueType, $dataValue);
    }

    public function makeEntries(array $values) : array
    {
        $entries = [];

        foreach ($values as $value) {
            [$keyValues, $dataValue, $dataValueType] = $value;
            $entries[] = $this->makeEntry($keyValues, $dataValueType, $dataValue);
        }

        return $entries;
    }

    public function findOrMakeEntry(
        Collection $collection,
        array $keyValues,
        $dataValueType = 'Total',
        $dataValue = 0
    ) : Entry {

        if ($entry = $collection->findFirstByKeysAndKeyValues($this->keys, $keyValues, $dataValueType)) {
            return $entry;
        }

        return $this->makeEntry($keyValues, $dataValueType, $dataValue);
    }
}
