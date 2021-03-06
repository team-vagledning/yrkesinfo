<?php

namespace App\Console\Commands;

use App\Importers\Sunkoder\FileImporter;
use Illuminate\Console\Command;

class ImportSunkoder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:sunkoder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import sunkoder';

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

        $this->info("Done!");
    }
}
