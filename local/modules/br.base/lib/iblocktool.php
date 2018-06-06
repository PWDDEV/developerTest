<?php

namespace Br\Base;
use \Br\Base\Debug;
use \Bitrix\Main\Data\Cache;
use \Bitrix\Main\Loader;
use \Bitrix;

class IblockTool {

    private $_aOrder;
    private $_aFilter;
    private $_aSelect;
    private $_iPageCount;
    private $_iCacheTtl;
    private $_mResult;


    function __construct($iIbId, $aOrderParam = false, $aFilterParam = false, $aSelectParam = false){

        if( (int)$iIbId == 0 ){
            die("Не указан ID инфоблока");
        }

        $this->_aOrder  = (is_array($aOrderParam)) ? $aOrderParam : ['ID' => 'ASC'];
        $this->_aFilter = (is_array($aFilterParam)) ? array_merge(['IBLOCK_ID' => $iIbId], $aFilterParam) : ['IBLOCK_ID' => $iIbId];
        $this->_aSelect = (is_array($aSelectParam)) ? $aSelectParam : [];
        $this->_iCacheTtl = 86400;
        $this->_iPageCount = ["nPageSize" => "20"];
        $this->_mResult = false;
    }

    public function setCacheTtl($iCacheTtl){
        if( (int)$iCacheTtl > 0 ){
            $this->_iCacheTtl = $iCacheTtl;
        }
    }

    public function setPageCount($iPageCount){
        if( (int)$iPageCount > 0 ){
            $this->_iPageCount = $iPageCount;
        }
    }

    public function getList(){

        $oCache = Cache::createInstance();
        if ($oCache->initCache($this->_iCacheTtl, md5(serialize(array_merge($this->_aOrder, $this->_aFilter, $this->_aSelect))))) {
            $this->_mResult = $oCache->getVars();
        }
        elseif ($oCache->startDataCache()) {
            Loader::includeModule("iblock");

            $oResult = \CIBlockElement::GetList($this->_aOrder, $this->_aFilter, false, $this->_iPageCount, $this->_aSelect);
            $aResult = [];
            while ($aElement = $oResult->GetNext()) {
                $aResult[] = $aElement;
            }

            if(count($aResult) > 0){
                $this->_mResult['RESULT'] = $aResult;
                $oCache->endDataCache(array('RESULT' => $aResult));
            }

        }

        return $this->_mResult;
    }



}