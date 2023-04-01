<?php
require_once 'spider_lib_ag/XPathHelper.php';
require_once 'spider_lib_ag/spider_parser.php';
class Spider_IherbCom_Parser extends Spider_Parser
{
	public function __construct()
	{
		parent::__construct('http://www.iherb.com/');
	}
	
	public function parseSearchResult(XPathHelper $xph)
	{
		return $xph->xpSubQueries("//div[@id='display-results-content']/div[@class='prodSlotWide']",
			array(
				'product.name' =>	"p[@class='description']",
				'product.url' =>	"p[@class='description']/a/@href",
				'product.rating' => "div[@class='details']/div[@class='starsAndPrice']/span/a/img/@title",
				'product.price_listed' =>	"div[@class='details']/div[@class='starsAndPrice']/span[@class='price']/text()[1]",
				'product.price_retail'	=>	"div[@class='details']/div[@class='starsAndPrice']/span[@class='price']/span[@class='crossed-out-price']",
			)
		);
	}
	
	public function parseSearchResultPagination(XPathHelper $xph)
	{
		return null;
	}
	
	public function parseProductDetails(XPathHelper $xph)
	{
		//FIXME: not implemented. URL would not download through scraper.
		return  array();
		throw new Exception(__FUNCTION__." not implemented ");
	}
	
	public function translateRecord($record)
	{
		foreach($record as $key=>&$value)
		{
			switch($key)
			{
				case 'product.price_listed':
				case 'product.price_retail':
					if($value === null)
						continue;
					if($value ==='Add to Cart to see Price')
					{
						$value=null;
						continue;
					}
					if( !preg_match("#^$|^[$][0-9]+\.[0-9]{2}$#", $value,$matches))
							throw new Exception("Unexpected value for $key=>$value");
					break;
				case 'product.rating':
					if($value === null)
					{
						$record['product.reviews-count']=0;
						break;
					}
					$value=$this->extract_preg("#^(?<value>[0-9](\.[0-9])?) of 5 based on (?<reviewscount>[0-9]+)$#",$value,$matches);
					$record['product.reviews-count']=intval($matches['reviewscount']);
					unset($matches); //TODO: review existing code for such flaw.
					break;
				case 'product.reviews-count':
				case 'product.url':
				case 'product.name':
					
					break;
				default:
						throw new Exception("Unexpected key $key=>$value");
			}
		}
		return $record;
	}
}
?>
