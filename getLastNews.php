<?php
$content = file_get_contents('https://lenta.ru/rss');

$dom = new DOMDocument;
$dom->loadXML($content);
$xpath = new DOMXpath($dom);

$elements = $xpath->query('*/item');
foreach($elements as $key => $element){
    $nodes = $element->childNodes;
    foreach ($nodes as $node) {
    switch ($node->nodeName) {
        case "title":
            $title =  $node->nodeValue;
        break;
        case "link":
            $link =  $node->nodeValue;
        break;
        case "description" :
            $description = $node->nodeValue;
        break;
    }
    }
    echo "$title\n$link\n$description\n";
    if ($key == 4) {
        break;
    }
    echo "#\n";
}
?>
