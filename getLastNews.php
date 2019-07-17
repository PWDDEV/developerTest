#!/usr/bin/php
<?php
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/");
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true); //запрет сбора статистики на данной странице.
define("NOT_CHECK_PERMISSIONS", true); //отключение проверки прав на доступ к файлам и каталогам
define('NO_AGENT_CHECK', true); //отключение выполнение всех агентов

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

@set_time_limit(0); // сброс ограничения времени выполнения скрипта
@ignore_user_abort(true); // при отключении клиента прерывать выполнение скрипта

CModule::IncludeModule("iblock");

function showResult($value, $key, $arFields)
{
    if (in_array($key, $arFields))
    {
        echo $value . PHP_EOL;
    }
}

$arFields = [
    "title",
    "link",
    "description"
];

$arParams = [
    "URL" => "https://lenta.ru/rss",
    "NUM_NEWS" => 5
];

if (isset($arParams["URL"]))
{
    $parsedUrl = parse_url($arParams["URL"]);
    if (is_array($parsedUrl))
    {
        if (isset($parsedUrl["scheme"]))
            $arParams["SITE"] = $parsedUrl["scheme"] . "://" . $parsedUrl["host"];
        else
            $arParams["SITE"] = $parsedUrl["host"];

        if (isset($parsedUrl["port"]))
            $arParams["PORT"] = $parsedUrl["port"];
        else
            $arParams["PORT"] = ($parsedUrl["scheme"] == "https" ? 443 : 80);

        $arParams["PATH"] = $parsedUrl["path"];
        $arParams["QUERY_STR"] = $parsedUrl["query"];
    }
}

$arResult = CIBlockRSS::GetNewsEx($arParams["SITE"], $arParams["PORT"], $arParams["PATH"], $arParams["QUERY_STR"], $arParams["OUT_CHANNEL"]);
$arResult = CIBlockRSS::FormatArray($arResult);

if ($arParams["NUM_NEWS"] > 0 && !empty($arResult["item"]) && is_array($arResult["item"]))
{
    while (count($arResult["item"]) > $arParams["NUM_NEWS"])
    {
        array_pop($arResult["item"]);
    }

    array_walk_recursive($arResult["item"], "showResult", $arFields);
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
?>