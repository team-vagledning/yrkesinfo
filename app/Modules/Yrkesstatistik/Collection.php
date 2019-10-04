<?php

namespace App\Modules\Yrkesstatistik;

use Illuminate\Contracts\Support\Arrayable;
use mysql_xdevapi\Exception;

class Collection implements Arrayable
{
    public $collection = [];

    public function initializeFromArray(array $fromArray) : self
    {
        foreach ($fromArray['entries'] as $entry) {
            $this->addEntry((new Entry())->initializeFromArray($entry));
        }
        return $this;
    }

    public function addEntry(Entry $entry, $replace = false) : self
    {
        if ($replace) {
            if ($previousEntry = $this->findFirstByKeysAndKeyValues($entry->getKeys(), $entry->getKeyValues())) {
                $previousEntry->replace($entry);
                return $this;
            }
        }

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
            if (empty(array_diff(self::removeUnknownKeys($keys), $entry->getKeys()))) {
                $results[] = $entry;
            }
        }

        return $results;
    }

    public function findAllByKeyValues(array $keyValues, array $entries = null, $valueType = false) : array
    {
        $results = [];

        if (is_null($entries)) {
            $entries = $this->collection;
        }

        foreach ($entries as $entry) {
            foreach ($this->possibleKeyValuePairs($keyValues) as $keyValuePair) {
                if (empty(array_diff(self::removeUnknownKeys($keyValuePair), $entry->getKeyValues()))) {
                    if ($valueType) {
                        if ($valueType == $entry->getValueType()) {
                            $results[] = $entry;
                        }
                    } else {
                        $results[] = $entry;
                    }
                }
            }
        }

        return $results;
    }

    public static function possibleKeyValuePairs($keyValues, &$all = [], $group = [], $val = null, $i = 0)
    {
        if (isset($val)) {
            array_push($group, $val);
        }
        if ($i >= count($keyValues)) {
            array_push($all, $group);
        } else {
            if (!is_array($keyValues[$i])) {
                $keyValues[$i] = [$keyValues[$i]];
            }

            foreach ($keyValues[$i] as $v) {
                self::possibleKeyValuePairs($keyValues, $all, $group, $v, $i + 1);
            }
        }
        return $all;
    }

    /**
     * @param array $keys
     * @param array $keyValues
     * @param bool|mixed $valueType
     * @return bool|Entry
     */
    public function findFirstByKeysAndKeyValues(array $keys, array $keyValues, $valueType = false)
    {
        $entries = $this->findAllByKeysAndKeyValues($keys, $keyValues, $valueType);

        if (count($entries)) {
            return $entries[0];
        }

        return false;
    }

    public function findAllByKeysAndKeyValues(array $keys, array $keyValues, $valueType = false) : array
    {
        $entries = $this->findAllByKeys($keys);
        $entries = $this->findAllByKeyValues($keyValues, $entries, $valueType);

        return $entries;
    }

    public function getUniqueKeyValuesByKeys(array $keys) : array
    {
        $results = [];

        $entries = $this->findAllByKeys($keys);

        foreach ($entries as $entry) {
            foreach ($entry->getKeys() as $key) {
                if (!isset($results[$key])) {
                    $results[$key] = [];
                }

                if (!in_array($entry->getKeyValue($key), $results[$key])) {
                    $results[$key][] = $entry->getKeyValue($key);
                }
            }
        }

        return $results;
    }

    public function sumEntries(array $entries)
    {
        $sum = 0;

        foreach ($entries as $entry) {
            $sum += $entry->getValue();
        }

        return $sum;
    }

    public function toArray()
    {
        $results = ['entries' => []];

        foreach ($this->collection as $entry) {
            $results['entries'][] = $entry->toArray();
        }

        return $results;
    }

    public static function removeUnknownKeys(array $fromArray)
    {
        $results = [];

        foreach ($fromArray as $value) {
            if ($value !== "?") {
                $results[] = $value;
            }
        }

        return $results;
    }
}
