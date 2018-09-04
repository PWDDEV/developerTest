<?php

class RSSParse {

    protected $url;

    function __construct($url)
    {
        $this->url = $url;
    }

    public function run(){

        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );

        $response = file_get_contents($this->url, false, stream_context_create($arrContextOptions));

        $rss = simplexml_load_string($response);

        $index = 0;

        echo "\n\r ===================================================\n\r";

        foreach ($rss->channel->item  as $item) {

            if($index>4) break;

            echo "\n\r";
            echo "Link: ".$item->link."\n\r";
            echo "Title: ".$item->title."\n\r";
            echo "Description: ".$item->description."\n\r";
            echo "===================================================\n\r";


            $index ++;
        }
    }
}

$rssParser = new RSSParse('https://lenta.ru/rss');
$rssParser->run();
