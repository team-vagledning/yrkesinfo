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

    /**
     * @var \Zend\Soap\Client
     */
    protected $client;

    /**
     * @var \Illuminate\Support\Collection;
     */
    public $yrkesomraden;

    /**
     * @var \Illuminate\Support\Collection;
     */
    public $yrkesgrupper;

    /**
     * @var \Illuminate\Support\Collection;
     */
    public $yrkesbenamningar;

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

    /**
     * @return \stdClass
     */
    public static function params(): \stdClass
    {
        return (object)([
            'languageId' => self::LANG_CODE
        ]);
    }

    public function run()
    {
        $this->fetchAll();

        $this->mapYrkesbenamningarToYrkesgrupper();

        $this->insertYrkesomraden();
        $this->insertYrkesgrupper();
    }

    /**
     * @return $this
     */
    public function insertYrkesomraden()
    {
        $this->yrkesomraden->each(function ($yrkesomrade) {
            Yrkesomrade::updateOrCreate(['external_id' => $yrkesomrade->externalId], $yrkesomrade->toArray());
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function insertYrkesgrupper()
    {
        $this->yrkesgrupper->each(function ($yrkesgrupp) {
            // Find matching yrkesområde by the external id
            $yrkesomrade = Yrkesomrade::fromArbetsformedlingenByExternalId($yrkesgrupp->yrkesomradeId)->first();

            // Update or create yrkesgrupp
            $yrkesgrupp = Yrkesgrupp::updateOrCreate(['ssyk' => $yrkesgrupp->ssyk], $yrkesgrupp->toArray());

            // Sync yrkesgrupp to yrkesområde
            $yrkesgrupp->yrkesomraden()->syncWithoutDetaching($yrkesomrade);
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function mapYrkesbenamningarToYrkesgrupper()
    {
        $this->yrkesbenamningar->each(function ($yrkesbenamning) {
            $this->yrkesgrupper->firstWhere('ssyk', $yrkesbenamning->ssyk)->addYrkesbenamning($yrkesbenamning->name);
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function fetchAll()
    {
        $this->yrkesomraden = $this->fetchYrkesomraden();
        $this->yrkesgrupper = $this->fetchYrkesgrupper();
        $this->yrkesbenamningar = $this->fetchYrkesbenamningar();

        return $this;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function fetchYrkesomraden()
    {
        $res = $this->client->GetAllLocaleFields(self::params())->GetAllLocaleFieldsResult;

        return collect($res)->flatten();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function fetchYrkesgrupper()
    {
        $res = $this->client->GetAllLocaleGroups(self::params())->GetAllLocaleGroupsResult;

        return collect($res)->flatten();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function fetchYrkesbenamningar()
    {
        $res = $this->client->GetAllOccupationNames(self::params())->GetAllOccupationNamesResult;

        return collect($res)->flatten();
    }
}
