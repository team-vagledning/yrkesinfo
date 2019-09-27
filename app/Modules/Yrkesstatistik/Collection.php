<?php

namespace App\Modules\Yrkesstatistik;

class Collection
{
    public $collection = [];

    public function addEntry(Entry $entry) : self
    {
        $this->collection[] = $entry;
        return $this;
    }

    public function addEntries(array $entries) : self
    {
        foreach ($entries as $entry) {
            $this->addEntry($entry);
        }
        return $this;
    }

    /**
     * @param array $keys
     * @return bool|Entry
     */
    public function findFirstByKeys(array $keys)
    {
        $entries = $this->findAllByKeys($keys);

        if (count($entries)) {
            return $entries[0];
        }

        return false;
    }

    public function findAllByKeys(array $keys) : array
    {
        $results = [];

        foreach ($this->collection as $entry) {
            if (empty(array_diff($keys, $entry->getKeys()))) {
                $results[] = $entry;
            }
        }

        return $results;
    }

    public function findAllByKeyValues(array $keyValues, array $entries = null) : array
    {
        if (is_null($entries)) {
            $entries = $this->collection;
        }

        $results = [];

        foreach ($entries as $entry) {
            if (empty(array_diff($keyValues, $entry->getKeyValues()))) {
                $results[] = $entry;
            }
        }

        return $results;
    }

    /**
     * @param array $keys
     * @param array $keyValues
     * @return bool|Entry
     */
    public function findFirstByKeysAndKeyValues(array $keys, array $keyValues)
    {
        $entries = $this->findAllByKeysAndKeyValues($keys, $keyValues);

        if (count($entries)) {
            return $entries[0];
        }

        return false;
    }

    public function findAllByKeysAndKeyValues(array $keys, array $keyValues) : array
    {
        $entries = $this->findAllByKeys($keys);
        $entries = $this->findAllByKeyValues($keyValues, $entries);

        return $entries;
    }
}
