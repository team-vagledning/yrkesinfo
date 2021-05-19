<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewYrkestatistikSources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sources = \App\YrkesstatistikSource::all();

        foreach ($sources as $source) {
            if (in_array($source->name, ['lon,sektor,kon', 'lon,sektor,kon,utbildningsniva'])) {
                continue;
            }

            $newSource = $source->replicate();

            $newSource->description .= " - från 2019";

            $meta = $newSource->meta;
            $meta['endpoint'] .= "N";

            if ($source->name === 'anstallda,utbildningsniva,kon') {
                if ($meta['query'][1]['code'] === 'UtbNivaSUN2000') {
                    $meta['query'][1]['code'] = 'UtbNivaSun2020';
                }
            }

            $newSource->meta = $meta;

            $newSource->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sources = \App\YrkesstatistikSource::skip(5)->get();

        foreach ($sources as $source) {
            $source->delete();
        }
    }
}
