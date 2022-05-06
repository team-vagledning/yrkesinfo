<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetArtalForOldBristindex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*$bristindex = \App\Bristindex::get();

        foreach ($bristindex as $b) {
            $b->artal = ($b->omfang == 1) ? '2019' : '2023';
            $b->meta = ['old' => true];
            $b->save();
        }*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*$bristindex = \App\Bristindex::get();

        foreach ($bristindex as $b) {
            $b->artal = null;
            $b->meta = null;
            $b->save();
        }*/
    }
}
