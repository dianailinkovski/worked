<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class semantics extends base {

	protected $_requestor;

	public function __construct($config) {

		parent::__construct();

		//require semantics3 library
		require_once(FCPATH . 'system/application/libraries/semantics3/Semantics3.php');

		$this->_requestor = new Semantics3_Products($config['key'], $config['secret']);
	}

	protected function getOffers($identifier) {
		//we need to filter the offers
		$offerList = array();

		$offersFromModel = $this->_model->_offers;
		foreach($offersFromModel as $offerRow) {

			foreach($offerRow['latestoffers'] as $offer) {

					//we dont want amazon
					if(stripos($offerRow['name'], 'amazon') !== false) {
						continue;
					}	

					if($offer['lastrecorded_at'] >= strtotime("-2 day")) {

						if(stripos($offerRow['url'], 'amazon.com') !== false) {
							$offerRow['url'] = 'http://www.amazon.com/gp/offer-listing/' . $offerRow['sku'];
						}

						if(empty($offerRow['listprice']))
							$offerRow['listprice'] = $this->_model->get("price");

						$offerList[] = array(
							'url' => $offerRow['url'],
							'id' => $offer['id'],
							'merchant_url' => $offerRow['url'],
							'marketplace' => $offerRow['name'],
							'price' => $offer['price'],
							'price_floor' => $offerRow['listprice'],
							'firstrecorded' => $offer['firstrecorded_at'],
							'lastrecorded' => $offer['lastrecorded_at'],
							'merchant' => $offer['seller']
						);
					}
			}
		}
		return $offerList;
	}

	protected function getProductInformation($identifier) {
		$this->_requestor->products_field("upc", trim($identifier));
		$results = json_decode($this->_requestor->get_products(), true );

		if(!empty($results)) {
			if($results['total_results_count'] > 0) {
				if(!empty($results['results'][0])) {
					$productInformation = $results['results'][0];
					if(empty($productInformation['manufacturer'])) 
						$productInformation['manufacturer'] = null;

					if(empty($productInformation['price']))
						$productInformation['price'] = null;

					if(empty($productInformation['description']))
						$productInformation['description'] = null;

					return array(
						'upc' => $identifier,
						'price' => $productInformation['price'],
						'title' => $productInformation['name'],
						'manufacturer' => $productInformation['manufacturer'],
						'shipping_price' => 0,
						'product_image' => $productInformation['images'][0],
						'merchant_logo' => null,
						'description' => $productInformation['description'],
						'internal_id' => $productInformation['sem3_id'],
						'offers' => $productInformation['sitedetails']
					);
				}
			}
		}
		return false;
	}

	protected function getHistoricalInformation($identifier) {

	}

	public function reset() {
		$this->_requestor->clear_query();
		$this->_model = null;
		$this->_identifier = null;
	}
}