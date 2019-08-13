<?php
use \Bitrix\Main\Data\Cache;
use \Bitrix\Main\Loader;
use \Bitrix\Main\LoaderException;

/**
 * Class ElementList
 * Обёртка для выборки элементов инфоблока и их кеширования
 */
class ElementList
{
	private $defaultCacheTime = 36000;
	private $cacheTime;
	private $cachePath = 'ElementList';
	/* @var Cache Объект кеша */
	private $cache;

	/**
	 * При создании класса задаём время кеширования, если время не задано то используется время по умолчанию.
	 * @param int $cacheTime
	 */
	function __construct($cacheTime = null)
	{
		// Установка времени кеша
		if ($cacheTime) {
			$this->cacheTime = $cacheTime;
		} else {
			$this->cacheTime = $this->defaultCacheTime;
		}

		// Получаем объект кеша
		$this->cache = Cache::createInstance();
	}

	/**
	 * @param array $sort Значение по умолчанию ['SORT' => 'ASC']
	 * @param array $filter Поля идентичны arFilter
	 * https://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockelement/getlist.php
	 * @param array|null $fields Поля элемента, которые попадают в выборку. Значение по умолчанию ['ID', 'IBLOCK_ID', 'NAME']
	 * @param array|null $properties Свойства элемента, которые попадают в выборку. Указывать в массиве
	 * только символьный код свойства. Значение по умолчанию false.
	 * Тип свойства "множественное" указывать нельзя так как выборка элементов будет неверной.
	 * @return array
	 * @throws LoaderException
	 */
	public function getElements($sort, $filter, $fields = null, $properties = null)
	{
		$selectSort = $this->getSort($sort);
		$selectFields = array_merge($this->getFields($fields), $this->normalizeProperties($properties));

		if (!($filter and is_array($filter))) {
			echo new \Bitrix\Main\Error('Фильтр не указан (' . __FILE__ . ' ' . __LINE__ . ')');
			return array();
		}

		// Получение выборки из базы или из кеша
		$cacheKey = json_encode(array_merge($filter, $selectSort, $selectFields));
		if ($this->cache->initCache($this->cacheTime, $cacheKey, $this->cachePath)) {
			// Возвращаем кешируемый массив
			return $this->cache->getVars();
		} elseif ($this->cache->startDataCache()) {
			$elements = $this->getDbElements($selectSort, $filter, $selectFields);
			if ($elements and is_array($elements)) {
				// Пишем данные в кеш и возвращаем массив
				$this->cache->endDataCache($elements);
				return $elements;
			} else {
				// Элементы не найдены возвращаем пустой массив и отменяем кеширование
				$this->cache->abortDataCache();
				return array();
			}
		}
	}

	//***** Методы проверки входных параметров *****

	/**
	 * Устанавливает пользовательскую сортировку или сортировку по умолчанию
	 * @param $sort
	 * @return array
	 */
	private function getSort($sort) {
		if ($sort and is_array($sort)) {
			$resultSort = $sort;
		} else {
			$resultSort = array('SORT' => 'ASC');
		}

		return $resultSort;
	}

	/**
	 * Добавляет пользовательские поля к полям по умолчанию
	 * @param $userFields
	 * @return array
	 */
	private function getFields($userFields)
	{
		$defaultFields = array('ID', 'IBLOCK_ID', 'NAME');

		if ($userFields) {
			$resultFields = array_merge($defaultFields, $userFields);
		} else {
			$resultFields = $defaultFields;
		}

		return $resultFields;
	}

	/**
	 * Добавляет префикс к пользовательским свойствам для валидности их выборки
	 * @param $properties
	 * @return array
	 */
	private function normalizeProperties($properties)
	{
		$normProps = array();
		if ($properties and is_array($properties)) {
			foreach ($properties as $prop) {
				$normProps[] = 'PROPERTY_' . $prop;
			}
			return $normProps;
		} else {
			return $normProps;
		}
	}

	//***** Методы выборки данных *****

	/**
	 * Выберает данные из базы, тегирует выборки кеша, возвращает массив со списком элементов ИБ
	 * @param $sort
	 * @param $filter
	 * @param $selectFields
	 * @return array
	 * @throws LoaderException
	 */
	private function getDbElements($sort, $filter, $selectFields)
	{
		Loader::includeModule('iblock');

		$dbElements = CIBlockElement::GetList($sort, $filter, false, false, $selectFields);
		$elements = array();
		global $CACHE_MANAGER;
		// Включаем запись тегированого кеша и указываем путь к каталогу нашего кеша
		$CACHE_MANAGER->StartTagCache($this->cachePath);
		// Fetch сам подписывает тегами кеш к инфоблоку
		// после изменения элемента инфоблока весь кеш для него сбрасывается
		while ($element = $dbElements->Fetch()) {
			$elements[] = $element;
		}
		$CACHE_MANAGER->EndTagCache();

		return $elements;
	}

}
/* Пример использования

$elementList = new ElementList();
$elementList = $elementList->getElements(array(), array('IBLOCK_ID' => 1), array('DATE_CREATE'), array('CATEGORY'));

?>
<pre><? print_r($elementList); ?></pre>
*/