<?php

namespace App\Importers\Taxonomy;

use App\Importers\ImporterInterface;
use App\Yrkesgrupp;

class AlternativeSsykImporter implements ImporterInterface
{
    public function run()
    {
        foreach (self::data() as $ssyk => $alternatives) {
            Yrkesgrupp::whereSsyk($ssyk)->first()->update([
                'alternative_ssyk' => $alternatives
            ]);
        }
    }

    public static function data()
    {
        return [
            '1210' => ['1211', '1212'],
            '1220' => ['1221', '1222'],
            '1240' => ['1241', '1242'],
            '1250' => ['1251', '1252'],
            '1290' => ['1291', '1292'],
            '1310' => ['1311', '1312'],
            '1320' => ['1321', '1322'],
            '1330' => ['1331', '1332'],
            '1340' => ['1341', '1342'],
            '1350' => ['1351', '1352'],
            '1360' => ['1361', '1362'],
            '1370' => ['1371', '1372'],
            '1410' => ['1411', '1412'],
            '1420' => ['1421', '1422'],
            '1490' => ['1491', '1492'],
            '1510' => ['1511', '1512'],
            '1520' => ['1521', '1522'],
            '1530' => ['1531', '1532'],
            '1590' => ['1591', '1592'],
            '1610' => ['1611', '1612'],
            '1710' => ['1711', '1712'],
            '1720' => ['1721', '1722'],
            '1730' => ['1731', '1732'],
            '1740' => ['1741', '1742'],
            '1790' => ['1791', '1792'],
        ];
    }
}
