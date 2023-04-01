<?php

require_once 'spider_lib_ag/XPathHelper.php';
require_once 'spider_lib_ag/spider_parser.php';

class Spider_NeweggCom_Parser extends Spider_Parser {

    public function __construct() {
        parent::__construct('http://www.newegg.com/');
    }

    public function parseSearchResult(XPathHelper $xph) {
        return $xph->xpSubQueries("//div[@class='productList']/div[1]/div[@class='itemCell']", array(
                    'product.name' => "div[@class='itemText']/div[@class='wrapper']/a/span[@class='itemDescription' and starts-with(@id, 'title')]",
                    'product.url' => "div[@class='itemText']/div[@class='wrapper']/a/@href",
                    'product.sku' => "div[@class='itemText']/ul[@class='featureList']/li[contains(., 'Model #:')]/text()",
                    'product.rating' => "div[@class='itemGraphics']/a[@class='itemRating']/@title",
        ));
    }

    public function parseSearchResultPagination(XPathHelper $xph) {
        return null;
    }
    
    public function parseOfferSellerList($xph) {
//        return $xph->xpSubQueries("//table[@class='gridSellerList']/tbody/tr", array(
//            'offer.price' => "td[@class='grpPricing']/ul/li[@class='price-current ']/strong",
//            'offer.price-shipping' => "div[1]/p/span/span[@class='olpShippingPrice']",
//            'seller.name' => "div[3]/p[contains(@class, 'olpSellerName')]/span/a",
//            'seller.logo' => "div[3]/p[contains(@class, 'olpSellerName')]/a/img/@src",
//        ));)
    }

    public function parseProductDetails(XPathHelper $xph) {
        return array();
        throw new Exception(__FUNCTION__ . " not implemented ");
    }

    public function translateRecord($record) {
        foreach ($record as $key => &$value) {
            switch ($key) {
                case 'product.price_listed':
                    if ($value === null)
                        continue;
                    if (!preg_match("#^$|^[$][0-9]+\.[0-9]{2}$#", $value, $matches))
                        throw new Exception("Unexpected value for $key=>$value");
                    break;
                case 'product.reviews-count':
                case 'product.url':
                case 'product.sku':
                case 'product.name':
                    break;
                case 'product.rating':
                    $value = substr($value, 9);
                    break;
                default:
                    throw new Exception("Unexpected key $key=>$value");
            }
        }
        return $record;
    }

}

?>
