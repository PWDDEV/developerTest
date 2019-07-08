<?php

$url = "https://lenta.ru/rss";
$count = 5;
$response = simplexml_load_file($url);
$items = array();

foreach($response->channel->item as $item){
	if(count($items) < $count){
		$items[] = array('title'=>$item->title->__toString(), 'link'=>$item->link->__toString(), 'description'=>$item->description->__toString());
	} 
}
foreach ($items as $item){
    print $item['title'].' '.$item['link'].' '.$item['description'].'/n';
}