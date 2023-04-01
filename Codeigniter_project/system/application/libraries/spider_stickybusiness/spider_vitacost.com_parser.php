<?php
require_once 'spider_lib_ag/XPathHelper.php';
require_once 'spider_lib_ag/spider_parser.php';
class Spider_VitacostCom_Parser extends Spider_Parser
{
	public function __construct()
	{
		parent::__construct("http://www.vitacost.com/");
	}
	
	public function parseSearchResult(XPathHelper $xph)
	{
		if( $this->parsePageType($xph)==='SearchResult')
			throw new Exception(__FUNCTION__.": Unexpected document");
		$record=$xph->xpSubQueries("//div[@class='spPLB']/div/div[@class='pltgR']/div[@class='pltgRin cf']/div[@class='pltgPL brR']/div[@class='pltgPLin']/div[@class='pt0 cf']/div[@class='pt9P cf clear']",
			array(
				'product.name_and_quantity'	=>	"div[@class='prdTrm']/a",
				'product.url'			=>	"div[@class='prdTrm']/a/@href",
				'product.price_listed'	=>	"div[@id='bottomPricing']//div[@class='pOurPriceL']",
				'product.price_retail'	=>	"div[@id='bottomPricing']//div[@class='fs-1 txtI']",
				//BUG:rating is returning 5 nodes.
				'product.rating'		=>	"div/div[@class='pRating']/a/img/@src",
			)
		);
		return $record;
	}
	
	public function parseSearchResultPagination(XPathHelper $xph)
	{
		return $xph->queryValue("//a[@id='IamMasterFrameYesIam_ctl02_EndecaTopPagination_next']/@href");
	}
	
	public function parseProductDetails(XPathHelper $xph)
	{
		$record=reset($xph->xpSubQueries("//div[@id='productDesc']",
			array(
				'product.name'=>		"//div[@class='RSTL_RightTitle_Product']",
				'product.price_retail'=>"//div[@id='rightSideWrapper']/div[@class='RSTR_TopRetail_Product']",
				'product.price_listed'=> "//div[@id='rightSideWrapper']/div[@class='RSTR_TopValue_Product']/div[@class='pOurPriceM']",
				//TRICK: order sensitive. sale price will override listed price in the translator 
				'product.price_sale'=> "//div[@id='rightSideWrapper']/div[@class='RSTR_TopValue_Product']/div[@class='pSalePrice']",
				'product.url'	=>		"//link[@rel='canonical']/@href",
			)
		));
		$record['product.numeric_details[]']=$xph->queryValue("//div[@id='productDesc']//div[@id='RSTL_Right_Product']/div[@class='RSTL_RightCount_Product']/div",-1);
		$record['product.rating']=$xph->queryValue("//span[@class='BVRRNumber BVRRRatingNumber']");
		return $record;
	}
	
	public function parsePageType(XPathHelper $xph)
	{
		if($xph->xpQuery("//div[@class='srListing']") !== null )
			return "searchResult";
		if(true)//FIXME detect product
			return "product";
			//FIXME: detect no result
	}
	
	public function translateRecord($record)
	{
		foreach($record as $key=>&$value)
		{
			switch($key)
			{
				case 'product.price_sale':
					if( $value != null )
						$record['product.price_listed'] = $this->extract_preg("#^$|^(Vitacost [pP]rice|Sale price|Retail price):\s+(?<value>[$][0-9]+\.[0-9]{2})$#", $value,$matches);
					unset($record[$key]);
					break;
				case 'product.price_listed':
				case 'product.price_retail':
					if($value === null)
						continue;
					$value=$this->extract_preg("#^$|^(Vitacost [pP]rice|Sale price|Retail price):\s+(?<value>[$][0-9]+\.[0-9]{2})$#", $value,$matches);
					break;
				case 'product.url':
					$value=$this->href2url($value);
					break;
				case 'product.name_and_quantity':
					list($record['product.name'],$record['product.quantity'])=explode(" -- ",$value);
					unset($record[$key]);
					break;
				case'product.numeric_details[]':
					$record['product.reference']=$this->extract_preg("/^Item #: (?<value>.*)$/",$value[0]);
					$record['product.sku']=$this->extract_preg("/^SKU #: (?<value>.*)$/",$value[1]);
					$record['product.quantity']=$this->extract_preg("/^Count: (?<value>.*)$/",$value[2]);
					$record['product.weight']=$this->extract_preg("/^Weight: (?<value>.*)$|Serving:.*/",$value[3]);
					unset($record[$key]);
					break;
				case 'product.sku':
					$record['product.upc']=$value;
				case 'product.upc':
				case 'product.name':
				case 'product.description_preview':
				case 'product.description':
				case 'product.rating':
				case 'product.quantity':
				case 'product.reference':
				case 'product.weight':
					break;
				default:
						throw new Exception("Unexpected key $key=>$value");
			}
		}
		return $record;
	}
}
?>
