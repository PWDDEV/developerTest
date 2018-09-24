<?/* 
Можно вот так http://joxi.ru/GrqDpEHQdKQZAz

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->IncludeComponent("bitrix:rss.show","",Array(
        "URL" => "https://lenta.ru/rss", 
        "OUT_CHANNEL" => "N",
        "PROCESS" => "NONE", 
        "NUM_NEWS" => "5", 
        "CACHE_TYPE" => "A", 
        "CACHE_TIME" => "3600" 
    )
);
*/


//Но мне нравится вот так
// http://joxi.ru/YmEdMOi0z69Rr6

$url = "https://lenta.ru/rss"; 

$rss = simplexml_load_file($url);
if ( $rss === false ) die('Error parse RSS: '.$url);
$c = 1;
$dm = str_repeat('-',100);
foreach ($rss->channel->item as $item) {
    if ($c <= 5) {
        $ar = [
            $c.'. '.$item->title,
            $dm,
            $item->link,
            $dm,
            $item->description,
            $dm,
            ''
        ];
        echo implode(PHP_EOL,$ar);//PHP_EOL т.к. вызов будет из консоли
        $c++;
    } else break;
}
?>