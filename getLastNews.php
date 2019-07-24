class GetLastNews{
	
	public function GetNews($count=5,$json=false){
		
		if(!$json){
			$cn=0;
			$xml=simplexml_load_file("https://lenta.ru/rss", null, LIBXML_NOCDATA);
			foreach($xml->channel->item as $item){
				
				if($cn<$count){
					$txt.= '<div><a href="'.$item->link.'">'.$item->title.'</a><div>'.$item->description.'</div></div>';
				}
				$cn++;
			}
			return $txt;
		}else{
			$cn=0;
			$xml=simplexml_load_file("https://lenta.ru/rss", null, LIBXML_NOCDATA);
			foreach($xml->channel->item as $item){
				
				if($cn<$count){
					$txt[]=array("link"=>$item->link,"title"=>$item->title,"description"=>$item->description);
				}
				$cn++;
			}
			return json_encode($txt);
		}
	}
	
}
