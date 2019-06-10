<?php

if(!class_exists('SimpleXmlElement')) {
    die('install php-xml, php-simplexml');
}

class Lenta {

    const URL = 'https://lenta.ru/rss';

    var $data;

    function __construct() {
        $content = file_get_contents(self::URL);
        $this->data = new SimpleXmlElement($content);
    }

    function show($count) {
        $n = 0;
        foreach ($this->data->channel->item as $item) {
            if ($n++ >= $count) {
                break;
            }
            echo $item->title . PHP_EOL;
            echo $item->guid . PHP_EOL;
            echo trim($item->description) . PHP_EOL;
            echo PHP_EOL;
        }
    }

}

$lenta = new Lenta();

$news = $lenta->show(5); 