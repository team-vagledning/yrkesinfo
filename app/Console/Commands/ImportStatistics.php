<?php

namespace App\Console\Commands;

use App\Importers\Yrkesstatistik\SCB\ApiImporter;
use Illuminate\Console\Command;

class ImportStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import yrkesstatistik';

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
        app(ApiImporter::class)->run();
    }
}
