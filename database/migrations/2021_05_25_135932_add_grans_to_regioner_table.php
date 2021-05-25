<?php

use App\Region;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGransToRegionerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('regioner', function (Blueprint $table) {
            $table->jsonb('grans')->nullable();
        });

        $import = json_decode(file_get_contents(storage_path('imports/regioner/regioner.json')));

        foreach ($import as $i) {
            $region = Region::where('name', $i->lansnamn)->firstOrFail();
            $region->grans = $i->grans;
            $region->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('regioner', function (Blueprint $table) {
            $table->dropColumn('grans');
        });
    }
}
