
class GetListCache
{
	public function GetList($select,$filter,$cachetime=6000){
		
			if(empty($filter))
				return array(
					'result' => 'error',
					'data'   => 'Заполнить фильтр',
				);
			$arReturn = array(
					'result' => 'error',
					'data'   => 'Неправильный запрос',
				);
		
			$code=md5(serialize(array_merge($select,$filter)));
			$cache = new CPHPCache();

			// Устанавливаем время, на которое нужно кешировать
			$cache_time = $cachetime;

			// Уникальный ID
			$cache_id = 'sectCache'.$code;

			// Папка для хранения кеша
			$cache_path = '/sectCache/sicache/'.$code;

			// Проверяем существование кеша
			if ( $cache->InitCache($cache_time, $cache_id, $cache_path) )
			{
				// Кеш есть, достаем данные
				$arCacheData = $cache->GetVars();
				$arReturn['result']="success";
				// Достаем по ключу, с которым положили
			
				$arReturn['data'] = $arCacheData['sectCache'.$sCode];
				
			} 
			else 
			{
				// Кеша нет, нужно получить данные

				// Хороший тон - проверить, подключен ли модуль
				if (\CModule::IncludeModule("iblock") )
				{
					if(empty($select))
					$select= array('ID', 'NAME', 'XML_ID');


					// Массив элементов
					$arItems = [];

					// Получаем данные
					$res = \CIBlockElement::GetList(["ID"=>"DESC"], $filter, false, false, $select);
					while($ob = $res->GetNextElement()){ 
					 $arFields = $ob->GetFields();  
				
					 $arProps = $ob->GetProperties();
					 $arFields["PROPERTIES"]=$arProps;
					 $arItems[ $arFields['ID'] ] = $arFields;
					}

					// Сохраняем данные в кеше
					$cache->StartDataCache($cache_time, $cache_id, $cache_path);
					$cache->EndDataCache(['sectCache'.$sCode => $arItems]);
					$arReturn['result']="success";
					$arReturn['data'] = $arItems;
					
				}
				else
				{
					$arReturn['data'] = "Модуль инфоблоков не подключен";
				}
			}
		
		
		
		return $arReturn;
	}
	
	public function ClearCache(){
		$obCache = new CPHPCache(); 
		$obCache->CleanDir("/sectCache/sicache");
		return true;
	}
	
}
