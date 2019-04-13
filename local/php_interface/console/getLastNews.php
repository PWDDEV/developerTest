<?php
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 13.04.2019
 * Time: 14:50
 */
use \Bitrix\Main\Loader;

set_time_limit(0);
ini_set('memory_limit', '1024M');

$url=@$argv[1]; // передаем rss url при вызове скрипта
$cnt=5; // сколько новостей показывать
$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', realpath(__DIR__ . '/../../../'));
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_CHECK", true);

require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_before.php");

if(!Loader::includeModule('iblock')) {
    print "Error iblock module including!";
    return; // Выводим сообщение об ошибке, если не подключается модуль ИБ
}
$parsedUrl = parse_url($url);
if (isset($parsedUrl["scheme"]))
    $domen = $parsedUrl["scheme"]."://".$parsedUrl["host"];
else
    $domen = $parsedUrl["host"];
if (isset($parsedUrl["port"]))
    $port = $parsedUrl["port"];
else
    $port = ($parsedUrl["scheme"] == "https"? 443 : 80);
$arRSS = \CIBlockRSS::GetNewsEx($domen, $port, $parsedUrl["path"], $parsedUrl["query"]);
$arRSS = \CIBlockRSS::FormatArray($arRSS, true);
$i=0;
while(++$i<$cnt) {
    print "---------------------------------------------------------------------\n";
    $item = array_shift($arRSS["item"]);
    print $item["title"]."\n---\n";
    print $item["link"]."\n---\n";
    print $item["description"]."\n";
    print "---------------------------------------------------------------------\n";
}