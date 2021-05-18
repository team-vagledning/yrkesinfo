<?php

namespace App\Console\Commands;

use App\Importers\Yrkesinfo\Yrkesgrupper\ApiImporter as YrkesgrupperImporter;
use Illuminate\Console\Command;

class ImportOldYrkesinfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:old-yrkesinfo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import and map old yrkesinfo';

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
        app(YrkesgrupperImporter::class)->run();
    }
}
