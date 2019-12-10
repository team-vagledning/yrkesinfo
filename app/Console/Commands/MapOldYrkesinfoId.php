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
