
usage:

$a = new CachedIblockElement();


$items = $a->GetList(['IBLOCK_ID' => 7],['NAME', 'ID', 'CODE',]);

$items = $a->GetList(['IBLOCK_ID' => 7]);

$items = $a->GetList(['IBLOCK_ID' => [1,2,3]], ['NAME', 'ID'], ['NAME' => 'ASC', 'CODE' => 'DESC']);
 
