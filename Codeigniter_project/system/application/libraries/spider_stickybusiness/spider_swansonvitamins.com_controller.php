<?php
require_once 'spider_swansonvitamins.com_parser.php';
require_once 'spider_lib_ag/spider_controller.php';

class Spider_SwansonvitaminsCom_Controller extends Spider_Controller
{
  
  public function __construct()
  {
    parent::__construct( 'http://www.swansonvitamins.com/', new Spider_SwansonvitaminsCom_Parser() );
  }
  
  //override openHref to enforce custom user agent
  //TODO include timestamps in openHref ? or similar construct ?
  public function openHref($href, $pageUrl = NULL)
  {
    $bak=XPathHelper::$_curlopts;
    XPathHelper::$_curlopts[CURLOPT_USERAGENT]= "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11";
    
    $res=parent::openHref($href);
    
    XPathHelper::$_curlopts=$bak;
    
    return $res;
  }
  
  public function search($keyword)
  {
    if($keyword !== '')
    {
      throw new Exception(__FUNCTION__.":searching for keyword not supported");
    }
    $result=array();
    foreach($this->getParser()->parseBrands($this->openHref('/Brands')) as $href => $brand)
    {
      $result= array_merge($result, $this->listProduct($href));
    }
    return $result;
  }

  public function listProduct($hrefBrand)
  {     
    $results=array();
    for($href= $hrefBrand;
      $href !== null;
      $href= $this->getParser()->parseSearchResultPagination($xph)
    ){
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
    $product=
      $this->getParser()->translateRecord(
        $this->getParser()->parseProductDetails(new XPathHelper($url))
      );
    if(count($product)===1)
      $product[0]['product.url']=$url;
    elseif(count($product) >1)
      throw new Exception(__FUNCTION__.": returning more than 1 product.\n".var_export($product,true));
    return $product;
  }
}