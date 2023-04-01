<?php
/**
 * An object used to generate reports
 */

class Reportinfo {

	protected $_data = array();
	protected $_defaults = array();

	public function __construct(array $info = array()) {
		$this->_defaults = $this->defaults();
		$this->_data = $this->_defaults;

		if ( ! empty($info)){
			foreach ($info as $key => $value)
				$this->$key = $value;
		}

		$this->standardize();
	}

	protected function defaults() {
		$now = time();
		return array(
			'api_type' => array('all'),
			'by_which' => NULL,
			'competitor_map' => array(),
			'competitor_store_id' => FALSE,
			'controller' => NULL,
			'controller_function' => NULL,
			'crawl_range' => array(),
			'cron_ids' => NULL,
			'date_from' => $now,
			'date_to' => $now,
			'datetime' => $now,
			'email_addresses' => array(),
			'file_name' => 'Price Over Time',
			'flagDates24' => FALSE,
			'gData' => array(),
			'id' => NULL, //synonym of report_id
			'isoverview' => FALSE,
			'last_crawl' => NULL,
			'last_tracked_arr' => array(),
			'last_tracked_date' => NULL,
			'market_violations' => 0,
			'marketplace_products' => array(),
			'marketplaces' => array(),
			'merchants' => array('all'),
			'number_of_merchants' => 0,
			'product_ids' => array(),
			'products_monitored' => 0,
			'report_datetime' => $now,
			'report_id' => NULL,
			'report_name' => NULL,
			'report_recursive_frequency' => 0,
			'report_type' => NULL,
			'report_where' => array(), //report criteria
			'reportMarketPlace' => NULL,
			'reportMerchantName' => NULL,
			'retailers' => array(),
			'show_comparison' => FALSE,
			'store_id' => NULL,
			'time_frame' => NULL, // 24, 7, or 30
			'total_violations' => 0,
			'totalMarketplaces' => 0,
			'totalRetailers' => 0,
			'user_id' => NULL,
			'violatedMarketplaces' => array(),
			'violatedRetailers' => array(),
			'violation' => FALSE,
			'whoisSwitch' => NULL
		);
	}

	protected function standardize() {
		$CI = &get_instance();

		$this->store_id = $CI->store_id;
		$CI->load->model('crawl_data_m', 'Crawl');

		$this->merchants = ensure_array($this->merchants);
		$this->api_type = ensure_array($this->api_type);
		$this->flagDates24 = ($this->date_from === $this->date_to);

		// get the last crawl data
		$this->last_crawl = $CI->Crawl->last_crawl();
		$this->crawl_range = $CI->Crawl->last_crawl_range();

		if ($this->time_frame) { // This report's dates are the last x many days
			$tF = getTimeFrame($this->time_frame);
			$this->date_from = $tF['date_from'];
			$this->date_to = $tF['date_to'];
		}

		switch ($this->report_type) {
			case 'violationoverview':
				$this->time_frame = 24;
				break;
			case 'pricingoverview':
				$this->time_frame = 24;
				break;
			case 'pricingviolation':
				break;
			case 'whois':
				break;
		}
		
		$this->set_filename();
	}
	
	function set_filename(){
		switch ($this->report_type) {
			case 'violationoverview':
				$this->file_name = 'Price Over Time';
				break;
			case 'pricingoverview':
				$this->file_name = 'Price Overview';
				break;
			case 'pricingviolation':
				$this->file_name = 'Price Violations';
				break;
			case 'whois':
				$this->file_name = 'Who\'s Selling My Products';
				break;
		}
	}

	public function get() {
		return $this->_data;
	}

	public function __set($key, $value) {
		if ( ! array_key_exists($key, $this->_defaults))
			throw new UnexpectedValueException($key . ' is not a valid Reportinfo property.');

		$this->_data[$key] = $value;
	}

	public function __isset($key) {
		return isset($this->_data[$key]);
	}

	public function __get($key) {
		return isset($this->_data[$key]) ? $this->_data[$key] : NULL;
	}


	/*
	*
	* The following functions are an attempt to keep the reporting standardized
	* across all of the seperate reporting methods: UI, email, pdf, etc.
	* Please be keen on keeping things tidy...
	*
	*/

	/**
	* Overview reporting:
	*
	* @param Integer $reportId
	* @return object $data
	**/
	public function report_overview($reportId = false)
	{
		$CI = &get_instance();

		$CI->load->model('crawl_data_m');
		
		// tell the view this is the pricing overview report
		$this->_data['report_name'] = 'Pricing Overview';
		$this->_data['report_type'] = 'pricingoverview';
		$this->_data['file_name'] = str_replace(' ', '_', 'Pricing Overview ' . date('Y-m-d'));

		if($reportId) $this->_set_defaults($reportId);
		
		$this->_data['number_of_merchants'] = getNumberOfMerchants(	
		    $this->store_id,
				$this->crawl_range['from'],
				$this->crawl_range['to']
		);
		
		$this->_data['last_tracked_arr'] = $CI->Report->last_tracked_image($this->crawl_range['from']);
		$this->_data['last_tracked_date'] = trackingDateFormat($this->crawl_range['from']);

		// calculate the overview statistics
		$this->_data['products_monitored'] = $CI->Product->getProductsMonitoredCount($this->store_id);
		$this->_data['total_violations'] = $CI->Violator->countViolatedProducts($this->store_id);
		$this->_data['marketplace_products'] = array();
		$this->_data['market_violations'] = array();

		/*
		$markets = array_keys(get_market_lookup(TRUE,TRUE));
		
		foreach ($markets as $market)
		{
			$crawl_info = ! empty($this->last_crawl[$market]) ? $this->last_crawl[$market] : FALSE;
			
			// using global crawl time - change by Christophe on 9/2/2015 
			$crawl_info = TRUE;
			
			if ($crawl_info)
			{
				//$from = $crawl_info->start_datetime;
				//$to = $crawl_info->end_datetime;

			  // change by Christophe on 9/2/2015  
        //$from = $CI->Violator->crawlStart;
        //$to = $CI->Violator->crawlEnd;
        
        if ($CI->config->item('environment') == 'local')
        {
            $from = '2015-08-01 00:00:00';
            $to = '2015-08-02 00:00:00';
        }
        else
        {
            $from = date('Y-m-d H:i:s', strtotime('-24 hours'));
            $to = date('Y-m-d H:i:s');
        } 
			            
				$marketplace_products = $CI->MProducts->getCountByMarketplace($this->store_id, $market, $from, $to);
								
				if (!empty($marketplace_products))
				{
            $this->_data['marketplace_products'][] = $marketplace_products[0];
            
            $market_violations = $CI->Violator->getViolatedMarkets($this->store_id, '', $market, $from, $to);
            //$market_violations = $CI->Violator->getMarketViolations($this->store_id, '', $market);
            					
            $this->_data['market_violations'][$market] = $market_violations[$market];
				}			
			}
		}
		*/

    /*		
		//for saving reports - not necessary to provide info for overview
		$this->_data['report_where'] = array('report_type' => 'pricingoverview');
		$this->_data['gData'] = array(
			'type'       => 'pie',
			'width'      => '220',
			'height'     => '150',
			'googleData' => array(
				array('State','Count'),
				array('Non Violation', (int)max($this->_data['products_monitored'] - $this->_data['total_violations'], 0)),
				array('Violation', (int)$this->_data['total_violations'])
			)
		);

		// separate marketplaces from retailers
		$this->_data['marketplaces'] = array();
		$this->_data['retailers'] = array();
		$this->_data['violatedMarketplaces'] = array();
		$this->_data['violatedRetailers'] = array();
		
		for ($i = 0, $n = count($this->_data['marketplace_products']); $i < $n; $i++) 
		{
			$name = $this->_data['marketplace_products'][$i]['marketplace'];
			
			if ($this->_data['marketplace_products'][$i]['is_retailer'] === '1') 
			{
				$merchant = $CI->MProducts->getMerchantDetailsByMarketplace($name);
				
				if (isset($merchant[0]['id'])) 
				{
					$this->_data['marketplace_products'][$i] = array_merge($this->_data['marketplace_products'][$i], $merchant[0]);
					$this->_data['retailers'][] = $this->_data['marketplace_products'][$i];
					
					if ( ! empty($this->_data['market_violations'][$name]))
					{
						$this->_data['violatedRetailers'][$name] = TRUE;
					}
				}
			}
			else 
			{
				$this->_data['marketplaces'][] = $this->_data['marketplace_products'][$i];
				
				if ( ! empty($this->_data['market_violations'][$name]))
				{
					$this->_data['violatedMarketplaces'][$name] = TRUE;
				}
			}
		}
		
		usort($this->_data['marketplaces'], function($a, $b) 
		{
			return strtolower($a['marketplace']) > strtolower($b['marketplace']);
		});
		
		usort($this->_data['retailers'], function($a, $b) 
		{
			return strtolower($a['marketplace']) > strtolower($b['marketplace']);
		});

		$this->_data['totalMarketplaces'] = count($this->_data['marketplaces']);
		$this->_data['totalRetailers'] = count($this->_data['retailers']);
    */
		
		return $this->_data;
	}


	/**
	* setting report data
	*
	* @param Integer $reportId
	* @return array $reportData
	**/
	private function _set_defaults($reportId){
		$this->report_id = $this->id = base64_decode(urldecode($reportId));
		$this->report_info = $this->get_save_report_by_id($this->report_id);

		$report_where = json_decode($this->report_info['report_where'], true);
		if ( ! empty($report_where))
			foreach ($report_where as $key => $value)
				$this->$key = $value;
	}
}
