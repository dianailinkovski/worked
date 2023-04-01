<?php
require_once 'spider_google.com_parser.php';
require_once 'spider_lib_ag/spider_controller.php';

class Spider_GoogleCom_Controller extends Spider_Controller
{
  
  public function __construct()
  {
    parent::__construct( 'http://www.google.com/shopping', new Spider_GoogleCom_Parser() );
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
    $results=array();
    for($href= 'http://www.google.com/shopping?'
                . http_build_query(array('hl'=>'en','tbm'=>'shop','q' => $keyword));
        $href !== null;
        $href= $this->getParser()->parseSearchResultPagination($xph)
       )
    {
      $xph= $this->openHref($href);
      if($xph->queryValue("/html/head/title") == "Sorry...")
        throw new Exception("IP banned");
      $scraping_timestamp = new DateTime("now",new DateTimeZone('UTC'));
      $scraping_timestamp = $scraping_timestamp->format("Y-m-d h:i:s");
      $new_results=$this->getParser()->parseSearchResult($xph);
      foreach($new_results as &$record)
      {
        $record= $this->getParser()->translateRecord($record);
        $record['timestamp']=$scraping_timestamp;
      }
      if( count($new_results) == 0 )
        return $results;
      $results=array_merge($results,$new_results);
    }
    return $results;
  }

  public function listProduct($hrefBrand)
  {     
    $results=array();
    for($href= $hrefBrand;
      $href !== null;
      $href= $this->getParser()->parseSearchResultPagination($xph)
    ){
      $xph= $this->openHref($href);
      if($xph->queryValue("/html/head/title") == "Sorry...")
        throw new Exception("IP banned");
      $scraping_timestamp = new DateTime("now",new DateTimeZone('UTC'));
      $scraping_timestamp = $scraping_timestamp->format("Y-m-d h:i:s");
      $new_results=$this->getParser()->parseSearchResult($xph);
      foreach($new_results as &$record)
      {
        $record= $this->getParser()->translateRecord($record);
        $record['timestamp']=$scraping_timestamp;
      }

      if( count($new_results) == 0 )
        return $results;
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
    $product=$this->getParser()->parseProductDetails(new XPathHelper($url));
    $product=$this->getParser()->translateRecord($product);
    //FIXME: replace with loop
    if(count($product)===1)
      $product[0]['product.url']=$url;
    return $product;
  }
  
    public function getProductOffers($url)
    {
        //REFACT: rewrite elegant domain check 
        if( strpos($url,"www.google.com") == false)
            return array();
        
        $details= $this->getProductDetails($url);
        if($details == array())
            return array();
        
        //TRICK getPRoductDetails() returns a collection of 1 single element with current product. 
        $details_item0=reset($details);
        $offers=$details_item0['product.sellers'];
   
        if( $details_item0['product.sellers_more_url'] != null )
        {
            $offers=array();
            for($href=$details_item0['product.sellers_more_url'] ;
                $href !==null ;
                $href=$href
            )
            {
                //REFACT: duplicate code with amazon
                $xph=new XPathHelper($this->getParser()->href2url($href));
                $offers=array_merge($offers,$this->getParser()->parseProductSellers($xph));
                $href=$this->getParser()->parseOfferSellerListPagination($xph);
            }
        }
        foreach($offers as &$seller)
            $seller=$this->getParser()->translateRecord($seller);
        return $offers;
    }
  
}
