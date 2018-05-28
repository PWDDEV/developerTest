<?php
$xml_file_link = "https://lenta.ru/rss";

$xml_file = simplexml_load_file($xml_file_link) or die ("Error: Cannot get the xml file");
for ($i = 0; $i < 5; $i++) {
    $item = $xml_file->channel->item[$i];

    echo $item->title . "<br />";
    echo $item->link . "<br />";
    echo $item->description . "<br /><br />";
}
?>