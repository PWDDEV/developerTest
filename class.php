<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

class IblockCashed{
	public static function GetCachedList($sort, $Filter, $arGroupBy = false, $pageParams = false, $arSelect=false){
		if(!CModule::IncludeModule("iblock")) return false;
		if(empty($Filter)) return false;

		$elArr = Array();

		$obCache = new CPHPCache;
		$life_time = 3600;
		$cache_params = array();
		$cache_params['filter'] = $Filter;
		$cache_params['func']='IblockCashed::GetCachedList';
		$cache_params['arSelect']=$arSelect;
		$cache_params['sort']=$sort;
		$cache_params['pageParams']=$pageParams;
		$cache_id = md5(serialize($cache_params));

		if($obCache->InitCache($life_time, $cache_id, "/")){
			$elArr = $obCache->GetVars();
		} elseif($obCache->StartDataCache()) {
			$res = CIBlockElement::GetList($sort, $Filter, $arGroupBy, $pageParams, $arSelect);
			while($ob = $res->GetNextElement())
			{
				$arFields = $ob->GetFields();
				array_push($elArr, $arFields);
			}
			$obCache->EndDataCache($elArr);
		}
		return $elArr;
	}		
}

$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM");
$arFilter = Array("IBLOCK_ID"=>2, "ACTIVE"=>"Y");
$res = IblockCashed::GetCachedList(Array("NAME"=>"ASC"), $arFilter, false, false, $arSelect);
print_r($res);


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>