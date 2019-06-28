<?php
$url = 'https://lenta.ru/rss';   

$rss = simplexml_load_file($url);      
$i = 0;
foreach ($rss->channel->item as $item) {
	if ($i > 4) break;

	echo $item->title . "\n";
	echo $item->link . "\n"; 
	echo $item->description . "\n";

	$i++;
}
?>