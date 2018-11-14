<?php

function request($url){
     // create curl resource 
     $ch = curl_init(); 
     // set url 
     curl_setopt($ch, CURLOPT_URL, $url); 
     //return the transfer as a string 
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
     // $output contains the output string 
     $output = curl_exec($ch); 
     // close curl resource to free up system resources 
     curl_close($ch);
     return $output;
}

function prettyPrint($data){
    ?>
    <pre><?php print_r($data);?></pre>
    <?php
}


function sort_by_date($a, $b){
    // asc
    return -(strtotime($a['pub_date']) - strtotime($b['pub_date']));
}

// news url
$url_data = 'https://lenta.ru/rss';

// show count
$show_count = 5;

//string of data
$data = request($url_data);

//parsed xml data
$xml_data = new SimpleXMLElement($data);

// iterate all items
$items_count = count($xml_data->channel->item);


$show_result = [];

for($i = 0; $i < $items_count; $i++){
    $item = $xml_data->channel->item[$i];
    $show_result[] = [
        'name' => (string)$item->title,
        'link' => (string)$item->link,
        'pub_date' => (string)$item->pubDate,
        'description' => (string)$item->description
    ];
}

unset($item);

$row_delimiter = '<br>';
if (php_sapi_name() == 'cli'){
    $row_delimiter = "\n";
}

// xml данные идут по убыванию даты  можно взять просто первые пять

// sort
usort($show_result, 'sort_by_date');


//show
foreach(array_slice($show_result, 0, $show_count) as $item){
    echo "title:{$item['name']}{$row_delimiter}".
          "link:{$item['link']}{$row_delimiter}".
          "publicate date:{$item['pub_date']}{$row_delimiter}".
          "description:{$item['description']}".$row_delimiter.
                                              $row_delimiter;
} 
