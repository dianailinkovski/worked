<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Controller for handling most cron jobs
 */
class Schedule_cron extends MY_Controller {

	private $_amazon_proxy_setting = array();
	private $_amazon_has_blocked_us = array();
	private $_amazon_settings_cache = array();
	private $_amazon_connection_ctr = 0;
	private $_save_cache = false;
	private $_test_violator_notifications = false;
	
	public $captcha_breaker;
	public $captcha_answer;
	public $captcha_enabled = false;
	
	protected $_acl = array(
	//'*' => '',
	/*
	'*' => 'cli',
	'render_graph' => ''
	*/
	);

	function __construct() {
		parent::__construct();

		$this->load->library('mvformat');
		$this->load->library('email');
		$this->load->library('encrypt');

		$this->load->model('account_m', "account");
		$this->load->model("chart_m", "Chart");
		$this->load->model("crawl_data_m");
		$this->load->model('marketplace_m', 'Marketplace');
		$this->load->model("merchant_products_m");
		$this->load->model("Users_m", 'User');
		$this->load->model("products_m", 'Product');
		$this->load->model("report_m", 'Report');
		$this->load->model("store_m", 'Store');
		$this->load->model("violations_m", "Violations");
		$this->load->model("violator_m", "Violator");
		$this->load->model("amazon_settings_m", "AmazonSetting");

		require_once(APPPATH . '3rdparty/pchart/pChart/pData.class');
		require_once(APPPATH . '3rdparty/pchart/pChart/pChart.class');
		
		if ( !isset($this->data) ) $this->data = new stdClass();

		$this->data->is_demo = false;
		$this->data->report_id = 0;

		$this->_layout = NULL;
		$this->_view = NULL;
		
		$this->load->helper("password_helper");
		
		// Use DeathByCaptcha_HttpClient() class if you want to use HTTP API.
		require_once(APPPATH . 'libraries/deathbycaptcha.php');
		$captcha_api_username = 'TrackStreet';//TODO: $this->config->item('captcha_api_username');
		$captcha_api_password = 'Sticky73!';//TODO: $this->config->item('captcha_api_password');
		$this->captcha_breaker = new DeathByCaptcha_SocketClient($captcha_api_username, $captcha_api_password);
		$this->check_captcha_balance();
	}

	function run_daily_price_cron() {
		$this->load->model('daily_price_average_m', 'DailyPriceAvg');
		$this->DailyPriceAvg->update_average_price();
	}
	
	function bad_upc() {
		$store_list = $this->Store->get_all_store_with_users();
		foreach ($store_list as $store) {
			$upc_list = $this->Product->getBadUpcByStoreId($store['store_id']);
			$number_bad = count($upc_list);
			if ($number_bad > 0) {
				$report_data = get24ReportTitleAndDate(
				array('time_frame' => 24,
						'title' => "Bad UPC List",
						'fromDate' => strtotime('-24 hours'),
						'toDate' => time()
				)
				);

				$data = array(
					'upc_list' => $upc_list,
					'merchant_logo' => get_merchant_logo_url($store['brand_logo']),
					'headerDate' => $report_data['date'],
					'title' => $number_bad . ' Invalid UPC' . ($number_bad > 1 ? 's' : ''),
					'brand_name' => $this->Store->get_brand_by_store($store['store_id']),
					'store_id' => $store['store_id']
				);

				$html = $this->load->view($this->_controller . '/_bad_upc', $data, true);
				$text = $this->load->view($this->_controller . '/_bad_upc_txt', $data, true);

				$template = 'general';
				template_email_send($template, $store['id'], 'Bad UPC Notification', $store['email'], $html, $text);
			}
		}

		exit;
	}

	/**
	 * Go through the automated reports and send the
	 * recurring report if the schedule recursion falls into
	 * this five minute interval
	 */
	function schedule() {
		// Set the 5 minute window
		$window_start = new DateTime(date("Y-m-d H:i:00"));
		$window_end = new DateTime(date("Y-m-d H:i:00", strtotime('+5 minutes')));

		$schArrays = array('daily_reports', 'weekly_reports', 'monthly_reports', 'yearly_reports');

		// Get ALL scheduled reports fitting this timeframe
		// could be very useful in implementing user timezones in the future

		/* daily */
		$daily_reports = $this->db
		->query(
			"select sr.*, srs.*
			from saved_reports sr
			left join saved_reports_schedule srs on srs.saved_reports_id=sr.id
			where 
			date_format(srs.report_datetime, '%k') = date_format(now(), '%k')
			and (
					date_format(now(), '%i')-date_format(srs.report_datetime, '%i') >= 0
				and date_format(now(), '%i')-date_format(srs.report_datetime, '%i') < 5
			)
			and CURDATE() >= date(srs.report_datetime)
			and
			report_recursive_frequency = 1"
		)
		->result_array();
//print_r($daily_reports); exit;
		/* weekly */
		$weekly_reports = $this->db
		->query(
			"select sr.*, srs.*
			from saved_reports sr
			left join saved_reports_schedule srs on srs.saved_reports_id=sr.id
			where date_format(srs.report_datetime, '%w') = date_format(now(), '%w')
			and date_format(srs.report_datetime, '%k') = date_format(now(), '%k')
			and (
					date_format(now(), '%i')-date_format(srs.report_datetime, '%i') >= 0
				and date_format(now(), '%i')-date_format(srs.report_datetime, '%i') < 5
			)
			and CURDATE() >= date(srs.report_datetime)
			and report_recursive_frequency = 7"
		)
		->result_array();

		/* monthly */
		$monthly_reports = $this->db
		->query(
			"select sr.*, srs.*
			from saved_reports sr
			left join saved_reports_schedule srs on srs.saved_reports_id=sr.id
			where (
				   date_format(srs.report_datetime, '%e') = date_format(now(), '%e')
				OR date_format(now(), '%e')-date_format(srs.report_datetime, '%e') < 0
			)
			and (
					date_format(now(), '%i')-date_format(srs.report_datetime, '%i') >= 0
				and date_format(now(), '%i')-date_format(srs.report_datetime, '%i') < 5
			)
			and CURDATE() >= date(srs.report_datetime)
			and report_recursive_frequency = 31"
		)
		->result_array();

		/* yearly */
		$yearly_reports = $this->db
		->query(
			"select sr.*, srs.*
			from saved_reports sr
			left join saved_reports_schedule srs on srs.saved_reports_id=sr.id
			where (
				   date_format(srs.report_datetime, '%e') = date_format(now(), '%e')
				OR date_format(now(), '%e')-date_format(srs.report_datetime, '%e') < 0
			)
			and date_format(srs.report_datetime, '%m') = date_format(now(), '%m')
			and (
					date_format(now(), '%i')-date_format(srs.report_datetime, '%i') >= 0
				and date_format(now(), '%i')-date_format(srs.report_datetime, '%i') < 5
			)
			and CURDATE() >= date(srs.report_datetime)
			and report_recursive_frequency = 365"
		)
		->result_array();

		//none of the schedule arrays are populated - nothing to see here...
		if (empty($daily_reports) && empty($weekly_reports) && empty($monthly_reports) && empty($yearly_reports)){
			return;
		}

		foreach ($schArrays as $arr) {
			$curSched = $$arr;

			for($i=0, $n=sizeof($curSched); $i<$n; $i++){
				$report = $curSched[$i];

				$report['report_schedule_date'] = $report['report_datetime'];
				$this->data->report_id = $report['id'];
				$this->prepareData($report);

				if ( ! empty($this->report_info->report_type)) {
					$this->report_info->set_filename();
					$receivers = $report['email_addresses'];
					$template = 'general';

					$html = $this->renderHtml();
					$txt = $this->renderTxt();

					if ($html){
						$returnVal = template_email_send($template, $this->data->user_id, $this->report_info->file_name, $receivers, $html, $txt);
					}
					unset($html, $txt, $this->data, $this->report_info->store_id);
				}
			}
		}//end foreach

	}

	/**
	 * Generate the report data array
	 *
	 * @param array $report
	 * @return array
	 */
	private function prepareData($report) {

		$saveReportData = $this->Report->get_save_report_by_id($report['saved_reports_id']);

		$this->data->report_info = $this->Report->get_save_report_by_id($report['saved_reports_id']);

		if(empty($saveReportData)) return;
		if(empty($this->data->report_info)) return;

		$report_where = json_decode($saveReportData['report_where'], true);
		if ( ! empty($report_where)){
			foreach ($report_where as $key => $value)
				$this->data->$key = $value;
		}

		$report_where = json_decode($this->data->report_info['report_where'], true);
		if ( ! empty($report_where)){
			foreach ($report_where as $key => $value){
				$this->data->$key = $value;
			}
		}

		$this->data->last_crawl = $this->crawl_data_m->last_crawl();
		$this->crawl_range = $this->crawl_data_m->last_crawl_range();

		$saveReportData['report_where'] = $report_where;
		$this->data->report_where = $report_where;

		$store_data = $this->Store->get_store_track($saveReportData['store_id']);

		$this->load->library('reportinfo');
		$this->report_info = new Reportinfo($saveReportData);
		$this->report_info->report_id = $saveReportData['id'];
		$this->report_info->report_name = $saveReportData['report_name'];
		$this->report_info->report_type = $report_where['report_type'];
		$this->report_info->controller = $saveReportData['controller'];
		$this->report_info->controller_function = $saveReportData['controller_function'];
		$this->report_info->user_id = isset($store_data->user_id) ? $store_data->user_id : 0;
		$this->report_info->store_id = $report['store_id'];
		$this->store_id = $report['store_id'];

		//prep data for specific type of report
		if ($this->report_info->report_type === 'group_report') {
			$groupProducts = $this->Product->getProductsByGroupId($report_where['group_id']);
			$product_ids = array();
			foreach ($groupProducts as $gp) {
				$product_ids[] = $gp['product_id'];
			}
			$this->report_info->product_ids = $product_ids;
		}

		//
		// Set all the request data
		//
		$this->report_info->store_id = ($this->report_info->store_id == '0') ? 'all' : $this->report_info->store_id;
		$this->user_id = $this->report_info->user_id;

		$this->_get_report_data();

		$this->data->report_chart = 'line';
		$this->data->request_info = $this->report_info->get();

		foreach ($this->data->request_info as $key => $value){
			$this->data->$key = $value;
		}

		$brandInfo = $this->account->get_brand_info($this->report_info->store_id);
		$this->data->brand_name = $brandInfo['store_name'];
		$this->data->noRecord = $this->config->item('no_record');
		$this->data->url_modifier = ($this->data->controller_function === 'bycompetition') ? '0/bycompetition' : '';
		$this->data->merchant_logo = $this->account->get_merchant_thumb($this->report_info->store_id);

		if ($this->report_info->report_type === 'whois') {
			$this->data->is_report = true;
			if (!trim($this->data->reportMarketPlace)) {
				$report_data = get24ReportTitleAndDate(array(
						'time_frame' => 24,
						'title' => "Who's selling my products in",
						'fromDate' => strtotime('-24 hours'),
						'toDate' => time()
				));
				$this->data->headerDate = $report_data['date'];
				$this->data->title = $report_data['title'];
				$this->data->is_report = false;
			}
		}
		elseif($this->report_info->report_type === 'pricingoverview' || $this->report_info->report_type === 'violationoverview'){
			$this->data->headerDate = date("m/d/Y H:ia", time());
			$this->data->title = $this->report_info->report_name;
		}
		else {
			$rptInfo = getTitleReporting($this->data->request_info);
			$this->data->headerDate = $rptInfo['date'];
			$this->data->title = $rptInfo['title'];
		}
		// the variables are populated in the wrong sequence.  We pass them here to ensure accuracy.  // TODO: refactor?
		if(!empty($this->data->Data)){
			foreach ($this->data->Data as $key => $value){
				$this->data->$key = $value;
			}
		}
	}

	/**
	 * Return the report as an txt String
	 *
	 * @param array $data
	 * @return String
	 */
	private function renderTxt(){

		// Now use the data to create the text view
		switch ($this->report_info->report_type){
			case 'violationoverview':
				$txt = $this->load->view($this->_controller . '/_violationoverview_email_txt', $this->data, TRUE);
				break;
			case 'pricingoverview':
				$txt = $this->load->view($this->_controller . '/_pricingoverview_email_txt', $this->data, TRUE);
				break;
			case 'whois':
				if ($this->data->request_info['whoisSwitch'] === 'marketplace')
				$txt = $this->load->view($this->_controller . '/_whois_marketplace_email_txt', '', TRUE);
				else
				$txt = $this->load->view($this->_controller . '/_whois_retailer_email_txt', '', TRUE);
				break;
			case 'pricingviolation':
				$txt = $this->load->view($this->_controller . '/_violations_over_time_email', $this->data, TRUE);
				break;
			default:
				$txt = $this->load->view($this->_controller . '/_price_over_time_email', $this->data, TRUE);
		}

		return $txt;
	}

	/**
	 * Return the report as an html String
	 *
	 * @return String
	 */
	private function renderHtml() {
		$products_w_comparisons = array();
		if ($this->data->controller_function === 'bycompetition') { // this is a competitor's product
			$this->data->competitor_products = $this->Product->getProductsById(null, $this->data->products);
			$this->data->competitor_store_id = array();
			for ($i = 0, $n = count($this->data->competitor_products); $i < $n; $i++) {
				$this->data->competitor_store_id[] = $this->data->competitor_products[$i]['store_id'];
			}
			$this->data->competitor_store_id = array_unique($this->data->competitor_store_id);
			$this->data->proMerchants = getProductMerchant($this->data->competitor_store_id);

			if ($this->data->show_comparison) { // and it's a comparison
				$competitor_map = $this->Store->getCompetitorMap($this->report_info->store_id, $this->data->products);
				$this->data->competitor_store_id[] = $this->report_info->store_id;
				$products_wo_comparisons = $this->data->products;
				$this->data->competitor_map = array();
				foreach ($competitor_map as $bpp) {
					$this->data->products[] = $bpp['owner_brand_product'];
					$owned_product = $this->Product->getProductsById(null, $bpp['owner_brand_product']);
					if (!empty($owned_product[0]['id'])) {
						$this->data->competitor_map[$bpp['competitor_brand_product']] = $owned_product[0];
					}
				}
				$products_w_comparisons = $this->data->products;
			}
		}

		if ($this->data->show_comparison and isset($products_wo_comparisons)) { // don't populate product fields with the compared
			$this->data->products = $products_wo_comparisons;
		}

		$this->_get_retailers_marketplaces_exists();
		$this->_prep_competitor_map($products_w_comparisons);
		$this->_prep_graph_image();
		$html = $this->_get_report_html();

		return $html;
	}

	/**
	 * Use the request info to get the report data
	 *
	 * @param array $request_info
	 */
	private function _get_report_data() {
		// Get the report data based on report type
		switch ($this->report_info->report_type){
			case 'violationoverview':
				$this->_prep_violation_overview();
				break;
			case 'pricingoverview':
				$this->_prep_pricing_overview();
				break;
			case 'whois':
				$this->_prep_whois_report();
				break;
			case 'pricingviolation':
				$this->_prep_violation_report();
				break;
			default:
				$this->_prep_pricing_report();
				break;
		}
	}

	private function _prep_violation_overview() {
		$this->data->my = 'violationoverview';
		$this->data->report_chart = 'na';

		// Get the last crawl data
		$this->data->last_crawl = $this->crawl_data_m->last_crawl();
		$this->crawl_range = $this->crawl_data_m->last_crawl_range();

		// Get market/merchant/product violations
		$this->data->priceViolators = $this->Violator->_countPriceViolations();

		foreach($this->data->priceViolators as $id=>$vals){
			$crowl_merchant = $this->merchant_products_m->getMerchantDetailsById($id);
			$this->data->priceViolators[$id]['crowl_merchant'] = $crowl_merchant;
		}
		$this->data->violatedProducts = $this->Violator->getViolatedProducts($this->report_info->store_id);

		// Calculate the overview statistics
		$this->data->products_monitored = $this->Product->getProductsMonitoredCount($this->report_info->store_id);
		$this->data->total_violations = count($this->data->violatedProducts);
		$this->data->marketplace_products = array();
		$this->data->market_violations = array();
		$markets = array_to_lower(getMarketArray());
		foreach ($markets as $market)
		{
			$crawl_info = ! empty($this->data->last_crawl[$market]) ? $this->data->last_crawl[$market] : FALSE;
			if ($crawl_info)
			{
				$from = $crawl_info->start_datetime;
				$to = $crawl_info->end_datetime;

				$marketplace_products = $this->merchant_products_m->getCountByMarketplace($this->report_info->store_id, $market, $from, $to);
				if ( ! empty($marketplace_products))
				{
					$market_violations = $this->Violator->getViolatedMarkets($this->report_info->store_id, '', $market, $from, $to);
					$this->data->marketplace_products[] = $marketplace_products[0];
					$this->data->market_violations[$market] = $market_violations[$market];
				}
			}
		}

		$this->data->request_info = array(
			'fromDate'   => strtotime($this->crawl_range['from']),
			'toDate'     => strtotime($this->crawl_range['to']),
			'time_frame' => 1,
			'product_ids' => array()
		);
		$this->data->gData = array(
			'type'       => 'pie',
			'width'      => '220',
			'height'     => '150',
			'googleData' => array(
				array('State','Count'),
				array('Non Violation', (int)max($this->data->products_monitored - $this->data->total_violations, 0)),
				array('Violation', (int)$this->data->total_violations)
			)
		);

		// Separate marketplaces from retailers
		$this->data->marketplaces = array();
		$this->data->retailers = array();
		$this->data->violatedMarketplaces = array();
		$this->data->violatedRetailers = array();
		for ($i = 0, $n = count($this->data->marketplace_products); $i < $n; $i++) {
			$name = $this->data->marketplace_products[$i]['marketplace'];
			if ($this->data->marketplace_products[$i]['is_retailer'] === '1') {
				$merchant = $this->merchant_products_m->getMerchantDetailsByMarketplace($this->data->marketplace_products[$i]['marketplace']);
				if (isset($merchant[0]['id'])) {
					$this->data->retailers[] = $this->data->marketplace_products[$i];
					$this->data->marketplace_products[$i] = array_merge($this->data->marketplace_products[$i], $merchant[0]);
					if ( ! empty($this->data->market_violations[$name])){
						$this->data->violatedRetailers[$name] = TRUE;
					}
				}
			}
			else {
				$this->data->marketplaces[] = $this->data->marketplace_products[$i];
				if ( ! empty($this->data->market_violations[$name])){
					$this->data->violatedMarketplaces[$name] = TRUE;
				}
			}
		}
		// variable instantiation is out of sequence.  We ensure it here.  // TODO: refactor?
		$this->data->Data->violatedMarketplaces = $this->data->market_violations;
		$this->data->Data->violatedRetailers = $this->data->violatedRetailers;
		$this->data->Data->priceViolators = $this->data->priceViolators;
		$this->data->Data->violatedProducts = $this->data->violatedProducts;
		$this->data->Data->retailers = $this->data->retailers;
		$this->data->Data->marketplaces = $this->data->marketplaces;
		$this->data->Data->market_violations = $this->data->market_violations;
	}

	private function _prep_pricing_overview() {
		$this->data->my = 'pricingOverview';
		$this->data->report_chart = 'na';

		// Get the last crawl data
		$this->data->last_crawl = $this->crawl_data_m->last_crawl();
		$this->crawl_range = $this->crawl_data_m->last_crawl_range();
		$this->data->number_of_merchants = getNumberOfMerchants(
		$this->report_info->store_id,
		$this->crawl_range['from'],
		$this->crawl_range['to']
		);
		$this->data->last_tracked_arr = $this->Report->last_tracked_image($this->crawl_range['from']);
		$this->data->last_tracked_date = trackingDateFormat($this->crawl_range['from']);

		// Calculate the overview statistics
		$this->data->products_monitored = $this->Product->getProductsMonitoredCount($this->report_info->store_id);
		$this->data->total_violations = $this->Violator->countViolatedProducts($this->report_info->store_id);
		$this->data->marketplace_products = array();
		$this->data->market_violations = array();

		$markets = array_keys(get_market_lookup(TRUE));
		foreach ($markets as $market)
		{
			$crawl_info = ! empty($this->data->last_crawl[$market]) ? $this->data->last_crawl[$market] : FALSE;
			if ($crawl_info)
			{
				$from = $crawl_info->start_datetime;
				$to = $crawl_info->end_datetime;

				$marketplace_products = $this->merchant_products_m->getCountByMarketplace($this->report_info->store_id, $market, $from, $to);
				if ( ! empty($marketplace_products))
				{
					$market_violations = $this->Violator->getViolatedMarkets($this->report_info->store_id, '', $market, $from, $to);
					$this->data->marketplace_products[] = $marketplace_products[0];
					$this->data->market_violations[$market] = $market_violations[$market];
				}
			}
		}

		$this->data->request_info = array(
			'fromDate' => strtotime($this->crawl_range['from']),
			'toDate'     => strtotime($this->crawl_range['to']),
			'time_frame' => 1,
			'product_ids' => array()
		);
		$this->data->gData = array(
			'type'       => 'pie',
			'width'      => '220',
			'height'     => '150',
			'googleData' => array(
				array('State','Count'),
				array('Non Violation', (int)max($this->data->products_monitored - $this->data->total_violations, 0)),
				array('Violation', (int)$this->data->total_violations)
			)
		);

		// Separate marketplaces from retailers
		$this->data->marketplaces = array();
		$this->data->retailers = array();
		$this->data->violatedMarketplaces = array();
		$this->data->violatedRetailers = array();

		for ($i = 0, $n = count($this->data->marketplace_products); $i < $n; $i++) {
			$name = $this->data->marketplace_products[$i]['marketplace'];
			if ($this->data->marketplace_products[$i]['is_retailer'] === '1') {
				$merchant = $this->merchant_products_m->getMerchantDetailsByMarketplace($name);
				if (isset($merchant[0]['id'])) {
					$this->data->marketplace_products[$i] = array_merge($this->data->marketplace_products[$i], $merchant[0]);
					$this->data->retailers[] = $this->data->marketplace_products[$i];
					if ( ! empty($this->data->market_violations[$name])){
						$this->data->violatedRetailers[$name] = TRUE;
					}
				}
			}
			else {
				$this->data->marketplaces[] = $this->data->marketplace_products[$i];
				if ( ! empty($this->data->market_violations[$name])){
					$this->data->violatedMarketplaces[$name] = TRUE;
				}
			}
		}
		$this->data->Data->retailers = $this->data->retailers;
		$this->data->Data->marketplaces = $this->data->marketplaces;
//print_r($this->data); exit;
	}

	private function _prep_whois_report() {
		$this->data->my = 'whois';
		$this->data->report_chart = 'na';

		// Separate marketplaces from retailers and
		// group marketplace_products by marketplace
		$this->data->marketplaces = array();
		$this->data->retailers = array();

		$markets = $this->report_info->whoisSwitch === 'marketplace' ? getMarketplaceArray($this->report_info->store_id) : getRetailerArray(false,$this->report_info->store_id);
		$markets = array_to_lower($markets);
		foreach ($markets as $market)
		{
			$crawl_info = $this->crawl_data_m->last_crawl($market);
			if ($crawl_info)
			{
				$from = $crawl_info->start_datetime;
				$to = $crawl_info->end_datetime;

				$marketplace_products = $this->merchant_products_m->getCountByMarketplace($this->report_info->store_id, $market, $from, $to);
				if ( ! empty($marketplace_products))
				$this->data->marketplace_products[] = $marketplace_products[0];
			}
		}

		if(isset($this->data->marketplace_products)){
			for ($i = 0, $n = count($this->data->marketplace_products); $i < $n; $i++) {
				$marketplace = $this->data->marketplace_products[$i]['marketplace'];
				if ($this->data->marketplace_products[$i]['is_retailer'] === '1') {
					$merchant = $this->merchant_products_m->getMerchantDetailsByMarketplace($marketplace);
					if (isset($merchant[0]['id'])) {
						$this->data->marketplace_products[$i] = array_merge($this->data->marketplace_products[$i], $merchant[0]);
						$this->data->retailers[] = $this->data->marketplace_products[$i];
					}
				}
				else {
					$this->data->marketplaces[] = $this->data->marketplace_products[$i];
				}
				$this->data->marketplace_products[$marketplace] = $this->data->marketplace_products[$i];
				unset($this->data->marketplace_products[$i]);
			}
		}

		// sort by keys
		$this->data->marketplace_keys = array();
		if( ! empty($this->data->marketplace_products)){
			ksort($this->data->marketplace_products);
			$this->data->marketplace_keys = array_keys($this->data->marketplace_products);
			// graph data
			$this->data->gData = mvFormat::whoIsSellingMyProductDefault($this->data->marketplace_products);
		}
		$this->data->Data->retailers = $this->data->retailers;
		$this->data->Data->marketplaces = $this->data->marketplaces;
	}

	private function _prep_violation_report() {
		$this->data->my = 'pricingviolation';
		$this->data->report_chart = 'scatter';
		if($this->data->time_frame == 24 || $this->data->flagDates24)
		$this->data->request_info['cron_ids'] = getLast24HoursCronIds($this->data->flagDates24 ? $this->data->date_from : '', '', $this->data->request_info['api_type']);

		$response = $this->Violations->searchProductPricingViolations($this->data->request_info);
		$this->data->Data = $response;
		$this->data->gData = $this->Chart->prepGoogleData($response, $this->data->request_info, $this->data->report_chart);
	}

	private function _prep_pricing_report() {
		$this->data->my = 'productpricing';
		$this->data->report_chart = 'line';
//print_r($this->data); exit;
		if($this->data->time_frame == 24 || $this->data->flagDates24){
			$this->data->report_chart = 'scatter';
			$this->data->request_info['cron_ids'] = getLast24HoursCronIds($this->data->flagDates24 ? $this->data->date_from : '', '', $this->data->request_info['api_type']);
			$response = $this->Report->productPricingReport24($this->data->request_info);
			$this->data->Data = $response['data'];
			$this->data->gData = $this->Chart->prepGoogleData($response['data'], $this->data->request_info, $this->data->report_chart);
		}
		else {
			$this->data->Data = $this->Report->productPricingHistory($this->data->request_info);
			$this->data->gData = $this->Chart->prepGoogleData($this->data->Data, $this->data->request_info, $this->data->report_chart);
		}
	}

	/**
	 * Create data arrays that tell the view whether retailers
	 * or marketplaces exist and which market is which
	 */
	private function _get_retailers_marketplaces_exists() {
		//
		// Set retailer/marketplace information
		//
		$this->data->marketRetailer = array();
		$this->data->retailersExist = array();
		$this->data->marketplacesExist = array();

		if ($this->data->report_chart === 'line') {
			if(isset($this->data->Data)){
				foreach ($this->data->Data as $prodId => $marketArr) {
					foreach ($marketArr as $marketName => $productData) {
						if (!isset($this->data->marketRetailer[$marketName]))
						$this->data->marketRetailer[$marketName] = $this->Marketplace->is_retailer($marketName);
						if ($this->data->marketRetailer[$marketName])
						$this->data->retailersExist[$prodId] = true;
						else
						$this->data->marketplacesExist[$prodId] = true;
					}
				}
			}
		}
		elseif ($this->data->report_chart === 'scatter') {
			if(isset($this->data->Data)){
				foreach ($this->data->Data as $prodId => $productData) {
					for ($i = 0, $n = count($productData); $i < $n; $i++) {
						$marketName = strtolower($productData[$i]['marketplace']);
						if (!isset($this->data->marketRetailer[$marketName]))
						$this->data->marketRetailer[$marketName] = $this->Marketplace->is_retailer($marketName);
						if ($this->data->marketRetailer[$marketName])
						$this->data->retailersExist[$prodId] = true;
						else
						$this->data->marketplacesExist[$prodId] = true;
					}
				}
			}
		}
	}

	/**
	 * If this is a show comparison report map each competitor
	 * product to their associated product
	 *
	 * @param array $products_w_comparisons
	 */
	private function _prep_competitor_map($products_w_comparisons) {
		//
		// Set up map between competitor product data and user product data
		//

		$this->data->comparison_data = array();
		if ($this->data->show_comparison) {
			$color_index = 0;
			$this->data->color_index = array();

			if (!empty($this->data->competitor_map)) {

				//
				// If this is a scatter chart we need to add the crawl id for all the data
				//

				if ($this->data->report_chart === 'scatter') {
					foreach ($this->data->Data as $prodId => $productData) {
						for ($i = 0, $n = count($productData); $i < $n; $i++) {
							if (isset($productData[$i]['marketplace'])) {
								$api_type = explode('.', $productData[$i]['marketplace']);
								$api_type = $api_type[0];
								$crawl = $this->Crawl->get_crawl_by_time($productData[$i]['dt'], $api_type, 'id');
								$this->data->Data[$prodId][$i]['crawl_id'] = isset($crawl['id']) ? $crawl['id'] : false;
							}
						}
					}
				}

				//
				// Now loop through and set the comparison data
				//

				foreach ($products_w_comparisons as $prodId) {
					$this->data->color_index[$prodId] = $color_index++;

					//
					// Check if this competitor product is associated with user product
					//

					$comparison_id = false;
					if (isset($this->data->competitor_map[$prodId]))
					$comparison_id = $this->data->competitor_map[$prodId]['id'];

					if ($comparison_id and isset($this->data->Data[$comparison_id])) {
						$comparison_data = $this->data->Data[$comparison_id];

						//
						// Map values via productid->marketplace->crawltime
						//

						if (!empty($comparison_data)) {

							if ($this->data->report_chart === 'line') {
								foreach ($comparison_data as $market => $market_prod_data) {
									for ($i = 0, $n = count($market_prod_data); $i < $n; $i++) {
										if (isset($market_prod_data[$i]['dt'])) {
											$this->data->comparison_data[$prodId][$market][$market_prod_data[$i]['dt']] = $market_prod_data[$i];
										}
									}
								}
							}
							elseif ($this->data->report_chart === 'scatter') {
								for ($i = 0, $n = count($comparison_data); $i < $n; $i++) {
									if (isset($comparison_data[$i]['marketplace'])) {
										$market = strtolower($comparison_data[$i]['marketplace']);
										$user_id = $comparison_data[$i]['user_id'];
										$crawl_id = $comparison_data[$i]['crawl_id'];
										$this->data->comparison_data[$prodId][$market][$crawl_id][$user_id] = $comparison_data[$i];
									}
								}
							}
						}
						unset($this->data->Data[$comparison_id]);
					}
				}
			}
		}
	}

	/**
	 * Generate the graph using phantomJS and gData.
	 * Upload the graph to bucket and save the name of
	 * the generated image.
	 */
	private function _prep_graph_image() {
		//
		// We need to save the graph to an image
		// in order to email it
		//

		$this->data->graph_image_name = '';
		if (!empty($this->data->gData))
		$this->data->graph_image_name = $this->generateImageThroughPhantomJS($this->data->gData);
	}

	/**
	 * Generate the html using the email report view
	 *
	 * @return String/FALSE
	 */
	private function _get_report_html() {
		$ret = FALSE;

		//
		// Now use the data to create the view
		//

		switch ($this->report_info->report_type){
			case 'violationoverview':
				if(!empty($this->data->merchant_logo)) $this->data->merchant_logo = $this->config->item('s3_cname').'stickyvision/brand_logos/'.$this->data->merchant_logo;
				$ret = $this->load->view($this->_controller . '/_violationoverview_email', $this->data, TRUE);
				break;
			case 'pricingoverview':
				if(!empty($this->data->merchant_logo)) $this->data->merchant_logo = $this->config->item('s3_cname').'stickyvision/brand_logos/'.$this->data->merchant_logo;
				$body = $this->load->view($this->_controller . '/_pricingoverview_email', $this->data, TRUE);
				break;
			case 'whois':
				if ($this->data->request_info['whoisSwitch'] === 'marketplace')
				$body = $this->load->view('components/sellingMarketPlaces', $this->data, TRUE);
				else
				$body = $this->load->view('components/sellingRetailers', $this->data, TRUE);
				break;
			case 'pricingviolation':
				$body = $this->load->view($this->_controller . '/_violations_over_time_email', $this->data, TRUE);
				break;
			default:
				$body = $this->load->view($this->_controller . '/_price_over_time_email', $this->data, TRUE);
		}

		if(isset($body)){
			$this->data->content = $body;
			$ret = $this->load->view('layouts/email', $this->data, TRUE);
			unset($this->data->content);
		}

		return $ret;
	}

	protected function generateGraphImage($finalData, $hourflag, $title = 'Sticky Charts', $x_axis_format = 'date') {
		$path = $this->config->item('csv_upload_path');

		if (!empty($finalData['data'])) {
			$DataSet = new pData();
			$in = 0;
			foreach ($finalData['data'] as $seriesData) {
				$in++;
				$seriesIndex = 'Serie' . $in;
				$DataSet->AddPoint($seriesData['data'], $seriesIndex);
				$DataSet->SetSerieName($seriesData['name'], $seriesIndex);
				$DataSet->AddSerie($seriesIndex);
			}

			$xAxisArray = array();
			$in++;
			$seriesIndex = 'Serie' . $in;
			$catCount = count($finalData['cat']);
			if ($catCount <= 10)
			$DataSet->SetXAxisFormat($x_axis_format);
			foreach ($finalData['cat'] as $catD) {
				if ($catCount > 10) {
					$xAxisArray[] = '';
				}
				else {
					$xAxisArray[] = strtotime($catD);
				}
			}

			$DataSet->SetYAxisFormat("number");
			$DataSet->AddPoint($xAxisArray, $seriesIndex);
			$DataSet->SetAbsciseLabelSerie($seriesIndex);
			$DataSet->SetYAxisName($finalData['y_title']);
			$DataSet->SetXAxisName($finalData['x_title']);

			// Initialise the graph
			$Test = new pChart(985, 458);
			$Test->drawBackground(247, 226, 180);
			$Test->setFontProperties(APPPATH . '3rdparty/pchart/Fonts/tahoma.ttf', 8);
			$Test->setGraphArea(40, 30, 950, 400);
			$Test->drawGraphArea(109, 110, 114, false);
			$Test->drawGrid(4, false, 0, 0, 0, 50);
			$Test->setFontProperties(APPPATH . '3rdparty/pchart/Fonts/tahoma.ttf', 6);
			// Draw the line graph
			if ($title == 'Violation Report') {//!$hourflag &&
				$sCount = count($finalData['data']);
				if ($sCount > 0) {
					for ($m = 0; $m < $sCount; $m++) {
						$color = Color_handler::get_next($m);
						$rgb = $color->get_rgb();
						$Test->setColorPalette($m, $rgb['r'], $rgb['g'], $rgb['b']);     }
				}
				$Test->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(), SCALE_NORMAL, 0, 0, 0, TRUE, 0, 0, TRUE);
				$Test->drawBarGraph($DataSet->GetData(), $DataSet->GetDataDescription());
			}
			else {
				$sCount = count($finalData['data']);
				if ($sCount > 0) {
					for ($m = 0; $m < $sCount; $m++) {
						$color = Color_handler::get_next($m % 3);
						$rgb = $color->get_rgb();
						$Test->setColorPalette($m, $rgb['r'], $rgb['g'], $rgb['b']);
					}
				}
				$Test->setLineStyle(2);
				$Test->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(), SCALE_START0, 0, 0, 0, TRUE, 0, 2);
				$Test->drawLineGraph($DataSet->GetData(), $DataSet->GetDataDescription());
				$Test->drawPlotGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 2);
			}
			// Finish the graph
			$Test->setFontProperties(APPPATH . '3rdparty/pchart/Fonts/tahoma.ttf', 8);
			$Test->setFontProperties(APPPATH . '3rdparty/pchart/Fonts/tahoma.ttf', 10);
			$imgName = uniqid('graph_') . '.png';
			$Test->Render($path . $imgName);

			return upload_to_amazon_graphImage($imgName, $path);
		}
	}

	/**
	 * Save the graph data in the database so that phantomjs can use it
	 * to create an image.
	 *
	 * @param array $data
	 * @return String
	 */
	protected function generateImageThroughPhantomJS($data) {
		$enc_data = $this->db->escape(json_encode($data));
		$qStr = "insert into " . $this->_table_cron_graph_data . " set
                id = " . $this->data->report_id . ",
                data = " . $enc_data . "
             ON DUPLICATE KEY update
                data = " . $enc_data . "
              ";
		$this->db->query($qStr);

		$script_path = $this->config->item('phantomjs');
		$script_name = 'rasterize_graph.js';

		$image = uniqid('graph_') . '.png';
		$path = $this->config->item('csv_upload_path');
		$url = site_url("schedule_cron/render_graph/" . $this->data->report_id);

		$cmd = 'phantomjs ' . $script_path . $script_name . '  "' . $url . '" ' . $path . $image;
		exec($cmd);

		return upload_to_amazon_graphImage($image, $path, $this->report_info->store_id);
	}

	/**
	 * PhantomJS navigates to here to view the graph and rasterize
	 *
	 * @param int $id
	 */
	function render_graph($id) {
		$row = $this->db
		->where('id', (int)$id)
		->get($this->_table_cron_graph_data)
		->row();

		$this->data->gData = json_decode($row->data, TRUE);

		if (isset($this->data->gData['make_array'])) {
			$violationData = $this->data->gData;
			$this->data->gData = array(
				'googleData' => $violationData['googleData'],
				'googleDataColors' => $violationData['googleDataColors'],
				'y_title' => $violationData['graph']['y_title'],
				'x_title' => $violationData['graph']['x_title'],
				'type' => $violationData['type']
			);
		}

		$this->_layout = 'blank';
		$this->_view = $this->_controller . '/' . $this->_method;
	}
	


	private function _get_violator_type($merchant_name_id) {
		$cmn = $this->crawl_data_m->crowlMerchantByID($merchant_name_id);
		$type = 'retailer';
		if ( ! empty($cmn->marketplace) ) {
			if ( ! $this->Marketplace->is_retailer($cmn->marketplace) ) {
				$type = 'marketplace';
			}
		}

		return $type;
	}

	/**
	 * Check if any crowl merchant's violation streak has started or ended
	 */
	public function update_violation_streaks() {
		$stores = $this->Store->get_stores(TRUE);
		if (empty($stores))
		return;

		$day = date('Y-m-d');
		$start = strtotime($day . ' 00:00:00');
		$end = strtotime($day . ' 23:59:59');

		$where = array(
			'cpl.last_date >=' => $start,
			'cpl.last_date <=' => $end,
			'cpl.violated' => 1,
			's.store_enable' => '1'
		);

		foreach ($stores as $store) {
			$where['s.id'] = (int)$store->id;

			$streaks = $this->db
			->select('cmn.id')
			->distinct()
			->join($this->_table_crowl_product_list . ' cpl', 'cmn.id=cpl.merchant_name_id')
			->join($this->_table_products . ' p', 'cpl.upc=p.upc_code')
			->join($this->_table_store . ' s', 's.id=p.store_id')
			->where($where)
			->get($this->_table_crowl_merchant_name . ' cmn')
			->result_array();

			//$merchant_name_ids = array();
			if ( ! empty($streaks)) {
				foreach ($streaks as $row) {
					$this->Store->start_violation_streak($row['id'], $store->id);
					//$merchant_name_ids[] = $row['id'];
				}
				// TODO: why did they set the streak_start to NULL here, for every merchant not currently in violation?
				//$this->db
				//->set('streak_start', NULL)
				//->where('store_id', (int)$store->id)
				//->where_not_in('crowl_merchant_name_id', $merchant_name_ids)
				//->update($this->_table_violation_streaks);
			}
		}
	}
	
	function _get_text_from_html_contents( $html ) {
		$text = str_replace(array('<br>', '<br/>', '<br >', '<br />'), "\n", $html);

		$text = str_replace(array("&emsp;", "&nbsp;"), " ", $text);
		$text = str_replace(array("\t"), "", $text);
		$text = str_replace(array("&rsquo;"), "'", $text);

		return strip_tags($text);
	}
	
	// maintenance function to grab all current records from dynamo
	function get_dynamo_dump(){
		ini_set('memory_limit', '512M');
        $this->_amazon_proxy_setting = $this->AmazonSetting->getAmazonProxy();
		
		$store_ids = array(52,60); // hardcoded for now
		foreach($store_ids as $k => $store_id){
			
			// get all merchants for store_id
			$merchants = $this->db
				->select('cmn.id, cmn.merchant_name')
				->distinct()
				->join($this->_table_crowl_product_list . ' cpl', 'cmn.id=cpl.merchant_name_id')
				->join($this->_table_products . ' p', 'cpl.upc=p.upc_code')
				->join($this->_table_store . ' s', 's.id=p.store_id')
				->where(array('s.id' => $store_id))
				->get($this->_table_crowl_merchant_name . ' cmn')
				->result_array();
				
			$start = 0;
			$end = time();
			// daily price average dump, all dates
			$filters =array(
				array(AmazonDynamoDB::TYPE_STRING => date("Y-m-d", $start)),
				array(AmazonDynamoDB::TYPE_STRING => date("Y-m-d", $end))
			);
			
			$hashKeys = array();// unique
			foreach($merchants as $merchantInfo){
				
				//now setup the violation data
				$products = $this->Violator->getSellerViolations($store_id, $merchantInfo['id'], $start, $end);
				//print_r($products); exit;
				
				// Get the violation data from dynamo
				foreach ($products as $product) {
					//echo "in products loop {$product->id}\n";
					
					// daily price average dump
					$reverseHashKey = $product->upc_code.'#'.$product->marketplace;
					if(empty($hashKeys[$reverseHashKey])){
						$response = $this->amzdb->getDailyAverage($this->_dynamo_daily_price_average, $reverseHashKey, $filters);
						$countRes = $response->body->Count;
						if ($countRes > 0) {
							for ($k=0;$k<$countRes;$k++) {
								$dataRow = $response->body->Items->{$k};
								// filter out existing (get new ones only)
								$existsDPA = $this->db
									->select('upc')
									->where(array('upc' 		=> (string)$product->upc_code,
												  'marketplace'	=> (string)$product->marketplace,
												  'date' 		=> (string)$dataRow->date->S))
									->get($this->_dynamo_daily_price_average_archive) //,null,null,true
									->result_array();
									
								if(empty($existsDPA)){
									$this->db
										->set(array(
											'upc' 			=> (string)$product->upc_code,
											'marketplace' 	=> (string)$product->marketplace,
											'date' 			=> (string)$dataRow->date->S,
											'price_total' 	=> (float)$dataRow->price_total->N,
											'seller_total' 	=> (float)$dataRow->seller_total->N
										))
										->insert($this->_dynamo_daily_price_average_archive);
									//echo "{$reverseHashKey}, ";
									//echo $this->db->last_query();
									echo "o";
								}
								else{
									echo "x";
									continue;
								}
							}
						}
						$hashKeys[$reverseHashKey] = true;
					}
					
					// Get the price trend data from dynamo
					$violatedItems = $this->amzdb->getQueryWithoutRange($this->_dynamo_violations, $product->hashKey);
					//var_dump($violatedItems->body); exit;
			
					$n = $violatedItems->body->Count;
					//echo $n; exit;
					
					for ($i=0; $i<$n; $i++) {
						//echo "in violation loop {$i}\n";
						
						$violation = $violatedItems->body->Items[$i];
						//var_dump($violation); exit;
						if(empty($violation->um->S)){
							echo "empty hashkey ".$violation->um->S."\n"; 
							continue;
						}
						if(empty($violation->dt->N)){
							echo "empty timestamp ".$violation->dt->N."\n"; 
							continue;
						}
						
						$hashKey 	= (string)$violation->um->S;
						$stamp 		= (int)$violation->dt->N;
						$screenshot = (string)$violation->ss->S;
						
						
						// filter out existing (get new ones only)
						$exists = $this->db
							->select('um')
							->where(array('um' => $hashKey,
										  'dt' => $stamp))
							->limit(1)
							->get($this->_dynamo_products_trends)
							->result_array();
						if(!empty($exists)){
							//echo "skip {$merchantInfo['merchant_name']}#{$product->upc_code}, ";
							echo "_";
							continue;
						}
						
						$priceTrendArray = array();
						$priceTrendArray[] = array(
							'HashKeyElement'  => $hashKey,
							'RangeKeyElement' => $stamp
						);
						//print_r($priceTrendArray); 
						$priceTrends = $this->amzdb->batchGetItem($this->_dynamo_products_trends, $priceTrendArray);
						//var_dump($priceTrends); exit;
						
						// Compile all the data
						$m=sizeof($priceTrends);
						//echo $m; exit;
						for ($j=0; $j<$m; $j++) {
							//echo "in priceTrends loop {$j}\n";
							$priceTrend = $priceTrends[$j];
							//var_dump($priceTrend); exit;
							if(empty($priceTrend->um->S)){
								echo "empty hashkey".$priceTrend->um->S."\n"; 
								continue;
							}
							
							$us = explode("#", $priceTrend->um->S);
							$upc = $us[1];
							$prodId = $product->id;
							$tstamp = (int)$priceTrend->dt->N;
							
							$ret = array(
								'um'  => (string)$hashKey,							//hash_key
								'dt'  => (int)$tstamp,								//timestamp
								'ap'  => (float)$priceTrend->ap->N,					//MAP
								'ar'  => (string)$priceTrend->ar->S,				//marketplace
								'il'  => (string)$priceTrend->il->S,				//product_image
								'l'   => (string)$priceTrend->l->S,					//violation_url
								'mil' => (string)$priceTrend->mil->S,				//merchant_logo
								'mpo' => (float)$priceTrend->mpo->N,				//merchant_price
								'msp' => (float)$priceTrend->msp->N,				//merchant_shipping_price
								'mu'  => (string)$priceTrend->mu->S,				//merchant_url
								't'   => (string)$priceTrend->t->S,					//product_title
								'pid' => (int)$prodId,								//product_id
								'upc' => (string)$upc,								//upc_code
								'mid' => (int)$merchantInfo['id'], 					//merchant_id
								'ss'  => (string)$violation->ss->S);				//screenshot
							
							$this->db->insert($this->_dynamo_products_trends, $ret);
							//print_r($ret); exit;
							//echo "$hashKey, ";
							echo ".";
						}
					}
				}
			}
		}
	}// end dynamo_dump

	
	// true to generate a test run and send all emails to dev
	// you might also like to set config.php log_threshold
	function set_test_mode($tf=false){
		$this->_test_violator_notifications = $tf;
		$this->captcha_breaker->is_verbose  = $tf;
	}
	
	/**
	 * Send the notifications to violators enabled by each store.
	 * php crons.php /schedule_cron/violator_notifications
	 */
	function violator_notifications($testmode=false)
	{
		if($testmode){
			echo "We are in test mode: $testmode\n";
			$this->set_test_mode(true);
		}
		
        // get the notifications to send
        $notifications = $this->db
            ->select('vn.*, vnes.notification_frequency, vn.name_from as company')
			->join($this->_table_violator_notification_email_settings.' vnes', 'vn.store_id=vnes.store_id')
            ->where('vnes.notifications_on_off', 'on')
            ->where('vn.active', 1)
            ->group_by('vn.crowl_merchant_name_id')
			->orderBy('rand()')
            ->get($this->_table_violator_notifications.' vn')
            ->result_array();
			
        // send the notifications
        $this->notification_stats = array();
		
		// load all amazon logins
		$this->_amazon_settings_cache = $this->AmazonSetting->getAll();
		
		// TODO: keep a separate cookie for each login credential and store it in _amazon_settings_cache array.
		@unlink('./amazoncookie.txt');
		
        if ( !empty($notifications) ) {
            foreach ($notifications as $notification) {
		        $this->_amazon_proxy_setting = $this->AmazonSetting->getAmazonProxy();//TODO: ($notification['store_id']);
//print_r($this->_amazon_proxy_setting ); exit;
                $this->_send_violator_notification($notification);
				//sleep(1);
            }
        }

        // alert people of failures
        if ( ! empty($this->notification_stats)) {
            foreach ($this->notification_stats as $store_id => $stats) {
                if ($stats->failures > 0) {
                    $this->_send_failure_alert($store_id, $stats);
                }
    		}
        }
        //$this->_violator_notification_repeat(); // DEPRECATED
	}

	protected function _send_failure_alert($store_id, $stats) {
		$stats->store = $this->Store->get_store_info($store_id);
		$stats->merchant_logo = $this->account->get_merchant_thumb($store_id);
		$stats->brand_name = $stats->store['store_name'];
		$stats->headerDate = date('m/d/Y');
		$stats->title = 'Enforcement Notification Failure';

		$msg_txt = $this->load->view('layouts/email/violator_notification_failure_txt', $stats, TRUE);

		$html_content = $this->load->view('layouts/email/violator_notification_failure_html', $stats, TRUE);
		$msg_html = $this->load->view('layouts/email', array('content' => $html_content), TRUE);

		if($this->_test_violator_notifications){
			$to = 'chris@juststicky.com';
		}
		else{
			$to = array();
			//$to[] = $this->Store->get_smtp_email($stats->smtp);
			$to[] = 'support@juststicky.com';
			$to[] = 'chris@juststicky.com';
		}
		send_email($to, 'support@juststicky.com', $stats->title, $msg_html, $msg_txt);
	}

	/**
	 * Generate the HTML in the violator notification email
	 * for a specific notification
	 *
	 * @param array $notification
	 */
	private function _send_violator_notification(array $notification, $last_history = FALSE) {
		log_message('info', "\n==============================================================================");

		// smtp settings are required to send out email
		$settings = $this->Store->get_violator_notification_email_settings($notification['store_id']);
        log_message('info', "notification:".print_r($notification, true)); 
        log_message('info', "settings:".print_r($settings,true));

        if(empty($settings['id'])) {
            log_message('Notice', "Incomplete Settings for store {$notification['store_id']}: ".var_export($settings, true));
			//$this->Store->update_violator_notification($notification['id'], array('active'=>0));
            return;
        }
		
		// minimal merchant data required
		$merchant_details = $this->merchant_products_m->getMerchantDetailsById($notification['crowl_merchant_name_id']);
		if(empty($merchant_details)){
			// todo: delete this notification
			return;
		}
        log_message('info', "merchant_details:".print_r($merchant_details, true)); 
        
        if(@!$this->Marketplace->is_active($merchant_details['marketplace'])){
			log_message('info', "Marketplace is not active - {$merchant_details['marketplace']}");
			$this->Store->update_violator_notification($notification['id'], array('active'=>0));
			return;
        }
        
		if ( $merchant_details['marketplace'] != 'amazon'){
			if( empty($notification['email_to']) ) {
			   log_message('Notice', "Incomplete Contact Info, cannot send violation email: ".var_export($notification, true));
				// set this merchant inactive for this store
				$this->Store->update_violator_notification($notification['id'], array('active'=>0));
				return;
			}
			if(empty($settings['notification_levels'])) {
				log_message('Notice', "Incomplete Settings notification_levels for store {$notification['store_id']}: ".var_export($settings, true));
				return;
			}
		}
		if ( $merchant_details['marketplace'] == 'amazon'){
            if (strpos($merchant_details['merchant_name'], 'Amazoncom')!==false){
                log_message('info', "Special AmazonCom seller, cannot send them a violation notice: ".var_export($merchant_details, true));
                return;
            }
			if ( empty($merchant_details['seller_id']) ) {
                log_message('Notice', "Incomplete Merchant Info, cannot send Amazon violation notice: ".var_export($merchant_details, true));
				return;
		   }
		}
		
		// we only care about merchants who have violations in the last crawl
		$this->data->productData = $this->Violator->getViolatorReport($notification['store_id'], $merchant_details);
		if (empty($this->data->productData)){
			log_message('info', "Merchant {$merchant_details['merchant_name']} is not currently violating MAP.");
			return;
		}
        //log_message('info', print_r($this->data->productData,true)); exit;
		
		// get last notification history
		$last_history = $this->Violator->getLastViolationNotificationHistory($notification['store_id'], $notification['crowl_merchant_name_id']);
		log_message('info', "last_history: ".var_export($last_history, true));
		
		if($last_history and !empty($notification['notification_frequency'])){
			$nfDays = intval($notification['notification_frequency']);
			$next_delivery_date = strtotime('+'.$nfDays.' day', strtotime(substr($last_history['date'], 0, 10)." 00:00:00"));
			if ( $next_delivery_date  > time() ) {
				log_message("info", "limitation policy notification_frequency: {$nfDays} days.  Next delivery date is {$next_delivery_date}.");
				return;
			}
		}
		
		if ( $last_history === FALSE ) {
			$email_level = 1;
			$email_repeat = 0;
		} else {
			$email_level = $last_history['email_level'];
			$email_repeat = $last_history['email_repeat'];
			
			//$email_level_sent_count = $this->Violator->countViolationNotificationHistory($notification['store_id'], $notification['crowl_merchant_name_id'], $email_level);
			if(empty($template[$email_level])){
				$template[$settings['id']][$email_level] = $this->Store->get_violator_email_template($settings['id'], $email_level);
			}
			$no_of_days_to_repeat = (int)$template[$settings['id']][$email_level]['no_of_days_to_repeat'];
			if($email_repeat >= $no_of_days_to_repeat)
			{
				log_message("info", "limitation policy no_of_days_to_repeat: {$no_of_days_to_repeat} days, email_repeat {$email_repeat}. ");
				
				// finished all notifications?
				$reset = false;
				if(isset($settings['notification_levels']) && ($email_level >= $settings['notification_levels'])) {
					//check if we need to reset count back to 1 after reaching top limit
					// TODO: sanity checking.  If seller doesn't respond then how would this ever end?
					if(!empty($settings['reset_after_reaching']) && $settings['reset_after_reaching'] == 1) {
						$reset = true;
						log_message("info", "resetting to email level {$email_level}.");
					}
					else{
						log_message("info", "Finished all notifications.  Email_level {$email_level}.");
						return;
					}
				}
				
				// increase the email level if ready
				$email_level = ($reset)? 1 : ($email_level + 1);
				$email_repeat = 0;
				log_message("info", "increasing to email level {$email_level}.");
			}
		}
		
		// assert use of the highest level template if counter incremented too high
		if(isset($settings['notification_levels']) && ($email_level > $settings['notification_levels'])) {
			$email_level = $settings['notification_levels'];
		}
		
		
		// get the letter template to use
		// if we had templates set, use those
		if(!empty($template[$settings['id']][$email_level])){
			$emailTemplate = $template[$settings['id']][$email_level]; }
		else{
			$emailTemplate = $this->Store->get_violator_email_template($settings['id'], $email_level); }
			
		if( empty($emailTemplate) ){
			log_message('info', "Incomplete Template for email: ".var_export($settings, true));
			return;
		}
		
		// adhere to limitation policies (enforcement/email_settings)
		if ( $last_history !== FALSE ) {
			$NadDays = !empty($emailTemplate['notify_after_days']) ? $emailTemplate['notify_after_days'] : 0;
			if ( strtotime('+'.$NadDays.' day', strtotime(substr($last_history['date'], 0, 10)." 00:00:00")) > time()) {
				log_message("info", "limitation policy notify_after_days: {$NadDays} days");
				return;
			}
		}
		
		// TODO: who cares?
		//$history = $this->Store->get_violator_notifications_history_by_merchant_name_id($notification['crowl_merchant_name_id'], $notification['store_id']);
		//if(empty($history)) {
		//	log_message('info', "No history available yet ", true);
		//	return;
		//}
		
		//
		// Everything is configured correctly and a notification is scheduled
		//

		// set the variables used in the templates
		$this->data->merchant = trim($merchant_details['original_name']);
		$this->data->name_to = (!empty($notification['name_to'])) ? $notification['name_to'] : '';
		$this->data->subject = $emailTemplate['subject'] == '' ? 'MAP Violation Warning - Email #'. $email_level : $emailTemplate['subject'];
		
		//TODO: fetch image data here
		$this->data->attachment = empty($img) ? array() : array($img);
		
		$smtpInfo = array(
			'host' => $settings['smtp_host'],
			'port' => $settings['smtp_port'],
			'use_ssl' => $settings['smtp_ssl'],
			'use_tls' => $settings['smtp_tls'],
			'username' => $settings['smtp_username'],
			'password' => $settings['smtp_password']
		);

		$this->load->library('SMTP_auth', $smtpInfo, 'smtp');

		if (empty($this->notification_stats[$notification['store_id']])) {
			$this->notification_stats[$notification['store_id']] = new stdClass();
			$stats =& $this->notification_stats[$notification['store_id']];
			$stats->smtp = $smtpInfo;
			$stats->exceptions = array();
			$stats->failed = array();
			$stats->failures = 0;
			$stats->attempts = 0;
		}
		$stats =& $this->notification_stats[$notification['store_id']];
		$stats->exception_recorded = false;
		$stats->attempts++;
		$stats->success = FALSE;
		
		/**
		 * send violation notification to merchant
		 */
		
		if (!empty($notification['email_to']) ){
			if($this->_test_violator_notifications){
				$notification['email_to'] = 'chris@juststicky.com';
			}
			$this->_notify_seller_by_email($merchant_details, $notification, $settings, $emailTemplate);
		}
		elseif( $merchant_details['marketplace'] == 'amazon'){
			$this->_notify_seller_by_amazon($merchant_details, $notification, $settings, $emailTemplate);
		}
		else {
			log_message('error', 'unknown recipient type: '.print_r($merchant_details,true) );
		}
		// finished sending violation message to seller
		
		if ( $stats->success === TRUE )
		{
			$email_repeat++;
			$this->_notify_store_admin($merchant_details, $notification, $settings, $emailTemplate);
			
			$history = array(
				"store_id"					=> $notification['store_id'],
				"crowl_merchant_name_id"	=> $notification['crowl_merchant_name_id'],
				"email_level"				=> $email_level,
				"email_repeat"				=> $email_repeat,
				"date"						=> date('Y-m-d H:i:s'),
				"email_from"				=> $notification['email_from'],
				"name_from"					=> $notification['name_from'],
				"email_to"					=> $notification['email_to'],
				"name_to"					=> $notification['name_to'],
				"phone"						=> $notification['phone'],
				"title"						=> $this->data->subject,
				"regarding"					=> $this->data->text
			);			
            log_message("info", "success == true " . print_r($history,true));
			if(!$this->_test_violator_notifications){
				$this->db->insert($this->_table_violator_notifications_history, $history);
			}
		}
		else { 
			$stats->failures++;
			$stats->failed[] = $notification;
			if ( !$stats->exception_recorded ) {
				$stats->exceptions[] = NULL;
			}
		}
		
		unset($this->smtp);
		//TODO: unset $this->data, too?
	}

	function test_amazon_contact( ) {
		$this->set_test_mode(true);
		$merchant = array (
			'id' => '12287',
			'merchant_name' => 'Susans Bargain Place',
			'original_name' => 'Susan\'s Bargain Place',
			'marketplace' => 'amazon',
			'seller_id' => 'A3V5HM82N49M6',
		);
		//TODO: use Fubar Industries, cfortune, get seller_id.
		$credentials = array (
			'id' => '39',
			'store_id' => '612',
			'email' => 'renee@n-icorp.com',
			'password' => 'TJsf5qn$D7M/gXg+lbQ=+-N&Aa0R',
			'login_failed' => '0',
			'message' => '',
			'marketplace' => 'amazon',
		);
		$violation_message = "Test";
		echo $this->_send_amazon_contact( $merchant, $credentials, $violation_message );
	}
	
	
	function _send_amazon_contact( $merchant, $credentials, $violation_message ) {
		$seller_id = $merchant['seller_id']; 
		$merchant_url = "http://www.amazon.com/gp/help/seller/home.html?seller=".$seller_id;//echo "merchant_url: $merchant_url\n";		
		
		$html_contents = $this->HTTP_fetch($merchant_url, 'amazon', 'GET');
		$this->saveAmazonCommentFormHtml('seller_home', $html_contents);
		
		// parse A tag and get merchant ID
		if ( !preg_match_all('/<a href=.*?<\/a>/is', $html_contents, $atags) ) 
			return 'Failed to find A-tags!';
		//echo "atags\n"; print_r($atags); 
		$contact_page_url = "";
		for ( $i = 0; $i < count($atags[0]); $i ++ ) {
			$text = trim( strip_tags($atags[0][$i]) );
			//echo "text: $text\n";
			if ( $text == 'Contact the seller' ) {
				$str = str_replace(array("\r","\n"), '', $atags[0][$i]);
				if ( preg_match_all('/<a[^>]+href=([\'"])(.+?)\1[^>]*>/i', $str, $result) ) {
					//echo "hrefs\n"; print_r($result); exit;
					$contact_page_url = $result[2][0];
					break;
				}
			}
		}
		
		if ( $contact_page_url == '' ) 
			return 'Failed to find a "Contact the seller" URL!';

		$contact_page_url = str_replace("&amp;", "&", $contact_page_url);
		$temp = explode("?", $contact_page_url);
		$temp = explode("&", $temp[1]);
		$parameters = array();
		for ( $i = 0; $i < count($temp); $i ++ ) {
			$p = explode("=", $temp[$i]);
			$parameters[$p[0]] = isset($p[1]) ? $p[1] : "";
		}

		if ( !isset($parameters['marketplaceID']) || !isset($parameters['sellerID']) ) 
			return "Failed to find marketplaceID or sellerID from URL: ".$merchant_url;
		
		$contact_page_url = "https://www.amazon.com/gp/help/contact/contact.html?ie=UTF8&asin=&isCBA=&marketplaceID=".$parameters['marketplaceID']."&orderID=&ref_=aag_m_fi&sellerID=".$parameters['sellerID'];
		$signin_page_url = "https://www.amazon.com/ap/signin?_encoding=UTF8&openid.assoc_handle=usflex&openid.claimed_id=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select&openid.identity=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select&openid.mode=checkid_setup&openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0&openid.ns.pape=http%3A%2F%2Fspecs.openid.net%2Fextensions%2Fpape%2F1.0&openid.pape.max_auth_age=900&openid.return_to=".urlencode($contact_page_url);
	
		$html_contents = $this->HTTP_fetch($signin_page_url, 'amazon');
		$this->saveAmazonCommentFormHtml('signin_page', $html_contents);

		/*******************************************
		 *
		 * Signin Form
		 *
		 *******************************************/

		if ( !preg_match('/<form name="signIn".*?<\/form>/is', $html_contents, $form) ) 
			return "Failed to find a signin form";
		
		$form = $form[0]; //echo "form: $form\n";
		
		// find the action of the login form
		if (!preg_match('/action="([^"]+)"/i', $form, $action)) 
			return 'Failed to find login form url';

		$signin_form_action = $action[1]; // this is our new post url
		//echo "signin_form_action: $signin_form_action\n";

		$postFields = $this->extract_hidden_inputs_from_html_form($form);
		
		// add our login values
		$postFields['email']    = $credentials['email'];
		$postFields['create']   = 0;
		$postFields['password'] = my_decrypt($credentials['password']);
		//print_r($postFields); exit;

		$html_contents = $this->HTTP_fetch($signin_form_action, 'amazon', 'POST', $postFields, $signin_page_url);
		$this->saveAmazonCommentFormHtml('signin_form', $html_contents);
		if(strpos($html_contents, 'we limit the number of e-mails sent between buyers and sellers per day')!==false){ // There were > 20 msgs to the same seller
			return "Amazon has blocked us temporarily";
		}

		/*******************************************
		 *
		 * Contact Option Form
		 *
		 *******************************************/
		
		if ( !preg_match('/<form action="\/gp\/help\/contact\/contact.html".*?<\/form>/is', $html_contents, $form) ) {
			$message = 'Failed to find a contact form';
			//$this->AmazonSetting->updateStatus($credentials['id'], 1, $message);
			return $message;
		}

		$form = $form[0];

		// find the action of the contact form
		if (!preg_match('/action="([^"]+)"/i', $form, $action)) {
			$message = 'Failed to find contact form url';
			return $message;
		}

		$contact_form_action = $action[1]; // this is our new post url
		if ( substr($contact_form_action, 0, 4) != 'http' ) $contact_form_action = "https://www.amazon.com".$contact_form_action."?";

		// find all hidden fields
		// add our contact option values
		$formFields = $this->extract_hidden_inputs_from_html_form($form);
		$formFields['assistanceType']   = "asin";
		$formFields['subject']			= 5;
		$formFields['writeMessageButton']="Write message";

		$contact_form_action .= http_build_query($formFields);  //echo "contact_form_action: $contact_form_action\n";
		
		$html_contents = $this->HTTP_fetch($contact_form_action, 'amazon', 'GET');
		$this->saveAmazonCommentFormHtml('contact_form', $html_contents);
		if ( !preg_match('/<form id="writeMessageForm".*?<\/form>/is', $html_contents, $form) ) {
			$message = 'Failed to find a write form';
			return $message;
		}

		/*******************************************
		 *
		 * Write Message Form
		 *
		 *******************************************/

		$form = $form[0];

		// find the action of the contact form
		if (!preg_match('/action="([^"]+)"/i', $form, $action)) {
			$message = 'Failed to find write form url';
			return $message;
		}

		$write_form_action = $action[1]; // this is our new post url
		if ( substr($write_form_action, 0, 4) != 'http' ) $write_form_action = "https://www.amazon.com/gp/help/contact/contact.html".$write_form_action;
		
		// add our contact option values
		$form_fields = $this->extract_hidden_inputs_from_html_form($form);
		$form_fields['sendEmailButton']	= "Send e-mail";
		$form_fields['commMgrComments']  = $violation_message;
		
		if($this->_test_violator_notifications){
			$message = "We got to the final step in amazon POST, {$credentials['email']} {$this->_amazon_connection_ctr}.  Looks OK so far :) <br>\n";
			//$message .= $violation_message . "<hr><br>\n<br>\n";
			return $message;
		}
		
		$html_contents = $this->HTTP_fetch($write_form_action, 'amazon', 'POST', $form_fields);
		$this->saveAmazonCommentFormHtml('write_form', $html_contents);
	
		/*******************************************
		 *
		 * Check result
		 *
		 *******************************************/
		if ( preg_match('/<div class="message success.*?<\/div>/is', $html_contents, $divSucces) ) {
			//echo "<h1>Success</h1>";
		} else {
			$message = 'Failed to send amazon message to violator.';
			return $message;
		}
		
		return TRUE;
		
	}

	// find all hidden fields which we need to send with our login, this includes security tokens
	public function extract_hidden_inputs_from_html_form($form, $postFields=array())
	{
		$count = preg_match_all('/<input type="hidden"\s*name="([^"]*)"\s*value="([^"]*)"/i', $form, $hiddenFields);
		//print_r($hiddenFields); exit;
		
		// turn the hidden fields into an array
		for ($i = 0; $i < $count; ++$i) {
			$postFields[$hiddenFields[1][$i]] = $hiddenFields[2][$i];
		}
		unset($postFields['ue_back']);
		
		return $postFields;
	}
	
	// DEV: save contents to file for examination
	private function saveAmazonCommentFormHtml($page, $html_contents){
		//return false;
		$date = date('Y-m-d H:i:s');
		$html_contents = "$page : ".$date."<br/>\n".$html_contents;
		file_put_contents( $this->config->item('file_root') . "test/amazon.forms/{$date}-{$page}.html", $html_contents);//, FILE_APPEND 
	}
	
	
			
	private function _notify_seller_by_email($merchant_details, $notification, $settings, $emailTemplate){
		
		$stats =& $this->notification_stats[$notification['store_id']];
		
		$to = $notification['email_to'];
		if ( !empty($notification['name_to']) ) {
			$to = array($notification['email_to'] => $notification['name_to']);
		}

		$from = $settings['email_from'];
		if ( !empty($notification['name_from']) ) {
			$from = array($from => $notification['name_from']);
		}
		
		$searches = array(
			'#SellerName',
			'#CompanyName',
			'#ContactName',
			'#Phone',
			'#NameTo',
			'#EmailTo',
			'#Date',
			'#Evidence'
		);
		$replacements = array(
			$this->data->merchant,
			$settings['company'],
			$notification['name_from'],
			$notification['phone'],
			$notification['name_to'],
			$notification['email_to'],
			date('m/d/Y'),
			$this->_get_evidence_table()
		);

		$html = str_replace($searches, $replacements, $emailTemplate['known_seller_html_body']);
		$this->data->text = $this->_get_text_from_html_contents($html);
		
		try {
			//$stats->success = send_smtp( //doesn't work.. why???
            $stats->success = TRUE;
			send_smtp(
				$this->smtp,
				$to,
				$this->data->subject,
				$html,
				$this->data->text,
				$from,
				$this->data->attachment
			);
		}
		catch (phpmailerException $e) {
			$stats->exceptions[] = $e->getMessage();
			$stats->exception_recorded = TRUE;
            $stats->success = FALSE;
			log_message('error', 'phpMailerException sending violator notification for merchant ' . $notification['crowl_merchant_name_id'] . ' of store ' . $notification['store_id'] . ' to ' . $notification['email_to'] . ' from . ' . $notification['email_from'] . ': ' . $e->getMessage());
		}
		catch (Exception $e) {
			log_message('error', 'Exception sending violator notification for merchant ' . $notification['crowl_merchant_name_id'] . ' of store ' . $notification['store_id'] . ' to ' . $notification['email_to'] . ' from . ' . $notification['email_from'] . ': ' . $e->getMessage());
            $stats->success = FALSE;
		}
	}
	
	
	private function _notify_seller_by_amazon($merchant_details, $notification, $settings, $emailTemplate){
		
		$store_id = $notification['store_id'];
		$stats =& $this->notification_stats[$store_id];
		
		if($this->_amazon_connection_ctr == 20){
			@array_shift($this->_amazon_settings_cache[$store_id]);
			$this->_amazon_connection_ctr = 0;
			@unlink('./amazoncookie.txt');
		}
		
		if(empty($this->_amazon_settings_cache[$store_id][0])){
			$stats->exceptions[] = 'Reached our daily limit of Amazon messages.';
			$stats->exception_recorded = TRUE;
			return;
		}
		
		$credentials = $this->_amazon_settings_cache[$store_id][0];
		if ( !empty($credentials['email']) && !empty($credentials['password']) )
		{
			$this->_amazon_connection_ctr++;
			
			$searches = array(
				'#SellerName',
				'#CompanyName',
				'#ContactName',
				'#Phone',
				'#NameTo',
				'#EmailTo',
				'#Date',
				'#Evidence'
			);
			$replacements = array(
				$this->data->merchant,
				$settings['company'],
				$notification['name_from'],
				$notification['phone'],
				$notification['name_to'],
				(isset($notification['email_to'])) ? $notification['email_to'] : 'amazon seller',
				date('m/d/Y'),
				$this->_get_evidence_table(false) 
			);	
			$templateName = (isset($notification['name_to'])) ? 'known_seller_html_body' : 'unknown_seller_html_body';
			$html = str_replace($searches, $replacements, $emailTemplate[$templateName]);
			$html = html_entity_decode($html);
			$this->data->text = $this->_get_text_from_html_contents($html);
			$this->data->text = str_replace('Evidence of this violation is included as Attachment 1 to this letter.',
											'Evidence of this violation is included below.',
											$this->data->text);
			
			// cut up large messages and send in parts
			$text_fragments = $this->_cut_text_into_fragments($this->data->text, 4000);
			foreach($text_fragments as $textf){
				$return = $this->_send_amazon_contact($merchant_details, $credentials, $textf);
				if ( $stats->success !== TRUE ) {
					$stats->success = $return;
					break;
				}
				else{
					$stats->success = true;
				}
				break; //send only the first fragment.  Amazon won't accept more than one per day per seller.
					  // TODO: rotate logins here in order to deliver all message chunks
			}
            
				
			if ( $stats->success !== TRUE ) {
				// DEBUG:
				$output =  "\n -------------------------------------------------------------------------\n ";
				$output .= date('Y-m-d H:i:s');
				$output .= "\n settings: \n";
				$output .= print_r($settings, true);
				$output .= "\n merchant_details:";
				$output .= print_r($merchant_details, true);
				$output .= "\n Replacements:";
				$output .= print_r($replacements, true);
				$output .= "\n amazon_setting:";
				$output .= print_r($credentials, true);
				$output .= "\n text: \n";
				$output .= $this->data->text ."\n";
				//echo $output;
				//file_put_contents( $this->config->item('file_root') . "output.vn.errs.txt", $output, FILE_APPEND );
				log_message('error', 'Amazon contact failed: '.$output);
				//$output = "";
				
				$stats->exceptions[] = $stats->success;
				$stats->exception_recorded = TRUE;
				log_message('error', 'Failed contact to amazon for merchant ' . $notification['crowl_merchant_name_id']
							. ' of store ' . $store_id . ': ' . $stats->success);

				if($stats->success == 'Amazon CAPTCHA'){
					$stats->exceptions[] = 'Amazon is blocking '.$credentials['email'].' login. Please login manually and pass the CAPTCHA test.';
					$this->AmazonSetting->updateStatus($credentials['id'], 1, 'Please login manually and pass the CAPTCHA test.'); //NOK
					@array_shift($this->_amazon_settings_cache[$store_id]);
					@unlink('./amazoncookie.txt');
					//if(empty($this->_amazon_settings_cache[$store_id])){
					//	$this->_amazon_has_blocked_us[$store_id] = true;
					//}
				}
			}
			else{
				$this->AmazonSetting->updateStatus($credentials['id']); //OK
			}
		} else {
			log_message('error', "Incomplete Amazon Account for store ".$store_id);
		}
	}
	
	public function testcutter(){
		$testtxt = "Hogue, Inc. 06/09/2015"; // or get file
		$ret = $this->_cut_text_into_fragments($testtxt);
		print_r($ret); exit;
	}
	
	private function _cut_text_into_fragments($text, $maxlen = 4000){
		$return = array();
		
		$text = str_replace("\n\n\n", "\n\n", $text);
		$text = str_replace("\n\n\n", "\n\n", $text);
		$text = str_replace("\n\n\n", "\n\n", $text);
		
		$text = trim($text);
		$header = "=== Part %d of %d ===\n\n";
		$maxlen = $maxlen - strlen($header) - 10;
		$sep = "----------------------------------------------------------------------";
		$frags = explode($sep, $text);
		$cnt = count($frags);
		$retIdx = 0;
		$tmplen = 0;
		for($i=0; $i<$cnt; $i++){
			$frags[$i] = ($i==0) ? $frags[0] : $this->_amazon_prep_listing($frags[$i]);
			$tmplen += strlen($frags[$i]) + 1;
			if($tmplen > $maxlen){
				$retIdx++;
				$tmplen = 0;
			}
			$delim = ($i>=1) ? "\n" : "";
			@$return[$retIdx] .= $delim . $frags[$i];
		}
		
		$cnt = count($return);
		if($cnt > 1){
			for($i=0; $i<$cnt; $i++){
				$return[$i] = sprintf($header, ($i+1), $cnt) . $return[$i];
			}
		}
		
		return $return;
	}
	
	private function _amazon_prep_listing($frag){
		$lines = explode("\n", $frag);
		
		/*
		Marketplace: Amazon
		Date:        06/09/2015
		Time:        04:27 AM
		Name:         Ruger SP101 Rubber Monogrip Black
		UPC:         743108810002
		Retail:      $26.95
		MAP:         $21.56
		Price:       $21.16
		URL:         http://www.amazon.com/Hogue-Rubber-Ruger-SP101-Monogrip/dp/B001AZJ08C/ref=sr_1_1/177-8730651-5300710?ie=UTF8&sr=8-1&keywords=743108810002
		*/

		$output = '';
		foreach($lines as $line){
			$line = trim($line);
			if(empty($line)) continue;
			
			$line = str_replace(":      ", ":", $line);
			
			if(strpos($line, 'Marketplace:') !== false){
				continue;
			}
			elseif(strpos($line, 'UPC:') !== false){
				continue;
			}
			elseif(strpos($line, 'Retail:') !== false){
				continue;
			}
			elseif(strpos($line, 'URL:') !== false){
				$regex = '#/dp/([A-Z0-9]*)/ref#';
				preg_match($regex, $line, $matches);
				$line = "ASIN:  " . $matches[1];
			}
			elseif(strpos($line, 'Date:') !== false){
				$date = rtrim($line);
				continue;
			}
			elseif(strpos($line, 'Time:') !== false){
				$time = str_replace("Time:", " ", $line);
				$line = $date . $time;
			}
			$output .= $line . "\n";
		}
		return $output;
	
		/*
		Date:  06/09/2015 04:27 AM
		Name:  Ruger SP101 Rubber Monogrip Black
		MAP:   $21.56
		Price: $21.16
		ASIN:  B001AZJ08C
		*/
	}

	//backup
	//private function _cut_text_into_fragments($text, $maxlen = 4000){
	//	$return = array();
	//	$text = trim($text);
	//	$header = "=== Part %d of %d ===\n\n";
	//	$maxlen = $maxlen - strlen($header) - 10;
	//	$sep = "----------------------------------------------------------------------";
	//	$frags = explode($sep, $text);
	//	$cnt = count($frags);
	//	$retIdx = 0;
	//	$tmplen = 0;
	//	for($i=0; $i<$cnt; $i++){
	//		$tmplen += strlen($frags[$i]) + strlen($sep) + 1;
	//		if($tmplen > $maxlen){
	//			$retIdx++;
	//			$tmplen = 0;
	//		}
	//		$delim = ($i>=1) ? "$sep\n" : "";
	//		@$return[$retIdx] .= $delim . $frags[$i];
	//	}
	//	
	//	$cnt = count($return);
	//	if($cnt > 1){
	//		for($i=0; $i<$cnt; $i++){
	//			$return[$i] = sprintf($header, ($i+1), $cnt) . $return[$i];
	//		}
	//	}
	//	
	//	return $return;
	//}
	
	
    /** send email to store owner */
	private function _notify_store_admin($merchant_details, $notification, $settings, $emailTemplate){
		/**
		 * send email to store owner
		 */
		$from = array($settings['email_from'] => $settings['company']);
		if($this->_test_violator_notifications){
			$to = 'chris@juststicky.com';
		}
		else{
			$to = array(
				$settings['email_from']
				//,'chris@juststicky.com'
			);
		}
		
		
		$searches = array(
			'#SellerName',
			'#CompanyName',
			'#ContactName',
			'#Phone',
			'#NameTo',
			'#EmailTo',
			'#Date',
			'#Evidence'
		);
		$replacements = array(
			$this->data->merchant,
			$settings['company'],
			$settings['name_from'],
			$settings['phone'],
			$settings['name_to'],
			$settings['email_to'],
			date('m/d/Y'),
			$this->_get_evidence_table()
		);	
		$html = str_replace($searches, $replacements, $emailTemplate['unknown_seller_html_body']);
		$text = $this->_get_text_from_html_contents($html);
		
		try {
			send_smtp(
				$this->smtp,
				$to,
				$this->data->subject,
				$html,
				$text,
				$from,
				$this->data->attachment
			);
		}
		catch (phpmailerException $e) {
			log_message('error', 'phpMailerException sending admin notification for store ' . $settings['store_id']
						. ' to ' . $settings['email_to']
						. ' from ' . $settings['email_from']
						. ': ' . $e->getMessage());
		}
	}

	// get the evidence table
	private function _get_evidence_table($html=true){
		$evidence_view = $this->_controller . '/_violator_notification_email';
		$evidence_view .= ($html <> true) ? '_txt' : '';
		$this->data->evidence = $this->load->view($evidence_view, $this->data, TRUE);
		if($html){
			$eTable = '<table width="650" border="0" cellspacing="0" cellpadding="0" style="margin: 50px;"><tr id="evidence"><td align="center">'.$this->data->evidence.'</td></tr></table>';
		}
		else{
			$eTable = $this->data->evidence;
		}
		return $eTable;
	}

    // our own little web client
	function HTTP_fetch($url, $marketplace, $action='GET', $postfields=array(), $referer='', $recursion_count=1)
	{
		$msg = "HTTP_fetch ///////////////////////////////////////////////////////////////////
			url: {$url}
			marketplace: {$marketplace}
			action: {$action}
			postfields: ".var_export($postfields,true)."
			referer: {$referer}
			recursion_count: {$recursion_count}
			//////////////////////////////////////////////////////////////////////////////
		";
		log_message('info', $msg);
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_COOKIEJAR, "./{$marketplace}cookie.txt");
		curl_setopt($ch, CURLOPT_COOKIEFILE, "./{$marketplace}cookie.txt");
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.13) Gecko/20101206 Ubuntu/10.10 (maverick) Firefox/3.6.13');
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, "");     // handle all encodings
		
		if(!empty($referer)){
			curl_setopt($ch, CURLOPT_REFERER, $referer);
		}
		if(!empty($postfields)){
			$post = http_build_query($postfields);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		else{
			curl_setopt($ch, CURLOPT_POST, FALSE);
		}
		
		if ( !empty($this->_amazon_proxy_setting) ) {
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
			curl_setopt($ch, CURLOPT_PROXY, $this->_amazon_proxy_setting['proxy_address']);
			if ( !empty($this->_amazon_proxy_setting['proxy_port'] ) ){
				curl_setopt($ch, CURLOPT_PROXYPORT, $this->_amazon_proxy_setting['proxy_port']);
			}
			if ( $this->_amazon_proxy_setting['proxy_user']!='' ) {
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->_amazon_proxy_setting['proxy_user'].":".$this->_amazon_proxy_setting['proxy_password']);
			}
		}
		
		$page = curl_exec($ch);
		
		/* status reporting*/
		$err     = curl_errno( $ch );
		$errmsg  = curl_error( $ch );
		$header  = curl_getinfo( $ch );
		$header['errno']   = $err;
		$header['errmsg']  = $errmsg;
		//$header['content'] = $page;
		log_message('info', 'curl status: '.print_r($header, true));
		
		@curl_close($ch);
		
		// CAPTCHA parsing and recursive re-attempt
		if( (strpos($page, 'Captcha=true')!==false) and $this->captcha_enabled )
		{
			$this->saveAmazonCommentFormHtml('captcha-form'.$recursion_count, $page);
			
			if($recursion_count > 1){
				$this->captcha_breaker_failed($this->captcha_answer['captcha']);
			}
			if($recursion_count <= 3){
				// parse img tag and get captcha URL
				if ( !preg_match_all('/<img src=.*?\/>/is', $page, $atags) ) {
					log_message('error', 'It is a captcha form but we failed to find any image tags.');
				}
				else{
					$captcha_url = "";
					for ( $i = 0; $i < count($atags[0]); $i ++ ) {
						if ( strpos($atags[0][$i], 'opfcaptcha')!==false ) {
							$str = str_replace(array("\r","\n"), '', $atags[0][$i]);
							if ( preg_match_all('/<img[^>]+src=([\'"])(.+?)\1[^>]*>/i', $str, $result) ) {
								$captcha_url = $result[2][0];
								break;
							}
						}
					}
					if (empty($captcha_url)){
						log_message('error', 'It is a captcha form but we failed to find a captcha image url.');
					}
					else{
						$captcha_text = $this->solve_captcha($captcha_url);
						if($captcha_text){
							$postfields_new = $this->extract_hidden_inputs_from_html_form($page, $postfields);
							$postfields_new['guess'] = $captcha_text;
							if(!empty($postfields['password'])) $postfields_new['password'] = $postfields['password'];
							$recursion_count++;
							$page = $this->HTTP_fetch($url, $marketplace, $action, $postfields_new, $referer, $recursion_count);
						}
					}
				}
			}
		}

		//$tidy_conf = array('show-errors' => 0, 'hide-comments'=>1, 'wrap'=>0);
		//$page = tidy_repair_string($page, $tidy_conf);
			
        return $page;
    }

	function check_captcha_balance(){
		log_message('info', "Your balance is {$this->captcha_breaker->balance} US cents.");
		if($this->captcha_breaker->balance > 0){
			$this->captcha_enabled = true;
		}
		else{
			$this->captcha_enabled = false;
		}
	}
	
	// Report an incorrectly solved CAPTCHA.
	// Make sure the CAPTCHA was in fact incorrectly solved! (how? all we know is that amazon didn't accept it)
	public function captcha_breaker_failed($captcha){
		log_message('info', 'captcha_breaker_failed: '.var_export($captcha,true));
		$this->captcha_breaker->report($captcha['captcha']);
	}
	
	// "abstract" method to wrap implementation method
	public function solve_captcha($captcha_url){
		return $this->death_by_captcha($captcha_url);
	}
	
	// implement Death-By-Captcha API
	public function death_by_captcha($captcha_url){
		
		if ($this->captcha_answer = $this->captcha_breaker->upload($captcha_url)) {
			log_message('info', "CAPTCHA  uploaded: ".var_export($this->captcha_answer,true)."\n URL: {$captcha_url}");
	
			sleep(DeathByCaptcha_Client::DEFAULT_TIMEOUT);
	
			// Poll for CAPTCHA text:
			if ($text = $this->captcha_breaker->get_text($this->captcha_answer['captcha'])) {
				$this->check_captcha_balance();
				log_message('info', "CAPTCHA {$this->captcha_answer['captcha']} solved: {$text}");
				return $text;
			}
			log_message('error', "CAPTCHA text not returned");
			return false;
		}
		log_message('error', "CAPTCHA not uploaded");
		return false;
	}
	
	public function dbc_test($captcha_url){
		
	}

	// DEPRECATED
	//function _violator_notification_repeat() {
	//	return null;// disable this
	//	$notification_histories = $this->db
	//		->where("is_exit", 0)
	//		->order_by("date")
	//		->get($this->_table_violator_notifications_history)
	//		->result_array();
	//	
	//	for ( $i = 0; $i < count($notification_histories); $i ++ ) {
	//		$history = $notification_histories[$i];
	//		
	//		// skip amazon repeats
	//		// TODO: read amazon error messages from html for temporary/permanent failures.
	//		$is_amazon = false;
	//		$merchant = $this->merchant_products_m->getMerchantDetailsById($history['crowl_merchant_name_id']);
	//		if (isset($merchant[0]['marketplace']) and $merchant[0]['marketplace']=='amazon')
	//			$is_amazon = true;
	//			
	//		$notification = $this->Violator->getViolatorNotification($history['store_id'], $history['crowl_merchant_name_id']);
	//		if ( $notification === FALSE || $is_amazon) {
	//			$this->Store->update_violator_notifications_history($last_history['id'], array("is_exit"=>1));
	//			continue;
	//		}
	//		
	//		$this->_send_violator_notification($notification, $history);
	//	}
	//}
}