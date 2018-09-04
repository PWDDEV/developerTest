<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

if(!CModule::IncludeModule('iblock'))
    die('iblock module not found, operation impossible');

/**
 * Class IBlockHelper
 *
 */
class IBlockHelper extends CIBlockElement{

    protected $PCache;

    function __construct()
    {
        $this->PCache = new CPHPCache();
    }

    /**
     * @param $arr
     */
    public function pr($arr){
        echo '<pre>';
            print_r($arr);
        echo '</pre>';
    }

    /**
     * @param $arr
     * @return array
     */
    private function getParams($arr){

        // значения по умолчанию
        $defaultParams = Array(
            // сколько записей выбрать
            'count'     =>  10,

            // кеширование
            'cache'     =>  Array(

                // нужно ли кешировать вывод true / false
                'run'=>true,

                // ручное создание идентификатора кеша
                // если передать false, идетификатор будет
                // построен от объеденения массива filter
                'id'=>false,

                // сколько держать кеш
                'time'=>30,

                // удалить ли принудительно кеш
                'destroy' => false
            ),

            // как сортировать
            'order'     =>  Array('SORT'=>'DESC'),

            // по каким полям выбирать
            'filter'    =>  Array("IBLOCK_ID"=>3, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y"),

            // какие поля выбирать
            'select'    =>  Array("NAME", "ID")
        );

        return array_merge($defaultParams, $arr);

    }

    /**
     * @param $arParams
     * @return array
     */
    protected function _GetList($arParams){
        $arResult = Array();

        $res = self::GetList($arParams['order'], $arParams['filter'], false, Array("nPageSize"=>$arParams['count']), $arParams['select']);

        while($ob = $res->GetNextElement()) {
            $arResult[] = $ob->GetFields();
        }

        return $arResult;
    }

    /**
     * @param $arParams
     * @return array
     */
    public function GetElements($arParams=Array()){

        $arResult = Array();
        $arParams = $this->getParams($arParams);

        // формируем идентификатор кеша в зависимости от всех параметров
        // которые могут повлиять на результирующий HTML
        // !! или пишем кеш разработчика

        if($arParams['cache']['id']){
            $cache_id = $arParams['cache']['id'];
        }else{
            $cache_id = 'helper_'.implode("_", $arParams['filter']);
        }

        // если не надо использовать кеш
        // сразу вернем элементы инфоблока
        if(!$arParams['cache']['run'])
            return $this->_GetList($arParams);

        //если надо принудительно очистить кеш
        if($arParams['cache']['destroy']) {
            $this->PCache->CleanDir();
        }

        // если кеш есть и он ещё не истек, то
        if($this->PCache->InitCache($arParams['cache']['time'], $cache_id, "/")){

            // получаем закешированные переменные
            $vars = $this->PCache->GetVars();
            return $vars['arrElements'];
        }

        // начинаем буферизирование вывода
        if($this->PCache->StartDataCache()){

            $arResult = $this->_GetList($arParams);

            // записываем предварительно буферизированный вывод в файл кеша
            // вместе с дополнительной переменной
            $this->PCache->EndDataCache(array(
                "arrElements"    => $arResult
            ));

        }

        return $arResult;

    }
}

$IBlockHelper = new IBlockHelper();

$elements = $IBlockHelper->GetElements(Array(
    'count'     =>  3,
    'cache'     =>  Array(
        'time'=>30,
        'id'=>false,
        'run'=>true,
        'destroy' =>false,
    ),
    'select'    =>  Array("NAME", "ID"),
    'order'     =>  Array('SORT'=>'DESC'),
    'filter'    =>  Array("IBLOCK_ID"=>1, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y"),
));

$IBlockHelper->pr($elements);
?>