<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Yrkesstatistik;

interface YrkesstatistikAggregatorInterface
{
    public function firstRun(Yrkesstatistik $yrkesstatistik);
    public function lastRun(Yrkesstatistik $yrkesstatistik);
    //public function run(Yrkesstatistik $yrkesstatistik);
}
