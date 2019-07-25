<?
class Parse {
	private $url;
	private $newsCount;
	private $newsList;

	function __construct()
	{
		// Параметры парсинга
		$this->url = 'https://lenta.ru/rss';
		$this->newsCount = 5;

		// Процедура парсинга
		$xml = $this->getXmlInUrl($this->url);
		$this->newsList = $this->parseXml($xml);
	}

	/**
	 * Получает $xml объект по ссылке
	 * @param $url
	 * @return SimpleXMLElement
	 */
	private function getXmlInUrl($url)
	{
		$xml = simplexml_load_file($url);
		return $xml;
	}

	/**
	 * Парсит xml в массив
	 * @param SimpleXMLElement $xml
	 * @return array
	 */
	private function parseXml(SimpleXMLElement $xml)
	{
		$newsList = [];
		for ($i = 0; $i < $this->newsCount; $i++) {
			$item = $xml->channel->item[$i];
			$newsList[] = [
				'title' => $item->title->__toString(),
				'link' => $item->link->__toString(),
				'description' => $item->description->__toString()
			];
		}

		return $newsList;
	}

	/**
	 * Выводит данные полученные по url извлечённые из xml
	 */
	public function getNewsList()
	{
		foreach ($this->newsList as $key => $item) {
			echo 'Название: ' . $item['title'] . PHP_EOL;
			echo 'Ссылка на новость: ' . $item['link'] . PHP_EOL;
			echo 'Анонс:' . PHP_EOL . trim($item['description']) . PHP_EOL;
			echo PHP_EOL;
		}
	}
}

$news = new Parse();
$news->getNewsList();