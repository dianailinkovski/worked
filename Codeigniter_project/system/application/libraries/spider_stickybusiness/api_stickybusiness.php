<?php	

//-- include spiders
require_once 'spider_livamed.com_controller.php';
require_once 'spider_vitacost.com_controller.php';
require_once 'spider_amazon.com_controller.php';
require_once 'spider_iherb.com_controller.php';
require_once 'spider_vitanherbs.com_controller.php';
require_once 'spider_vitaminshoppe.com_controller.php';
require_once 'spider_luckyvitamin.com_controller.php';
require_once 'spider_swansonvitamins.com_controller.php';
require_once 'spider_google.com_controller.php';
//-- end include spiders

class StickyBusinessException extends Exception{};

class StickyBusiness
{
	private $_spiders=array();
	
	public function __construct()
	{
		//-- create spiders	
		$this->addSpider(new Spider_LivamedCom_Controller()); 
		$this->addSpider(new Spider_VitacostCom_Controller()); 
		$this->addSpider(new Spider_AmazonCom_Controller()); 
		$this->addSpider(new Spider_IherbCom_Controller()); 
		$this->addSpider(new Spider_VitanherbsCom_Controller()); 
		$this->addSpider(new Spider_VitaminshoppeCom_Controller()); 
		$this->addSpider(new Spider_LuckyvitaminCom_Controller()); 
		$this->addSpider(new Spider_SwansonvitaminsCom_Controller()); 
		$this->addSpider(new Spider_GoogleCom_Controller());
    //--print_r(XPathHelper::$_curlopts); exit;
		XPathHelper::$_curlopts[52]=true;
		XPathHelper::$_curlopts[13]=20;
	}

	private function getSpiderId($url)
	{
		return parse_url($url,PHP_URL_HOST);
	}
	
	/**
	 * 
	 * @param Spider_Controller $spider
	 */
	public function addSpider(Spider_Controller $spider)
	{
		$this->_spiders[$this->getSpiderId($spider->getBaseUrl())]=$spider;
	}
	
	/**
	 * 
	 * @param url $merchantUrl
	 * @return a specific spider controller
	 */
	private function getSpider($merchantUrl)
	{
		return $this->_spiders[$this->getSpiderId($merchantUrl)];
	}
	
	public function getSpidersList()
	{
		return array_keys($this->_spiders);
	}
	
	/**
	 * 
	 * @param string $upc  product code
	 * @param url $merchantUrl target ecommerce website
	 * @throws StickyBusinessException
	 * @return array	1 product entity
	 */
	public function searchUpc($upc,$merchantUrl)
	{
		if(is_array($upc))
		{
			$searchResults=array();
			foreach($upc as $upc_item)
			{
				$searchResults[$upc_item]=$this->searchUpc($upc_item,$merchantUrl);
			}
			return $searchResults;
		}
		$searchResults= $this->getSpider($merchantUrl)->searchUpc($upc);

		return $this->filterDataFields($searchResults,$merchantUrl);
	}
	
	/**
	 * 
	 * @param string $keyword search keyword
	 * @param url $merchantUrl target ecommerce website
	 * @return array of arrays product entities
	 */
	public function search($keyword,$merchantUrl)
	{
		if(is_array($keyword))
		{
			$searchResults=array();
			foreach($keyword as $keyword_item)
				$SearchResults[$keyword]=$this->search($keyword, $merchantUrl);
			return $SearchResults;
		}
		$searchResults= $this->getSpider($merchantUrl)->search($keyword);
		$searchResults= $this->filterDataFields($searchResults,$merchantUrl);
		return $searchResults;
	}
	
	public function getProductDetails($productUrl)
	{
		return $this->getSpider($productUrl)->getProductDetails($productUrl);
	}
	
	/**
	 * 
	 * @param array $searchResults array of product entities
	 * @param string $merchantUrl ecommerce platform
	 * @return array of product entities strip out unused data fields.
	 */
	private function filterDataFields($searchResults,$merchantUrl)
	{
		$filteredResults=array();
		foreach( $searchResults as $key => $record )
		{
			$filteredRecord= array();
			foreach( explode('|','product.sku|timestamp|product.price_listed|product.price_retail|product.url|product.name|product.sellers') as $field )
				if(isset($record[$field]))
					$filteredRecord[$field]= $record[$field];

			$filteredRecord['merchant.url']=$merchantUrl;
			$filteredRecord['product.image_url']=null; // FIXME: not supported by scrapers yet.
			$filteredResults[$key]= $filteredRecord;
		}
		return $filteredResults;
	}
}
