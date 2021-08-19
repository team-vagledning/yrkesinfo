<?php

namespace App\Console\Commands;

use App\Importers\KeywordedSearches\Yrkesgrupp;
use Illuminate\Console\Command;

class ImportKeywordedSearches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:keyworded-searches {--target=}';

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
        if ($this->option('target') === 'yrkesgrupp') {
            app(Yrkesgrupp::class)->run();
        }
    }
}
