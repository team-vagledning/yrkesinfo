<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatisticsSource1 extends Migration
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
            'name' => 'Anställda 16-64 år med arbetsplats i regionen (dagbef) efter län, yrke (4-siffrig SSYK 2012) och kön. År 2014 - 2017',
            'meta' => $data,
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
            ewoJInF1ZXJ5IjogW3sKCQkJImNvZGUiOiAiUmVnaW9uIiwKCQkJInNlbGVjd
            GlvbiI6IHsKCQkJCSJmaWx0ZXIiOiAidnM6UmVnaW9uTMOkbjk5VVMiLAoJCQ
            kJInZhbHVlcyI6IFsKCQkJCQkiMDEiLAoJCQkJCSIwMyIsCgkJCQkJIjA0Iiw
            KCQkJCQkiMDUiLAoJCQkJCSIwNiIsCgkJCQkJIjA3IiwKCQkJCQkiMDgiLAoJ
            CQkJCSIwOSIsCgkJCQkJIjEwIiwKCQkJCQkiMTIiLAoJCQkJCSIxMyIsCgkJC
            QkJIjE0IiwKCQkJCQkiMTciLAoJCQkJCSIxOCIsCgkJCQkJIjE5IiwKCQkJCQ
            kiMjAiLAoJCQkJCSIyMSIsCgkJCQkJIjIyIiwKCQkJCQkiMjMiLAoJCQkJCSI
            yNCIsCgkJCQkJIjI1IiwKCQkJCQkiOTkiCgkJCQldCgkJCX0KCQl9LAoJCXsK
            CQkJImNvZGUiOiAiWXJrZTIwMTIiLAoJCQkic2VsZWN0aW9uIjogewoJCQkJI
            mZpbHRlciI6ICJpdGVtIiwKCQkJCSJ2YWx1ZXMiOiBbXQoJCQl9CgkJfSwKCQ
            l7CgkJCSJjb2RlIjogIktvbiIsCgkJCSJzZWxlY3Rpb24iOiB7CgkJCQkiZml
            sdGVyIjogIml0ZW0iLAoJCQkJInZhbHVlcyI6IFsKCQkJCQkiMSIsCgkJCQkJ
            IjIiCgkJCQldCgkJCX0KCQl9CgldLAoJInJlc3BvbnNlIjogewoJCSJmb3JtY
            XQiOiAianNvbiIKCX0sCgkiZW5kcG9pbnQiOiAiaHR0cDovL2FwaS5zY2Iuc2
            UvT1YwMTA0L3YxL2RvcmlzL3N2L3NzZC9TVEFSVC9BTS9BTTAyMDgvQU0wMjA
            4TS9ZUkVHNjAiCn0=
        ");

        return json_decode($data);
    }
}
