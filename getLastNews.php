<?php

$xml = @simplexml_load_file('https://lenta.ru/rss');
$arXML = json_decode(json_encode($xml), TRUE);
$count = count($arXML['channel']['item']);
for ($i = $count - 5; $i < $count; $i++) {
    echo 'Название: ' . $arXML['channel']['item'][$i]['title'] . "\n";
    echo 'Ссылка на новость: ' . $arXML['channel']['item'][$i]['link'] . "\n";
    echo 'Анонс: ' . implode($arXML['channel']['item'][$i]['description']) . "\n\n";
}
