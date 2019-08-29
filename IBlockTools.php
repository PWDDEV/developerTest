<?php
use Bitrix\Main\Data\Cache;

/**
 * Class IBlockTools
 * содержит метод для вывода списка элементов инфоблока. Данные кешируются.
 */
class IBlockTools
{
    /**
     * @param $cacheId
     * @param array $params
     * Возвращает список элементов инфоблока. Данные кешируются. Кеширование на 1 час.
     * $params['sort'] - сортировка
     * $params['filter'] - фильтр
     * $params['select'] - список выбираемых полей и свойств
     * @return array
     */
    public static function cachedElements($cacheId, $params = array())
    {
        $cache = Cache::createInstance();
        if ($cache->initCache(3600, $cacheId)) {
            return $cache->getVars();
        } elseif ($cache->startDataCache(3600, $cacheId, '/' . SITE_ID . '/' . 'cachedElements')) {
            $sort = $params['sort'] ? : array('SORT' => 'ASC');
            $filter = $params['filter'] ? : array();
            $select = $params['select'] ? : array();
            $result = array();
            $dbElements = CIBlockElement::GetList(
                $sort,
                $filter,
                false,
                false,
                $select
            );
            while ($element = $dbElements->Fetch()) {
                $result[] = $element;
            }
            $cache->endDataCache($result);
            return $result;
        }
    }
}
