<?php

namespace App\Collections;

use App\YrkeseditorYrke;
use Illuminate\Database\Eloquent\Collection;

class Formagor extends Collection
{
    protected static $cacheTag = 'yrkessok';
    protected static $cacheKey = 'formagor';

    protected static $data = [
        [
            'id' => 1,
            'name' => 'Analytisk förmåga',
        ],
        [
            'id' => 2,
            'name' => 'Argumentationsförmåga',
        ],
        [
            'id' => 3,
            'name' => 'Balanssinne',
        ],
        [
            'id' => 4,
            'name' => 'Beslutsförmåga',
        ],
        [
            'id' => 5,
            'name' => 'Empatisk förmåga',
        ],
        [
            'id' => 6,
            'name' => 'Finmotorik',
        ],
        [
            'id' => 7,
            'name' => 'Förmåga att bemöta människor',
        ],
        [
            'id' => 8,
            'name' => 'Förmåga att följa regler och instruktioner',
        ],
        [
            'id' => 9,
            'name' => 'Förmåga att inspirera och stödja andra',
        ],
        [
            'id' => 10,
            'name' => 'Förmåga att passa tider',
        ],
        [
            'id' => 11,
            'name' => 'Förmåga att ta ansvar',
        ],
        [
            'id' => 12,
            'name' => 'Förmåga att vara flexibel',
        ],
        [
            'id' => 13,
            'name' => 'Förmåga att värdera information',
        ],
        [
            'id' => 14,
            'name' => 'Förmåga till abstrakt tänkande',
        ],
        [
            'id' => 15,
            'name' => 'Förmåga till logiskt tänkande',
        ],
        [
            'id' => 16,
            'name' => 'Förmåga till självkännedom',
        ],
        [
            'id' => 17,
            'name' => 'Förtroendeingivande',
        ],
        [
            'id' => 18,
            'name' => 'God fysik',
        ],
        [
            'id' => 19,
            'name' => 'Gott omdöme',
        ],
        [
            'id' => 20,
            'name' => 'Hörsel',
        ],
        [
            'id' => 21,
            'name' => 'Initiativförmåga',
        ],
        [
            'id' => 22,
            'name' => 'Inlärningsförmåga',
        ],
        [
            'id' => 23,
            'name' => 'Kommunikativ förmåga',
        ],
        [
            'id' => 24,
            'name' => 'Koncentrationsförmåga',
        ],
        [
            'id' => 25,
            'name' => 'Konflikthanteringsförmåga',
        ],
        [
            'id' => 26,
            'name' => 'Konstnärlig förmåga',
        ],
        [
            'id' => 27,
            'name' => 'Koordinationsförmåga',
        ],
        [
            'id' => 28,
            'name' => 'Kreativ förmåga',
        ],
        [
            'id' => 29,
            'name' => 'Kundfokus',
        ],
        [
            'id' => 30,
            'name' => 'Kvalitetsinriktad',
        ],
        [
            'id' => 31,
            'name' => 'Ledarskapsförmåga',
        ],
        [
            'id' => 32,
            'name' => 'Luktsinne',
        ],
        [
            'id' => 33,
            'name' => 'Målinriktad',
        ],
        [
            'id' => 34,
            'name' => 'Noggrannhet',
        ],
        [
            'id' => 35,
            'name' => 'Organisationsförmåga',
        ],
        [
            'id' => 36,
            'name' => 'Pedagogisk förmåga',
        ],
        [
            'id' => 37,
            'name' => 'Problemlösningsförmåga',
        ],
        [
            'id' => 38,
            'name' => 'Psykisk stabilitet',
        ],
        [
            'id' => 39,
            'name' => 'Resultatinriktad',
        ],
        [
            'id' => 40,
            'name' => 'Rörlighet',
        ],
        [
            'id' => 41,
            'name' => 'Samarbetsförmåga',
        ],
        [
            'id' => 42,
            'name' => 'Serviceinriktad',
        ],
        [
            'id' => 43,
            'name' => 'Simultanförmåga',
        ],
        [
            'id' => 44,
            'name' => 'Självständighet',
        ],
        [
            'id' => 45,
            'name' => 'Social förmåga',
        ],
        [
            'id' => 46,
            'name' => 'Stresstålighet',
        ],
        [
            'id' => 47,
            'name' => 'Styrka',
        ],
        [
            'id' => 48,
            'name' => 'Syn',
        ],
        [
            'id' => 49,
            'name' => 'Säkerhetsfokus',
        ],
        [
            'id' => 50,
            'name' => 'Säljinriktad',
        ],
        [
            'id' => 51,
            'name' => 'Tålamod',
        ],
        [
            'id' => 52,
            'name' => 'Uppmärksam',
        ],
        [
            'id' => 53,
            'name' => 'Uthållighet',
        ],
        [
            'id' => 54,
            'name' => 'Utvecklingsinriktad',
        ],
    ];

    public function __construct($items = [])
    {
        if (cache()->tags([self::$cacheTag])->has(self::$cacheKey)) {
            $data = cache()->tags([self::$cacheTag])->get(self::$cacheKey);
        } else {
            $data = empty($items) ? static::$data : $items;

            foreach ($data as $key => $item) {
                $data[$key]['antal_yrken'] = YrkeseditorYrke::getByFormagor($item['name'])->count();
            }

            cache()->tags([self::$cacheTag])->put(self::$cacheKey, $data);
        }

        parent::__construct($data);
    }
}
