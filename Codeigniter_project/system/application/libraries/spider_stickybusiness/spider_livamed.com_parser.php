<?php
require_once 'spider_lib_ag/XPathHelper.php';
require_once 'spider_lib_ag/spider_parser.php';

class Spider_LivamedCom_Parser extends Spider_Parser
{
	public function __construct()
	{
		parent::__construct('http://www.livamed.com/');
	}
	
	public function parseSearchResult(XPathHelper $xph)
	{
		return $xph->xpSubQueries("//ul[@class='ProductList List Clear']/li",
			array(
				'product.name' =>	"div[@class='ProductDetails']/strong",
				'product.url' =>	"div[@class='ProductDetails']/strong/a/@href",
				'product.rating' => "div[@class='ProductDetails']/div[contains(@class,'Rating')]/@class",
				'product.description_preview' =>	"div[@class='ProductDetails']/div[@class='ProductDescription']",
				'product.price_listed' =>	"div[@class='ProductDetails']/span[@class='ProductRightCol']/span[@class='ProductPrice']/text()",
				'product.price_retail'	=>	"div[@class='ProductDetails']/span[@class='ProductRightCol']/span[@class='ProductPrice']/strike",
			)
		);
	}
	
	public function parseSearchResultPagination(XPathHelper $xph)
	{
		return $xph->queryValue("//div[@class='CategoryPagination'][2]/ul[@class='PagingList']/li[@class='ActivePage']/following-sibling::*[1]/a/@href");
	}
	
	public function parseProductDetails(XPathHelper $xph)
	{
		$record=reset($xph->xpSubQueries("//div[@id='ProductDetails']//div[@class='BlockContent']",
			array(
				'product.name'=> 'h2',
				'product.price_retail' => "div[@class='ProductMain']/div[@class='ProductDetailsGrid']/div[@class='DetailRow RetailPrice']/div[@class='Value']",
				'product.price_listed' => "div[@class='ProductMain']/div[@class='ProductDetailsGrid']/div[@class='DetailRow PriceRow']/div[@class='Value']/em[@class='ProductPrice VariationProductPrice']",
				'product.sku' => "div[@class='ProductMain']/div[@class='ProductDetailsGrid']/div[@class='DetailRow ProductSKU']/div[@class='Value']",
			)
		));
		$record['product.description']= $xph->queryValue("//div[@id='ProductDescription']/div[@class='ProductDescriptionContainer']");
		/** TODO: parse additional data fields for weight, quantity, brand,rating..
				 *	//div[@id='ProductDetails']//div[@class='BlockContent']/div[@class='ProductMain']/div[@class='ProductDetailsGrid']/div[@class='DetailRow']
 		 *	will return a list of div[@class='Label'] and div[@class='Value'] children 
		 */
		return $record;
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
				case 'product.sku':
					$record['product.upc']=$value;
				case 'product.upc':
				case 'product.url':
				case 'product.name':
				case 'product.description_preview':
				case 'product.description':
				case 'product.rating':
					break;
				default:
						throw new Exception("Unexpected key $key=>$value");
			}
		}
		return $record;
	}
}
?>
