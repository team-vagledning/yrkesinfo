<?php

namespace App\Importers\Taxonomy\Mappings;

class YrkesbenamningMapper extends BaseMapper
{
    public $ssyk;
    public $name;

    protected static function mappings()
    {
        return [
            'LocaleCode' => 'ssyk',
            'Term' => 'name',
        ];
    }
}
