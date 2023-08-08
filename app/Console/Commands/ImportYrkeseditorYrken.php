<?php

namespace App\Console\Commands;

use App\Importers\Yrkesinfo\YrkeseditorYrken\ApiImporter as YrkeseditorYrkenImporter;
use Illuminate\Console\Command;

class ImportYrkeseditorYrken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:yrkeseditor-yrken';

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
        app(YrkeseditorYrkenImporter::class)->run();
        cache()->tags(['yrkessok'])->flush();
    }
}
