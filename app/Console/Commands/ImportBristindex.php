<?php

namespace App\Console\Commands;

use App\Bristindex;
use App\Importers\Bristindex\V2\ApiImporter;
use App\Importers\Bristindex\V3\CurrentData;
use App\Importers\Bristindex\V3\HistoricData;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
        // Actual, clear first
        Bristindex::truncate();
        Excel::import(
            new CurrentData,
            storage_path('imports/bristindex/v3/bedomning.csv'),
            null,
            \Maatwebsite\Excel\Excel::CSV
        );

        // Historical
        Excel::import(
            new HistoricData,
            storage_path('imports/bristindex/v3/historik.csv'),
            null,
            \Maatwebsite\Excel\Excel::CSV
        );
    }

}
