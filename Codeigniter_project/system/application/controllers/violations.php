<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//xdebug_start_trace();

require_once('report_types.php');
class Violations extends Report_types{

	private $countProduct,$countViolation;

	function __construct(){
		parent::__construct();

		$this->load->model('chart_m', 'Chart');
		$this->load->model('crawl_data_m', 'Crawl');
		$this->load->model('marketplace_m', 'Marketplace');
		$this->load->model("merchant_products_m");
		$this->load->model("Users_m", 'User');
		$this->load->model("Products_m", 'Product');
		$this->load->model("Report_m", 'Report');
		$this->load->model("store_m", 'Store');
		$this->load->model("violations_m", "Violations");

		$this->_view = $this->_controller . '/index';

		$this->_default_report();
	}

	private function _default_report(){
		$this->data->report_name = '';
		$this->data->report_type = $this->data->my = 'pricingviolation';
		$this->data->icon = 'ico-report';
		$this->data->widget = 'mv-report';
		$this->data->displayBookmark = true;

		//almost all of the controller functions use the same basic data
		$this->data->report_chart = NULL;
		$this->data->Data = NULL;
		$this->data->graphDataType = 'chart';
		$this->data->products = $this->data->productNames = $this->data->merchants = $this->data->markets = $this->data->proMerchants = array();
		$this->data->markertArr = getMarketArray();
		$this->data->marketplaceArr = getMarketplaceArray();
		$this->data->retailerArr = getRetailerArray(false,$this->store_id);
		$this->data->all_markets = (boolean)$this->input->post('all_markets');
		$this->data->all_retailers = ((boolean)$this->input->post('all_retailers') AND ! empty($this->subscriber_retailer_addons));
		$this->data->show_comparison = (boolean)$this->input->post('show_comparison');

		$this->submit = $this->input->post('formSubmit');
		$this->data->report_where = array();
		if($this->submit){
			$this->data->report = $this->input->post('report');
			$this->data->date_from = $this->input->post('date_from');
			$this->data->date_to = $this->input->post('date_to');
			$this->data->time_frame = $this->input->post('time_frame');
			$this->data->merchants = $this->input->post('merchants');
			$this->data->markets = $this->input->post('markets');
			$this->data->flagDates24 = ($this->data->date_from == $this->data->date_to) ? true: false;
			$this->data->report_chart = 'scatter';
			$this->data->graphImageName = '';

			$this->data->productNames = array();
			if ($this->input->post('product_name')){
				$this->data->productNames = $this->input->post('product_name');
			}
			$this->data->products = array();
			if ($this->input->post('products')){
				$this->data->products = $this->input->post('products');
			}
			elseif ($this->input->post('group_id')){
				$this->data->selected_group_id = $this->input->post('group_id');
				$this->data->product_groups = $this->Product->getGroups($this->store_id);
				$group_prods = $this->Product->getProductsByGroupId((int)$this->input->post('group_id'));
				if ( ! empty($group_prods)){
					foreach ($group_prods as $prod){
						$prod = $this->Product->getProductsByID($this->store_id, $prod['product_id']);
						$this->data->products[] = $prod[0]['id'];
						$this->data->productNames[] = $prod[0]['title'];
					}
				}
			}
		}

		$this->data->noRecord = $this->config->item('no_record');
//log_message('info', 'violations data: '. print_r($this->data,true));
		$this->_prep_report();
	}

	protected function _prep_report(){
		if ($this->submit){
			if( ! empty($this->data->products[0])){
				$this->data->competitor_store_id = false;
				if ($this->data->report === 'bycompetition') { // this is a competitor's product
					$this->data->competitor_products = $this->Product->getProductsById(null, $this->data->products);
					$this->data->competitor_store_id = array();
					for($i = 0, $n = count($this->data->competitor_products); $i < $n; $i++){
						$this->data->competitor_store_id[] = $this->data->competitor_products[$i]['store_id'];
					}
					$this->data->competitor_store_id = array_unique($this->data->competitor_store_id);
					$this->data->proMerchants = getProductMerchant($this->data->competitor_store_id);

					if($this->data->show_comparison){ // and it's a comparison
						$competitor_map = $this->Store->getCompetitorMap($this->store_id, $this->data->products);
						$this->data->competitor_store_id[] = $this->store_id;
						$products_wo_comparisons = $this->data->products;
						$this->data->competitor_map = array();
						foreach($competitor_map AS $bpp){
							$this->data->products[] = $bpp['owner_brand_product'];
							$owned_product = $this->Product->getProductsById(null, $bpp['owner_brand_product']);
							if( ! empty($owned_product[0]['id'])){
								$this->data->competitor_map[$bpp['competitor_brand_product']] = $owned_product[0];
							}
						}
						$products_w_comparisons = $this->data->products;
					}
				}
			}

			if($this->data->date_from !== 'Start' && $this->data->date_to !== 'Stop'){
				// TODO: set this to last crawl?
				$this->data->date_from = strtotime($this->data->date_from . " 00:00:00");
				$this->data->date_to = strtotime($this->data->date_to . " 23:59:59");
			}else{
				$tF = getTimeFrame($this->data->time_frame);
				$this->data->date_from = $tF['date_from'];
				$this->data->date_to = $tF['date_to'];
			}

//			$lookup_markets = $this->data->markets;
//			if ( ! $this->data->all_markets AND $this->data->all_retailers)
//				$lookup_markets = array_merge($lookup_markets, $this->data->retailerArr);
//			elseif ( ! $this->data->all_retailers AND $this->data->all_markets)
//				$lookup_markets = array_merge($lookup_markets, $this->data->marketplaceArr);
			$lookup_markets = array_merge($this->data->marketplaceArr, $this->data->retailerArr);

			$this->data->report_where = array(
				'report_type' => $this->data->report_type,
				'api_type' => $lookup_markets,
				'time_frame' => $this->data->time_frame,
				'date_from' => $this->data->date_from,
				'date_to' => $this->data->date_to,
				'cron_ids' => '',
				'product_ids' => $this->data->products,
				'merchants' => $this->data->merchants,
				'store_id' => $this->store_id,
				'competitor_store_id' => $this->data->competitor_store_id,
				'violation' => true,
			);

			//get cronIds for 24 hour scatter
			if($this->data->time_frame == 24 || $this->data->flagDates24) {
				$this->data->report_where['cron_ids'] = getLast24HoursCronIds(($this->data->flagDates24 ? $this->data->date_from : ''), '', $this->data->report_where['api_type']);
            }

			$response = $this->Violations->searchProductPricingViolations($this->data->report_where);
//log_message('info', '_prep_report violation bymerchant data: '. print_r($response,true));
			$this->data->Data = $response;
			$this->data->gData = $this->Chart->prepGoogleData($response, $this->data->report_where, $this->data->report_chart);
		}

		if($this->data->show_comparison AND isset($products_wo_comparisons)){ // don't populate product fields with the compared
			$this->data->products = $products_wo_comparisons;
		}

		$this->data->searchProducts = array();
		for($i=0, $n=sizeof($this->data->products); $i<$n; $i++){
			$this->data->searchProducts[$this->data->products[$i]] = $this->data->productNames[$i];
		}

		//display data
		$this->data->dateStartField = ( ! $this->submit || ($this->submit && $this->data->time_frame != '')) ? 'Start' : $this->data->date_from;
		$this->data->dateEndField = ( ! $this->submit || ($this->submit && $this->data->time_frame != '')) ? 'Stop' : $this->data->date_to;
		$this->data->time_frame = $this->submit ? $this->data->time_frame : '';
		$this->data->submitted = (boolean)$this->submit;

		//
		// Set retailer/marketplace information
		//

		$this->data->marketRetailer = array();
		$this->data->retailersExist = array();
		$this->data->marketplacesExist = array();
		if ($this->data->report_chart === 'scatter') {
			foreach ($this->data->Data as $prodId => $productData) {
				for ($i = 0, $n = count($productData); $i < $n; $i++) {
					$marketName = strtolower($productData[$i]['marketplace']);
					if( ! isset($this->data->marketRetailer[$marketName]))
						$this->data->marketRetailer[$marketName] = $this->Marketplace->is_retailer($marketName);
					if($this->data->marketRetailer[$marketName])
						$this->data->retailersExist[$prodId] = true;
					else
						$this->data->marketplacesExist[$prodId] = true;
				}
			}
		}


		//
		// Set up map between competitor product data and user product data
		//

		$this->data->comparison_data = array();
		if($this->data->show_comparison){
			$color_index = 0;
			$this->data->color_index = array();

			if( ! empty($this->data->competitor_map)){

				//
				// If this is a scatter chart we need to add the crawl id for all the data
				//

				if($this->data->report_chart === 'scatter'){
					foreach($this->data->Data as $prodId=>$productData){
						for($i = 0, $n = count($productData); $i < $n; $i++){
							if(isset($productData[$i]['marketplace'])){
								$api_type = explode('.', $productData[$i]['marketplace']);
								$api_type = $api_type[0];
								$crawl = $this->Crawl->get_crawl_by_time($productData[$i]['timestamp'], $api_type, 'id');
								$this->data->Data[$prodId][$i]['crawl_id'] = isset($crawl['id']) ? $crawl['id'] : false;
							}
						}
					}
				}

				//
				// Now loop through and set the comparison data
				//

				foreach($products_w_comparisons as $prodId){
					$this->data->color_index[$prodId] = $color_index++;

					//
					// Check if this competitor product is associated with user product
					//

					$comparison_id = false;
					if(isset($this->data->competitor_map[$prodId]))
						$comparison_id = $this->data->competitor_map[$prodId]['id'];

					if($comparison_id AND isset($this->data->Data[$comparison_id])){
						$comparison_data = $this->data->Data[$comparison_id];

						//
						// Map values via productid->marketplace->crawltime
						//

						if( ! empty($comparison_data)){

							if($this->data->report_chart === 'line'){
								foreach($comparison_data as $market => $market_prod_data){
									for($i = 0, $n = count($market_prod_data); $i < $n; $i++){
										if(isset($market_prod_data[$i]['dt'])){
											$this->data->comparison_data[$prodId][$market][$market_prod_data[$i]['dt']] = $market_prod_data[$i];
										}
									}
								}
							}
							elseif($this->data->report_chart === 'scatter'){
								for($i = 0, $n = count($comparison_data); $i < $n; $i++){
									if(isset($comparison_data[$i]['marketplace'])){
										$market = strtolower($comparison_data[$i]['marketplace']);
										$merchant_id = $comparison_data[$i]['merchant_id'];
										$crawl_id = $comparison_data[$i]['crawl_id'];
										$this->data->comparison_data[$prodId][$market][$crawl_id][$merchant_id] = $comparison_data[$i];
									}
								}
							}
						}
						unset($this->data->Data[$comparison_id]);
					}
				}
			}
		}
//log_message('info', '_prep_report final data: '. print_r($this->data,true));
	}

	public function index(){
		$this->bydate();
	}

	public function bydate(){
		$this->data->report = 'bydate';
		$this->data->proMerchants = getProductMerchant($this->store_id);
		$this->_build_options_array($this->data->report);
	}

	public function view($reportId) {
		//$reportId = base64_decode(urldecode($reportId));
		if (empty($reportId))
			redirect('savedreports');

		$savedReport = $this->Report->get_save_report_by_id($reportId, false);

		if (empty($savedReport))
			redirect('savedreports');

		$report_where = (isset($prodSavedRpt['report_where'])) ? json_decode($prodSavedRpt['report_where'], TRUE) : array();

		// The saved report exists, let's load it
		$this->data->report_id = $reportId;

		$this->submit = $this->data->submitted = TRUE;
		$this->data->productNames = array();

		//$this->data->prodSavedRpt = $savedReport;
		$report_where = json_decode($savedReport->report_where, TRUE);
		$this->data->report_where = $report_where;

		$this->data->controller = $savedReport->controller;
		$this->data->report = $this->data->by_which = $savedReport->controller_function;

		$this->data->time_frame = isset($report_where['time_frame']) ? $report_where['time_frame'] : FALSE;
        if ( isset($savedReport->report_recursive_frequency) ) {
            $this->data->time_frame = $savedReport->report_recursive_frequency;
            if ( $savedReport->report_recursive_frequency == 1 ) {
                
            }
            
        }
		$this->data->date_from = $this->data->time_frame ? 'Start' : ( isset($report_where['date_from']) ? date('Y-m-d', $report_where['date_from']) : substr($savedReport->datetime, 0, 10) );
		$this->data->date_to = $this->data->time_frame ? 'Stop' :  ( isset($report_where['date_to']) ? date('Y-m-d', $report_where['date_from']) : substr($savedReport->datetime, 0, 10) );
        $this->data->flagDates24 = ($this->data->date_from == $this->data->date_to) ? true: false;

		$this->data->report_name = $savedReport->report_name;
		$this->data->show_comparison = isset($report_where['show_comparison']) ? (boolean)$report_where['show_comparison'] : FALSE;

		$this->data->product_ids = (isset($report_where['product_ids'])) ? $report_where['product_ids'] : array();
		if ( ! empty($this->data->product_ids)) {
			foreach ($this->data->product_ids as $product) {
				$prod = $this->Product->get($product);
				$this->data->productNames[] = $prod->title;
			}
		}

		if ($this->data->by_which === 'bygroup') {
			$this->data->group_id = isset($report_where['group_id']) ? $report_where['group_id'] : NULL;
			$this->data->product_groups = $this->Product->getGroups($this->store_id);
		}

		$this->data->products = $this->data->product_ids = array_values(array_filter($this->data->product_ids));
		$this->data->markets = isset($report_where['api_type']) ? $report_where['api_type'] : array();
		$this->data->merchants = isset($report_where['merchants']) ? $report_where['merchants'] : array();
		if (empty($this->data->markets)) {
			$this->data->all_markets = TRUE;
			$this->data->all_retailers = TRUE;
		}
		if (empty($this->data->merchants))
			$this->data->all_merchants = TRUE;

		$this->session->set_userdata('report_where', $report_where);

		$this->_prep_report();
		$this->_build_options_array($this->data->by_which);
	}

}

/* End of file violations.php */
/* Location: ./system/application/libraries/violations.php */