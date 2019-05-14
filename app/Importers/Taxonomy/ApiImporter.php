<?php

namespace App\Importers\Taxonomy;

use App\Importers\ImporterInterface;
use App\Importers\Taxonomy\Mappings\YrkesbenamningMapper;
use App\Importers\Taxonomy\Mappings\YrkesgruppMapper;
use App\Importers\Taxonomy\Mappings\YrkesomradeMapper;
use App\Yrkesgrupp;
use App\Yrkesomrade;
use Zend\Soap\Client;

class ApiImporter implements ImporterInterface
{
    const LANG_CODE = 502;
    const API_URL = 'http://api.arbetsformedlingen.se/taxonomi/v0/TaxonomiService.asmx?wsdl';

    protected $client;

    public function __construct()
    {
        $this->client = new Client(self::API_URL, [
            'classmap' => [
                'LocaleField' => YrkesomradeMapper::class,
                'LocaleGroup' => YrkesgruppMapper::class,
                'OccupationName' => YrkesbenamningMapper::class,
            ]
        ]);
    }

    public static function params(): \stdClass
    {
        return (object)([
            'languageId' => self::LANG_CODE
        ]);
    }

    public function run()
    {
        $yrkesomraden = $this->getYrkesomraden();
        $yrkesgrupper = $this->getYrkesgrupper();
        $yrkesbenamningar = $this->getYrkesbenamningar();

        $yrkesomraden->each(function ($yrkesomrade) {
            Yrkesomrade::updateOrCreate(['optional_id' => $yrkesomrade->optionalId], $yrkesomrade->toArray());
        });

        // Map yrkesbenamningar to yrkesgrupper
        $yrkesbenamningar->each(function ($yrkesbenamning) use ($yrkesgrupper) {
            $yrkesgrupper->firstWhere('ssyk', $yrkesbenamning->ssyk)->addYrkesbenamning($yrkesbenamning->name);
        });

        // Create yrkesgrupp and sync it to yrkesomrade
        $yrkesgrupper->each(function ($yrkesgrupp) {
            $yrkesomrade = Yrkesomrade::taxonomyId($yrkesgrupp->yrkesomradeId)->first();
            $created = Yrkesgrupp::updateOrCreate(['ssyk' => $yrkesgrupp->ssyk], $yrkesgrupp->toArray());
            $created->yrkesomraden()->syncWithoutDetaching($yrkesomrade);
        });
    }

    public function getYrkesomraden()
    {
        $res = $this->client->GetAllLocaleFields(self::params())->GetAllLocaleFieldsResult;

        return collect($res)->flatten();
    }

    public function getYrkesgrupper()
    {
        $res = $this->client->GetAllLocaleGroups(self::params())->GetAllLocaleGroupsResult;

        return collect($res)->flatten();
    }

    public function getYrkesbenamningar()
    {
        $res = $this->client->GetAllOccupationNames(self::params())->GetAllOccupationNamesResult;

        return collect($res)->flatten();
    }
}
