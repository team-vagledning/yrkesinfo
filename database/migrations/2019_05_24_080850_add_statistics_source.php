<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatisticsSource extends Migration
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
            'name' => 'Anställda (yrkesregistret) 16-64 år efter Yrke (SSYK 2012), arbetsställets sektortillhörighet, kön och år',
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
            ewoJInF1ZXJ5IjogW3sKCQkJImNvZGUiOiAiWXJrZTIwMTIiLAoJCQkic2VsZ
            WN0aW9uIjogewoJCQkJImZpbHRlciI6ICJpdGVtIiwKCQkJCSJ2YWx1ZXMiOi
            BbXQoJCQl9CgkJfSwKCQl7CgkJCSJjb2RlIjogIkFyYmV0c1Nla3RvciIsCgk
            JCSJzZWxlY3Rpb24iOiB7CgkJCQkiZmlsdGVyIjogIml0ZW0iLAoJCQkJInZh
            bHVlcyI6IFsKCQkJCQkiMTEiLAoJCQkJCSIxMTEwIiwKCQkJCQkiMTEyMCIsC
            gkJCQkJIjExMzAiLAoJCQkJCSIxNSIsCgkJCQkJIjE1MTAiLAoJCQkJCSIxNT
            IwIiwKCQkJCQkiMTUzMCIsCgkJCQkJIjE1NDAiLAoJCQkJCSIxNTYwIiwKCQk
            JCQkiVVMiCgkJCQldCgkJCX0KCQl9LAoJCXsKCQkJImNvZGUiOiAiS29uIiwK
            CQkJInNlbGVjdGlvbiI6IHsKCQkJCSJmaWx0ZXIiOiAiaXRlbSIsCgkJCQkid
            mFsdWVzIjogWwoJCQkJCSIxIiwKCQkJCQkiMiIKCQkJCV0KCQkJfQoJCX0KCV
            0sCgkicmVzcG9uc2UiOiB7CgkJImZvcm1hdCI6ICJqc29uIgoJfSwKCSJlbmR
            wb2ludCI6ICJodHRwOi8vYXBpLnNjYi5zZS9PVjAxMDQvdjEvZG9yaXMvc3Yv
            c3NkL1NUQVJUL0FNL0FNMDIwOC9BTTAyMDhFL1lSRUc1MCIKfQ==
        ");

        return json_decode($data);
    }
}
