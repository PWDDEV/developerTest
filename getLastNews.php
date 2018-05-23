<?php

/**
 * Reader of rss chanel
 */
class RssReader implements \Iterator
{
	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var XMLReader
	 */
	protected $source;

	/**
	 * @var array
	 */
	protected $items = [];

	/**
	 * @var integer
	 */
	protected $position = 0;

	/**
	 * Construct of class
	 * 
	 * @param string $url Path to rss chanel
	 */
	public function __construct($url)
	{
		$this->url = $url;
	}

	public function rewind()
	{
		$this->position = 0;
	}

	public function key()
	{
		return $this->position;
	}

	public function current()
	{
		return $this->items[$this->position];
	}

	public function next()
	{
		$this->position++;
	}

	public function valid()
	{
		if (array_key_exists($this->position, $this->items)) {
			return true;
		}

		if ($item = $this->read()) {
			$this->items[$this->position] = $item;

			return true;
		}

		return false;
	}

	/**
	 * Open rss chanel for read
	 * 
	 * @throws Exception
	 * 
	 * @return DomNodeList
	 */
	protected function getSource()
	{
		if ($this->source === null)
		{
			$dom = new DomDocument('1.0');

			if (!$dom->load($this->url)) {
				throw new Exception('Unable open rss chanel');
			}

			$xpath = new DomXPath($dom);
			$this->source = $xpath->query('//*/item');
		}

		return $this->source;
	}

	/**
	 * Read rss item from chanel
	 * 
	 * @return array|false
	 */
	protected function read()
	{
		$items = $this->getSource();

		if ($items->length <= $this->position) {
			return false;
		}

		$node = $items->item($this->position);

		return static::nodeToArray($node);
	}

	/**
	 * Convert DomNode to array
	 * 
	 * @param DomNode $node
	 * 
	 * @return mixed
	 */
	protected static function nodeToArray($node)
	{
		return (array) simplexml_import_dom($node);
	}
}

$rss = new RssReader('https://lenta.ru/rss');

foreach ($rss as $index => $item)
{
	echo 'Название: '. $item['title'], PHP_EOL;
	echo 'Ссылка: '. $item['link'], PHP_EOL;
	echo $item['description'], PHP_EOL;
	echo '----------------------------', PHP_EOL;

	if ($index >= 4) {
		break;
	}
}