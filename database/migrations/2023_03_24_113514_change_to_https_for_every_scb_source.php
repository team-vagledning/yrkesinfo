<?php

use App\YrkesstatistikSource;
use Illuminate\Database\Migrations\Migration;

class ChangeToHttpsForEveryScbSource extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sources = YrkesstatistikSource::where('supplier', 'SCB')->get();

        foreach ($sources as $source) {
            $source->update([
                'meta->endpoint' => str_replace('http://', 'https://', $source->meta['endpoint'])
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sources = YrkesstatistikSource::where('supplier', 'SCB')->get();

        foreach ($sources as $source) {
            $source->update([
                'meta->endpoint' => str_replace('https://', 'http://', $source->meta['endpoint'])
            ]);
        }
    }
}
