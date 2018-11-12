<?
$url = 'https://lenta.ru/rss';

    
  $xml = simplexml_load_file($url, null, LIBXML_NOCDATA);
  foreach ($xml->channel->item as $value) {
    $dk = date($value->pubDate);
    $news[$dk] = $value;
  }

  ksort($news);
  $c = 0;

  foreach ($news as $new) {
    $c++;
    if($c<6){

echo $new->title.'
';
echo $new->link.'
';
echo $new->description.'
';
    }
  }

?>