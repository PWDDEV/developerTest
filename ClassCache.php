<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

class iElemsCached{
	
	public $cache_time;
    public $cache_path;
    public $cache;
    
    function __construct($cache_time=3600, $cache_path="icache"){
        $this->cache= new CPHPCache();
        $this->cache_time = $cache_time;
        $this->cache_path = $cache_path;
	}
	
	private function getElementList($arOrder=array("SORT"=>"ASC"), $arFilter=array(), $arGroupBy=false, $arNavStartParams=false, $arSelectFields=array('ID','NAME')){
		CModule::IncludeModule('iblock');
        $res = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
        $arResult = array();
        while($rec = $res->fetch()){
			$arResult[] = $rec;
		}
		return $arResult;
	}
	
	public function getCachedElementList($arOrder=array("SORT"=>"ASC"), $arFilter=array(), $arGroupBy=false, $arNavStartParams=false, $arSelectFields=array('ID','NAME')){
		$arParams = array('arOrder'=>$arOrder, 'arFilter'=>$arFilter, 'arGroupBy'=>$arGroupBy, 'arNavStartParams'=>$arNavStartParams, 'arSelectFields'=>$arSelectFields);
		
		$cache_id=md5(serialize($arParams));
		$arResult = false;

		if ($this->cache_time > 0 && $this->cache->InitCache($this->cache_time, $cache_id, $this->cache_path)){
			$cache_res = $this->cache->GetVars();
            if (is_array($cache_res["arResult"]) && (count($cache_res["arResult"]) > 0)){
                $arResult = $cache_res["arResult"];
            }
		}else{
			$this->clearCache();
		}
		
		if(!$arResult){
			$arResult = $this->getElementList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
			if ($this->cache_time > 0)
            {
                $this->cache->StartDataCache($this->cache_time, $cache_id, $this->cache_path);
                $this->cache->EndDataCache(array("arResult"=>$arResult));
            }
		}
		return $arResult;
	}
	
	private function clearCache(){
		$this->cache->CleanDir( $this->cache_path );
	}
}

//Непосредственно Usage
$testing = new iElemsCached(300); 
$testing_glist = $testing->getCachedElementList(array("SORT"=>"ASC"), array("IBLOCK_ID"=>"1"));
echo "<pre>";
var_dump($testing_glist);
echo"</pre>";