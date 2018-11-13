<?php

namespace TestApp;

/** т.к. нотация не указана в задании - будем использовать 5.6 - для более широкой реализации, пока опускаем d7 - так как неизвестно как подключать таблицу свойств - а универсальный метод делать долго */
class IblockHelper
{
    const CACHE_TTL = 30 * 24 * 60 * 60;
    const TYPE_OLD = 1;
    const TYPE_D7 = 1;
    const TYPE_D7_OBJECT = 1;

    /** $params = array(
     *      order
     *      select
     *      filter
     *      limit
     *      ttl
     *      type
     * )
     *
     * @param array $params
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getList(array $params)
    {
        if (empty($params['type']) || !\in_array($params['type'], [
                static::TYPE_OLD . static::TYPE_D7_OBJECT,
                static::TYPE_D7,
            ], true)) {
            throw new \Bitrix\Main\ArgumentException('не установлен тип запроса или он неверный');
        }
        /** так как не проект не юзаем автозагрзчик */
        \Bitrix\Main\Loader::includeModule('iblock');
        $ttl = $params['ttl'] ?: static::CACHE_TTL;
        $instance = \Bitrix\Main\Application::getInstance();
        $cache = $instance->getCache();
        $taggedCache = $instance->getTaggedCache();
        $items = [];
        $iblockIds = [];
        if (\is_array($params) && !empty($params['IBLOCK_ID'])) {
            if (\is_array($params['IBLOCK_ID'])) {
                $iblockIds = $params['IBLOCK_ID'];
            } elseif (\is_numeric($params['IBLOCK_ID'])) {
                $iblockIds[] = $params['IBLOCK_ID'];
            }
        }
        $cachePath = '/custom_iblock';
        if ($cache->initCache($ttl, md5(serialize($params)), $cachePath)) {
            $vars = $cache->getVars();
            $items = $vars['items'];
        } elseif ($cache->startDataCache()) {
            if (!empty($iblockIds)) {
                $taggedCache->startTagCache($cachePath);
            }
            switch ($params['type']) {
                case self::TYPE_OLD:
                    $items = static::getListOld($params);
                    break;
                case self::TYPE_D7:
                    $items = static::getListD7($params);
                    break;
                case self::TYPE_D7_OBJECT:
                    $items = static::getListD7Objects($params);
                    break;
            }
            if (!empty($iblockIds)) {
                foreach ($iblockIds as $iblockId) {
                    $taggedCache->registerTag('iblock_' . $iblockId);
                }
                $taggedCache->endTagCache();
            }
            $cache->endDataCache(['items' => $items]);
        }

        return $items;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected static function getListOld(array $params)
    {
        $items = [];
        $res = \CIBlockElement::GetList($params['order'], $params['filter'], false, false, $params['select']);
        if(isset($params['select']['DETAIL_PAGE_URL']) || isset($params['select']['LIST_PAGE_URL'])){
            while ($item = $res->GetNextElement()) {
                $items[] = $item;
            }
        } else {
            while ($item = $res->Fetch()) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param array $params
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected static function getListD7(array $params)
    {
        return static::getListD7Base($params)->fetchAll();
    }

    /**
     * @param array $params
     *
     * @return \Bitrix\Main\ORM\Query\Result
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected static function getListD7Base(array $params)
    {
        /** это тестовый хелпер - универсально под свойство делать дольше(под 2 типа), поэтмоу на тестовом задании пропускаем */
        $filter = $params['filter'];
        $select = $params['select'] ?: ['*'];
        $query = \Bitrix\Iblock\ElementTable::query();
        if ($filter instanceof \Bitrix\Main\Entity\Query) {
            $query->where($filter);
        } else {
            $query->setFilter($filter);
        }
        $query->setSelect($select);
        if ((int)$params['limit'] > 0) {
            $query->setLimit((int)$params['limit']);
        }
        if (!empty($params['order'])) {
            $query->setOrder($params['order']);
        }
        return $query->exec();
    }

    /**
     * @param array $params
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected static function getListD7Objects(array $params)
    {
        return static::getListD7Base($params)->fetchCollection()->getAll();
    }
}