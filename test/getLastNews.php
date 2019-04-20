<?php

class RssReader
{
    protected $items = [];

    public function __construct($url)
    {
        if (!$url) {
            throw new Exception('Url is undefined.');
        }

        $this->loadRss($url);
    }

    private function loadRss($url)
    {
        $rss = file_get_contents($url);
        $rss = simplexml_load_string($rss);

        foreach ($rss->channel->item as $item) {
            $pubDate = strtotime($item->pubDate);
            $this->items[$pubDate] = $item;
        }
    }

    public function getItems($count = 0)
    {
        if ($count > 0) {
            return array_slice($this->items, 0, $count);
        } else {
            return $this->items;
        }
    }
}

$rss = new RssReader('https://lenta.ru/rss');

foreach ($rss->getItems(5) as $item) {
    echo "\n Название: " . $item->title;
    echo "\n Ссылка: " . $item->link;
    echo "\n Описание: " . $item->description;
}