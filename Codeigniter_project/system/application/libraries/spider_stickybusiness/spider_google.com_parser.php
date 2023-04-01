<?php
require_once 'spider_lib_ag/XPathHelper.php';
require_once 'spider_lib_ag/spider_parser.php';
class Spider_GoogleCom_Parser extends Spider_Parser
{
  public function __construct()
  {
    parent::__construct('http://www.google.com/');
  }
  
  public function parseSearchResult(XPathHelper $xph)
  {
    //WARNING: this requires finer testing to avoid false positive
    if($xph->queryValue("//li[@class='psmkhead']") != null )
        return array();
    if( $xph->queryValue("//div[@id='ires']/ol/li[1]/div[@class='pslicont']") != null ) 
      return $xph->xpSubQueries("//div[@id='ires']/ol/li",
        array(
          'product.brand'=> "div[@class='search-result-item']/div[@class='item-info']/ul[@class='item-details-list']/li[@class='vendor']",
          'product.name' => "div[@class='pslicont']/div[ @class='pslimain' or @class='pslmain' ]/h3/a",
          'product.url'  => "div[@class='pslicont']/div[ @class='pslimain' or @class='pslmain' ]/h3/a/@href",
          #'product.url' => "div[@class='pslires']/div[@class='pslimain']/h3/a/@href",
          'product.price_listed' =>   "div[@class='search-result-item']/div[@class='item-price-wrapper']/div[@class='item-price']|div[@class='pslicont']/div[@class='pslipricecol']/div[@class='psliprice']"
                                      . "|div[@class='pslicont']/div[@class='pslimain']/div[@class='pslline']/div[@class='_hG']/span[@class='price']/b"
                                      . "|div[@class='pslicont']/div[@class='pslmain']/div[@class='pslline']/div[@class='_vH']/span[@class='price']/b"
                                      . "|div[@class='pslicont']/div[@class='pslmain']/div[@class='pslline']/div[@class='_RH']/span[@class='price']/b"
                                      . "|div[@class='pslicont']/div[@class='pslmain']/div[@class='pslline']/div[@class='_Cs']/div/span[@class='_qm']/b"
                                      . "|div[@class='pslicont']/div[@class='pslmain']/div[@class='pslline']/div[@class='_it']/div/span[@class='_Mm']/b"
                                      . "|div[@class='pslicont']/div[@class='pslmain']/div[@class='pslline']/div[@class='_et']/div/span[@class='_Am']/b"
                                      ,
          'product.price_retail'  =>  "div[@class='search-result-item']/div[@class='item-price-wrapper']/div[@class='item-reg-price']"
        							."|div[@class='pslicont']/div[@class='pslmain']/div[@class='pslline']/span[@class='psmkprice']/b",
        )
      );
    elseif( $xph->queryValue("//div[@id='ires']/ol/li[1]/div[@class='pslires' or @id='pslires']" ) != null)
      return $xph->xpSubQueries("//div[@id='ires']/ol/li",
        array(
          'product.name' => "div[@class='pslires']/div[@class='_Hf']/h3/a/text()",
          'product.url'  => "div[@class='pslires']/div[@class='_Hf']/h3/a/@href",
          'product.price_listed' => "div[@class='pslires']/div[@class='_Tb' or @class='_zd']/div/b",
        )
      );
    else
	return array();
  }
  
  public function parseSearchResultPagination(XPathHelper $xph)
  {
    return $xph->queryValue("//a[@id='pnnext']/@href");
  }
  
  public function parseProductDetails(XPathHelper $xph)
  {
	
    $res= $xph->xpSubQueries("//body", array(
        'product.name' => "//h1[@id='product-name']",
        'product.brand' => "//div[@id='product-brand']",
        'product.price_retail' => "//div[@id='summary-prices']/span/span[@class='price']",
        'product.price_listed' => "div[@id='item-attributes']/div[@id='item-price']/div[@id='retail-price']/b[@class='price']", //TODO
        'product.sellers_more_url' => "//div[@id='os-content']/div[@class='pag-bottom-links']/a[@class='pag-detail-link']/@href",
        'product.image-url' => "//a[@id='condensed-image-cont']/div[@class='_Wk']/img/@src",
    ));
    $res[0]['product.sellers'] = $this->parseProductSellers($xph);
    return $res;
  }
  
  public function parseProductSellers(XPathHelper $xph)
  {
    return $xph->xpSubQueries("//tr[@class='os-row']",array(
      'seller.name' => "td[@class='os-seller-name']",
      'seller.url' => "td[@class='os-seller-name']/span[@class='os-seller-name-primary']/a/@href",
      'product.price_listed' => "td[@class='os-price-col']/span[@class='os-base_price']",
    ));
  }

    public function parseOfferSellerListPagination(XPathHelper $xph)
    {
        return $xph->queryValue("//span[@id='online-pagination']/a[contains(.,'Next')]/@href");
    }

    public function translateGoogleUrls($value)
    {
        //REFACT: 3 times duplicate code
        $value=$this->href2url($value);
        $parse_url=parse_url($value);
        if( $parse_url['host'] == 'www.google.com' and  isset($parse_url['query']) )
        {
            parse_str($parse_url['query'],$query);
            if( isset($query['adurl']) )
                $value=$query['adurl'];
        }
        $parse_url=parse_url($value);
        if( strpos($parse_url['host'],'xg4ken.com') != false and  isset($parse_url['query']) )
        {
            parse_str($parse_url['query'],$query);
            if( isset($query['url']) )
                $value=$query['url'];
        }
        $parse_url=parse_url($value);
        if( $parse_url['host'] =='clickserve.dartsearch.net' and  isset($parse_url['query']) )
        {
            parse_str($parse_url['query'],$query);
            if( isset($query['ds_dest_url']) )
                $value=$query['ds_dest_url'];
        }
        return $value;
    }
  public function translateRecord($record)
  {
    $price_regex="(?<value>\\$[0-9.]+)";
    foreach($record as $key=>&$value)
    {
      switch($key)
      {
        case 'product.brand':
        case 'product.name':
          break;
        case 'product.url':
          $value= $this->translateGoogleUrls($value);
          break;
        case 'product.price_listed':
          break;
        case 'product.price_retail':
          break;
        case 'product.sellers':
          break;
        case 'seller.name':
            break;
        case 'seller.url':
            $value= $this->translateGoogleUrls($value);
            break;
        default:
            throw new Exception("Unexpected key $key=>$value");
      }
    }
    return $record;
  }
}
?>
