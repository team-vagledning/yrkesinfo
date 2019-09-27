<?php

namespace App\Modules\Yrkesstatistik;

use App\Exceptions\NotFoundException;
use Illuminate\Contracts\Support\Arrayable;

class Entry implements Arrayable
{
    protected $keys;
    protected $keyValues;
    protected $value;
    protected $valueType;

    public function initialize($keys, $keyValues, $value, $valueType) : self
    {
        $this->keys = $keys;
        $this->keyValues = $keyValues;
        $this->value = $value;
        $this->valueType = $valueType;

        return $this;
    }

    public function initializeFromArray(array $fromArray) : self
    {
        return $this->initialize(
            $fromArray['keys'],
            $fromArray['keyValues'],
            $fromArray['value'],
            $fromArray['valueType']
        );
    }

    public function toArray()
    {
        return [
            'keys' => $this->keys,
            'keyValues' => $this->keyValues,
            'value' => $this->value,
            'valueType' => $this->valueType,
        ];
    }

    public function getKeys() : array
    {
        return $this->keys;
    }

    public function getKeyValues() : array
    {
        return $this->keyValues;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getKeyValue($key)
    {
        $keyIndex = array_search($key, $this->keys);

        if ($keyIndex === false) {
            throw new NotFoundException("Key $key is not found");
        }

        return $this->keyValues[$keyIndex];
    }
}
