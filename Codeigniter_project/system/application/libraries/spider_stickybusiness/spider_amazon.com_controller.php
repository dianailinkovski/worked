<?php

require_once 'spider_amazon.com_parser.php';
require_once 'spider_lib_ag/spider_controller.php';

class Spider_AmazonCom_Controller extends Spider_Controller {
	
	var $retries = array();

    public function __construct() {
        parent::__construct('http://www.amazon.com/', new Spider_AmazonCom_Parser());
    }

	
    public function search($keyword) {
        $results = array();
        for ($href = 's/ref=nb_sb_noss?'.http_build_query(
				array(
					'url' => "search-alias=aps",
					'field-keywords' => $keyword,
				)
			);
			$href !== null;
			$href = $this->getParser()->parseSearchResultPagination($xph)
		){
            $xph = $this->openHref($href);
            $scraping_timestamp = new DateTime("now", new DateTimeZone('UTC'));
            $scraping_timestamp = $scraping_timestamp->format("Y-m-d h:i:s");
            switch ($this->getParser()->parsePageType($xph)) {
                case 'product':
                    $record = $this->getParser()->translateRecord($this->getParser()->parseProductDetails($xph));
					$record['source.href'] = $href;
                    $record['timestamp'] = $scraping_timestamp;
                    return array($record);
                    break;
                case 'searchResult':
                    $new_results = $this->getParser()->parseSearchResult($xph);
                    foreach ($new_results as &$record) {
                        $record = $this->getParser()->translateRecord($record);
                        $sellers = array();
                        $counter = 0;
                        for ($offer_href = '/gp/offer-listing/' . $record['product.sku'];
							 $offer_href !== NULL;
							 $offer_href = $this->getParser()->parseOfferMerchantListPagination($xpath_helper))
						{
						    $xpath_helper = $this->openHref($offer_href);
							$tmpSellers = $this->getParser()->parseOfferMerchantList($xpath_helper);
                            $sellers = array_merge($sellers, $tmpSellers);
                        }
                        $record['timestamp'] = $scraping_timestamp;
						$record = $this->formatRecord($record, $sellers);
                    }
                    $results = array_merge($results, $new_results);
                    break;
                case 'captcha':
					log_message("error", "Amazon captcha encountered, {$xph->proxy->proxy_host}");
					$this->retries[$href] = isset($this->retries[$href]) ? $this->retries[$href]+1 : 1;
					log_message("info", "Amazon retry {$this->retries[$href]}");
					if($this->retries[$href] > 2){ // recursion base case. TODO: make this a config value?
						return array();
					}
					sleep(10);
					$xph->bad_proxy('Amazon captcha');
					$results = $this->search($keyword); // recurse
					break;
				default:
					$xph->bad_proxy('Unknown cause');
                    throw new Exception(__FUNCTION__ . ":unexpected page type");
            }
        }
		//log_message('debug', "search results before: " . print_r($results,true));
		//if(count($results)>0) exit;
		
		// put it into a format that _retailer_lookup() will enjoy.
		$output = array();
		foreach ($results as $subarray){
			if(isset($subarray[0]) && is_array($subarray[0])){
				foreach($subarray as $subsub){
					$output[] = $subsub;
				}
			}
			else{
				$output[] = $subarray;
			}
		}
		$results = $output;
		
		//log_message('debug', "search results after: " . print_r($results,true)."\n----------------------------------------------------------------------\n");
		//if(count($results)>0) exit;
		
        return $results;
    }

	// merge many seller data with one (redundant) product data
	public function formatRecord($record, $sellers){
		//log_message('debug', "formatRecord seller: ". print_r($sellers,true));
		//log_message('debug', "formatRecord before: ". print_r($record,true));
		$output = array();
		foreach($sellers as $i => $s){
			$r = $record;
			$r['product.url']          = !empty($r['product.sellers-percondition']['new']['sellers.url']) ? $r['product.sellers-percondition']['new']['sellers.url'] : $r['product.url'];
			$r['product.price_listed'] = !empty($s['offer.price']) 		   ? $s['offer.price']         : '';
			$r['product.shipping']     = !empty($s['offer.price-shipping'])? $s['offer.price-shipping']: '';
			$r['seller.name']          = !empty($s['seller.name']) 	       ? $s['seller.name']         : '';     
			$r['seller.logo']          = !empty($s['seller.logo'])         ? $s['seller.logo']         : '';
			$r['seller.aboutus']       = !empty($s['seller.aboutus'])      ? $s['seller.aboutus']      : '';
			$r['product.image_url']    = !empty($s['product.image'])       ? $s['product.image']       : '';
			$r['seller.seller_id']     = !empty($s['seller.seller_id'])    ? $s['seller.seller_id']    : '';
			$r['seller.real_name']     = !empty($s['seller.real_name'])    ? $s['seller.seller_id']    : '';
			$output[] = $r;
		}
		//log_message('debug', "formatRecord after: " . print_r($output,true));
		return $output;
	}
	
    public function searchUpc($upc) {
        return $this->search($upc);
    }

    public function getProductDetails($url) {
        return
                $this->getParser()->translateRecord(
                        $this->getParser()->parseProductDetails(new XPathHelper($url))
        );
    }

    public function getProductOffers($url) {
        $details = $this->getProductDetails($url);
        //TRICK: first item of sellers collection is either "new" or 0/any
        //FIXME: we might encounter not new condition.
        if (count($details['product.sellers-percondition']) === 0)
            return array();
        $sellersperconditionnew = reset($details['product.sellers-percondition']);
        if ($sellersperconditionnew['sellers.url'])
            for ($href = $sellersperconditionnew['sellers.url']; $href !== null; $href = $this->getParser()->parseOfferSellerListPagination($xph)
            ) {
                $xph = new XPathHelper($this->getParser()->href2url($href));
                $offers = $this->getParser()->parseOfferSellerList($xph);
                foreach ($offers['offers.sellers'] as &$seller)
                    $seller = $this->getParser()->translateRecord($seller);
            }
        return $offers['offers.sellers'];
    }

}