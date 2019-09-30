<?php

namespace App\Aggregators\Yrkesstatistik;

trait ScbFormatter
{
    protected static $SEXES = [
        '1' => 'man',
        '2' => 'kvinna',
        '1+2' => 'bada',
    ];

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
        '0' => 'samtliga',
        '11' => 'offentlig', //'statlig förvaltning',
        '1110' => 'offentlig', //'statliga affärsverk',
        '1120' => 'offentlig', //'primärkommunal förvaltning',
        '1130' => 'offentlig', //'landsting',
        '15' => 'offentlig', //'övriga offentliga institutioner',
        '1510' => 'privat', //'aktiebolag ej offentligt ägda',
        '1520' => 'privat', //'övriga företag ej offentligt ägda',
        '1530' => 'offentlig', //'statligt ägda företag och organisationer',
        '1540' => 'offentlig', //'kommunalt ägda företag och organisationer',
        '1560' => 'privat', //'övriga organisationer',
        '1' => 'offentlig',
        '1-3' => 'offentlig',
        '2' => 'offentlig',
        '3' => 'offentlig',
        '4-5' => 'privat',
        '4' => 'privat',
        '5' => 'privat',
        'US' => 'saknas', //'uppgift saknas',
    ];

    public static $utbildningsniva = [
        '1' => 'Ingen gymnasieutbildning',
        '2' => 'Ingen gymnasieutbildning',
        '3' => 'Gymnasieutbildning',
        '4' => 'Gymnasieutbildning',
        '5' => 'Gymnasieutbildning',
        '6' => 'Eftergymnasial utbildning upp till 2 år',
        '7' => 'Eftergymnasial utbildning upp till 2 år',
        '8' => 'Eftergymnasial utbildning 3 år eller mer',
        '9' => 'Eftergymnasial utbildning 3 år eller mer',
        'TOTALT' => 'Samtliga utbildningsnivåer',
        'US' => 'Uppgift saknas',
    ];

    public static function getRegionName($from)
    {
        $id = self::getKeyValue($from, self::getKey('REGION'));

        if (array_key_exists($id, self::$regions)) {
            return self::$regions[$id];
        }

        return end(self::$regions[99]);
    }

    public static function getSektionName($from)
    {
        $id = self::getKeyValue($from, self::getKey('SEKTOR'));

        if (array_key_exists($id, self::$sections)) {
            return self::$sections[$id];
        }

        return end(self::$sections);
    }

    public static function getKon($from)
    {
        $value = self::getKeyValue($from, self::getKey('SEX'));
        return self::$SEXES[$value];
    }

    public static function getAr($from)
    {
        return self::getKeyValue($from, self::getKey('YEAR'));
    }

    public static function getUtbildningsniva($from)
    {
        $value = self::getKeyValue($from, self::getKey('UTBILDNINGSNIVA'));
        return self::$utbildningsniva[$value];
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
