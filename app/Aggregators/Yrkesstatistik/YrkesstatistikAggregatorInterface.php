<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Yrkesstatistik;

interface YrkesstatistikAggregatorInterface
{
    public function run(Yrkesstatistik $yrkesstatistik);
}
