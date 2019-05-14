<?php

namespace App\Console\Commands;

use const App\Console\EXIT_FAILURE;
use const App\Console\EXIT_OK;
use App\Importers\Taxonomy\ApiImporter;
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
    protected $signature = 'import:taxonomy';

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
            app(ApiImporter::class)->run();
        } catch (\Exception $e) {
            echo $e->getMessage();
            return EXIT_FAILURE;
        }

        return EXIT_OK;
    }
}
