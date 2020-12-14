<?php

namespace App\Console\Commands;

use App\Importers\Bristindex\ApiImporter;
use App\Importers\Bristindex\EttArImport;
use App\Importers\Bristindex\FemArImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportBristindex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:bristindex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        //Excel::import(new FemArImport, storage_path('imports/bristindex/5-ar.xlsx'));
        //Excel::import(new EttArImport, storage_path('imports/bristindex/1-ar.xlsx'));
        app(ApiImporter::class)->run();
    }
}
