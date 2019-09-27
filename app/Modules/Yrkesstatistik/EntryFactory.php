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

    public function makeEntry(array $keyValues, $dataValue, $dataValueType)
    {
        // Make room for the base, should always be the first key
        array_unshift($keyValues, "base");

        return (new Entry())->initialize($this->keys, $keyValues, $dataValue, $dataValueType);
    }
}
