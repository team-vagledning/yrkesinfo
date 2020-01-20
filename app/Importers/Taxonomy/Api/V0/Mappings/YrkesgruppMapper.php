<?php

namespace App\Importers\Taxonomy\Mappings;

class YrkesgruppMapper extends BaseMapper
{
    public $ssyk;
    public $name;
    public $description;
    public $yrkesbenamningar = [];

    protected $yrkesomradeId;

    protected static function mappings()
    {
        return [
            'LocaleCode' => 'ssyk',
            'Term' => 'name',
            'Description' => 'description',
            'LocaleFieldID' => 'yrkesomradeId'
        ];
    }

    public function addYrkesbenamning($yrkesbenamning)
    {
        $this->yrkesbenamningar[] = $yrkesbenamning;
    }
}
