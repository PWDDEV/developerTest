<?php

namespace Test;

use Bitrix\Main\Loader;
use Bitrix\Main\Data\Cache;

if (!Loader::includeModule('iblock')) {
    throw new Exception('Iblock module ist installed.');
}

class Elements
{
    protected $id = 0;

    public function __construct($id = 0)
    {
        if ($id) {
            $this->id = $id;
        }

        if (!$this->id) {
            throw new Exception('Iblock ID is undefined.');
        }
    }

    public function getElements($params = [])
    {
        if (array_key_exists('cacheTime', $params)) {
            $cacheTime = $params['cacheTime'];
            unset($params['cacheTime']);
        } else {
            $cacheTime = 3600;
        }

        $order = $params['order'] ? $params['order'] : [];
        $select = $params['select'] ? $params['select'] : ['*'];
        $filter = $params['filter'] ? $params['filter'] : [];
        $group = $params['group'] ? $params['group'] : false;
        $nav = $params['nav'] ? $params['nav'] : false;

        if (!$filter['IBLOCK_ID']) {
            $filter['IBLOCK_ID'] = $this->id;
        }

        $cache = Cache::createInstance();
        if ($cache->initCache($cacheTime, serialize($params), '/cache_elements')) {
            $result = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            $result = [];

            $res = \CIBlockElement::GetList($order, $filter, $group, $nav, $select);

            while ($element = $res->GetNextElement()) {
                $item = $element->GetFields();
                $item['PROPERTIES'] = $element->GetProperties();
                $result[$item['ID']] = $item;
            }

            if (!empty($result)) {
                $cache->endDataCache($result);
            } else {
                $cache->abortDataCache();
            }
        }

        return $result;
    }

    protected function getCacheDir()
    {
        return get_class($this);
    }
}