<?

const RSS_SOURCE = 'https://lenta.ru/rss';
const NEWS_COUNT = 5;

class PARSED_NEWS
{
	private $news = [];
	
	/**
	 * Add parsed new to news array.
	 * Values will be converted to strings by (string), instead simplexml's __toString().
	 * @param $title
	 * @param $link
	 * @param $description
	 */
	public function addNew($title, $link, $description)
	{
		array_push($this->news, [
			'title' => (string)$title,
			'link' => (string)$link,
			'description' => (string)$description
		]);
	}
	
	public function getAll()
	{
		return $this->news;
	}
}

$rss = simplexml_load_file(RSS_SOURCE);

$obLastNews = new PARSED_NEWS();
for ($i = 0; $i < NEWS_COUNT; $i++) {
	$item = $rss->channel->item[$i];
	$obLastNews->addNew($item->title, $item->link, $item->description);
}

print_r($obLastNews->getAll());