<?

class InfoBlocks{
  

  public function Elements(&$arBlock,&$cashtime='3600')
  {
    if(Cmodule::IncludeModule('iblock'))
      $arOreder = empty($arBlock['ORDER']) ? $arOreder = array() : $arOreder = $arBlock['ORDER'];

      $arGroup = empty($arBlock['GROUP']) ? $arGroup = false : $arGroup = $arBlock['GROUP'];

      $arSelect = empty($arBlock['SELECT']) ? $arSelect = array() : $arSelect = $arBlock['SELECT'];

      $arFilter = empty($arBlock['FILTER']) ? $arFilter = false : $arFilter = $arBlock['FILTER'];

      $nPageSize = empty($arBlock['PAGE_SIZE']) ? $nPageSize = array("nPageSize"=>50) : $nPageSize = $arBlock['PAGE_SIZE'];

      $key = md5(json_encode($arBlock));

        $obCache = \Bitrix\Main\Data\Cache::createInstance();
        $cache_time = $cashtime;
        $cache_id = 'ModuleInfoBlocks_'.$key;

    if( $obCache->initCache($cache_time,$cache_id,'/ModuleInfoBlocks/') )
        {
          $arResult = $obCache->GetVars();
          
        }
        elseif($obCache->startDataCache())
        {

      $res = CIBlockElement::GetList(
                      $arOreder, 
                      $arFilter, 
                      $arGroup, 
                      $nPageSize, 
                      $arSelect
                    );


      while($ob = $res->GetNextElement())
      { 
         $arFields = $ob->GetFields();  
         $arProps = $ob->GetProperties();

         $arResult[$arFields['ID']]['FIELDS'] = $arFields;
         $arResult[$arFields['ID']]['PROP'] = $arProps;

      }

      $obCache->endDataCache($arResult);

    }
     return $arResult;
  }

}

?>