<?php

namespace Tests\Feature\Aggregators\Yrkesstatistik;

use App\Aggregators\Yrkesstatistik\BaseAggregator;
use ErrorException;
use Tests\TestCase;

class WeightedHelperMethodTest extends TestCase
{
    public $baseAggregator;

    public function setUp(): void
    {
        parent::setUp();
        $this->baseAggregator = (new class extends BaseAggregator {
            // Empty class
        });
    }

    public function testShouldBeAblteToGetAllWeighted()
    {
        $this->baseAggregator->setWeighted(['Foo', 'Bar'], 10, 1000);
        $this->baseAggregator->setWeighted(['Foo', 'Bar'], 5, 1500);

        $this->baseAggregator->setWeighted(['Foo2', 'Bar'], 2, 1);
        $this->baseAggregator->setWeighted(['Foo2', 'Bar'], 9, 5);

        $res = $this->baseAggregator->getAllWeighted();

        $this->assertEquals(1166.67, round_number(array_shift($res)['weighted_value']));
        $this->assertEquals(4.27, round_number(array_shift($res)['weighted_value']));
    }

}
