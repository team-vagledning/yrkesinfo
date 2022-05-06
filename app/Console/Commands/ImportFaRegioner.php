<?php

namespace App\Console\Commands;

use App\Importers\FaRegion\FileImporter;
use App\Importers\Texts\TextImporter;
use App\Importers\Yrkesstatistik\SCB\ApiImporter;
use Illuminate\Console\Command;

class ImportFaRegioner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:fa-regioner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import fa-regioner';

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
