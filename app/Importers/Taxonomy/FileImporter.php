<?php

namespace App\Importers\Taxonomy;

use App\Importers\CsvObject;
use App\Importers\ImporterInterface;
use App\Yrkesgrupp;
use App\Yrkesomrade;
use League\Csv\Reader;


class FileImporter implements ImporterInterface
{
    const REMOTE_FILE = 'https://raw.githubusercontent.com/JobtechSwe/taxonomy-dump/master/taxonomy.csv';
    const DELIMITER = '|';
    
    protected $content;

    public function __construct()
    {
        $this->content = new CsvObject();
    }

    public function run()
    {
        $this->parseFile(self::REMOTE_FILE)->transformRecords();

        foreach ($this->getRecords() as $record) {

            $yrkesomrade = Yrkesomrade::updateOrCreate(['external_id' => $record['yrkesomrade']['external_id']], [
                'external_id' => $record['yrkesomrade']['external_id'],
                'name' => $record['yrkesomrade']['name'],
                'source' => 'Arbetsförmedlingen',
                'description' => 'N/A',
            ]);

            $yrkesgrupp = Yrkesgrupp::updateOrCreate(['ssyk' => $record['ssyk']], \Arr::except($record, 'yrkesomrade'));

            // Sync yrkesgrupp to yrkesområde
            $yrkesgrupp->yrkesomraden()->syncWithoutDetaching($yrkesomrade);
        }
    }

    public function transformRecords()
    {
        $transformed = [];

        foreach ($this->getRecords() as $record) {

            $ssyk = $record['ssyk'];

            // If not yet transformed
            if (array_key_exists($ssyk, $transformed) === false) {
                $transformed[$ssyk] = [
                    'yrkesomrade' => [
                        'external_id' => $record['occupationFieldId'],
                        'name' => $record['occupationFieldTerm']
                    ],
                    'ssyk' => $ssyk,
                    'name' => $record['ssykTerm'],
                    'description' => $record['ssykDescription'],
                    'yrkesbenamningar' => []
                ];
            }

            // Add every copy of the same SSYK as yrkesbenamning
            $transformed[$ssyk]['yrkesbenamningar'][] = $record['occupationNameTerm'];
        }

        // Reset array keys from SSYK to serial (0..) and set the records
        $this->setRecords(array_values($transformed));

        return $this;
    }

    public function parseFile($url, $delimiter = self::DELIMITER)
    {
        $csv = Reader::createFromString(file_get_contents($url))
            ->setDelimiter($delimiter)
            ->setHeaderOffset(0);

        $this->content = new CsvObject($csv);

        return $this;
    }

    public function getHeader()
    {
        return $this->content->header;
    }

    public function getRecords()
    {
        return $this->content->records;
    }

    public function setRecords($records)
    {
        $this->content->records = $records;
        return $this;
    }
}
