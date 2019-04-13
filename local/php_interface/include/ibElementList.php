<?php
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 13.04.2019
 * Time: 13:09
 */
namespace silver;

use \Bitrix\Iblock;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Data\Cache;
use \Bitrix\Main\Config\Option;

class ibElementList
{
    /**
     * функция получения элементов инфоблока с кешированием
     * @param array $filter массив фильтров в части WHERE запроса в виде "(condition)field"=>"value"
     * @param array $select массив фильтров в части SELECT запроса, алиасы возможны в виде: "alias"=>"field"
     * @param array $order массив полей в части ORDER BY запроса в виде: "field"=>"asc|desc"
     * @param int $limit целое число, указывающее максимальное число столбцов в выборке (Подобно LIMIT n в MySql)
     * @param int $offset целое число, указывающее номер первого столбца в результате. (Подобно LIMIT n, 100 в MySql)
     * @param array $group массив полей в части GROUP BY запроса
     * @return array|bool
     */
    function GetCachedElements($filter, $select = false, $order = ["ID"=>"ASC"], $limit = 100, $offset = 0, $group = '' ){
        if(!Loader::includeModule('iblock')) return false;
        if(empty($filter)) return false;
        $arResult = false;
        $cache = Cache::createInstance(); // получаем экземпляр класса кеширования
        $life_time = Option::get("iblock","cache_life_time",3600); // получаем время жизни кеша
        $cache_params = $filter;
        $cache_params['func']='CIBlockElement::GetList';
        $cache_params['select']=$select;
        $cache_params['order']=$order;
        $cache_params['pager']=$limit . "_" . $offset;
        $cache_id = md5(serialize($cache_params)); // формируем ключ, который зависит от параметров
        if($cache->initCache($life_time, $cache_id, "/")) : // проверяем наличие данных в кеше
            $arResult = $cache->GetVars(); // получаем данные из кеша
        elseif($cache->StartDataCache()):
            // получаем данны из БД
            $elList = Iblock\ElementTable::GetList([
                "filter" =>  $filter,
                "select" => $select,
                "order" => $order,
                "limit" => $limit,
                "offset" => $offset,
            ]);
            while($arElement = $elList->Fetch())
            {
                $arResult[] = $arElement;
            }
            $cache->EndDataCache($arResult); // записываем данные в кеш
        endif;
        return $arResult; // возвращаем данные
    }
}