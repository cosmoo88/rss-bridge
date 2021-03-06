<?php
class TagBoardBridge extends BridgeAbstract{

	public function loadMetadatas() {

		$this->maintainer = "Pitchoule";
		$this->name = "TagBoard";
		$this->uri = "http://www.TagBoard.com";
		$this->description = "Returns most recent results from TagBoard.";
		$this->update = "2014-09-10";

		$this->parameters[] =
		'[
			{
				"name" : "keyword",
				"identifier" : "u"
			}
		]';

	}

    public function collectData(array $param){
        $html = '';
        $this->request = $param['u'];
        $link = 'https://post-cache.tagboard.com/search/' .$this->request;
		
        $html = $this->file_get_html($link) or $this->returnError('Could not request TagBoard for : ' . $link , 404);
        $parsed_json = json_decode($html);

        foreach($parsed_json->{'posts'} as $element) {
                $item = new Item();
                $item->uri = $element->{'permalink'};
		$item->title = $element->{'text'};
                $item->thumbnailUri = $element->{'photos'}[0]->{'m'};
                if (isset($item->thumbnailUri)) {
                  $item->content = '<a href="' . $item->uri . '"><img src="' . $item->thumbnailUri . '" /></a>';
                }else{
                  $item->content = $element->{'html'};
                }
                $this->items[] = $item;
        }
    }

    public function getName(){
        return 'tagboard - ' .$this->request;
    }

    public function getURI(){
        return 'http://TagBoard.com';
    }

    public function getCacheDuration(){
        return 21600; // 6 hours
    }
}
							
