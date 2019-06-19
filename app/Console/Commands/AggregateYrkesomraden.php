<?php

namespace App\Console\Commands;

use App\Aggregators\Yrkesstatistik\YrkesomradeAggregator;
use Illuminate\Console\Command;

class AggregateYrkesomraden extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aggregate:yrkesomraden';

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
        resolve(YrkesomradeAggregator::class)->run();
    }
}
