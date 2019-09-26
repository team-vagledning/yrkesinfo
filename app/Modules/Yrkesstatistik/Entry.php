<?php

namespace App\Modules\Yrkesstatistik;

use Illuminate\Contracts\Support\Arrayable;

class Entry implements Arrayable
{
    protected $keys;
    protected $keyValues;
    protected $value;

    public function __construct($keys, $keyValues, $value)
    {
        $this->keys = $keys;
        $this->keyValues = $keyValues;
        $this->value = $value;
    }

    public function toArray()
    {
        return [
            'keys' => $this->keys,
            'keyValues' => $this->keyValues,
            'value' => $this->value,
        ];
    }
}
