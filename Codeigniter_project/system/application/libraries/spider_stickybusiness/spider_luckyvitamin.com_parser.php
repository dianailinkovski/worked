<?php
require_once 'spider_lib_ag/XPathHelper.php';
require_once 'spider_lib_ag/spider_parser.php';
//XPathHelper::$_debug=1;
class Spider_LuckyvitaminCom_Parser extends Spider_Parser
{
	public function __construct()
	{
		parent::__construct('http://www.luckyvitamin.com/');
	}
	
	public function parseSearchResult(XPathHelper $xph)
	{
		return $xph->xpSubQueries("//ul[@class='product-list']/li",
			array(
				'product.name' =>		"div[@class='description']/div[@class='listDataPad']/div[@class='listItemLink']/a",
				'product.url' =>		"div[@class='description']/div[@class='listDataPad']/div[@class='listItemLink']/a/@href",
				'product.sku' =>		"div[@class='description']/div[@class='listDataPad']/div[@class='listCode']",
				'product.quantity' =>	"div[@class='description']/div[@class='listDataPad']/div[@class='listData']",
				
				'product.price_listed' =>	"div[@class='add-to-cart preview-cart-wrapper']/div[@class='prices']/span[@class='salePrice']/span",
				'product.price_retail' =>	"div[@class='add-to-cart preview-cart-wrapper']/div[@class='prices']/span[@class='retailPrice']/span",
				'product.reviews-count' =>	"div[@class='add-to-cart preview-cart-wrapper']/div[@class='listRating']/span[@class='product-rating-number']",
				'product.rating' =>	array(	"div[@class='add-to-cart preview-cart-wrapper']/div[@class='listRating']/div[@class='product-rating']/img",
					array( 'ratings' => "@src" )
				),
			)
		);
	}
	
	public function parseSearchResultPagination(XPathHelper $xph)
	{
		return null;
	}
	
	public function parsePageType(XPathHelper $xph)
	{
		if( $xph->queryValue("//div[@class='itemPageWrapper']") !== null )
			return 'product';
		else
			return 'search';
	}
	
	public function parseProductDetails(XPathHelper $xph)
	{

	//broken page with error message
	if(substr($xph->queryValue("//div[@class='itemPageWrapper']/text()[5]"),0,9)==='Exception')
		return array();
	
	$result=array();
	//Discontinued items have no price.
	if( $xph->queryValue("//div[@id='unluckyInfo']/div[@id='apology']/h2") ==="We're Sorry, We No Longer Carry This Item")
	{
		$result=$this->array_merge_first_record($result,$xph->xpSubQueries("//div[@id='unluckyInfo']/div[@id='apology']",
			array(
				'product.sku_and_upc' => "//div[@class='itemDataCode']",
			)));
		return $result;
	}

	//regular items
	$result=$this->array_merge_first_record($result,$xph->xpSubQueries("//div[@class='imgBorderBgNew']",
		array(
			'product.sku' => "//div[@class='itemDataCode'][span]",
			'product.upc' => "//div[@class='itemDataCode'][not(span)]",
		)));

	//TRICK: putting array arround collapseLabelValuePairs() to make it compatible with array_merge_first_record()
	$result=$this->array_merge_first_record($result,
				array($this->collapseLabelValuePairs(
					$xph->xpSubQueries("//div[@id='tabInfoContent']/table/tbody/tr/td/table/tbody/tr/td/div[@class='itemData']",
						array(
							'label' => 'b',
							'value' => 'text()',
			)))));

	$result= $this->array_merge_first_record($result,$xph->xpSubQueries("//div[@id='tabPricingContent']/table/tbody/tr/td/div[@class='pricingDisplay']",
		array(
			'product.price_retail' => "table/tbody/tr/td/div[@class='retailPrice']",
			'product.price_listed' => "table/tbody/tr/td/div[@class='salePrice']|table/tbody/tr/td/div[@class='specialPrice']",
		))
	);
	$result['product.url']=$xph->getUrl();
	return $result;
	}
	
	public function translateRecord($record)
	{
		$output_record=array();
		foreach($record as $key=>$input)
		{
			$value=$input;
			switch($key)
			{
				case 'product.price_retail':
					if($value === null)
						continue;
					$value=$this->extract_preg("#^(Retail Price: )?(?<value>[$][0-9]+\.[0-9]{2})$#", $value);
					break;
				case 'product.price_listed':
					if($value === null)
						continue;
					$value=$this->extract_preg("#^(Special Promotion: |LuckyVitamin: )?(?<value>[$][0-9]+\.[0-9]{2})$#", $value);
					break;
				case 'product.sku':
					if($value === null)
						continue;
					$value=$this->extract_preg("#^(Item Code|Code): (?<value>[0-9]+)$#", $value);
					break;
				case 'product.quantity':
					if($value === null)
						break;
					$value=$this->extract_preg("#^(?<value>[0-9]+) (?<unit>[A-Za-z .]+)$#", $value,$matches);
					$output_record['product.quantity-unit']=$matches['unit'];
					unset($matches);
					break;
				case 'product.reviews-count':
					if($value === null)
					{
						$value=0;
						break;
					}
					$value=intval($this->extract_preg("#^\((?<value>[0-9]+) Ratings\)$#", $value));
					break;
				case 'product.rating':
					if(count($value) === 0)
					{
						$this->assert($output_record['product.reviews-count'] === 0 );
						break;
					}
					$this->assertEquals(5,count($value));
					$rating=0;
					foreach($value as $ratingStarImg)
					{
						$this->assert( count($ratingStarImg) === 1 );
						$ratingStarImg=$ratingStarImg['ratings'];
						if( $ratingStarImg==='skins/Skin_1/downloaded_files/ratingGold.gif' )
							$rating++;
						elseif( $ratingStarImg==='skins/Skin_1/downloaded_files/ratingGray.gif' )
							continue;
						else
							throw new Exception("unexpected rating star img/@src ".$ratingStarImg);
					}
					$value=$rating;
					unset($rating);
					unset($ratingStarImg);
					break;
				case 'product.sku_and_upc':
					$output_record['product.upc']=$this->extract_preg("#^(Item Code:\s+(?<sku>[0-9]+))?UPC Code: (?<value>[0-9]+)$#", $value,$matches);
					unset($value);
					if($matches['sku']!=='')
						$output_record['product.sku']=$matches['sku'];
					unset($matches);
					break;
				case 'product.upc':
					$value=$this->extract_preg("#^UPC Code: (?<value>[0-9]+)$#", $value);
					unset($matches);
					break; 
				case 'product.url':
					$value=$this->href2url($value);
					break;
				case 'product.quantity-unit':
				case 'product.name':
					
					break;
				/* sample data
Product: Cordyceps Power CS-4
Code#: Lucky ID: 52387 | UPC: 021078104261
Manufacturer:Planetary Herbals
Size/Form: 120  Tablets
Packaged Ship Weight: 0.20  lbs
Servings: 60
Dosage Size: 2  Tablet(s)
Potency Size: 800  mg.
*/
				case 'Product:':
					$output_record['product.name']=$value;
					unset($value);
					break;
				case 'Code#:':
					$output_record['product.upc']=$this->extract_preg("#^Lucky ID: (?<sku>[0-9]+)( \| UPC: (?<value>[0-9]+))?$#", $value,$matches);
					unset($value);
					if($matches['sku']!=='')
						$output_record['product.sku']=$matches['sku'];
					unset($matches);
					break;
				case 'Manufacturer:':
					$output_record['product.brand']=$value;
					unset($value);
					break;
				//INFO: ignore the following values.
				case 'Size/Form:':
				case 'Packaged Ship Weight:':
				case 'Servings:':
				case 'Dosage Size:':
				case 'Potency Size:':
				case 'Flavor title:':
					unset($value);
					break; 
				default:
					throw new Exception("Unexpected key $key=>$value");
			}
			if(isset($value))
				$output_record[$key]=$value;
		}
		return $output_record;
	}
	
	public function buildSearchHref($keyword)
	{
		$keyword=str_replace(' ','-',$keyword);
		return '/sb-'.$keyword;
	}
}
?>
