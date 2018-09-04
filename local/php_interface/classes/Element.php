<?
namespace Developer\Test;

use Bitrix\Main\Loader;

class Element {
    
    public function __construct() {
        Loader::includeModule('iblock');
    }
    
    private $_cached = [];
    
    /**
     * Возвращает список элементов инфоблока
     *
     * @param array $arParams
     * @return array
     */
    public function getList($arParams) {
        $arReturn = [];
        
        // Реализовал временный php кеш
        // если по заданию нужно было реализовать обычный кеш через файлы кеша, то
        // можно было бы использовать штатный класс \CPhpCache и тегировать кеш через объект $GLOBALS['CACHE_MANAGER']
        // или аналоги на d7.
        $hash = md5(serialize($arParams));
        if(key_exists($hash, $this->_cached)) {
            return $this->_cached[$hash];
        }
        
        $rsElement = \CIBlockElement::GetList(
            is_array($arParams['sort']) ? $arParams['sort'] : [],
            is_array($arParams['filter']) ? $arParams['filter'] : [],
            false,
            false,
            is_array($arParams['select']) ? $arParams['select'] : []
        );
        while($arElement = $rsElement->GetNext()) {
            // если это инфоблок 1.0 (свойства лежат в общей таблице), то
            // элементы будут дублироваться при выборке значений множ. свойства
            // в задании про это ничего не сказано.
            $arReturn[] = $arElement;
        }
        
        $this->_cached[$hash] = $arReturn;
        
        return $arReturn;
    }
    
}
?>