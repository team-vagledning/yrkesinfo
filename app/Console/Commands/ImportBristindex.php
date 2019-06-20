<?php

namespace App\Console\Commands;

use App\Importers\Bristindex\YrkesgruppImport;
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
        Excel::import(new YrkesgruppImport, storage_path('imports/bristindex/yrkesomraden.xlsx'));
    }
}
