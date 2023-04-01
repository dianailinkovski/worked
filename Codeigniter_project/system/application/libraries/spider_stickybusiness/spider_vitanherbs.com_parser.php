<?php
require_once 'spider_lib_ag/XPathHelper.php';
require_once 'spider_lib_ag/spider_parser.php';
class Spider_VitanHerbsCom_Parser extends Spider_Parser
{
	public function __construct()
	{
		parent::__construct('http://www.vitanherbs.com/');
	}
	
	public function parseSearchResult(XPathHelper $xph)
	{
		return $xph->xpSubQueries("//div[@class='category-products']/ul[@class]/li",
			array(
				'product.name' =>	"h2[@class='product-name']",
				'product.url' =>	"h2[@class='product-name']/a/@href",
				'product.price_listed' =>	"div[@class='price-box']/p[@class='special-price']/span[@class='price']",
				'product.price_retail'	=>	"div[@class='price-box']/p[@class='old-price']/span[@class='price']",
			)
		);
	}
	
	public function parseSearchResultPagination(XPathHelper $xph)
	{
		return null;
	}
	
	public function parseProductDetails(XPathHelper $xph)
	{
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
					if( !preg_match("#^$|^[$][0-9]+\.[0-9]{2}$#", $value,$matches))
							throw new Exception("Unexpected value for $key=>$value");
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
