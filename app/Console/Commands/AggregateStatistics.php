<?php

namespace App\Console\Commands;

use App\Aggregators\Yrkesstatistik\YrkesomradeAggregator;
use App\Modules\Yrkesstatistik\Collection;
use App\Yrkesgrupp;
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
        foreach (Yrkesgrupp::get() as $yrkesgrupp) {
            // For debug
            //$yrkesgrupp = Yrkesgrupp::where('name', 'Arkitekter m.fl.')->first();

            $yrkesstatistik = Yrkesstatistik::latestPerSourceAndYrkesgrupp($yrkesgrupp)->get();
            $collection = new Collection();

            // First run
            foreach ($yrkesstatistik as $ys) {
                app()->make($ys->source->aggregator)->firstRun($ys, $collection);
            }

            // Last run
            foreach ($yrkesstatistik as $ys) {
                app()->make($ys->source->aggregator)->lastRun($ys, $collection);
            }

            // Update
            self::createAggregated($yrkesgrupp, $collection->toArray());
        }
    }

    public static function createAggregated(Yrkesgrupp $yrkesgrupp, $aggregation)
    {
        return $yrkesgrupp->yrkesstatistikAggregated()->create([
            'statistics' => $aggregation
        ]);
    }
}
