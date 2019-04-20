<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php'); ?>

<?
include($_SERVER['DOCUMENT_ROOT'] . '/include/pride/Elements.php');

$banners = new \Test\Elements(12);

//get all properties with params
$items = $banners->getElements([
    'select' => ['NAME', 'ID', 'IBLOCK_ID', 'PROPERTY_*'],
    'order' => ['SORT' => 'ASC'],
]);

print_r($items);

//get single property
$items = $banners->getElements([
    'select' => ['NAME', 'PROPERTY_HREF'],
    'order' => ['SORT' => 'ASC'],
]);

print_r($items);

?>

<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>