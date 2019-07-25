<?
class IblockElements extends CBitrixComponent
{
	/**
	 * Берём стандартный фильтр и к нему добавляем пользовательский фильтр,
	 * ожидаемое значение пользовательского фильтра строка формата 'PROP_NAME="text",PROP_NAME2="text"'
	 * Строка пользовательского фильтра форматируется в стандарты битрикса
	 * @return array
	 */
	private function getFilter()
	{
		$filter = array(
			'ACTIVE' => 'Y',
			'IBLOCK_ID' => $this->arParams['IBLOCK_ID']
		);

		// Обрабатываем строку пользовательского фильтра в соответствии с требованиями битрикса
		if ($this->arParams['FILTER']) {
			$userFilter = array();
			$filterParams = explode(',', $this->arParams['~FILTER']);
			foreach ($filterParams as $filterParam) {
				$filterParam = str_replace('"', '', $filterParam);
				$moreFilter = explode('=', $filterParam);
				$userFilter = array_merge($userFilter, [$moreFilter[0] => $moreFilter[1]]);
			}
			$filter = array_merge($filter, $userFilter);
		}

		return $filter;
	}

	/**
	 * Получаем из параметров массив с сортировкой
	 * @return array
	 */
	private function getSort()
	{
		$sort = array();
		if ($this->arParams['SORT_BY1']) {
			$sort[$this->arParams['SORT_BY1']] = $this->arParams['SORT_ORDER1'];

			if ($this->arParams['SORT_BY2']) {
				$sort[$this->arParams['SORT_BY2']] = $this->arParams['SORT_ORDER2'];
			}
		}

		return $sort;
	}


	/**
	 * Формируем массив названий пользовательских свойств в соответствии со стандартами битрикса
	 * @return array
	 */
	private function getUserProps()
	{
		$userProps = array();
		if ($this->arParams['PROPERTY_CODE']) {
			foreach ($this->arParams['PROPERTY_CODE'] as $prop) {
				$userProps[] = 'PROPERTY_' . $prop;
			}
		}

		return $userProps;
	}

	private function getElements()
	{
		\Bitrix\Main\Loader::includeModule('iblock');

		$sort = $this->getSort();
		$filter = $this->getFilter();
		$standardFields = array(
			'ID', // - числовой идентификатор элемента;
			'TIMESTAMP_X', // - время последней модификации в полном формате сайта;
			'MODIFIED_BY', // - идентификатор пользователя вносившего последние правки;
			'DATE_CREATE', // - время создания элемента в полном формате сайта
			'CREATED_BY', // - идентификатор пользователя создавшего элемент;
			'IBLOCK_ID', // - числовой идентификатор инфоблока элемента;
			'ACTIVE', // - активность (Y|N);
			'ACTIVE_FROM', // - начало активности в полном формате сайта;
			'ACTIVE_TO', // - окончание активности в полном формате сайта;
			'SORT', // - значение сортировки;
			'NAME', // - имя элемента;
			'PREVIEW_PICTURE', // - идентификатор изображения;
			'PREVIEW_TEXT', // - текст анонса;
			'PREVIEW_TEXT_TYPE', // - тип текста анонса (html|text);
			'DETAIL_PICTURE', // - идентификатор изображения;
			'DETAIL_TEXT', // - детальное описание;
			'DETAIL_TEXT_TYPE', // - тип детального описания (html|text);
			'CODE' // - символьный код элемента;
		);
		$fields = array_merge($standardFields, $this->getUserProps());

		$dbElements = \CIBlockElement::GetList(
			$sort,
			$filter,
			false,
			false,
			$fields
		);

		$elements = array();
		while ($element = $dbElements->Fetch()) {
			// Пользовательские свойства выводим в отдельный массив
			foreach ($element as $key => $value) {
				if (strpos($key, 'PROPERTY_') !== false and strpos($key, '_ID') === false) {
					$element['PROPERTIES'][str_replace(array('PROPERTY_', '_VALUE'), '', $key)] = $value;
				}
			}

			$elements[] = $element;
		}

		return $elements;
	}

	public function executeComponent()
	{
		// В кеше сохроняем только html вывод без хранения arResult
		$this->setResultCacheKeys([]);

		if ($this->startResultCache()) {
			$this->arResult = $this->getElements();

			$this->includeComponentTemplate();
		}
	}
}