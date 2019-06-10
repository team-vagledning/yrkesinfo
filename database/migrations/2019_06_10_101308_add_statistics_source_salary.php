<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatisticsSourceSalary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $data = self::data();

        \App\YrkesstatistikSource::create([
            'supplier' => 'SCB',
            'name' => 'lon,sektor,kon',
            'description' => 'Genomsnittlig månadslön och lönspridning efter sektor, Yrke (SSYK 2012), kön, tabellinnehåll och år',
            'meta' => $data,
            'aggregator' => \App\Aggregators\Yrkesstatistik\LonSektorKon::class,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\YrkesstatistikSource::orderBy('id', 'desc')->first()->delete();
    }

    public static function data()
    {
        $data = base64_decode("            
            ewoJInF1ZXJ5IjogW3sKCQkJImNvZGUiOiAiU2VrdG9yIiwKCQkJInNlbGVjd
            GlvbiI6IHsKCQkJCSJmaWx0ZXIiOiAiaXRlbSIsCgkJCQkidmFsdWVzIjogWw
            oJCQkJCSIwIgoJCQkJXQoJCQl9CgkJfSwKCQl7CgkJCSJjb2RlIjogIllya2U
            yMDEyIiwKCQkJInNlbGVjdGlvbiI6IHsKCQkJCSJmaWx0ZXIiOiAiaXRlbSIs
            CgkJCQkidmFsdWVzIjogW10KCQkJfQoJCX0sCgkJewoJCQkiY29kZSI6ICJLb
            24iLAoJCQkic2VsZWN0aW9uIjogewoJCQkJImZpbHRlciI6ICJpdGVtIiwKCQ
            kJCSJ2YWx1ZXMiOiBbCgkJCQkJIjEiLAoJCQkJCSIyIiwKCQkJCQkiMSsyIgo
            JCQkJXQoJCQl9CgkJfSwKCQl7CgkJCSJjb2RlIjogIkNvbnRlbnRzQ29kZSIs
            CgkJCSJzZWxlY3Rpb24iOiB7CgkJCQkiZmlsdGVyIjogIml0ZW0iLAoJCQkJI
            nZhbHVlcyI6IFsKCQkJCQkiMDAwMDAwQzUiLAoJCQkJCSIwMDAwMDBDNyIsCg
            kJCQkJIjAwMDAwMENBIgoJCQkJXQoJCQl9CgkJfQoJXSwKCSJyZXNwb25zZSI
            6IHsKCQkiZm9ybWF0IjogImpzb24iCgl9LAoJImVuZHBvaW50IjogImh0dHA6
            Ly9hcGkuc2NiLnNlL09WMDEwNC92MS9kb3Jpcy9zdi9zc2QvU1RBUlQvQU0vQ
            U0wMTEwL0FNMDExMEEvTG9uZVNwcmlkU2VrdG9yWXJrNEEiCn0=
        ");

        return json_decode($data);
    }
}
