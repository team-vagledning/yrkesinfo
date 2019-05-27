<?php

namespace App\Aggregators\Yrkesstatistik;

trait ScbMapper
{
    protected static $MAN = 1;
    protected static $WOMAN = 2;

    public static $regions = [
        '00' => 'Riket',
        '01' => 'Stockholms län',
        '03' => 'Uppsala län',
        '04' => 'Södermanlands län',
        '05' => 'Östergötlands län',
        '06' => 'Jönköpings län',
        '07' => 'Kronobergs län',
        '08' => 'Kalmar län',
        '09' => 'Gotlands län',
        '10' => 'Blekinge län',
        '12' => 'Skåne län',
        '13' => 'Hallands län',
        '14' => 'Västra Götalands län',
        '17' => 'Värmlands län',
        '18' => 'Örebro län',
        '19' => 'Västmanlands län',
        '20' => 'Dalarnas län',
        '21' => 'Gävleborgs län',
        '22' => 'Västernorrlands län',
        '23' => 'Jämtlands län',
        '24' => 'Västerbottens län',
        '25' => 'Norrbottens län',
        '99' => 'Län okänt',
    ];

    public static $sections = [
        '11' => 'statlig förvaltning',
        '1110' => 'statliga affärsverk',
        '1120' => 'primärkommunal förvaltning',
        '1130' => 'landsting',
        '15' => 'övriga offentliga institutioner',
        '1510' => 'aktiebolag ej offentligt ägda',
        '1520' => 'övriga företag ej offentligt ägda',
        '1530' => 'statligt ägda företag och organisationer',
        '1540' => 'kommunalt ägda företag och organisationer',
        '1560' => 'övriga organisationer',
        'US' => 'uppgift saknas',
    ];

    public static function getRegionName($from)
    {
        $id = self::getKeyValue($from, self::getKey('REGION'));

        if (array_key_exists($id, self::$regions)) {
            return self::$regions[$id];
        }

        return self::$regions[99];
    }

    public static function getSectionName($from)
    {
        $id = self::getKeyValue($from, self::getKey('SEKTOR'));

        if (array_key_exists($id, self::$sections)) {
            return self::$sections[$id];
        }

        return self::$sections['US'];
    }

    public static function getSex($from)
    {
        $value = self::getKeyValue($from, self::getKey('SEX'));

        return $value == self::$MAN ? 'man': 'kvinna';
    }

    public static function getYear($from)
    {
        return self::getKeyValue($from, self::getKey('YEAR'));
    }

    public static function getKey($name)
    {
        $keys = self::keys();

        if (array_key_exists($name, $keys) === false) {
            throw new \Exception("The key {$name} is not specified in class");
        }

        return $keys[$name];
    }

    public static function getKeyValue($row, $key)
    {
        return data_get($row, "key.{$key}");
    }
}
