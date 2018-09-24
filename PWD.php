<?

class PWD {
    public static function getElems($sort = array(),$filter = array(),$select = array()){
        $arResult = array();
        $obCache = new CPHPCache();
        $cache_time = 60 * 60 * 24;
        $m = array_merge($sort,$filter,$select);
        $cache_id = md5(implode(',',array_merge(array_keys($m),$m)));
        if ($cache_time > 0 && $obCache->InitCache($cache_time, $cache_id,"/")){
            $vars = $obCache->GetVars();
            $arResult = $vars[$cache_id];
        } elseif ($obCache->StartDataCache() && CModule::IncludeModule("iblock")) {
            $res = CIBlockElement::GetList(
                $sort,
                $filter,
                false,
                false,
                $select
            );           
            while ($ob = $res->Fetch()) {
                if ($ob["PREVIEW_PICTURE"]) $ob["PREVIEW_PICTURE"] = CFile::GetPath($ob["PREVIEW_PICTURE"]);
                if ($ob["DETAIL_PICTURE"]) $ob["DETAIL_PICTURE"] = CFile::GetPath($ob["DETAIL_PICTURE"]);
                if ($ob["DETAIL_PAGE_URL"]) $ob["DETAIL_PAGE_URL"] = CIblock::ReplaceDetailUrl($ob['DETAIL_PAGE_URL'], $ob, false, 'E');
                $arResult[$ob["ID"]] = $ob;
            }
            $obCache->EndDataCache(array($cache_id => $arResult));
        }
        return $arResult;
    }
}

//$res = PWD::getElems(array(),array("IBLOCK_ID"=>1));
?>