<?php
require_once 'spider_lib_ag/XPathHelper.php';
require_once 'spider_lib_ag/spider_parser.php';
class Spider_VitaminshoppeCom_Parser extends Spider_Parser
{
	public function __construct()
	{
		parent::__construct('http://www.vitaminshoppe.com/');
	}
	
	public function parseSearchResult(XPathHelper $xph)
	{
		return $xph->xpSubQueries("//div[@id='categoryTable']/div[@class='row']",
			array(
				'product.name' =>	"div[@class='rowInfo']/div[@class='rowWrapper']/div[@class='rowInfoA']/h3/a",
				'product.url' =>	"div[@class='rowInfo']/div[@class='rowWrapper']/div[@class='rowInfoA']/h3/a/@href",
				'product.price_listed' =>	"div[@class='rowInfo']/div[@class='rowWrapper']/div[@class='rowInfoB']/div/div[@id='priceLabelContainer']/div[@class='saleValue-Price-Search']",
				'product.price_retail' =>	"div[@class='rowInfo']/div[@class='rowWrapper']/div[@class='rowInfoB']/div/div[@id='priceLabelContainer']/div[@class='listRegular-Price'][1]",
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
				case 'product.price_retail':
					if($value === null)
						continue;
					$value=$this->extract_preg("#^(List) Price: (?<value>[$][0-9]+\.[0-9]{2})$#", $value);
					break;
				case 'product.price_listed':
					if($value === null)
						continue;
					//INFO: we are not checking for confusion between listed and retail price syntax. Accepting both syntax.
					$value=$this->extract_preg("#^(Sale|Your) Price: (?<value>[$][0-9]+\.[0-9]{2})$#", $value);
					break;
				case 'product.url':
					$value=$this->href2url($value);
					break;
				case 'product.reviews-count':
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
