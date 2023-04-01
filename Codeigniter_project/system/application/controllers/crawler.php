<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crawler extends MY_Controller {

	public $tables;

	protected $_acl = array(
		'*' => 'cli'
	);

	protected $_cron_log_id;
	protected $_key;
	protected $_secret;
	protected $_requestor;
	protected $_crawlers;
	protected $_products = array();
	protected $_numproducts = 0;
	protected $_crawlerClass;
	protected $myDB;
	protected $_numfoundproducts = 0;
	protected $_numofferings = 0;
	protected $_currentCrawler;
	protected $_current_product_id;
	protected $_marketplaces = array();
	protected $_last_upc;
	protected $_start_time;
	protected $_end_time;

	public function __construct() {
		set_time_limit(0);
		ini_set("memory_limit", "400M");

		parent::__construct();

		$this->load->library('email');
		$this->load->model("store_m", 'Store');
		$this->load->model('crawler_log_m', 'Log');
		$this->load->model('crowl_m', 'Crowl');
		$this->load->model('crawler_m', 'Crawler');
		$this->load->model('products_m', 'Products');
		$this->load->model('violator_m', 'Violator');
		$this->load->model('crawler_error_log_m', 'CrawlerErrorLog');
		$this->load->model('cron_log_m', 'CronLog');

		//require base framework for crawling
		require_once(FCPATH . 'system/application/crawlers/base.php');

		/* Load the db adapter class */
		require_once dirname(BASEPATH) . '/system/application/libraries/mydb.php';

		$this->_crawlers = $this->Crawler->get_active_crawlers();

		$this->myDB = new mydb();
	}

	public function __deconstruct() {
		$this->myDB->close();
	}

	public function run()
	{
		$this->_start_time = date('Y-m-d H:i:s');
		$this->_cron_log_id = $this->Log->create_log($this->_start_time);

		//need to get all products
		$productCount	= $this->Products->get_all_tracked_products_count();
		$offset = 0;

		//queue up products -- so we don't crash the machine with a lot of products
		do {
			$products  		= $this->Products->get_all_tracked_products();
			$this->_products = array_merge($products, $this->_products );
			$this->_numproducts += count($products);
			$offset += 500;
		} 
		while($offset < $productCount);

		if(count($this->_crawlers) > 0) {
			foreach($this->_crawlers as $crawler) {

				$crawlerName 		= strtolower(trim($crawler['name']));
				$filePath 			= FCPATH . 'system/application/crawlers/'. $crawlerName .'.php';

				//check to see if crawler exists
				if(@file_exists($filePath) === false) {
					throw new Exception('The crawler '. $crawlerName .' file does not exist. Please add it.');
				}

				require_once($filePath);

				if(class_exists($crawlerName) === false) {
					throw new Exception('The crawler '. $crawlerName .' crawler class is not available. Please check that its defined within the file');
				}

				$this->_currentCrawler = $crawlerName;
				$this->_crawlerClass = new $crawlerName($crawler);

				foreach($this->_products as $product) {

					if ($product === end($this->_products)) 
						$this->_last_upc = $product->upc_code;
					
				
					try {
						$this->_current_product_id = $product->id;
						$this->_crawlerClass->setIdentifier($product->upc_code);
						$productData 		= $this->_crawlerClass->getProduct();
						$offers 			= $this->_crawlerClass->getAllOffers();

						$this->_numfoundproducts++;

						foreach($offers as $offer) {
							$this->_numofferings++;

							$crawled_products = array();
							if(empty($offer['merchant']))
								throw new Exception('Merchant not defined.');

							$marketplace				= $offer['marketplace'];
							if(empty($marketplace)) 
								throw new Exception('Marketplace not defined');
							$r 							= preg_split('/(?=\.[^.]+$)/', $marketplace);
							$marketplace				= strtolower($r[0]);
							$merchant 					= $this->Crowl->getMerchantNameForKey($offer['merchant'], $product->upc_code, $marketplace, $offer['merchant'], $offer['merchant'], extractDomainByURL($offer['url']));

							$this->_marketplaces[] 		= $offer['marketplace'];

							if(empty($offer['price_floor']) || !is_numeric($offer['price_floor']))
								throw new Exception('Price floor is undefined');

							$crawled_products['ap'] 	= $offer['price_floor'];

							$title = $productData->get("title");

							if(empty($title))
								throw new Exception('Title is undefined');

							$crawled_products['t'] 		= $title;
							$crawled_products['ar'] 	= $marketplace;
							$crawled_products['il'] 	= null;

							if(empty($offer['url']))
								throw new Exception('Url is undefined');

							$crawled_products['l'] 		= $offer['url'];
							$crawled_products['mu']		= $offer['merchant_url'];

							if(empty($offer['price']) || !is_numeric($offer['price']))
								throw new Exception('Price is undefined');

							$crawled_products['mpo']	= $offer['price'];

							$hashKey = $marketplace . '#' . $product->upc_code;
							$crawled_products['um'] = $hashKey;
							$crawled_products['dt'] = time();

							// Get the price from the last crawl
								$dynamo = new AmazonDynamoDB();
								$lastRecordResponse = $dynamo->query(array(
									'TableName'		 => $this->_dynamo_products_trends,
									'HashKeyValue'	 => array(AmazonDynamoDB::TYPE_STRING	 => $crawled_products['um']),
									// optional parameters
									'ConsistentRead'			 => true,
									'Limit'						 => 1,
									'ScanIndexForward'			 => false
								));

							var_dump($crawled_products);

							$insert_response = $this->amzdb->insertData($this->_dynamo_products_trends, $crawled_products, $marketplace);

							if (isset($insert_response->status) && $insert_response->status == 200) {
								$this->Crowl->insertUPCMerchant($merchant, $product->upc_code, $marketplace, $crawled_products['mpo']);
							}

							$crowlMerchantName = $this->Crawl_data->crowlMerchantByMerchantName($merchant);
							if (empty($crowlMerchantName)) {
								log_message('error', __FILE__ . ' Crowl_m::amazon_lookup() Line ' . __LINE__ .
									': crowl_merchant_name record not found for merchant '
									. $offer['merchant']
								);
							}
							$crowlMerchantNameID = isset($crowlMerchantName->id) ? $crowlMerchantName->id : 0;

							$violatedPrice = (float)$crawled_products['mpo'];
							$dataVio = $this->Products->get_products_by_floor($product->upc_code, $violatedPrice, $product->store_id);

							if ($dataVio) {//$price_floor > $crowled_products['mpo'])
								$violation['um'] = $hashKey;
								$violation['dt'] = $crawled_products['dt']; //time();
								$violation['ss'] = date('Ymd', $violation['dt']) . '/' . md5($hashKey . $violation['dt']) . '.png';
								$this->Crowl->updateViolationSummary($dataVio);
								$this->Violator->updatePriceViolator($crowlMerchantNameID, $product->upc_code, 1, $crawled_products['dt']);
	
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
								if ($lastCrawlPrice <> $violatedPrice) {
									if (!isset($uniquArr[$crawled_products['l']])) {
										$uniquArr[$crawled_products['l']] = $violation['ss'];
										$this->Crowl->addScreenShot($crawled_products['l'], $violation['ss'], false, $violatedPrice);
									}
									else {
										$violation['ss'] = $uniquArr[$crawled_products['l']];
									}
								}
								else {
									$takeNewScreenShot = false;
									// Get the violation screen shot
									if (!is_null($hashKey) and !is_null($rangeKey)) {
										$lastViolationResponse = $dynamo->query(array(
											'TableName'		 => $this->_dynamo_violations,
											'HashKeyValue'	 => array(AmazonDynamoDB::TYPE_STRING	 => (string)$hashKey),
											'RangeKeyCondition'			 => array(
												'ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
												'AttributeValueList' => array(
													array(AmazonDynamoDB::TYPE_NUMBER => (string)$rangeKey)
												)
											)
											));
	
										if ($lastViolationResponse->isOK() and $lastViolationResponse->body->Count == 1) {
											if (isset($lastViolationResponse->body->Items->ss->S)) {
												if (@fopen(get_instance()->config->item('s3_cname') . 'stickyvision/violations/' . $lastViolationResponse->body->Items->ss->S, 'r')) {
													$violation['ss'] = $lastViolationResponse->body->Items->ss->S;
													$takeNewScreenShot = false;
												}
												else {
													$takeNewScreenShot = true;
												}
											}
										}
									}
									if ($takeNewScreenShot === true) {
										$this->Crowl->addScreenShot($crawled_products['l'], $violation['ss'], false, $violatedPrice);
									}
								}
	
								$this->amzdb->insertData($this->_dynamo_violations, $violation, $marketplace);
								$violationFlag = true;
								$violation = null;
							}
							else { // update price violators for products not in violation
								$this->Violator->updatePriceViolator($crowlMerchantNameID, $product->upc_code, 0, $crawled_products['dt']);
							}
							$this->Crowl->updateUPCFlag($product->upc_code, $marketplace, '1');
						} //end offers iteration

						$this->_crawlerClass->reset();
					}
					catch (Exception $e) {
						$exceptionDetail = array(
							'crawler_log_id' => $this->_cron_log_id,
							'crawler_name' => $this->_currentCrawler,
							'message' => $e->getMessage(),
							'created_at' => date('Y-m-d H:i:s'),
							'product_id' => $this->_current_product_id
						);

						echo 'exception detected..';
						var_dump($exceptionDetail);
						$this->CrawlerErrorLog->create_log($exceptionDetail);

						//send an alert to the team
						email_alertToTeam('Run Cron Job - Error Cron Log ID '. $this->_cron_log_id .' : ' . $e->getMessage() );
					}
				}
			}
		}

		$this->_end_time = date('Y-m-d H:i:s');

		$this->Log->update_log($this->_cron_log_id, array(
			'end_datetime' 		=> $this->_end_time,
			'products_count' 	=> $this->_numfoundproducts,
			'offerings_count'	=> $this->_numofferings
		));

		//create cron_log -- this is needed to handle code that still thinks we need to use old crawler
		$this->_marketplaces = array_unique($this->_marketplaces);

		foreach($this->_marketplaces as $marketplace) {
			$domain 					= trim($marketplace);
			$r 							= preg_split('/(?=\.[^.]+$)/', $domain);
			$marketplace				= strtolower($r[0] );

			$cron_log_id = $this->CronLog->create_log(array(
				'datetime' 		=> date('Y-m-d H:i:s'),
				'key' 			=> generate_rand(32),
				'api_type' 		=> $marketplace,
				'start_datetime'=> $this->_start_time,
				'end_datetime'	=> $this->_end_time,
				'google_count' 	=> 0,
				'last_UPC'		=> $this->_last_upc,
				'run_from' 		=> 'cronTab1'
			));

			$this->Marketplace->add_retailer(strtolower($marketplace), strtolower($domain));
		}

		exit();
	}

}