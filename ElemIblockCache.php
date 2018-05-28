<?
/**
 * Class ElemIblockCacheTable
 * в init.php подключаем класс
 * \Bitrix\Main\Loader::registerAutoLoadClasses(null, array('ElemIblockCacheTable' => '/local/php_interface/ElemIblockCacheTable.php'));
 *  передаем поля, фильтр и сортировку в ElemIblockCacheTable::getElem($select, $filter, $order);
 */
class ElemIblockCacheTable
{
    /**
     * @param array $select
     * @param array $filter
     * @param array $order
     * @return array
     */
    public static function getElem($select = array(), $filter = array(), $order = array())
    {

        $result = self::getElemSql($select, $filter, $order);

        return $result;
    }

    /**
     * @param array $select
     * @param array $filter
     * @param array $order
     * @return array
     */
    private static function getElemSql($select = array(),$filter = array(), $order = array()){

        global $USER;
        $obCache = \Bitrix\Main\Data\Cache::createInstance();
        $cacheLifetime = intval(36000);
        $cacheID = md5(serialize($select.$filter.$order. $USER->GetUserGroupString()));
        $cachePath = '/ElemIblockCacheTable';

        if( $obCache->initCache($cacheLifetime, $cacheID, $cachePath) ){

            $arElements = $obCache->getVars();

            //echo 'Из кеша<br>';

        }elseif(\Bitrix\Main\Loader::includeModule("iblock") && $obCache->StartDataCache()){

            global $CACHE_MANAGER;

            $CACHE_MANAGER->StartTagCache($cachePath);

            $res = CIBlockElement::GetList($order, $filter, false, false, $select);

            while($ob = $res->GetNextElement())
            {
                $arElements[] = $ob->GetFields();
                $CACHE_MANAGER->RegisterTag("iblock_id_" . $arElements["IBLOCK_ID"]);

            }
            $CACHE_MANAGER->RegisterTag("iblock_id_new");
            $CACHE_MANAGER->EndTagCache();

            $obCache->EndDataCache($arElements);

            //echo 'Без кеша<br>';

        }
        return $arElements;
    }
}