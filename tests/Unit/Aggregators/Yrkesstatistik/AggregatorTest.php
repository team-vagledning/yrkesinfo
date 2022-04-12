<?php

namespace Tests\Unit\Aggregators\Yrkesstatistik;

use App\Console\Commands\AggregateStatistics;
use App\YrkesstatistikAggregated;
use ErrorException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use DB;

class AggregatorTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testAggregation()
    {
        // Prepare DB
        DB::statement(file_get_contents(base_path('tests/database/yrkesgrupper.sql')));
        DB::statement(file_get_contents(base_path('tests/database/yrkesstatistik.sql')));

        // Load JSON from master file
        $statistics = json_decode(file_get_contents(base_path('tests/database/yrkesstatistik_aggregated.json')), true);

        // Aggregate data
        \Artisan::call(AggregateStatistics::class);

        // Compare with master
        foreach (YrkesstatistikAggregated::all() as $count => $agg) {
            //dd($statistics[$count]['statistics']['entries'][0], $agg->statistics['entries'][0]);
            $this->assertEqualsCanonicalizing($statistics[$count]['statistics'], $agg->statistics);
        }
    }
}
