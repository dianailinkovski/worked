<?php
require_once 'spider_livamed.com_parser.php';
require_once 'spider_lib_ag/spider_controller.php';

class Spider_LivamedCom_Controller extends Spider_Controller
{
	
	public function __construct()
	{
		parent::__construct( 'http://www.livamed.com/', new Spider_LivamedCom_Parser() );
	}
	
	public function search($keyword)
	{
		$results=array();
		for($href= '/search.php?'
				. http_build_query(array('search_query' => $keyword));
			$href !== null;
			$href= $this->getParser()->parseSearchResultPagination($xph)
			)
		{
			$xph= $this->openHref($href);
			$scraping_timestamp = new DateTime("now",new DateTimeZone('UTC'));
			$scraping_timestamp = $scraping_timestamp->format("Y-m-d h:i:s");
			$new_results=$this->getParser()->parseSearchResult($xph);
			foreach($new_results as &$record)
			{
				$record= $this->getParser()->translateRecord($record);
				$record['timestamp']=$scraping_timestamp;
			}
			$results=array_merge($results,$new_results);
		}
		return $results;
	}
	
	public function searchUpc($upc)
	{
		return $this->search($upc);
	}
	
	public function getProductDetails($url)
	{
		return
			$this->getParser()->translateRecord(
				$this->getParser()->parseProductDetails(new XPathHelper($url))
			);
	}
}
