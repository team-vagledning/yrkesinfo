<?php

namespace App\Importers\Taxonomy\Mappings;

use Illuminate\Support\Str;
use ReflectionObject;
use ReflectionProperty;

abstract class BaseMapper
{
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->mappings())) {
            $this->{$this->mappings()[$name]} = $value;
        }
    }

    public function __get($name)
    {
        if (isset($this->{$name})) {
            return $this->{$name};
        }
    }

    public function toArray()
    {
        $properties = (new \ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC);

        return collect($properties)->mapWithKeys(function ($property) {
            return [Str::snake($property->getName()) => $property->getValue($this)];
        })->toArray();
    }
}
