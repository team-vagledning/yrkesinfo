<?php

namespace App\Console\Commands;

use App\Yrkesomrade;
use Illuminate\Console\Command;

class MapOldYrkesinfoId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'map:old-yrkesinfo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Maps old yrkesinfo to new';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url = 'https://arbetsformedlingen.se/rest/yrkesvagledning/rest/vagledning/yrken/yrkesomraden';
        $oldYrkesomraden = json_decode(file_get_contents($url));

        foreach ($oldYrkesomraden as $oldYrkesomrade) {

            // Fix for Hantverksyrken
            if ($oldYrkesomrade->namn == "Hantverksyrken") {
                $oldYrkesomrade->namn = "Hantverk";
            }

            // Fix för Militärt arbete
            if ($oldYrkesomrade->namn == "Militärt arbete") {
                $oldYrkesomrade->namn = "Militära yrken";
            }

            // Fix för Naturvetenskapligt arbete
            if ($oldYrkesomrade->namn == "Naturvetenskapligt arbete") {
                $oldYrkesomrade->namn = "Naturvetenskap";
            }

            // Fix för Pedagogiskt arbete
            if ($oldYrkesomrade->namn == "Pedagogiskt arbete") {
                $oldYrkesomrade->namn = "Pedagogik";
            }

            // Fix för Socialt arbete
            if ($oldYrkesomrade->namn == "Socialt arbete") {
                $oldYrkesomrade->namn = "Yrken med social inriktning";
            }

            // Fix för Säkerhetsarbete
            if ($oldYrkesomrade->namn == "Säkerhetsarbete") {
                $oldYrkesomrade->namn = "Säkerhet och bevakning";
            }

            // Fix för Tekniskt arbete
            if ($oldYrkesomrade->namn == "Tekniskt arbete") {
                $oldYrkesomrade->namn = "Yrken med teknisk inriktning";
            }

            // Fix för Transport
            if ($oldYrkesomrade->namn == "Transport") {
                $oldYrkesomrade->namn = "Transport, distribution, lager";
            }

            $yrkesomrade = Yrkesomrade::where('name', $oldYrkesomrade->namn)->first();

            if (! $yrkesomrade) {
                throw new \Exception("Could not find yrkesomrade with name: {$oldYrkesomrade->namn}");
            }

            $extras = $yrkesomrade->extras;

            $extras['old_yrkesinfo_id'] = $oldYrkesomrade->id;

            $yrkesomrade->update([
                'extras' => $extras
            ]);
        }
    }
}
