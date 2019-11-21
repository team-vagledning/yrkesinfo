<?php

namespace App\Aggregators\Yrkesstatistik;

trait ScbFormatter
{
    public static $kon = [
        '1' => 'Man',
        '2' => 'Kvinna',
        '1+2' => 'Alla',
    ];

    public static $regioner = [
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

    public static $simpleSektioner = [
        '11' => 'Offentlig', // Offentlig
        '1110' => 'Offentlig', // Offentlig
        '1120' => 'Offentlig', // Offentlig
        '1130' => 'Offentlig', // Offentlig
        '15' => 'Offentlig', // Offentlig
        '1510' => 'Privat', // Privat
        '1520' => 'Privat', // Privat
        '1530' => 'Offentlig', // Offentlig
        '1540' => 'Offentlig', // Offentlig
        '1560' => 'Privat', // Privat

        '0' => 'Samtliga', // Samtliga
        '1' => 'Offentlig', // Offentlig sektor
        '1-3' => 'Offentlig', // Offentlig
        '2' => 'Offentlig', // Kommunal sektor
        '3' => 'Offentlig', // Landstingssektorn
        '4-5' => 'Privat', // Privat sektor
        '4' => 'Privat', // Privatanställda arbetare
        '5' => 'Privat', // Privatanställda tjänstemän

        'US' => 'Uppgift saknas', // Uppgift saknas
    ];

    public static $sektioner = [
        '11' => 'Statlig förvaltning', // Offentlig
        '1110' => 'Statliga affärsverk', // Offentlig
        '1120' => 'Primärkommunal förvaltning', // Offentlig
        '1130' => 'Landsting', // Offentlig
        '15' => 'Övriga offentliga institutioner', // Offentlig
        '1510' => 'Aktiebolag ej offentligt ägda', // Privat
        '1520' => 'Övriga företag ej offentligt ägda', // Privat
        '1530' => 'Statligt ägda företag och organisationer', // Offentlig
        '1540' => 'Kommunalt ägda företag och organisationer', // Offentlig
        '1560' => 'Övriga organisationer', // Privat
        'US' => 'Uppgift saknas', // Uppgift saknas

        '0' => 'Samtliga',
        '1' => 'Offentlig sektor',
        '1-3' => 'Offentlig',
        '2' => 'Kommunal sektor',
        '3' => 'Landstingssektorn',
        '4-5' => 'Privat sektor',
        '4' => 'Privatanställda arbetare',
        '5' => 'Privatanställda tjänstemän',
    ];

    public static $simpleUtbildningsniva = [
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

    public static $utbildningsniva = [
        '1' => 'Förgymnasial utbildning kortare än 9 år',
        '2' => 'Förgymnasial utbildning, 9 (10) år',
        '3' => 'Gymnasial utbildning, högst 2 år',
        '4' => 'Gymnasial utbildning, 3 år',
        '5' => 'Eftergymnasial utbildning, mindre än 3 år',
        '6' => 'Eftergymnasial utbildning, 3 år eller mer',
        '7' => 'Forskarutbildning',
        //'8' => 'Eftergymnasial utbildning 3 år eller mer',
        //'9' => 'Eftergymnasial utbildning 3 år eller mer',
        'TOTALT' => 'Samtliga utbildningsnivåer',
        'US' => 'Uppgift saknas',
    ];

    public function getSimpleUtbildningnivaFromUtbildningsniva($utbildningsniva)
    {
        $id = array_search($utbildningsniva, self::$utbildningsniva);

        if ($id === false) {
            throw new \Exception("Tried to get Utbildningsnivå but failed, with: $utbildningsniva");
        }

        return self::$simpleUtbildningsniva[$id];
    }


    public static function getRegionName($from)
    {
        $id = self::getKeyValue($from, self::getKey('REGION'));

        if (array_key_exists($id, self::$regioner)) {
            return self::$regioner[$id];
        }

        return end(self::$regioner[99]);
    }

    public static function getSektionName($from, $simple = false)
    {
        $id = self::getKeyValue($from, self::getKey('SEKTOR'));

        if (array_key_exists($id, $simple ? self::$simpleSektioner : self::$sektioner)) {
            return self::$sektioner[$id];
        }

        return end(self::$sektioner);
    }

    public static function getUtbildningsniva($from, $simple = false)
    {
        $id = self::getKeyValue($from, self::getKey('UTBILDNINGSNIVA'));

        if (array_key_exists($id, $simple ? self::$simpleUtbildningsniva : self::$utbildningsniva)) {
            return self::$utbildningsniva[$id];
        }

        return end(self::$utbildningsniva);
    }

    public static function getKon($from)
    {
        $value = self::getKeyValue($from, self::getKey('SEX'));
        return self::$kon[$value];
    }

    public static function getAr($from)
    {
        return self::getKeyValue($from, self::getKey('YEAR'));
    }

    public static function getAlder($from)
    {
        return self::getKeyValue($from, self::getKey('AGE'));
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
