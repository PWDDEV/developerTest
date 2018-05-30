<?php

namespace Test;


class CacheIBlockElement
{
    private $cacheTime;
    private $cacheId;
    private $cachePath;
    private $result;

    public function __construct($cacheTime = 86400, $cacheId = 'test.cacheiblockelement', $cachePath = '/cacheiblockelement')
    {
        $this->cacheTime = $cacheTime;
        $this->cacheId = $cacheId;
        $this->cachePath = $cachePath;
        $this->result = false;
    }

    static public function createInstance()
    {
        return new self();
    }

    public function GetList($arOrder = [], $arFilter = [], $arGroupBy = false, $arNavStartParams = [], $arSelectFields = [])
    {
        $obCache = \Bitrix\Main\Data\Cache::createInstance();
        $arCache['ORDER'] = implode($arOrder);
        $arCache['FILTER'] = implode($arFilter);
        $arCache['GROUP'] = implode($arGroupBy);
        $arCache['NAV'] = implode($arNavStartParams);
        $arCache['SELECT'] = implode($arSelectFields);
        $this->cacheId = md5($this->cacheId . implode($arCache));
        if ($obCache->initCache($this->cacheTime, $this->cacheId, $this->cachePath)) {
            $this->result = $obCache->getVars('RESULT');
        } elseif (\Bitrix\Main\Loader::includeModule('iblock') && $obCache->StartDataCache()) {
            global $CACHE_MANAGER;
            if (is_object($CACHE_MANAGER)) {
                $this->result = \CIBlockElement::GetList(
                    $arOrder,
                    $arFilter,
                    $arGroupBy,
                    $arNavStartParams,
                    $arSelectFields
                );
                if ($this->result) {
                    $CACHE_MANAGER->StartTagCache($this->cachePath);
                    $CACHE_MANAGER->EndTagCache();
                    $obCache->endDataCache();
                } else {
                    $obCache->abortDataCache(array('RESULY' => $this->result));
                }
            }
        }
        return $this->result;
    }
}
