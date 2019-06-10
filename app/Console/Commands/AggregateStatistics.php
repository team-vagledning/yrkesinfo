<?php

namespace App\Console\Commands;

use App\Aggregators\Yrkesstatistik\YrkesomradeAggregator;
use App\Yrkesstatistik;
use App\YrkesstatistikAggregated;
use Illuminate\Console\Command;

class AggregateStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aggregate:statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggregates statistics';

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

        foreach (Yrkesstatistik::latestPerSourceAndYrkesgrupp()->get() as $statistics) {
            $aggregator = $statistics->source->aggregator;

            if ($aggregator) {
                app()->make($aggregator)->run($statistics);
            }
        }

        //resolve(YrkesomradeAggregator::class)->run();

    }
}
