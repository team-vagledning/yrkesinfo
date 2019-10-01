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
            $yrkesstatistik = Yrkesstatistik::latestPerSourceAndYrkesgrupp($yrkesgrupp)->get();
            $collection = new Collection();

            // First run
            foreach ($yrkesstatistik as $ys) {
                app()->make($ys->source->aggregator)->firstRun($ys, $collection);
            }

            //dd($collection);

            // Last run
            foreach ($yrkesstatistik as $ys) {
                app()->make($ys->source->aggregator)->lastRun($ys, $collection);
            }


            // Update
            self::update($yrkesgrupp, $collection->toArray());


            //
            dd();
        }
    }

    public static function update(Yrkesgrupp $yrkesgrupp, $aggregation)
    {
        $aggregated = $yrkesgrupp->yrkesstatistikAggregated()->firstOrCreate([], [
            'statistics' => []
        ]);

        $aggregated->update([
            'statistics' => array_replace_recursive($aggregated->statistics, $aggregation)
        ]);
    }
}
