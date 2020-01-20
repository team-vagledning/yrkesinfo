<?php

namespace App\Importers\Taxonomy\Mappings;

class YrkesomradeMapper extends BaseMapper
{
    public $externalId;
    public $source = 'Arbetsförmedlingen';
    public $name;
    public $description;

    protected static function mappings()
    {
        return [
            'LocaleFieldID' => 'externalId',
            'Term' => 'name',
            'Description' => 'description',
        ];
    }
}
