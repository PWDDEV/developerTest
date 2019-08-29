#!/usr/bin/php
<?php
$_SERVER["DOCUMENT_ROOT"] = "/shared/httpd/pw-task/htdocs/";
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
set_time_limit(0);

$lentaUrl = 'https://lenta.ru/rss';

$result = array();
$feed = simplexml_load_file($lentaUrl);
$newsCounter = 0;
$newsLimit = 5;
foreach ($feed->xpath('//item') as $news) {
    if ($newsCounter < $newsLimit) {
        printf("%u. %s\r\n", $newsCounter + 1, str_repeat('=', 50));
        printf("%s\r\n", htmlspecialchars(strip_tags(trim((string) $news->title))));
        printf("%s\r\n", htmlspecialchars(strip_tags(trim((string) $news->link))));
        printf("%s\r\n", htmlspecialchars(strip_tags(trim(((string) $news->description)))));
        printf("%s\r\n", str_repeat('=', 50));
        echo "\r\n";
        $newsCounter++;
    } else {
        break;
    }
}
foreach ($result as $item) {

}

require($_SERVER["DOCUMENT_ROOT"] . "bitrix/modules/main/include/epilog_after.php");
?>
