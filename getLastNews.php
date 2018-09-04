<?
@set_time_limit(0);
@ini_set('memory_limit', '512M');

$xml = simplexml_load_file('https://lenta.ru/rss');
if(!$xml) {
    foreach(libxml_get_errors() as $error) {
        echo $error->message . "\n";
    }
    die();
}
$counter = 0;
foreach($xml->channel->item as $item) {
    
    if($counter >= 5) {
        break;
    }
    
    echo "\n\n----" . ($counter + 1) . "----\n\n";
    echo (string)$item->title . "\n";
    echo (string)$item->link . "\n";
    echo (string)$item->description . "\n";
    
    $counter++;
}

echo 'done!';

?>