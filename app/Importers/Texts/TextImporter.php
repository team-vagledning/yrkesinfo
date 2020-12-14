<?php

namespace App\Importers\Texts;

use App\Text;
use App\Yrkesomrade;

class TextImporter
{
    public function run()
    {
        foreach (self::texts() as $text) {
            $yrkesomrade = Yrkesomrade::where('external_id', $text['external_id'])->first();

            Text::updateOrCreate([
                'ref_id' => $yrkesomrade->id,
                'ref_type' => Yrkesomrade::class,
                'text_type' => $text['data']['text_type'],
            ], [
                'content' => $text['data']['content']
            ]);
        }
    }

    private static function texts()
    {
        return [['external_id' => 'X82t_awd_Qyc', 'data' =>
            ['text_type' => 'ingress', 'content' => "Har du god servicekänsla och är duktig på att förmedla information? Inom administration, ekonomi och juridik hittar du yrken som till exempel informatörer, jurister och handläggare."]],

            ['external_id' => 'j7Cq_ZJe_GkT', 'data' =>
                ['text_type' => 'ingress', 'content' => "Det här yrkesområdet behöver utbildad personal och speciellt kvinnor. Inom bygg och anläggning hittar du yrken som till exempel anläggningsarbetare, ingenjörer, tekniker och murare. "]],

            ['external_id' => 'apaJ_2ja_LuF', 'data' =>
                ['text_type' => 'ingress', 'content' => "Data/it är ett yrkesområde som ständigt utvecklas med nya yrkestitlar.  Dataspelsutvecklare, IT-molnspecialist, front-end-utvecklare, nätverkstekniker är bara några exempel på yrken som branschen söker."]],

            ['external_id' => 'RPTn_bxG_ExZ', 'data' =>
                ['text_type' => 'ingress', 'content' => "Är du intresserad av att ge god service och förstår kundernas önskemål? Då kan det här yrkesområdet vara något för dig. Här hittar du till exempel butikssäljare, marknadsförare och affärsutvecklare."]],

            ['external_id' => 'ScKy_FHB_7wT', 'data' =>
                ['text_type' => 'ingress', 'content' => "Ett yrkesområde där förmågan att ge service är gemensamt för alla som jobbar inom branschen.  Här hittar du bland annat kockar, kallskänkor, hovmästare och servitörer."]],

            ['external_id' => 'NYW6_mP6_vwf', 'data' =>
                ['text_type' => 'ingress', 'content' => "Ett yrkesområde där du kan jobba med allt från förebyggande hälsovård, undersökning till behandling och omvårdnad. Exempel på yrken är sjuksköterskeyrken, undersköterskor och tandhygienister."]],

            ['external_id' => 'yhCP_AqT_tns', 'data' =>
                ['text_type' => 'ingress', 'content' => "Ett yrkesområde där du kan göra allt från att sköta om en fastighet, felsökning av anläggningar till installationer av olika slag.  Här hittar du yrken som styr- och reglertekniker, elektriker, fordonsmekaniker och fastighetstekniker."]],

            ['external_id' => '9puE_nYg_crq', 'data' =>
                ['text_type' => 'ingress', 'content' => "Ett yrkesområde där det handlar om kreativitet och skapande i många former. Här hittar du yrken som webbdesigner, fotograf, filmare, artist, musiker, sångare och bloggare.  "]],

            ['external_id' => 'VuuL_7CH_adj', 'data' =>
                ['text_type' => 'ingress', 'content' => "Naturbruk handlar om att arbeta nära naturen, ofta med händerna och kroppen.  Här hittar du yrken inom jord- och skogsbruket, trädgård och parkanläggning. "]],

            ['external_id' => 'MVqp_eS8_kDZ', 'data' =>
                ['text_type' => 'ingress', 'content' => "Pedagogiskt arbete handlar om att inspirera barn, ungdomar och vuxna till lärande och utveckling. Här hittar du yrken som förskolelärare, fritidspedagog och lärare.  "]],

            ['external_id' => 'E7hm_BLq_fqZ', 'data' =>
                ['text_type' => 'ingress', 'content' => "Inom säkerhetsarbete hittar du bland annat väktare, poliser, brandmän, kriminalvårdare eller gränskontrollant. "]],

            ['external_id' => 'GazW_2TU_kJw', 'data' =>
                ['text_type' => 'ingress', 'content' => "Socialt arbete handlar om att stötta och stödja människor i olika stadier i livet. Här hittar du yrken som socialsekreterare, personlig assistent, barnskötare och vårdare. "]],

            ['external_id' => 'wTEr_CBC_bqh', 'data' =>
                ['text_type' => 'ingress', 'content' => "Ett brett yrkesområde med arbetsuppgifter som svetsning, maskinoperatör, snickeri och bevakning av industriella processer. Här hittar du yrken som  snickare, maskinoperatör, processoperatör och gasskärare. "]],

            ['external_id' => 'ASGV_zcE_bWf', 'data' =>
                ['text_type' => 'ingress', 'content' => "Transport handlar inte bara om att transportera varor utan också människor. Ett ansvarsfyllt jobb med omväxlande arbetsuppgifter. Här hittar du yrken som lastbilsförare, lokförare och bussförare. "]],

            ['external_id' => 'PaxQ_o1G_wWH', 'data' =>
                ['text_type' => 'ingress', 'content' => "Ett yrkesområde som erbjuder väldigt många material och arbetsformer. Här hittar du yrken som exempelvis smeder, finmekaniker, bagare och konditorer. "]],

            ['external_id' => 'Uuf1_GMh_Uvw', 'data' =>
                ['text_type' => 'ingress', 'content' => "Ett yrkesområde som vuxit de senaste åren och anledningen är att intresset för att vårda sitt utseende och kropp har ökat. Här hittar du yrken som exempelvis frisörer, hudterapeuter, massörer och fotterapeuter.  "]],

            ['external_id' => 'bH5L_uXD_ZAX', 'data' =>
                ['text_type' => 'ingress', 'content' => "Inom yrkesområdet militärt arbete deltar man i försvaret av Sverige. Här hittar du yrken som exempelvis soldat, officer och specialistofficer.  "]],

            ['external_id' => 'whao_Q6A_ScE', 'data' =>
                ['text_type' => 'ingress', 'content' => "Vård och skydd av naturen, sanering, källsortering och återvinning är några av de arbetsuppgifter som finns inom yrkesområdet Sanering och renhållning. Här hittar du yrken som återvinningsarbetare, städare, sotare och bilrekonditionerare.  "]],

            ['external_id' => 'kJeN_wmw_9wX', 'data' =>
                ['text_type' => 'ingress', 'content' => "Det forskas mycket på det naturvetenskapliga området och naturvetarens kunskap är efterfrågade. Både på universitet och på företag. Här hittar du yrken som exempelvis kemister, läkare och miljö- och hälsoskyddsinspektörer"]],

            ['external_id' => '6Hq3_tKo_V57', 'data' =>
                ['text_type' => 'ingress', 'content' => "Att jobba med tekniskt arbete handlar ofta om tillverkning och det kan röra sig om allt från fordon till livsmedel. Inom många yrken behöver du kunna programmering. Här hittar du yrken som exempelvis civilingenjör, arkitekter och laboratorieingenjörer. "]]];
    }
}
