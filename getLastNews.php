<?
$url = 'https://lenta.ru/rss';

$rss = simplexml_load_file($url);

$i = 0;
foreach ($rss->channel->item as $item) {

    $nom = $i+1;

    echo $nom.').'.$item->title."\n";
    echo '<a href="'.$item->guid.'">Ссылка на новость</a>'."\n";
    echo $item->description."\n";
    $i++;
    if($i > 4){
        exit();
    }
}