<?php

namespace App\Aggregators\Yrkesstatistik;

trait ScbFormatter
{
    public static $sexes = [
        '1' => 'Man',
        '2' => 'Kvinna',
        '1+2' => 'Alla',
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
        /*
        '0' => 'Samtliga',
        '11' => 'Offentlig', //'statlig förvaltning',
        '1110' => 'Offentlig', //'statliga affärsverk',
        '1120' => 'Offentlig', //'primärkommunal förvaltning',
        '1130' => 'Offentlig', //'landsting',
        '15' => 'Offentlig', //'övriga offentliga institutioner',
        '1510' => 'Privat', //'aktiebolag ej offentligt ägda',
        '1520' => 'Privat', //'övriga företag ej offentligt ägda',
        '1530' => 'Offentlig', //'statligt ägda företag och organisationer',
        '1540' => 'Offentlig', //'kommunalt ägda företag och organisationer',
        '1560' => 'Privat', //'övriga organisationer',
        '1' => 'Offentlig',
        '1-3' => 'Offentlig',
        '2' => 'Offentlig',
        '3' => 'Offentlig',
        '4-5' => 'Privat',
        '4' => 'Privat',
        '5' => 'Privat',
        'US' => 'Saknas', //'uppgift saknas',
        */
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
        return self::$sexes[$value];
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
