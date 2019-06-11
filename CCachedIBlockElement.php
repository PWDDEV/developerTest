class CCachedIBlockElement extends CIBlockElement
{
    public static function GetList($arOrder=array("SORT"=>"ASC"), $arFilter=array(), $arGroupBy=false, $arNavStartParams=false, $arSelectFields=array(), $cache_time)
    {
        $obCache = new CPHPCache();
        $cache_id = md5(serialize(array($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields)));
        $cache_path = 'CachedIBlockElement';
        if ($cache_time > 0 && $obCache->InitCache($cache_time, $cache_id, $cache_path)) {
            $arElements = $obCache->GetVars();
        } elseif ( $obCache->StartDataCache() ) {
            $arElements = array();
            $rElements = parent::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
            while($arElement = $rElements->Fetch()){
                $arElements[] = $arElement;
            }
            $obCache->EndDataCache($arElements);
        }
        return $arElements;
    }
}
