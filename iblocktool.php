<?
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

use \Bitrix\Main\Loader;
use \Br\Base\Debug;
use \Br\Base\IblockTool;

Loader::includeModule("br.base");


$oNewsList = new IblockTool(1, ['ID' => 'DESC'], ['%NAME' => 'Форум'], ['NAME', 'ID']);
$oNewsList->setCacheTtl(1);
$oNewsList->setPageCount(10);
$aNewsListResult = $oNewsList->getList();


Debug::pr($aNewsListResult);

