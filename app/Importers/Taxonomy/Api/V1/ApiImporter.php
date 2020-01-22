<?php

namespace App\Importers\Taxonomy\Api\V1;

use App\Importers\ImporterInterface;
use App\Yrkesbenamning;
use App\Yrkesgrupp;
use App\Yrkesomrade;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class ApiImporter implements ImporterInterface
{
    const API_URL = 'https://taxonomy.api.jobtechdev.se/v1/taxonomy/';

    protected $client;

    /**
     * @var Collection;
     */
    public $yrkesomraden;

    /**
     * @var Collection;
     */
    public $yrkesgrupper;

    /**
     * @var Collection
     */
    public $yrkesomradenYrkesgrupperMapping;

    /**
     * @var Collection
     */
    public $yrkesgrupperYrkesbenamningarMapping;

    /**
     * @var Collection;
     */
    public $yrkesbenamningar;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::API_URL,
            'headers' => [
                'api-key' => env('JOBTECH_API_KEY')
            ]
        ]);
    }

    public function run()
    {
        $this->fetchAll();

        $this->insertYrkesbenamningar();
        $this->insertYrkesomraden();
        $this->insertYrkesgrupper();
    }

    /**
     * @return $this
     */
    public function insertYrkesomraden()
    {
        $this->yrkesomraden->each(function ($yrkesomrade) {
            Yrkesomrade::updateOrCreate(['external_id' => $yrkesomrade->{'taxonomy/id'}], [
                'source' => 'Arbetsförmedlingen',
                'external_id' => $yrkesomrade->{'taxonomy/id'},
                'name' => $yrkesomrade->{'taxonomy/preferred-label'},
                'description' => $yrkesomrade->{'taxonomy/definition'},
            ]);
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function insertYrkesbenamningar()
    {
        $this->yrkesbenamningar->each(function ($yrkesbenamning) {
            Yrkesbenamning::updateOrCreate(['external_id' => $yrkesbenamning->{'taxonomy/id'}], [
                'external_id' => $yrkesbenamning->{'taxonomy/id'},
                'name' => $yrkesbenamning->{'taxonomy/preferred-label'},
            ]);
        });

        return $this;
    }

    public function getYrkesomradenFromMapping($taxonomyId)
    {
        return $this->yrkesomradenYrkesgrupperMapping
            ->where('taxonomy/source', '=', $taxonomyId)
            ->map(function ($edge) {
                return Yrkesomrade::fromArbetsformedlingenByExternalId($edge->{'taxonomy/target'})->first();
            });
    }

    public function getYrkesbenamningarFromMapping($taxonomyId)
    {
        return $this->yrkesgrupperYrkesbenamningarMapping
            ->where('taxonomy/target', '=', $taxonomyId)
            ->map(function ($edge) {
                return Yrkesbenamning::where('external_id', $edge->{'taxonomy/source'})->first();
            });
    }

    /**
     * @return $this
     */
    public function insertYrkesgrupper()
    {
        $this->yrkesgrupper->each(function ($yrkesgrupp) {

            // Update or create yrkesgrupp
            $yrkesgrupp = Yrkesgrupp::updateOrCreate(['external_id' => $yrkesgrupp->{'taxonomy/id'}], [
                'ssyk' => $yrkesgrupp->{'taxonomy/ssyk-code-2012'},
                'name' => $yrkesgrupp->{'taxonomy/preferred-label'},
                'description' => $yrkesgrupp->{'taxonomy/definition'},
            ]);

            $yrkesomraden = $this->getYrkesomradenFromMapping($yrkesgrupp->external_id);
            $yrkesbenamingar = $this->getYrkesbenamningarFromMapping($yrkesgrupp->external_id);
            
            // Sync yrkesgrupp to yrkesområde, and also yrkesbenämningar
            $yrkesgrupp->yrkesomraden()->syncWithoutDetaching($yrkesomraden->pluck('id'));
            $yrkesgrupp->yrkesbenamningar()->syncWithoutDetaching($yrkesbenamingar->pluck('id'));
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

        // Get mappings
        $this->yrkesomradenYrkesgrupperMapping = $this->fetchYrkesgrupperYrkesomradenMapping();
        $this->yrkesgrupperYrkesbenamningarMapping = $this->fetchYrkesgrupperYrkesbenamningarMapping();


        return $this;
    }

    /**
     * @return Collection
     */
    public function fetchYrkesomraden()
    {
        $params = [
            'type' => 'occupation-field'
        ];

        $res = $this->client->get('main/concepts', [
            'query' => $params
        ]);

        return collect(json_decode($res->getBody()));
    }

    /**
     * @return Collection
     */
    public function fetchYrkesgrupper()
    {
        $params = [
            'type' => 'ssyk-level-4'
        ];

        $res = $this->client->get('specific/concepts/ssyk', [
            'query' => $params
        ]);

        return collect(json_decode($res->getBody()));
    }

    public function fetchYrkesgrupperYrkesomradenMapping()
    {
        $params = [
            'edge-relation-type' => 'broader',
            'source-concept-type' => 'ssyk-level-4',
            'target-concept-type' => 'occupation-field',
        ];

        $res = $this->client->get('main/graph', [
            'query' => $params
        ]);

        return collect(json_decode($res->getBody())->{'taxonomy/graph'}->{'taxonomy/edges'});
    }

    public function fetchYrkesgrupperYrkesbenamningarMapping()
    {
        $params = [
            'edge-relation-type' => 'broader',
            'source-concept-type' => 'occupation-name',
            'target-concept-type' => 'ssyk-level-4',
        ];

        $res = $this->client->get('main/graph', [
            'query' => $params
        ]);

        return collect(json_decode($res->getBody())->{'taxonomy/graph'}->{'taxonomy/edges'});
    }

    /**
     * @return Collection
     */
    public function fetchYrkesbenamningar()
    {
        $params = [
            'type' => 'occupation-name'
        ];

        $res = $this->client->get('main/concepts', [
            'query' => $params
        ]);

        return collect(json_decode($res->getBody()));
    }
}
