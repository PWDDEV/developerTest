<?php
namespace Sokolov\Iblock;

class Prototype
{
    /**
     * ID инфоблока
     *
     * @var integer
     */
    protected $id = 0;

    /**
     * Конструктор
     *
     * @param integer $id ID инфоблока
     * @throws Exception
     */
    public function __construct($id = 0)
    {
        if(!\CModule::IncludeModule("iblock"))
        {
            throw new \Exception('Error loading module iblock');
        }

        if ($id)
        {
            $this->id = $id;
        }
        else
        {
            throw new \Exception('Iblock ID is undefined.');
        }
    }

    /**
     * Возвращает массив с элементами
     *
     * @param array $params массив с ключами, аналогичными ключам Bitrix\Main\Entity\DataManager::getList() и
     *      дополнительными ключами
     *          "cacheTime" => integer время кэширования
     *          "indexArray" => ключ который будет взят за основу результирующего массива
     * @return array|mixed
     */
    public function getElements($params = [])
    {
        if (key_exists('cacheTime', $params))
        {
            $cacheTime = $params['cacheTime'];
            unset($params['cacheTime']);
        }
        else
        {
            $cacheTime = 86400;
        }

        if (key_exists('indexArray', $params))
        {
            $indexArray = $params['indexArray'];
            unset($params['indexArray']);
        }
        else
        {
            $indexArray = false;
        }

        $order = $params['order'] ? $params['order'] : [];
        $arFilter = $params['filter'] ? $params['filter'] : [];
        $arSelect = $params['select'] ? $params['select'] : ['*'];
        $arGroup = $params['group'] ? $params['group'] : false;
        $arNav = $params['nav'] ? $params['nav'] : false;

        if (!$arFilter['IBLOCK_ID'])
        {
            $arFilter['IBLOCK_ID'] = $this->id;
        }

        $cache_id = md5(serialize($arFilter));
        $cache_dir = 'proto';
        $obCache = \Bitrix\Main\Data\Cache::createInstance();

        if ($obCache->initCache($cacheTime,$cache_id,$cache_dir))
        {
            $arResult = $obCache->GetVars();
        }
        elseif ($obCache->startDataCache())
        {
            $rs = \CIBlockElement::GetList($order, $arFilter, $arGroup, $arNav, $arSelect);
            $index = 0;
            $arResult = [];

            while ($ar = $rs->GetNext())
            {
                if ($indexArray) {
                    $ar['INDEX'] = $index;
                    $arResult[$ar[$indexArray]] = $ar;
                }
                else
                {
                    $arResult[] = $ar;
                }
                $index++;
            }
            if (!empty($arResult))
            {
                $obCache->endDataCache($arResult);
            }
            else
            {
                $obCache->abortDataCache();
            }
        }

        return $arResult;
    }
}