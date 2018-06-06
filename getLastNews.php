<?php

if(empty($_SERVER['DOCUMENT_ROOT'])){
    $_SERVER['DOCUMENT_ROOT'] = '/home/wdev1/web/test.wdev1.ru/public_html';
}
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

use \Bitrix\Main\Loader;
use \Br\Base\Debug;

Loader::includeModule("br.base");

$oHttp = new \Bitrix\Main\Web\HttpClient;
$sRssResult = $oHttp->Get('https://lenta.ru/rss');

if($sRssResult){
    $objXML = new CDataXML();
    $objXML->LoadString($sRssResult);
    $aResult = $objXML->GetArray();

    foreach($aResult['rss']['#']['channel'][0]['#']['item'] as $iKey => $aItem){

        echo "---------------------\n";
        echo $aItem['#']['title'][0]['#']."\n";
        echo $aItem['#']['link'][0]['#']."\n";
        echo $aItem['#']['description'][0]['#']."\n";
        echo "---------------------\n";
        echo "\n";

        if($iKey >= 4){
            break;
        }
    }

}

