<?php

namespace App\Importers\Susanavet\Courses;

use App\Importers\ImporterInterface;
use App\Yrkesgrupp;
use GuzzleHttp\Client;

class ApiImporter implements ImporterInterface
{
    const API_URL = 'https://susanavet2.skolverket.se/api/1.1/';

    const MAX_FETCH = 2000;

    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::API_URL
        ]);
    }

    public function run()
    {
        $subjects = $this->fetchSubjects();
        $yrkesgrupper = Yrkesgrupp::has('sunkoder')->with('sunkoder')->get();

        foreach ($yrkesgrupper as $yrkesgrupp) {
            $courses = [];

            foreach ($yrkesgrupp->sunkoder as $sunkod) {
                $matchedSubjects = $this->findSubjectsFromSunkod($subjects, $sunkod->kod);

                foreach ($matchedSubjects as $matchedSubject) {
                    $info = $this->fetchInfos($matchedSubject);

                    if (count($info) > 0) {
                        $courses[] = $info;
                    }
                }
            }

            if (count($courses) > 0) {
                $yrkesgrupp->susanavetCourses()->create([
                    'data' => $courses
                ]);
            }
        }
    }

    public function findSubjectsFromSunkod($subjects, $sunkod)
    {
        $matched = [];

        foreach ($subjects as $subject) {
            if ($subject->code == $sunkod) {
                $matched[] = $subject;
            }
        }

        return $matched;
    }

    private function fetchSubjects()
    {
        $results = $this->client->get('subjects?page=0&size=' . self::MAX_FETCH);

        $subjects = json_decode($results->getBody());

        if ($subjects->page->totalElements > self::MAX_FETCH) {
            throw new \Exception("More elements than MAX_FETCH");
        }

        return $subjects->content;
    }

    private function fetchInfos($subject)
    {
        $results = $this->client->get('infos?page=0&size=' . self::MAX_FETCH . '&subjectIds=' . $subject->id);

        $infos = json_decode($results->getBody());

        if ($infos->page->totalElements > self::MAX_FETCH) {
            throw new \Exception("More elements than MAX_FETCH");
        }

        return $infos->content;
    }
}
