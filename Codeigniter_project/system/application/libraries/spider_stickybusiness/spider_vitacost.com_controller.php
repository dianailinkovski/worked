<?php
require_once 'spider_vitacost.com_parser.php';
require_once 'spider_lib_ag/spider_controller.php';

class Spider_VitacostCom_Controller extends Spider_Controller
{
	
	public function __construct()
	{
		parent::__construct('http://www.vitacost.com/', new Spider_VitacostCom_Parser());
	}
	
	public function search($keyword)
	{		
		$results=array();
		for($href= '/Search.aspx?'
				. http_build_query(array('ntk' => 'products','Ntt' => $keyword));
			$href !== null;
			$href= $this->getParser()->parseSearchResultPagination($xph)
			)
		{
			$xph= $this->openHref($href);
			$scraping_timestamp = new DateTime("now",new DateTimeZone('UTC'));
			$scraping_timestamp = $scraping_timestamp->format("Y-m-d h:i:s");
			switch($this->getParser()->parsePageType($xph))
			{
				case 'product':
					$record=$this->getParser()->translateRecord($this->getParser()->parseProductDetails($xph));
					$record['timestamp']= $scraping_timestamp;
					return array($record);
					break;
				case 'searchResult':
					$new_results=$this->getParser()->parseSearchResult($xph);
					foreach($new_results as &$record)
					{
						$record= $this->getParser()->translateRecord($record);
						$record['timestamp']=$scraping_timestamp;
					}
					$results=array_merge($results,$new_results);
					break;
				default:
					throw new Exception(__FUNCTION__.":unexpected page type");
			}
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