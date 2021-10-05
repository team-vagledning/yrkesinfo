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
                    $events = $this->fetchEvents($matchedSubject);

                    if (empty($events)) {
                        continue;
                    }

                    $handledEducations = [];

                    foreach ($events as $event) {
                        // Skip already handled courses
                        $educationCode = $event->content->educationEvent->education;
                        if (array_key_exists($educationCode, $handledEducations)) {
                            continue;
                        }

                        $courses[] = $this->followLinks($event->links);
                        $handledEducations[$educationCode] = true;

                        // Simple rate limiting
                        sleep(5);
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

    private function fetchEvents($subject)
    {
        $results = $this->client->get('events?page=0&size=' . self::MAX_FETCH . '&subjectIds=' . $subject->id);

        $events = json_decode($results->getBody());

        if ($events->page->totalElements > self::MAX_FETCH) {
            throw new \Exception("More elements than MAX_FETCH");
        }

        return $events->content;
    }

    private function followLinks($links)
    {
        $returnResults = [];

        foreach ($links as $link) {
            $name = $link->rel;

            if ($name == "self") {
                $name = "events";
            }

            $results = $this->client->get($link->href);

            $decoded = json_decode($results->getBody());

            $returnResults[$name] = $decoded->content;
        }
        
        return $returnResults;
    }
}
