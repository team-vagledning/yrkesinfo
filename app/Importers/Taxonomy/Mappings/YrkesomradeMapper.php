<?php

namespace App\Importers\Taxonomy\Mappings;

class YrkesomradeMapper extends BaseMapper
{
    public $optionalId;
    public $source = 'Arbetsförmedlingen';
    public $name;
    public $description;

    protected static function mappings()
    {
        return [
            'LocaleFieldID' => 'optionalId',
            'Term' => 'name',
            'Description' => 'description',
        ];
    }
}
