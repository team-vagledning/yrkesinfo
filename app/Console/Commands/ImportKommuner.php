<?php

namespace App\Console\Commands;

use App\Importers\Kommun\FileImporter;
use Illuminate\Console\Command;

class ImportKommuner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:kommuner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import kommuners';

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
        app(FileImporter::class)->run();
    }
}
