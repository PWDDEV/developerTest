<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); ?>

<?

/**
 * Все изначальные параметры методов идентичны тем, что описаны в доках битрикса:
 * https://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockelement/index.php
 *
 * К изначальным параметрам методов добавлена возможность передавать параметры кеширования запроса. Смотрите описание каждого метода.
 * Class CachedCIblockElement
 */
class CachedCIblockElement extends CIblockElement
{
	
	/**
	 * Переопределение битриксового метода GetList.
	 * Теперь после параметра SelectedFields принимает параметр $cacheTime - время кеширования и $fetchType - тип разбора объекта CDBResult (Fetch, GetList, GetNextElement).
	 * @param array $arOrder - сортировка
	 * @param array $arFilter
	 * @param bool $arGroupBy
	 * @param bool $arNavStartParams
	 * @param array $arSelectFields
	 * @param int $cacheTime
	 * @param string $fetchType
	 * @return array|mixed
	 */
	public static function GetList($arOrder = array("SORT" => "ASC"), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array(), $cacheTime = 10, $fetchType = 'Fetch')
	{
		
		$cacheID = md5(json_encode(array(
			$arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields, $cacheTime, $fetchType
		)));
		
		return self::Caching($cacheID, $cacheTime, function() use ($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields, $fetchType) {
			$res = parent::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
			
			return self::FetchByType($res, $fetchType);
		});
	}
	
	/**
	 * Добавил для примера переопределение метода GetByID.
	 * После стандартного параметра $ID теперь можно передать параметр $cacheTime - время кеширования и $fetchType - тип разбора объекта CDBResult (Fetch, GetList, GetNextElement).
	 * @param bool $ID
	 * @param int $cacheTime
	 * @param string $fetchType
	 * @return array|bool|mixed
	 */
	public static function GetByID($ID = false, $cacheTime = 10, $fetchType = 'Fetch')
	{
		if($ID === false) return false;
		
		$cacheID = md5(json_encode(array(
			$ID, $cacheTime
		)));
		
		return self::Caching($cacheID, $cacheTime, function() use ($ID, $fetchType) {
			$res = parent::GetByID($ID);
			
			return self::FetchByType($res, $fetchType);
		});
	}
	
	/**
	 * Метод для разбора объекта CDBResult в массив, в соответствии с одним из вариантов разбора:
	 * Fetch
	 * GetList
	 * GetNextElement
	 *
	 * https://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockresult/index.php
	 * https://dev.1c-bitrix.ru/api_help/main/reference/cdbresult/index.php
	 * @param CDBResult $CDBResult
	 * @param string $fetchType
	 * @return array|bool
	 */
	protected static function FetchByType(CDBResult $CDBResult, $fetchType = 'Fetch')
	{
		if($CDBResult === false) return false;
		
		$result = array();
		switch ($fetchType) {
			case 'GetNext':
				
				while ($elem = $CDBResult->GetNext()) {
					$result[] = $elem;
				}
				
				break;
			
			case 'GetNextElement':
				
				while ($obElem = $CDBResult->GetNextElement()) {
					$result[] = array(
						'FIELDS' => $obElem->GetFields(),
						'PROPERTIES' => $obElem->GetProperties()
					);
				}
				
				break;
			
			case 'Fetch':
			default:
				
				while ($fetched = $CDBResult->Fetch()) {
					$result[] = $fetched;
				}
				
				break;
		}
		
		return $result;
	}
	
	/**
	 * Метод отвечающий за кеширование запросов.
	 * Принимает ИД кеша, время кеширования и callback функцию, результат которой нужно закешировать.
	 *
	 * В callback имеет смысл передавать родительский метод и парсинг результата его работы.
	 * @param $cacheID
	 * @param $cacheTime
	 * @param $callback
	 * @return array|mixed
	 */
	private static function Caching($cacheID, $cacheTime, $callback)
	{
		$obCache = new CPHPCache();
		$result = array();
		if($obCache->InitCache($cacheTime, $cacheID, '/bitrix/cache/')) {
			$result = $obCache->GetVars()['DATA'];
		} else {
			$obCache->StartDataCache();
			
			if(is_callable($callback)) {
				$result = call_user_func($callback);
			}
			
			$obCache->EndDataCache(array('DATA' => $result));
		}
		
		return $result;
	}
}

// Клиентский код. Примеры использования:

//$data = CachedCIblockElement::GetList(
//	array('ID' => 'ASC'),
//	array('IBLOCK_ID' => 42),
//	false,
//	array('nTopCount' => 3),
//	array('ID', 'NAME'),
//	10,
//	'GetNext'
//);

//var_dump($data);

//var_dump(CachedCIblockElement::GetByID(7020, 5))

?>