<?php

namespace App\Console\Commands;

use App\Importers\Taxonomy\Api\V1\ApiImporter;
use App\Region;
use const App\Console\EXIT_FAILURE;
use const App\Console\EXIT_OK;
use App\Importers\Taxonomy\AlternativeSsykImporter;
use App\Importers\Taxonomy\FileImporter;
use Faker\Provider\File;
use Illuminate\Console\Command;

class ImportTaxonomy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:taxonomy {--source=api}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports taxonomy from JobTech';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            if ($this->option('source') === 'file') {
                app(FileImporter::class)->run();
            } else {
                app(ApiImporter::class)->run();
            }

            app(AlternativeSsykImporter::class)->run();

            // Also import koordinater
            $regioner = json_decode(file_get_contents(storage_path('imports/regioner/regioner.json')));

            foreach ($regioner as $r) {
                $region = Region::where('name', $r->lansnamn)->firstOrFail();
                $region->grans = $r->grans;
                $region->save();
            }

        } catch (\Exception $e) {
            echo $e->getMessage();
            return EXIT_FAILURE;
        }

        return EXIT_OK;
    }
}
