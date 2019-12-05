<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Modules\Yrkesstatistik\Collection;
use App\Yrkesstatistik;

interface YrkesstatistikAggregatorInterface
{
    public function firstRun(Yrkesstatistik $yrkesstatistik, Collection $collection);
    public function lastRun(Yrkesstatistik $yrkesstatistik, Collection $collection);
    //public function run(Yrkesstatistik $yrkesstatistik);
}
