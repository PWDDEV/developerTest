<?php

class CachedIblockElement {
    
    protected $cacheTime = 3600000;  
                    
    public function SetCacheTime($cacheTime) {
        $this->cacheTime = $cacheTime;
        return $this;
    }

    public function GetList($filter = [], $select = [], $order = ['SORT' => 'ASC']) { 
         
        $cacheDir = 'iblockElements';
        if($filter['IBLOCK_ID']) {
            $cacheDir.= implode('_', (array) $filter['IBLOCK_ID']);
        }
        
        $cacheId = serialize([$filter, $order, $select]);
      
        $obCache = new CPHPCache;
        if ($obCache->InitCache($this->cacheTime, $cacheId, $cacheDir)) {
            $itemsResult = $obCache->GetVars(); 
        } else {
            $obCache->StartDataCache();
                   
            if(!$filter['IBLOCK_ID'] && !in_array('IBLOCK_ID', $select)) {
                $select[] = 'IBLOCK_ID';
            }
            
            \Bitrix\Main\Loader::includeModule("iblock");
            $res = CIBlockElement::GetList($order, $filter, false, false, $select);
            while ($item = $res->Fetch()) {
                $itemsResult[] = $item;
            }
            
            if(count($itemsResult)) {
                $iblockId = $filter['IBLOCK_ID'];
                if(!$iblockId) {
                    foreach ($items as $item) {
                        if(!in_array($item['IBLOCK_ID'], $iblockId)) {
                            $iblockId[] = $item['IBLOCK_ID'];
                        }
                    }
                } 
                global $CACHE_MANAGER;
                $CACHE_MANAGER->StartTagCache($cacheDir);
                $iblockId = (array) $iblockId;
                foreach ($iblockId as $iblock) {
                    $CACHE_MANAGER->RegisterTag('iblock_id_' . $iblock);
                }
                $CACHE_MANAGER->EndTagCache();
                $obCache->EndDataCache($itemsResult);
            } else { 
                $obCache->AbortDataCache();
            }
        } 
        return $itemsResult;
    }
  
    public function GetByID($id) {
        return $this->GetOne(['ID' => $id]); 
    } 
   
}