<?php
/**
 * TODO: break this up into several model files, one for each merchant
 * @repaired by chris.fortune@gmail.com July 2014
 */
class Crowl_m extends MY_Model {

	private $globalArray = array(
		'all' => array()
	);
	private $IPS;
	private $myDB;

	public function Crowl_m() {
		parent::MY_Model();
		set_time_limit(0);
		ini_set("memory_limit", "400M");

		$this->load->library('simple_html_dom');
		$this->load->library('email');
		$this->ci->load->model('products_trends_m', 'ProductsTrends');
		$this->ci->load->model('products_m', 'Products');
		//$this->load->model("products_m", "Products:");
		/* Load the db adapter class */
		require_once dirname(BASEPATH) . '/system/application/libraries/mydb.php';

		$marketplaces = get_market_lookup(FALSE, TRUE);
		if (!empty($marketplaces)) {
			foreach ($marketplaces as $api) {
				$this->globalArray[$api] = array();
			}
		}
	}

	public function getMerchantList() {
		return $this->db->get($this->_table_crowl_merchant_name)->result_array();
	}

	public function updateCrowlProductList($id, $new_id, $marketplace) {
		$this->db
			->where('merchant_name_id', $id)
			->where('marketplace', $marketplace)
			->update($this->_table_crowl_product_list, array('merchant_name_id' => $new_id));

		return $this->db->affected_rows();
	}

	 public function getMerchantIdCountByMarketPlace($id) {
		return $this->db
			->select('count(*) as total, marketplace')
			->where('merchant_name_id', $id)
			->group_by('marketplace')
			->get($this->_table_crowl_product_list)
			->result_array();
	}

	public function updateMerchantNameTable($data, $id = 0) {
		if ($id) {
			$this->db
				->where('id', (int) $id)
				->update($this->_table_crowl_merchant_name, $data);
		}
		else {
			$this->db->insert($this->_table_crowl_merchant_name, $data);
			$id = $this->db->insert_id();
		}

		return $id;
	}

	public function update_crowled_product($product_info = array()) {
		$this->db->insert($this->_dynamo_products_trends, $product_info);
	}

	public function load_sticky_api($api = null) {
		switch ($api) {
			case 'amazon':
				require_once APPPATH . 'libraries/spider_stickybusiness/spider_amazon.com_controller.php';
				$this->sticky_api = new Spider_AmazonCom_Controller();
				break;
			default:
				require_once APPPATH . 'libraries/spider_stickybusiness/api_stickybusiness.php';
				$this->sticky_api = new StickyBusiness();
		}
	}

	public function crowl_products($store_id, $type = 'all', $cron_log_id = '0') {
		// A list of existing APIs
		$valid_types = get_market_lookup(FALSE, TRUE);
		$valid_types['all'] = TRUE;
		if ( ! isset($valid_types[$type]))
			throw new InvalidArgumentException($type . ' is not a valid marketplace.', 1301); // argument not in valid set

		// Crawl using these APIs
		$apis = $type === 'all' ? array_keys($valid_types) : array($type);
		$date_stamp = date('Y-m-d H:i:s');
		$offset = 0;

		// Get the total number of records
		$where = array(
			'store_id'			  => (int)$store_id,
			'is_tracked'		  => 1,
			'is_archived'		  => 0,
			'status'			  => 1,
			'length(upc_code) >=' => 10
		);
		$result_count = $this->db
			->select('count(*) as total_record')
			->where($where)
			->get($this->_table_products)
			->row();
		$total_records = $result_count ? $result_count->total_record : 0;

		// Crawl the products 100 at a time
		while ($total_records > $offset){
			$results = $this->db
				->where($where)
				->group_by('upc_code')
//->orderBy('RAND()')
//->limit(1, $offset)
				->limit(100, $offset)
				->get($this->_table_products)
				->result();

			// TODO: do a fallback strategy for keyword search:  if no UPC, then use other data
			//This scraper must:
			//	1) find products based upon UPC code
			//	2) if not successful above, find products based upon manufacturer + manufacturer part number
			//	3) if not successful above, find products based upon manufacturer + product name
			for ($i = 0, $n = count($results); $i < $n; $i++) {
				$upc = trim($results[$i]->upc_code);
				if ($upc) {
					if(empty($this->globalArray[$type][$upc])) {
						$this->globalArray[$type][$upc] = true;
						$id = $results[$i]->id;
						$upcs = array($id => $upc);
						$price_floors = array(
							$id => array(
								'floor_price' => $results[$i]->price_floor
							)
						);

						// Lookup UPC using proper API
						foreach ($apis as $api) {
							$lookup_func = $api . '_lookup';
							try {
								$this->$lookup_func($upcs, $price_floors, $cron_log_id, $store_id);
							}
							catch (Exception $e) {
								email_alertToTeam($lookup_func, $e->getMessage());
							}
						}
					}
				}
			}
			$offset += 100;
		}

		$this->db
			->where('id', (int)$store_id)
			->update($this->_table_store, array('tracked_at' => $date_stamp));

		return true;
	}

	// TODO: refactor these redundant $api . '_lookup' functions
    // TODO: add URL lookup per UPC x api
    public function iherb_lookup($upc, $floor_price, $cron_log_id, $store_id) {
		$api = 'iherb';
		$search_url = 'http://www.iherb.com';
		$this->_retailer_lookup($api, $search_url, $upc, $floor_price, $cron_log_id, $store_id);
	}

	public function livamed_lookup($upc, $floor_price, $cron_log_id, $store_id) {
		$api = 'livamed';
		$search_url = 'http://www.livamed.com';
		$this->_retailer_lookup($api, $search_url, $upc, $floor_price, $cron_log_id, $store_id);
	}

	public function luckyvitamin_lookup($upc, $floor_price, $cron_log_id, $store_id) {
		$api = 'luckyvitamin';
		$search_url = 'http://www.luckyvitamin.com';
		$this->_retailer_lookup($api, $search_url, $upc, $floor_price, $cron_log_id, $store_id);
	}

	public function vitacost_lookup($upc, $floor_price, $cron_log_id, $store_id) {
		$api = 'vitacost';
		$search_url = 'http://www.vitacost.com';
		$this->_retailer_lookup($api, $search_url, $upc, $floor_price, $cron_log_id, $store_id);
	}

	public function vitaminshoppe_lookup($upc, $floor_price, $cron_log_id, $store_id) {
		$api = 'vitaminshoppe';
		$search_url = 'http://www.vitaminshoppe.com';
		$this->_retailer_lookup($api, $search_url, $upc, $floor_price, $cron_log_id, $store_id);
	}

	public function vitanherbs_lookup($upc, $floor_price, $cron_log_id, $store_id) {
		$api = 'vitanherbs';
		$search_url = 'http://www.vitanherbs.com';
		$this->_retailer_lookup($api, $search_url, $upc, $floor_price, $cron_log_id, $store_id);
	}

	public function swansonvitamins_lookup($upc, $floor_price, $cron_log_id, $store_id) {
		$api = 'swansonvitamins';
		$search_url = 'http://www.swansonvitamins.com';
		$this->_retailer_lookup($api, $search_url, $upc, $floor_price, $cron_log_id, $store_id);
	}
	
	// only one upc per invocation
	private function _retailer_lookup_deprecated($api, $search_url, $upcs, $floor_price, $cron_log_id, $store_id)
	//private function _retailer_lookup($api, $search_url, $upcs, $floor_price, $cron_log_id, $store_id)
	{
		foreach ($upcs as $key => $upc) {} // only one index
		
		$price_floor = $floor_price[$key]['floor_price'];
		$retailer_data = $this->_spider_search($upc, $api, $search_url);
		if ( ! isset($retailer_data[0])) {
			$this->updateUPCFlag($upc, $api, '0');
			return;
		}
		$this->myDB = new mydb();
		$avg_price = 0;
		$n = 0;
		$violationFlag = false;
		$uniquArr = array();
		foreach ($retailer_data as $item) {
			$this->stats[$api]['data_found']++;
			$crawled_product = array();
			$violation = array();
			$author = $this->getMerchantNameForKey($api, $upc, $api, $api, $api, $search_url);
			if(empty($author)) {
				// assert dummy value
				$author = "No name found.";
			}
			$stQuery = "UPDATE cron_log set google_count=(google_count+1), last_UPC='{$upc}' WHERE id= '{$cron_log_id}' ";
			$this->myDB->simpleQuery($stQuery);
			$hashKey = $author . '#' . $upc;
			$crawled_product['um'] = $hashKey;
			$crawled_product['dt'] = time();
			$crawled_product['ap'] = $price_floor;
			$crawled_product['t'] = isset($item['product.name']) ? $item['product.name'] : '';
			$crawled_product['mu'] = isset($item['seller.aboutus']) ? $item['seller.aboutus'] : '';
			$crawled_product['l'] = isset($item['product.url']) ? $item['product.url'] : '';
			$crawled_product['ar'] = $api;
			$crawled_product['mil'] = isset($item['seller.logo']) ? $item['seller.logo'] : '';
			$crawled_product['il'] = isset($item['product.image_url']) ? $item['product.image_url'] : '';
			$crawled_product['mpo'] = isset($item['product.price_listed']) ? $this->parseDollarAmount($item['product.price_listed']) : 0;
			$crawled_product['msp'] = isset($item['product.shipping']) ? $item['product.shipping'] : 0;

			if ($crawled_product['mpo']) {
				$this->stats[$api]['price_found']++;
				$avg_price = $this->_new_avg($avg_price, $crawled_product['mpo']);

				// Get the price from the last crawl
				$dynamo = new AmazonDynamoDB();
				$lastRecordResponse = $dynamo->query(array(
					'TableName'		 => $this->_dynamo_products_trends,
					'HashKeyValue'	 => array(AmazonDynamoDB::TYPE_STRING	 => $crawled_product['um']),
					// optional parameters
					'ConsistentRead'			 => true,
					'Limit'						 => 1,
					'ScanIndexForward'			 => false
				));

				$insert_response = $this->amzdb->insertData($this->_dynamo_products_trends, $crawled_product, $api);
				if (isset($insert_response->status) && $insert_response->status == 200)
					$this->insertUPCMerchant($author, $upc, $api, $crawled_product['mpo'], $api, $api);

				// get the corresponding crowlMerchantName row
				$crowlMerchantName = $this->Crawl_data->crowlMerchantBySellerID($api);
				if (empty($crowlMerchantName)) {
					log_message('error', __FILE__ . ' Crowl_m::' . $api . '_lookup() Line ' . __LINE__ .
						': crowl_merchant_name record not found for seller ID ' . $api
					);
				}
				$crowlMerchantNameID = isset($crowlMerchantName->id) ? $crowlMerchantName->id : 0;
				$violatedPrice = (float)$crawled_product['mpo'];
				$dataVio = $this->Products->get_products_by_floor($upc, $violatedPrice, $store_id);
				
				if ($dataVio) {
					$violation['um'] = $hashKey;
					$violation['dt'] = $crawled_product['dt'];
					$violation['ss'] = date('Ymd', $violation['dt']) . '/' . md5($hashKey . $violation['dt']) . '.png';
					$this->updateViolationSummary($dataVio);
					$this->Violator->updatePriceViolator($crowlMerchantNameID, $upc, 1, $crawled_product['dt']);
					$lastCrawlPrice = (float)0;
					$hashKey = null;
					$rangeKey = null;
					
					if ($lastRecordResponse->isOK()) {
						$mpo = isset($lastRecordResponse->body->Items->mpo->N) ? (float)$lastRecordResponse->body->Items->mpo->N : (float)0; // Merchant Price Offered
						$lastCrawlPrice = $mpo;
						$hashKey = isset($lastRecordResponse->body->Items->um->S) ? $lastRecordResponse->body->Items->um->S : null;
						$rangeKey = isset($lastRecordResponse->body->Items->dt->N) ? $lastRecordResponse->body->Items->dt->N : null;
					}

					// Check if the price has changed
					if ((float)$lastCrawlPrice != (float)$violatedPrice) {
						if ( ! isset($uniquArr[$crawled_product['l']])) {
							$uniquArr[$crawled_product['l']] = $violation['ss'];
							$this->addScreenShot($crawled_product['l'], $violation['ss'], false, $violatedPrice);
						}
						else {
							$violation['ss'] = $uniquArr[$crawled_product['l']];
						}
					}
					else {
						$takeNewScreenShot = true;
						// Get the violation screen shot
						if (!empty($hashKey) and !empty($rangeKey)) {
							$lastViolationResponse = $dynamo->query(array(
								'TableName'		 => $this->_dynamo_violations,
								'HashKeyValue'	 => array(AmazonDynamoDB::TYPE_STRING	 => (string)$hashKey),
								'RangeKeyCondition'			 => array(
									'ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
									'AttributeValueList' => array(
										array(AmazonDynamoDB::TYPE_NUMBER	 => (string)$rangeKey)
									)
								),
								'ConsistentRead'			 => 'true'
								));

							if ($lastViolationResponse->isOK() and $lastViolationResponse->body->Count == 1) {
								if (isset($lastViolationResponse->body->Items->ss->S)) {
									if (@fopen(get_instance()->config->item('s3_cname') . 'stickyvision/violations/' . $lastViolationResponse->body->Items->ss->S, 'r')) {
										$violation['ss'] = $lastViolationResponse->body->Items->ss->S;
										$takeNewScreenShot = false;
									}
								}
							}
						}
						if ($takeNewScreenShot === true) {
							$this->addScreenShot($crawled_product['l'], $violation['ss'], false, $violatedPrice);
						}
					}

					$this->amzdb->insertData($this->_dynamo_violations, $violation, $api);
					$violationFlag = true;
					unset($dataVio);
				}
				else { // update price violators for products not in violation
					$this->Violator->updatePriceViolator($crowlMerchantNameID, $upc, 0, $crawled_product['dt']);
				}
				$n++;
			}
			else {
				email_alertToTeam($api . '_lookup', 'Product data found, but price not found for UPC ' . $upc);
			}
			unset($retailer_data, $crawled_product, $violation);
		}
		$this->myDB->close();
		$this->updateUPCFlag($upc, $api, '1');
	}

	/**
	 * Retrieve the retailer product data by UPC or a hash map to a URL.
	 */
	// TODO: figure out how to skip crawls for non-existant products
	protected function _spider_search($upc, $api, $search_url) {
		$api_data = $this->Market->get_marketplace_by_name($api);
		if ( ! empty($api_data) AND $api_data['upc_lookup'] == 0) {
			$ret = $this->_search_without_upc($upc, $api_data, $search_url);
		}
		else {
			$ret = $this->_search_with_upc($upc, $api, $search_url);
		}
		return $ret;
	}

	protected function _search_without_upc($upc, $api_data, $search_url) {
		$ret = array();
		$product = $this->Products->get_product_details($upc);
		if (! empty($product[0]['id'])) {
			$lookup_url = $this->Market->get_product_lookup_url($product[0]['id'], $api_data['id']);
			if ($lookup_url) {
				if (extract_domain($lookup_url) !== extract_domain($search_url)) {
					log_message('error', 'Warning: Sticky ' . $api_data['name'] . ' spider is using a lookup URL with a different domain than the search URL: ' . $lookup_url);
				}
				try {
					$ret = $this->sticky_api->getProductDetails($lookup_url);
				}
				catch (Exception $e) {
					log_message('error', $e->getMessage(), 'Sticky "' . $api_data['name'] . '" spider exception searching by URL: ' . $lookup_url . ' for UPC: ' . $upc);
				}
			}
		}
		
		return $ret;
	}

	protected function _search_with_upc($upc, $api, $search_url) {
		$ret = array();
		try {
			$ret = $this->sticky_api->searchUpc($upc, $search_url);
		}
		catch (Exception $e) {
			send_alert($e->getMessage(), 'Sticky "' . $api . '" spider exception searching by UPC: ' . $upc);
		}

		return $ret;
	}

	public function amazon_lookup($upc, $floor_price, $cron_log_id, $store_id) {
		$search_url = "http://www.amazon.com";
		$api = 'amazon';
		$this->_retailer_lookup($api, $search_url, $upc, $floor_price, $cron_log_id, $store_id);
	}

	// TODO: refactor into _retailer_lookup()
	private function _retailer_lookup($api, $search_url, $upcs, $floor_price, $cron_log_id, $store_id)
	//public function amazon_lookup($upcs, $floor_price, $cron_log_id, $store_id)
	{
		//$search_url = "http://www.amazon.com";
		//$api = 'amazon';
		//echo "\n-------------------------------------------------------------------------------------\n";
		//echo "_retailer_lookup(".print_r($upcs,true);//.",
		//floor_price: ".print_r($floor_price,true).",
		//cron_log_id: $cron_log_id,
		//store_id: $store_id)";
		//exit;
		
		foreach ($upcs as $key => $upc) {/*only one index, always*/} 
		
		if (empty($upc) ){
			log_message('error', "Missing UPC, $api ".print_r($upcs,true) );
			return;
		}
		$retailer_data = $this->_spider_search($upc, $api, $search_url);
		//echo "retailer_data: \n";
		//print_r($retailer_data); 
		//exit;
		if ( empty($retailer_data)) {
			$this->updateUPCFlag($upc, $api, '0');
			log_message('info', "upc $upc not found in $api");
			return;
		}
		$price_floor = getPricePoint($upc, $store_id, 'price_floor', time()); // MAP
		$this->myDB = new mydb();
		// only one screen_shot of each product page
		$uniquArr = array();
		foreach($retailer_data as $item)
		{
			log_message('info', 'item: '.print_r($item,true));
			if (empty($item) ){
				log_message('info', "Missing result, $api ".print_r($upcs,true) );
				continue;
			}
			$this->stats[$api]['data_found']++;
			$crawled_product = array();
			
			$sku = $item['product.sku'];
			if(empty($sku)) {
				$sku = "No product SKU found.";
			}
			
			$crawled_product['ap'] = $price_floor;
			$crawled_product['t'] = $item['product.name'];
			$crawled_product['l'] = $item['product.url'];
			$crawled_product['ar'] = $api;
			$crawled_product['il'] = !empty($item['product.image_url']) ? $item['product.image_url'] : '';
			
			// price offered
			$crawled_product['mpo'] = $this->parseDollarAmount( $item['product.price_listed'] );
			if(empty($crawled_product['mpo'])){
				log_message('error', 'Failed parse '.print_r($item,true));
				continue;
			}
			
			if(!empty($item['product.shipping'])) {
				$crawled_product['msp'] = $this->parseDollarAmount( $item['product.shipping'] );
			}
			if(!empty($item['seller.logo'])) {
				$crawled_product['mil'] = $item['seller.logo'];
			}
			
			// TODO: make the seller_id or merchant_id into mandatory data.  Merchant name changes all the time.
			// TODO: de-duplicate merchant table
			$seller_name = !empty($item['seller.name'])? trim($item['seller.name']) : "No name found.";
			$sellerUrl = trim($item['seller.aboutus'] );
			$seller_id = (!empty($item['seller.seller_id']))? $item['seller.seller_id'] : '';
			$real_name = $seller_name;
			$merchant = $this->getMerchantNameForKey($seller_name, $upc, $api, $real_name, $seller_id, $sellerUrl);
			$merchant_name = $merchant['merchant_name'];
			if (empty($merchant_name) or strlen($merchant_name)<2 ){
				log_message('error', "Missing merchant_name ".print_r($item,true));
				continue;
			}
			$crowlMerchantNameID = $merchant['id'];
			
			// TODO: analyze this.  what's it used for?  why is it updated for every item?
			$stQuery = "UPDATE cron_log set google_count=(google_count+1), last_UPC='{$upc}' WHERE id= '{$cron_log_id}' ";
			$this->myDB->simpleQuery($stQuery);

			// TODO: change the hashkey to use merchant_id#upc#storeId
			$hashKey = $merchant_name . '#' . $upc;
			$crawled_product['um']  = $hashKey;
			$crawled_product['dt']  = time();
			$crawled_product['pid'] = $this->Products->get_product_id_from_upc($upc, $store_id);
			$crawled_product['upc'] = $upc;
			$crawled_product['mid'] = $crowlMerchantNameID;
			$crawled_product['mu']  = $sellerUrl;
			$crawled_product['rp']  = getPricePoint($upc, $store_id, 'retail_price');
			$crawled_product['wp']  = getPricePoint($upc, $store_id, 'wholesale_price');

			$this->stats[$api]['price_found']++;
			$this->insertUPCMerchant($merchant_name, $upc, $api, $crawled_product['mpo']);
			
			// Should we ignore violations that are less than a dollar wrong?
			$dataVio = $this->Products->get_products_by_floor($upc, $crawled_product['mpo'], $store_id);
			//echo "dataVio: Products->get_products_by_floor(upc_code: $upc, violatedPrice: {$crawled_product['mpo']}, store_id:$store_id)\n";
			//print_r($dataVio);
			//exit;
			// Screen Shots
			$ss = '';
			$llr = $this->ProductsTrends->get_latest_by_hashkey($hashKey)->result_object();
			$lastCrawlPrice = (float)0;
			if (!empty($llr->um)) {
				$lastCrawlPrice = isset($llr->mpo) ? (float)$llr->mpo: (float)0; // Merchant Price Offered
				$ss = $llr->ss;
			}
			
			$violationFlag = false;
			if ($dataVio) //ie, the $price_floor is greater than $crowled_products['mpo'])
			{
				$violationFlag = true;
				$this->updateViolationSummary($dataVio);
				
				// take screenshot only if unique today, price has changed, and product is in violation 
				$new_ss = date('Ymd', $crawled_product['dt']) . '/' . md5($crawled_product['l']) . '.png';
				if ( ($ss != $new_ss) && ($lastCrawlPrice != $crawled_product['mpo']) ) {
					$ss = $new_ss;
					if (!isset($uniquArr[$ss])) {
						$uniquArr[$ss] = true;
						$this->addScreenShot($crawled_product['l'], $ss, false, $crawled_product['mpo']);
					}
				}
			}
			$crawled_product['ss'] = $ss;
			
			// store the nosql data
			$this->ProductsTrends->insertData($crawled_product, $api);
			
			// update price violators for all products, if MPO data exists
			$this->Violator->updatePriceViolator($crowlMerchantNameID, $upc, $violationFlag, $crawled_product['dt']);
			//echo "updatePriceViolator\(crowlMerchantNameID:$crowlMerchantNameID, upc_code:$upc, ".(int)$violationFlag.", dt:{$crawled_product['dt']} \)\n";
			//exit;
			
			// do not set active=1 if existing row has active=0, the customer does not want to crawl them
			$where = "crowl_merchant_name_id = $crowlMerchantNameID and store_id = $store_id";
			$rrr = $this->myDB->getByTableName($this->_table_violator_notifications , $where);
			if(empty($rrr[0]) or $rrr[0]['active']==1){
				// assure violation notification record exists and is active
				$arrViolation = array(
					'store_id' => $store_id,
					'crowl_merchant_name_id' => $crowlMerchantNameID,
					'active' => 1
				);
				$this->myDB->replace($this->_table_violator_notifications, $arrViolation, $where);
			}
			//echo "\ncrawled_products: \n";
			//print_r($crawled_product); 
			//exit;
			
		} //end iterator on returned retailer_data
		$this->updateUPCFlag($upc, $api, '1');
		$this->myDB->close();
	}

	




	public function pricegrabber_lookup($upcs, $floor_price, $cron_log_id, $store_id) {
		foreach ($upcs as $key => $upc) {
			$price_floor = $floor_price[$key]['floor_price'];
			$product_code = $upc;
			$crawled_product = array();
			$data = $this->fetchDataFromPriceGrabberApi($product_code);

			if (isset($data) && count($data) > 0) {
				$avg_price = 0;
				$n = 0;
				foreach ($data as $item) {
					$this->stats['pricegrabber']['data_found']++;
					$price = trim($item['price']);
					$author = htmlentities(trim(preg_replace('/[^0-9 a-zA-Z]/', '', $item['manu'])));
					$hashKey = $author . '#' . $product_code;
					$crawled_product['um'] = $hashKey;
					$crawled_product['dt'] = time();
					$crawled_product['ap'] = $price_floor;
					//$crowled_products['l']  = time();
					$crawled_product['t'] = $item['name'];
					$crawled_product['mu'] = $item['url'];
					$crawled_product['ar'] = 'pricegrabber';
					$crawled_product['l'] = $item['url'];

					// merchant logo
					$crawled_product['mil'] = $item['logo'];
					// image link
					$crawled_product['il'] = isset($item['productImage']) ? $item['productImage'] : '';
					// shipping price
					$crawled_product['msp'] = $item['shippingPrice'];
					// price offered
					$crawled_product['mpo'] = $price;

					if ($crawled_product['mpo']) {
						$this->stats['pricegrabber']['price_found']++;
						$avg_price = $this->_new_avg($avg_price, $crawled_product['mpo']);
						$insert_response = $this->amzdb->insertData($this->_dynamo_products_trends, $crawled_product, 'pricegrabber');
						if (isset($insert_response->status) && $insert_response->status == 200) {
							$this->insertUPCMerchant($author, $product_code, 'pricegrabber', $crawled_product['mpo'], $item['manu']);
						}

						// get the corresponding crowlMerchantName row
						$crowlMerchantName = $this->Crawl_data->crowlMerchantBySellerID($item['id']);
						if (empty($crowlMerchantName)) {
							log_message('error', __FILE__ . ' Crowl_m::pricegrabber_lookup() Line ' . __LINE__ .
								': crowl_merchant_name record not found for seller ID '
								. $item['id']
							);
						}
						$crowlMerchantNameID = isset($crowlMerchantName->id) ? $crowlMerchantName->id : 0;
						$violatedPrice = $crawled_product['mpo'];
						$dataVio = $this->Products->get_products_by_floor($product_code, $violatedPrice);
						$dataVio = array();
						
						if (count($dataVio) > 0) {//$price_floor > $crowled_products['mpo'])
							$violation['um'] = $hashKey;
							$violation['dt'] = time();
							$violation['ss'] = $violation['dt'] . '' . $n . '.png';
							$this->updateViolationSummary($dataVio);
							$this->Violator->updatePriceViolator($crowlMerchantNameID, $product_code, 1, $crawled_product['dt']);
							$this->addScreenShot($crawled_product['l'], $violation['ss']);
							$this->amzdb->insertData($this->_dynamo_violations, $violation, 'pricegrabber');
						}
						else {
							$this->Violator->updatePriceViolator($crowlMerchantNameID, $product_code, 0, $crawled_product['dt']);
						}
					}
					$n++;
				}
			}
		}//end of main loop
	}

	public function fetchcurl($product_code, $fromEngine, $proxy_ip = '', $proxy_port = '') {
		$url = "https://www.googleapis.com/shopping/search/v1/public/products?key=AIzaSyB4o9hRsr_TzZJK9AAP4QHIvvYqQTHWc4o&country=US&q=" . $product_code . "&rankBy=price:descending&startIndex=1&maxResults=200";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);

		if ($proxy_ip != '' && $proxy_port != '') {
			curl_setopt($ch, CURLOPT_PROXY, $proxy_ip);
			curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
		}
		// curl_setopt($ch, CURLOPT_POST, 0);
		// curl_setopt($ch, CURLOPT_POSTFIELDS, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);   // DO NOT RETURN HTTP HEADERS
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // RETURN THE CONTENTS OF THE CALL
		$Rec_Data = curl_exec($ch);
		//$info = curl_getinfo($ch);
		//var_dump($info);
		curl_close($ch);
		$result[0] = ($Rec_Data);
		$final_data = $result[0];
		return $final_data;
	}

	public function currencyConverter($amount, $from, $to = "USD") {
		$string = "1" . $from . "=?" . $to;
		//Call Google API
		$google_url = "http://www.google.com/ig/calculator?hl=en&q=" . $string;
		//Get and Store API results into an array
		$result = explode('"', file_get_contents($google_url));
		$converted_amount = explode(' ', $result[3]);
		$conversion = preg_replace('/[\x00-\x08\x0B-\x1F]/', '', $converted_amount[0]) * $amount;
		return number_format($conversion, 2);
	}

	public function getContent($url) {
		$url = $url;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$html = curl_exec($curl);
		curl_close($curl);
		return $this->getMetaTitle($html);
	}

	public function getMetaTitle($content) {
		$pattern = "|<[\s]*title[\s]*>([^<]+)<[\s]*/[\s]*title[\s]*>|Ui";
		if (preg_match($pattern, $content, $match)) {
			return str_replace('Amazon.com At a Glance: ', '', $match[1]);
		}
		else {
			return false;
		}
	}
	
	/**
	 * Get row from crowl_merchant_name_new table.
	 * 
	 * @author Christophe
	 * @param int $merchant_id
	 * @return array
	 */
	public function get_merchant_from_id($merchant_id)
	{
	    $merchant_id = intval($merchant_id);
	    
	    $this->db->select('*');
	    $this->db->from('crowl_merchant_name_new');
	    $this->db->where('id', $merchant_id);
	     
	    $query = $this->db->get();
	     
	    return $query->row_array();	    
	}
	
	// TODO: refactor this into / and use instead, getMerchantNameForKey()
	public function getMerchantNameForKey($merchant_name, $upc, $marketplace = '', $real_name = '', $seller_id = '', $merchant_url = '')
	{
		$ret = '';
		$merchant_url = trim($merchant_url);
		$merchant_name = preg_replace('/[^a-zA-Z0-9\s]/', '', $merchant_name);
		$marketplace = !empty($marketplace) ? $marketplace : 'amazon';
		
		// get merchant name
		$arrMerchant = array(
			'merchant_name' => $merchant_name,
			'original_name' => $real_name, // TODO: parse original name from amazon seller
			'marketplace' 	=> $marketplace,
			'seller_id' 	=> $seller_id,
			'merchant_url'	=> $merchant_url,
			'created' 		=> date('Y-m-d H:i:s')
		);
		$where = "merchant_name = '{$merchant_name}' "; // unreliable
		if(!empty($seller_id)){ 
			$where = " seller_id LIKE '{$seller_id}' "; // reliable
		}
		$where .= " and marketplace = '{$marketplace}' ORDER BY created DESC LIMIT 1"; //  - watch for duplicates!
		$row = $this->myDB->replaceThenSelect($this->_table_crowl_merchant_name, $arrMerchant, $where);
		$mnid = $row[0]['id'];
		$ret = $row[0]['merchant_name'];
		
		// assure product list record
		$arrProductList = array(
			'upc' => $upc,
			'last_date' => time(),
			'marketplace' => $marketplace,
			'merchant_name_id' => $mnid
		);
		$where = " merchant_name_id = '$mnid' and upc = '$upc' and marketplace = '$marketplace' ";
		$this->myDB->replace($this->_table_crowl_product_list, $arrProductList, $where);

		//return $ret;
		return $row[0];
		
	}

	public function getMerchantNameForKeyOld($merchant_name, $upc, $marketplace = '', $real_name = '', $seller_id = '', $merchant_url = '') {

		if (trim($merchant_name) == '' || $seller_id == '') {
			email_alertToTeam('Seller ID', 'Seller ID not found against UPC No(' . $merchant_name . ',' . $seller_id . ').: ' . $upc . ' in Marketplace: ' . $marketplace, 1);
			return false;
		}

		$ret = '';
		if ($marketplace == 'google') {
			$qStr = "select * from  " . $this->_table_crowl_merchant_name . " where merchant_name = '$merchant_name' and marketplace = '$marketplace'";
		}
		else {
			$qStr = "select * from  " . $this->_table_crowl_merchant_name . " where seller_id = '$seller_id' and marketplace = '$marketplace'";
		}
		$row = $this->myDB->select($qStr);

		if (is_array($row) && count($row) > 0) {
			$pid = $row[0]['id'];
			$ret = $row[0]['merchant_name'];
			$update = array(
				'seller_id' => $seller_id,
			);
			if (!empty($merchant_url)) {
				$update['merchant_url'] = $merchant_url;
				$this->myDB
				     ->where('id', $pid)
				     ->update($this->_table_crowl_merchant_name, $update);
			}
		}
		else {
			$insert = array(
				'merchant_name' => $merchant_name,
				'original_name' => $real_name,
				'seller_id' => $seller_id,
				'created' => date('Y-m-d H:i:s'),
				'marketplace' => $marketplace
			);
			
			if (!empty($merchant_url)) {
				$update['merchant_url'] = $merchant_url;
				$this->myDB->insert($this->_table_crowl_merchant_name, $insert);
				$pid = $this->myDB->insertid();
				$ret = $merchant_name;
			}
		}
		
		$qStr = "select * from `" . $this->_table_crowl_product_list . "`
                     where
                        merchant_name_id = '$pid' and
                        upc = '$upc' and
                        marketplace = '$marketplace'
                     limit 1";
		$rs = $this->myDB->select($qStr);

		if (is_array($rs) && count($rs) > 0) {
			$id = $rs[0]['id'];
			$qStr = "update " . $this->_table_crowl_product_list . " set last_date  = " . time() . " where id = " . $id;
		}
		else {
			$qStr = "insert into " . $this->_table_crowl_product_list . "
                       set
                          upc = '$upc',
                          last_date  = " . time() . ",
                          marketplace  = '$marketplace',
                          merchant_name_id = '$pid'
                ";
		}
		$this->myDB->simpleQuery($qStr);
		return $ret;
	}

	public function fetchDataFromShoppingApi($upc) {
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '400M');
		if (!class_exists('simple_html_dom_node')) {
			require_once dirname(BASEPATH) . '/system/application/libraries/simple_html_dom.php';
		}
		
		$url = 'http://www.shopping.com/products?KW=' . $upc . '&pg_lyt=grid';

		$html = new simple_html_dom();
		$html->load_file($url, true);
		$items = array();
		
		if (!$html->find('div[class=gridBox]', 0)) {
			$html->clear();
			log_message('error', 'Shopping.com: UPC lookup failure (gridBox Div not found) - UPC = ' . $upc);
			return $items;
		}

		foreach ($html->find('div[class=gridBox]') as $div) {
			$mystring = $div->getAttribute('class');
			$findme = 'deal';
			$pos = strpos($mystring, $findme);

			if ($pos === false) {
				$url = 'http://www.shopping.com' . str_replace('/info', '/prices', $div->find('.gridItemBtm .productName', 0)->getAttribute('href'));
				$items = $this->fetchDataFromInnerPageShoppingApi($items, $url);
				continue;
			}

			$data = array();
			if ($div->find('div[class=listsGroup] input')) {
				foreach ($div->find('div[class=listsGroup] input') as $input) {
					$data[$input->name] = $input->value;
				}
			}
			else {
				//email_alertToTeam('fetchDataFromShoppingApi',' input in listsGroup is not found');
			}

			if ($div->find('div[class=taxShippingArea]', 0)) {
				$data['shipPrice'] = $this->getNumber($div->find('div[class=taxShippingArea]', 0)->plaintext);
			}
			else {
				$data['shipPrice'] = 0;
			}
			
			if ($div->find('div[class=singleDealPageUrl]', 0)) {
				$data['url'] = 'http://www.shopping.com' . $div->find('div[class=singleDealPageUrl]', 0)->plaintext;
			}
			/* else
			  {
			  email_alertToTeam('fetchDataFromShoppingApi',' singleDealPageUrl DIV is not found');
			  } */

			if ($div->find('div[class=descTxt]', 0)) {
				$data['description'] = str_replace('<a href="#" class="readMore">Less</a>', '', $div->find('div[class=descTxt]', 0)->innertext);
			}
			/* else
			  {
			  email_alertToTeam('fetchDataFromShoppingApi',' descTxt DIV is not found');
			  } */

			if ($div->find('div[class=merchantLogo] img', 0)) {
				$data['logo'] = (isset($div->find('div[class=merchantLogo] img', 0)->src)) ? $div->find('div[class=merchantLogo] img', 0)->src : '';
				//$data['logo'] = $div->find('div[class=merchantLogo] img', 0)->src;
			}
			/* else
			  {
			  $data['logo'] = '';
			  email_alertToTeam('fetchDataFromShoppingApi',' merchantLogo DIV is not found');
			  } */
			/**
			 * For Merchant URL
			 */
			if ($topDiv = $div->find('div[class=gridItemTop]', 0)) {
				if ($a = $topDiv->find('a', 0)) {
					$href = $a->href;
					$queryString = getQueryStringFromUrl($href);
					$data['url'] = isset($queryString['url']) ? $queryString['url'] : '';
					$data['sellerId'] = isset($queryString['MerchantID']) ? $queryString['MerchantID'] : '';
				}
			}
			$items[] = $this->formatShoppingApiDataArray($data);
		}
		$html->clear();
		return $items;
	}

	public function fetchDataFromInnerPageShoppingApi($items, $url = '') {
		$html = new simple_html_dom();
		$html->load_file($url);
		echo $contant = $html->find('div[class=contentBox]', 0);

		if (!$contant) {
			$html->clear();
			log_message('error', 'Shopping.com lookup processing error: contentBox DIV not found (' . $url . ')');
			return $items;
		}

		$productImage = '';

		if ($tmp = $html->find('#productImageBox', 0)) {
			if ($tmp_img = $tmp->find('img', 0)) {
				$productImage = $tmp_img->getAttribute('src');
			}
		}
		/* else
		  {
		  email_alertToTeam('fetchDataFromInnerPageShoppingApi',$url.'<br> productImageBox ID is not found');
		  } */
		$tilte = 'No Title';

		if ($html->find('.productTitle', 0)) {
			$tilte = $html->find('.productTitle', 0)->plaintext;
		}
		/* else
		  {
		  email_alertToTeam('fetchDataFromInnerPageShoppingApi',$url.'<br> productTitle Class is not found');
		  } */

		if ($contant->find('div[class=offerItem]')) {
			foreach ($contant->find('div[class=offerItem]') as $div) {
				$data = array('imageUrl'	 => $productImage, 'itemTitle'	 => $tilte);

				if ($div->find('div[class=shipInfo]', 0)) {
					$data['shipPrice'] = $this->getNumber($div->find('div[class=shipInfo]', 0)->plaintext);
				}
				/* else
				  {
				  email_alertToTeam('fetchDataFromInnerPageShoppingApi',$url.'<br> DIV with shipInfo Class is not found');
				  } */

				if ($div->find('div[class=offerDetails]', 0)) {
					if ($div->find('div[class=offerDetails]', 0)->find('p[class=emphasis]', 0)) {
						$data['itemTitle'] = $div->find('div[class=offerDetails]', 0)->find('p[class=emphasis]', 0)->plaintext;
					}
					/* else
					  {
					  email_alertToTeam('fetchDataFromInnerPageShoppingApi',$url.'<br> P with emphasis Class in DIV with offerDetails Class is not found');
					  } */
				}
				/* else
				  {
				  email_alertToTeam('fetchDataFromInnerPageShoppingApi',$url.'<br> DIV with offerDetails Class is not found');
				  } */

				if ($div->find('div[class=offerDetails]', 0)) {
					if ($div->find('div[class=offerDetails]', 0)->find('p[class=desc]', 0)) {
						$data['description'] = $div->find('div[class=offerDetails]', 0)->find('p[class=desc]', 0)->plaintext;
					}
					/* else
					  {
					  email_alertToTeam('fetchDataFromInnerPageShoppingApi',$url.'<br> P with desc Class in DIV with offerDetails Class is not found');
					  } */
				}
				/* else
				  {
				  email_alertToTeam('fetchDataFromInnerPageShoppingApi',$url.'<br> DIV with offerDetails Class is not found');
				  } */

				if ($priceInfo = $div->find('div[class=priceInfo]', 0)) {
					if ($priceInfo->find('.orgPrice', 0)) {
						$data['itemPrice'] = $this->getNumber($priceInfo->find('.orgPrice', 0)->plaintext . ' ');
					}
					/* else
					  {
					  email_alertToTeam('fetchDataFromInnerPageShoppingApi',$url.'<br>orgPrice Class is not found in DIV with priceInfo Class');
					  } */

					if ($priceInfo->find('.toSalePrice', 0)) {
						$data['itemPrice'] = $this->getNumber($priceInfo->find('.toSalePrice', 0)->plaintext . ' ');
					}
					/* else
					  {
					  email_alertToTeam('fetchDataFromInnerPageShoppingApi',$url.'<br>toSalePrice Class is not found in DIV with priceInfo Class');
					  } */
				}
				/* else
				  {
				  email_alertToTeam('fetchDataFromInnerPageShoppingApi',$url.'<br> DIV with priceInfo Class is not found');
				  } */
				if ($merchInfo = $div->find('div[class=merchInfo] a', 0)) {
					$img = $merchInfo->find('img', 0);
					if ($img) {

						$data['logo'] = $img->getAttribute('src');
						$data['merchantName'] = $img->getAttribute('title');
					}
					else {
						$data['merchantName'] = $merchInfo->find('b[class=merchName]', 0)->plaintext;
					}
					$data['url'] = '';
				}
				/* else
				  {
				  email_alertToTeam('fetchDataFromInnerPageShoppingApi',$url.'<br> DIV with merchInfo Class is not found');
				  } */

				/**
				 * For merchant ID
				 *
				 */
				if ($dealDiv = $div->find('div[class=dealClick]', 0)) {
					if ($a = $dealDiv->find('a', 0))
						; {
						$href = $a->href;
						$queryString = getQueryStringFromUrl($href);
						$data['url'] = $queryString['url'];
						$data['sellerId'] = $queryString['MerchantID'];
					}
				}
				$items[] = $this->formatShoppingApiDataArray($data);
			}
		}
		else {
			log_message('error', 'Shopping.com lookup processing error: offerItem Class not found (' . $url . ')');
		}
		$html->clear();
		return $items;
	}

	public function getNumber($str) {
		preg_match_all('/&#36;(.*?) /si', $str, $matches);
		return isset($matches[1][0]) ? $matches[1][0] : 0;
	}

	public function formatShoppingApiDataArray($data) {
		$array = array(
			'name'			 => isset($data['itemTitle']) ? $data['itemTitle'] : '',
			'desc'			 => isset($data['description']) ? $data['description'] : '',
			'manu'			 => isset($data['merchantName']) ? $data['merchantName'] : '',
			'url'			 => isset($data['url']) ? $data['url'] : '',
			'productImage'	 => isset($data['imageUrl']) ? $data['imageUrl'] : '',
			'basePrice'		 => isset($data['itemPrice']) ? $data['itemPrice'] : 0,
			'shippingPrice'	 => isset($data['shipPrice']) ? $data['shipPrice'] : 0,
			'price'			 => isset($data['itemPrice']) ? $data['itemPrice'] : 0,
			'logo'			 => isset($data['logo']) ? $data['logo'] : 0,
			'id'			 => isset($data['sellerId']) ? $data['sellerId'] : 0,
		);

		return $array;
	}

	public function fetchDataFromPriceGrabberApi($upc) {
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '400M');
		require_once dirname(BASEPATH) . '/system/application/libraries/simple_html_dom.php';
		$debug = false;
		$url = 'http://www.pricegrabber.com/search_request.php?form_keyword=' . $upc . '&some_id=&id_type=&requestParams=Tjs%3D&vendorIds=YTowOnt9&st=query&sv=findit_top&kw_suggest=0&topcat_menu=6&zip_code=54001';
		if ($debug) {
			echo '<br />' . $url . '<br />';
		}
		$html = new simple_html_dom();
		$html->load_file($url);

		if ($html->find('div[class=product_description]', 0)) {
			$name = $html->find('div[class=product_description]', 0)->find('h1', 0)->plaintext;
		}
		else {
			$html->clear();
			return array();
		}

		$desc = '';
		if ($html->find('p[id=product_details_description]', 0)) {
			$desc = $html->find('p[id=product_details_description]', 0)->plaintext;
		}

		$product_image = '';
		if ($html->find('div[class=product_img]', 0)) {
			$product_image = $html->find('div[class=product_img]', 0)->find('img', 0)->getAttribute('src');
		}

		$items = array();

		if ($html->find('table[class=pricing_tbl]', 0)) {
			$price_table = $html->find('table[class=pricing_tbl]', 0);
		}
		else {
			$html->clear();
			return array();
		}

		if ($debug) {
			echo $price_table;
		}
		
		foreach ($price_table->find('tr') as $tr) {
			$mystring = $tr->getAttribute('class');
			$findme = 'section';
			$pos = strpos($mystring, $findme);

			if ($pos !== false) {
				continue;
			}

			if ('noseller' == $tr->find('td', 0)->getAttribute('class')) {
				break;
			}

			$bottom_price = $this->getPriceForPriceGrabber($tr->find('td', 1)->find('div[class=deftip]', 0)->plaintext);
			$price = $this->getPriceForPriceGrabber($tr->find('td', 2)->plaintext);
			$shopping_price = ($bottom_price - $price);
			$seller_link = $tr->find('td', 4)->find('a', 0);
			$href = $seller_link->getAttribute('href');
			$img = $seller_link->find('img', 0);

			if ($img) {
				$menu = $img->getAttribute('alt');
				$logo = $img->getAttribute('src');
			}
			else {
				$menu = $seller_link->plaintext;
				$logo = '';
			}
			$data = array(
				'name'			 => $name,
				'desc'			 => $desc,
				'manu'			 => $menu,
				'url'			 => $href,
				'productImage'	 => $product_image,
				'basePrice'		 => trim($price),
				'shippingPrice'	 => trim($shopping_price),
				'price'			 => trim($price),
				'logo'			 => $logo,
			);

			$items[] = $data;

			if ($debug) {
				echo '<br />';
				echo '--------------------------';
				echo '<br />';

				echo '<pre>';
				print_r($data);
				echo '</pre>';
				echo '<br />';

				echo 'Name: ' . $name;
				echo '<br />';

				echo 'Desc: ' . $desc;
				echo '<br />';

				echo 'Bottom Price: ' . $bottom_price;
				echo '<br />';

				echo 'Price: ' . $price;
				echo '<br />';

				echo 'Shipping Price: ' . $shopping_price;
				echo '<br />';

				echo 'Seller Name: ' . $menu;
				echo '<br />';
				echo 'Seller Image: ' . $logo;
				echo '<br />';

				echo 'Store Link: ' . $href;
				echo '<br />';
				echo '--------------------------';
				echo '<br />';
			}
		}
		$html->clear();
		return $items;
	}

	public function getPriceForPriceGrabber($str) {
		return str_replace('$', '', $str);
	}

	/**
	 * convert xml string to php array - useful to get a serializable value
	 *
	 * @param string $xmlstr
	 * @return array
	 * @author Adrien aka Gaarf
	 */
	public function xmlstr_to_array($xmlstr) {
		$doc = new DOMDocument();
		$doc->loadXML($xmlstr);
		return $this->domnode_to_array($doc->documentElement);
	}

	public function domnode_to_array($node) {
		$output = array();
		switch ($node->nodeType) {
			case XML_CDATA_SECTION_NODE:
			case XML_TEXT_NODE:
				$output = trim($node->textContent);
				break;
			case XML_ELEMENT_NODE:
				for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
					$child = $node->childNodes->item($i);
					$v = $this->domnode_to_array($child);
					if (isset($child->tagName)) {
						$t = $child->tagName;
						if (!isset($output[$t])) {
							$output[$t] = array();
						}
						$output[$t][] = $v;
					}
					elseif ($v) {
						$output = (string)$v;
					}
				}
				if (is_array($output)) {
					if ($node->attributes->length) {
						$a = array();
						foreach ($node->attributes as $attrName => $attrNode) {
							$a[$attrName] = (string)$attrNode->value;
						}
						$output['@attributes'] = $a;
					}
					foreach ($output as $t => $v) {

						if (is_array($v) && count($v) == 1 && $t != '@attributes') {
							$output[$t] = $v[0];
						}
					}
				}
				break;
		}
		return $output;
	}

	// TODO: move into screen_shots_model
	public function addScreenShot($url, $name, $cli = false, $price = '0') {
		if (trim($url) != '' && trim($name) != '') {
			if (!$cli) {
				list($path, $filename) = explode('/', $name);
				$name = empty($filename) ? $name : $filename;
				$data = array(
					'url'	 => $url,
					'name'	 => $name
				);
				$this->db->insert('screen_shots', $data);
			}
			else {
				$url = urlencode($url);
			}
		}
	}

	public function updateUPCFlag($upc = '', $type = '', $action = '1') {
		if (!empty($upc)) {
			$this->db
				 ->where('upc_code', $upc)
				 ->update($this->_table_products, array('is_processed' => $action));
		}
	}

	protected function _new_avg($avg, $price) {
		if ($avg < 0) {
			$avg = 0;
		}
		if ($avg == 0) {
			$avg = $price;
		}
		elseif ($price < ($avg * $avg)) {
			$avg = ($avg + $price) / 2;
		}
		return $avg;
	}

	public function insertUPCMerchant($merchant_name, $upc, $marketplace = '', $price = 0, $real_name = '', $seller_id = 0) {
		//Code for daily price aggregation
		$date = date('Y-m-d');
		$qStr = "select * from daily_price_average where upc = '$upc' and marketplace = '$marketplace' and `date` = '$date'";
		$query = $this->myDB->select($qStr);
		$price = str_replace(',', '', $price);
		if ($query) {
			$qStr = "update daily_price_average set seller_total = (seller_total + 1), price_total = (price_total + " . (float)$price . ") where upc = '$upc' and marketplace = '$marketplace' and `date` = '$date'";
echo "$qStr\n"; 
			$this->myDB->simpleQuery($qStr);
		}
		else {
			$where = array('upc'			 => $upc ,
						   'marketplace'	 => $marketplace ,
						   'date'			 => $date ,
						   'price_total'	 => (float)$price ,
						   'seller_total'	 => '1'
						);
			$this->myDB->insert('daily_price_average', $where);
		}
	}

	public function updateViolationSummary($dataVio) {
		if (is_array($dataVio) && count($dataVio) > 0) {
			foreach ($dataVio as $data) {
				$query = "UPDATE {$this->_table_store} set last_violation_count=(last_violation_count+1) WHERE id='{$data['store_id']}' ";
echo "$query\n";
				$this->myDB->simpleQuery($query);
				
				$isProductAlreadyViolated = $this->db
					->select('id')
					->mysql_cache()
					->where('is_violated', 1)
					->where('id', $data['id'])
					->get($this->_table_products);
				if ($isProductAlreadyViolated->num_rows() == 0) {
					$queryUp = "UPDATE {$this->_table_products} SET is_violated='1' where id='{$data['id']}' ";
					$this->myDB->simpleQuery($queryUp);
				}
			}
		}
	}

	public function parseDollarAmount($str) {
		$amt = explode('$', $str);
		$amt = isset($amt[1]) ? $amt[1] : $amt[0];
		return (float)$amt;
	}

	/**
	 * @deprecated
	 */
	//public function google_lookup($upcs, $floor_price, $cron_log_id, $store_id) {
	//	//echo 'in here';
	//	foreach($upcs as $key => $upc) {
	//		$upcInfo = $this->sticky_api->searchUpc($upc, "http://www.google.com");
	//		//var_dump($upcInfo);
	//	}
	//	
	//	return false;
	//	
	//	foreach ($upcs as $key => $upc) {
	//		$price_floor = $floor_price[$key]['floor_price'];
	//		$product_code = $upc;
	//		$google_data = json_decode($this->fetchcurl($product_code, 'fromGoogle'));
	//
	//		if (isset($google_data->items) && count($google_data->items) > 0) {
	//			$this->myDB = new mydb();
	//			$avg_price = 0;
	//			$n = 0;
	//			$violationFlag = false;
	//			$uniquArr = array();
	//			
	//			foreach ($google_data->items as $item) {
	//				$this->stats['google']['data_found']++;
	//				$crawled_product = array();
	//				$violation = array();
	//				$price = trim($item->product->inventories[0]->price);
	//				$author = htmlentities(trim(preg_replace('/[^0-9 a-zA-Z]/', '', $item->product->author->name)));
	//				$author = $this->getMerchantNameForKey($author, $product_code, 'google', $item->product->author->name, $item->product->author->accountId, extractDomainByURL($item->product->link));
	//				if (!$author) {
	//					continue;
	//				}
	//				$stQuery = "UPDATE cron_log set google_count=(google_count+1), last_UPC='{$upc}' WHERE id= '{$cron_log_id}' ";
	//				$this->myDB->simpleQuery($stQuery);
	//				$hashKey = $author . '#' . $product_code;
	//				$crawled_product['um'] = $hashKey;
	//				$crawled_product['dt'] = time();
	//				$crawled_product['ap'] = $price_floor;
	//				//$crowled_products['l']  = time();
	//				$crawled_product['t'] = $item->product->title;
	//				$crawled_product['mu'] = $item->product->link;
	//				$crawled_product['ar'] = 'google';
	//				$crawled_product['l'] = $item->product->link;
	//				// merchant logo
	//				$crawled_product['mil'] = '';
	//				// image link
	//				$crawled_product['il'] = isset($item->product->images[0]->link) ? $item->product->images[0]->link : '';
	//				// merchant price offered
	//				$crawled_product['mpo'] = $price;
	//				// merchant shipping price
	//				if (isset($item->product->shipping)) {
	//					$crawled_product['msp'] = $item->product->shipping;
	//				}
	//				else {
	//					$crawled_product['msp'] = isset($item->product->inventories[0]->shipping) ? $item->product->inventories[0]->shipping : 0;
	//				}
	//
	//				if ($crawled_product['mpo']) {
	//					$this->stats['google']['price_found']++;
	//					$avg_price = $this->_new_avg($avg_price, $crawled_product['mpo']);
	//
	//					// Get the price from the last crawl
	//					$dynamo = new AmazonDynamoDB();
	//					$lastRecordResponse = $dynamo->query(array(
	//						'TableName'		 => $this->_dynamo_products_trends,
	//						'HashKeyValue'	 => array(AmazonDynamoDB::TYPE_STRING	 => $crawled_product['um']),
	//						// optional parameters
	//						'ConsistentRead'			 => true,
	//						'Limit'						 => 1,
	//						'ScanIndexForward'			 => false
	//						));
	//
	//					$insert_response = $this->amzdb->insertData($this->_dynamo_products_trends, $crawled_product, 'google');
	//					if (isset($insert_response->status) && $insert_response->status == 200) {
	//						$this->insertUPCMerchant($author, $product_code, 'google', $crawled_product['mpo'], $item->product->author->name, $item->product->author->accountId);
	//					}
	//
	//					// get the corresponding crowlMerchantName row
	//					$crowlMerchantName = $this->Crawl_data->crowlMerchantBySellerID($item->product->author->accountId);
	//					if (empty($crowlMerchantName)) {
	//						log_message('error', __FILE__ . ' Crowl_m::google_lookup() Line ' . __LINE__ .
	//							': crowl_merchant_name record not found for seller ID '
	//							. $item->product->author->accountId
	//						);
	//					}
	//					$crowlMerchantNameID = isset($crowlMerchantName->id) ? $crowlMerchantName->id : 0;
	//
	//					$violatedPrice = (float)$crawled_product['mpo'];
	//					$dataVio = $this->Products->get_products_by_floor($product_code, $violatedPrice, $store_id);
	//					if ($dataVio) {//$price_floor > $crowled_products['mpo'])
	//						$violation['um'] = $hashKey;
	//						$violation['dt'] = $crawled_product['dt']; //time();
	//						//$violation['ss'] = $violation['dt'].'-'.$n.'.png';
	//						$violation['ss'] = date('Ymd', $violation['dt']) . '/' . md5($hashKey . $violation['dt']) . '.png';
	//						$this->updateViolationSummary($dataVio);
	//						$this->Violator->updatePriceViolator($crowlMerchantNameID, $product_code, 1, $crawled_product['dt']);
	//						$lastCrawlPrice = (float)0;
	//						$hashKey = null;
	//						$rangeKey = null;
	//						if ($lastRecordResponse->isOK()) {
	//							$mpo = isset($lastRecordResponse->body->Items->mpo->N) ? (float)$lastRecordResponse->body->Items->mpo->N : (float)0; // Merchant Price Offered
	//							$lastCrawlPrice = $mpo;
	//							$hashKey = isset($lastRecordResponse->body->Items->um->S) ? $lastRecordResponse->body->Items->um->S : null;
	//							$rangeKey = isset($lastRecordResponse->body->Items->dt->N) ? $lastRecordResponse->body->Items->dt->N : null;
	//						}
	//
	//						// Check if the price has changed
	//						if ((float)$lastCrawlPrice <> (float)$violatedPrice) {
	//							if (!isset($uniquArr[$crawled_product['l']])) {
	//								$uniquArr[$crawled_product['l']] = $violation['ss'];
	//								$this->addScreenShot($crawled_product['l'], $violation['ss'], false, $violatedPrice);
	//							}
	//							else {
	//								$violation['ss'] = $uniquArr[$crawled_product['l']];
	//							}
	//						}
	//						else {
	//							$takeNewScreenShot = true;
	//							// Get the violation screen shot
	//							if (!empty($hashKey) and !empty($rangeKey)) {
	//								$lastViolationResponse = $dynamo->query(array(
	//									'TableName'		 => $this->_dynamo_violations,
	//									'HashKeyValue'	 => array(AmazonDynamoDB::TYPE_STRING	 => (string)$hashKey),
	//									'RangeKeyCondition'			 => array(
	//										'ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
	//										'AttributeValueList' => array(
	//											array(AmazonDynamoDB::TYPE_NUMBER	 => (string)$rangeKey)
	//										)
	//									),
	//									'ConsistentRead'			 => 'true'
	//									));
	//
	//								if ($lastViolationResponse->isOK() and $lastViolationResponse->body->Count == 1) {
	//									if (isset($lastViolationResponse->body->Items->ss->S)) {
	//										if (@fopen(get_instance()->config->item('s3_cname') . 'stickyvision/violations/' . $lastViolationResponse->body->Items->ss->S, 'r')) {
	//											$violation['ss'] = $lastViolationResponse->body->Items->ss->S;
	//											$takeNewScreenShot = false;
	//										}
	//										else {
	//											$takeNewScreenShot = true;
	//										}
	//									}
	//								}
	//							}
	//							if ($takeNewScreenShot === true) {
	//								$this->addScreenShot($crawled_product['l'], $violation['ss'], false, $violatedPrice);
	//							}
	//						}
	//
	//						$this->amzdb->insertData($this->_dynamo_violations, $violation, 'google');
	//						$violationFlag = true;
	//						unset($dataVio);
	//					}
	//					else { // update price violators for products not in violation
	//						$this->Violator->updatePriceViolator($crowlMerchantNameID, $product_code, 0, $crawled_product['dt']);
	//					}
	//					$n++;
	//				}
	//				else {
	//					email_alertToTeam('google_lookup', 'Price is not found for UPC ' . $product_code);
	//				}
	//				unset($google_data, $crawled_product, $violation);
	//			}
	//			$this->myDB->close();
	//			$this->updateUPCFlag($upc, 'google', '1');
	//		}
	//		else {
	//			if (isset($google_data->error->errors[0])) {
	//				$msg = $google_data->error->errors[0]->domain . ' ' . $google_data->error->errors[0]->reason;
	//				email_alertToTeam('google_lookup', 'Google Data Items are not found for UPC ' . $product_code . ' ' . $msg, 1);
	//			}
	//			$this->updateUPCFlag($upc, 'google', '0');
	//		}
	//	}//end of main loop
	//}

	/**
	 * @deprecated
	 */
	//public function shopping_lookup($upcs, $floor_price, $cron_log_id, $store_id) {
	//	foreach ($upcs as $key => $upc) {
	//		$price_floor = $floor_price[$key]['floor_price'];
	//		$product_code = $upc;
	//		$data = $this->fetchDataFromShoppingApi($product_code);
	//		if (isset($data) && count($data) > 0) {
	//			$this->myDB = new mydb();
	//			$avg_price = 0;
	//			$n = 0;
	//			$violationFlag = false;
	//			$uniquArr = array();
	//			
	//			foreach ($data as $item) {
	//				$this->stats['shopping']['data_found']++;
	//				$crawled_product = array();
	//				$violation = array();
	//				$price = trim($item['price']);
	//				$author = htmlentities(trim(preg_replace('/[^0-9 a-zA-Z]/', '', $item['manu'])));
	//				$author = $this->getMerchantNameForKey($author, $product_code, 'shopping', $item['manu'], $item['id'], extractDomainByURL($item['url']));
	//				if (!$author)
	//					continue;
	//
	//				$stQuery = "UPDATE cron_log set google_count=(google_count+1), last_UPC='{$upc}' WHERE id= '{$cron_log_id}' ";
	//				$this->myDB->simpleQuery($stQuery);
	//				$hashKey = $author . '#' . $product_code;
	//				$crawled_product['um'] = $hashKey;
	//				$crawled_product['dt'] = time();
	//				$crawled_product['ap'] = $price_floor;
	//				
	//				//$crowled_products['l']  = time();
	//				$crawled_product['t'] = $item['name'];
	//				$crawled_product['mu'] = $item['url'];
	//				$crawled_product['ar'] = 'shopping';
	//				$crawled_product['l'] = $item['url'];
	//
	//				// merchant logo
	//				$crawled_product['mil'] = $item['logo'];
	//				// image link
	//				$crawled_product['il'] = isset($item['productImage']) ? $item['productImage'] : '';
	//				// shipping price
	//				$crawled_product['msp'] = $item['shippingPrice'];
	//				// manufacturer price offering
	//				$crawled_product['mpo'] = $price;
	//
	//				if ($crawled_product['mpo']) {
	//					$this->stats['shopping']['price_found']++;
	//					$avg_price = $this->_new_avg($avg_price, $crawled_product['mpo']);
	//
	//					// Get the price from the last crawl
	//					$dynamo = new AmazonDynamoDB();
	//					$lastRecordResponse = $dynamo->query(array(
	//						'TableName'		 => $this->_dynamo_products_trends,
	//						'HashKeyValue'	 => array(AmazonDynamoDB::TYPE_STRING	 => $crawled_product['um']),
	//						// optional parameters
	//						'ConsistentRead'			 => true,
	//						'Limit'						 => 1,
	//						'ScanIndexForward'			 => false
	//						));
	//
	//					$insert_response = $this->amzdb->insertData($this->_dynamo_products_trends, $crawled_product, 'shopping.com');
	//					if (isset($insert_response->status) && $insert_response->status == 200) {
	//						$this->insertUPCMerchant($author, $product_code, 'shopping', $crawled_product['mpo'], $item['manu'], $item['id']);
	//					}
	//
	//					// get the corresponding crowlMerchantName row
	//					$crowlMerchantName = $this->Crawl_data->crowlMerchantBySellerID($item['id']);
	//					if (empty($crowlMerchantName)) {
	//						log_message('error', __FILE__ . ' Crowl_m::shopping_lookup() Line ' . __LINE__ .
	//							': crowl_merchant_name record not found for seller ID '
	//							. $item['id']
	//						);
	//					}
	//					$crowlMerchantNameID = isset($crowlMerchantName->id) ? $crowlMerchantName->id : 0;
	//					$violatedPrice = (float)$crawled_product['mpo'];
	//					$dataVio = $this->Products->get_products_by_floor($product_code, $violatedPrice, $store_id);
	//					if ($dataVio) {//$price_floor > $crowled_products['mpo'])
	//						$violation['um'] = $hashKey;
	//						$violation['dt'] = $crawled_product['dt']; //time();
	//						//$violation['ss'] = $violation['dt'].'-'.$n.'.png';
	//						$violation['ss'] = date('Ymd', $violation['dt']) . '/' . md5($hashKey . $violation['dt']) . '.png';
	//						$this->updateViolationSummary($dataVio);
	//						$this->Violator->updatePriceViolator($crowlMerchantNameID, $product_code, 1, $crawled_product['dt']);
	//						$lastCrawlPrice = (float)0;
	//						$hashKey = null;
	//						$rangeKey = null;
	//						if ($lastRecordResponse->isOK()) {
	//							$mpo = isset($lastRecordResponse->body->Items->mpo->N) ? (float)$lastRecordResponse->body->Items->mpo->N : (float)0; // Merchant Price Offered
	//							$lastCrawlPrice = $mpo;
	//							$hashKey = isset($lastRecordResponse->body->Items->um->S) ? $lastRecordResponse->body->Items->um->S : null;
	//							$rangeKey = isset($lastRecordResponse->body->Items->dt->N) ? $lastRecordResponse->body->Items->um->N : null;
	//						}
	//
	//						// Check if the price has changed
	//						if ($lastCrawlPrice <> $violatedPrice) {
	//							if (!isset($uniquArr[$crawled_product['l']])) {
	//								$uniquArr[$crawled_product['l']] = $violation['ss'];
	//								$this->addScreenShot($crawled_product['l'], $violation['ss'], false, $violatedPrice);
	//							}
	//							else {
	//								$violation['ss'] = $uniquArr[$crawled_product['l']];
	//							}
	//						}
	//						else {
	//							$takeNewScreenShot = true;
	//							// Get the violation screen shot
	//							if (!empty($hashKey) and !empty($rangeKey)) {
	//								$lastViolationResponse = $dynamo->query(array(
	//									'TableName'		 => $this->_dynamo_violations,
	//									'HashKeyValue'	 => array(AmazonDynamoDB::TYPE_STRING	 => (string)$hashKey),
	//									'RangeKeyCondition'			 => array(
	//										'ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
	//										'AttributeValueList' => array(
	//											array(AmazonDynamoDB::TYPE_NUMBER => (string)$rangeKey)
	//										)
	//									)
	//									));
	//
	//								if ($lastViolationResponse->isOK() and $lastViolationResponse->body->Count == 1) {
	//									if (isset($lastViolationResponse->body->Items->ss->S)) {
	//										if (@fopen(get_instance()->config->item('s3_cname') . 'stickyvision/violations/' . $lastViolationResponse->body->Items->ss->S, 'r')) {
	//											$violation['ss'] = $lastViolationResponse->body->Items->ss->S;
	//											$takeNewScreenShot = false;
	//										}
	//										else {
	//											$takeNewScreenShot = true;
	//										}
	//									}
	//								}
	//							}
	//							if ($takeNewScreenShot === true) {
	//								$this->addScreenShot($crawled_product['l'], $violation['ss'], false, $violatedPrice);
	//							}
	//						}
	//						$this->amzdb->insertData($this->_dynamo_violations, $violation, 'shopping.com');
	//						$violationFlag = true;
	//					}
	//					else { // update price violators for products not in violation
	//						$this->Violator->updatePriceViolator($crowlMerchantNameID, $product_code, 0, $crawled_product['dt']);
	//					}
	//				}
	//				unset($crawled_product, $violation);
	//				$n++;
	//			}
	//			$this->myDB->close();
	//			$this->updateUPCFlag($upc, 'shopping', '1');
	//		}
	//		else {
	//			$this->updateUPCFlag($upc, 'shopping', '0');
	//		}
	//	}//end of main loop
	//}
}
