<?php

$url = 'https://lenta.ru/rss';       //адрес RSS ленты

$rss = \simplexml_load_file($url);       //Интерпретирует XML-файл в объект

//цикл для обхода всей RSS ленты
$i = 0;
$countNews = 5;
foreach ($rss->channel->item as $item) {
    $i++;
    echo 'Название - ' . trim($item->title) . PHP_EOL;
    echo 'Ссылка - ' . trim($item->link) . PHP_EOL;
    echo 'Описание - ' . trim($item->description) . PHP_EOL;
    if ($i === $countNews) {
        break;
    }
}