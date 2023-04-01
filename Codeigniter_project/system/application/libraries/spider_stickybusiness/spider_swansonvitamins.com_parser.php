<?php
require_once 'spider_lib_ag/XPathHelper.php';
require_once 'spider_lib_ag/spider_parser.php';
class Spider_SwansonvitaminsCom_Parser extends Spider_Parser
{
  public function __construct()
  {
    parent::__construct('http://www.swansonvitamins.com/');
  }
  
  public function parseSearchResult(XPathHelper $xph)
  {
    return $xph->xpSubQueries("//div[@id='search-results-wrapper']/form/div[@id]",
      array(
        'product.brand' => "div[@class='search-result-item']/div[@class='item-info']/ul[@class='item-details-list']/li[@class='vendor']",
        'product.name' => "div[@class='search-result-item']/div[@class='item-info']/ul[@class='item-details-list']/li[@class='productName']",
        'product.url' => "div[@class='search-result-item']/div[@class='item-info']/ul[@class='item-details-list']/li[@class='productName']/a/@href",
        'product.price_listed' =>   "div[@class='search-result-item']/div[@class='item-price-wrapper']/div[@class='item-price']",
        'product.price_retail'  =>  "div[@class='search-result-item']/div[@class='item-price-wrapper']/div[@class='item-reg-price']",
        'product.price_retail'  =>  "div[@class='search-result-item']/div[@class='item-price-wrapper']/div[@class='item-reg-price']",
        'swansonvitamins.product.hiddenprice' => "div[@class='search-result-item']/div[@class='item-price-wrapper']/div[@class='see-price']/a",
      )
    );
  }
  public function parseBrands(XPathHelper $xph)
  {
    return $this->collapseLabelValuePairs(
      $xph->xpSubQueries("//div[@id='innerStoreContent']/div[contains(@class,'col')]/ul/li",
        array(
          'label' => "a/@href",
          'value' => "a")));
  }
  
  public function parseSearchResultPagination(XPathHelper $xph)
  {
    return null;
  }
  
  public function parseProductDetails(XPathHelper $xph)
  {
      return $xph->xpSubQueries("//div[@id='item-spc']", array(
        'product.name' => "div[@id='item-summary']/h1[@class='item-name']",
        'product.brand' => "div[@id='item-summary']/h2[@class='item-brand']",
        'product.price_listed' => "div[@id='item-attributes']/div[@id='item-price']/div[@id='swanson-price']/b[@class='price']",
        'product.price_retail' => "div[@id='item-attributes']/div[@id='item-price']/div[@id='retail-price']/b[@class='price']",
      ));
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
          $value=$this->href2url($value);
          break;
        case 'product.price_listed':
          //INFO: accept no retail price when hidden in shopping cart.
          if(isset($record['swansonvitamins.product.hiddenprice'])
              and $record['swansonvitamins.product.hiddenprice'] === 'See price in cart')
            break;
          
          $value=$this->extract_preg("#^(LOW sale price!|(Swanson|Sale Price|Now [0-9]+ for) $price_regex)$#",$value);
          break;
        case 'product.price_retail':
          //INFO: accept product when no retail price
          if($value === null)
            break;
          
          $value=$this->extract_preg("#^(Retail|Regular) $price_regex$#",$value);
          break;
        case 'swansonvitamins.product.hiddenprice':
            unset($record[$key]);
            break;
        default:
            throw new Exception("Unexpected key $key=>$value");
      }
    }
    return $record;
  }
}
?>
