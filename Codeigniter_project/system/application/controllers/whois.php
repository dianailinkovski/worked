<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of whois
 *
 * @property  Merchant_products_m    $merchant_products_m
 * @property  Account_m    $account_m
 * @property  Report_m    $report_m
 */
class whois extends MY_Controller
{
    public function whois() 
    {
        parent::__construct();
        
        $this->load->model('account_m');
        $this->load->model("crawl_data_m");
        $this->load->model('marketplace_m');
        $this->load->model("Users_m", "User");
        $this->load->model("merchant_products_m");
        $this->load->model("products_m");
        $this->load->model("report_m", "Report");
        $this->load->model("store_m");
        $this->load->model('products_trends_m', 'ProductsTrends');
        
        $this->load->library('mvformat');
        
        require_once(APPPATH . '3rdparty/pchart/pChart/pData.class');
        require_once(APPPATH . '3rdparty/pchart/pChart/pChart.class');
        
        $this->data->is_default = true;
        $this->reportId = NULL;
        
        $this->data->graphDataType = 'chart';
        $this->data->report_name = '';
        $this->data->my = 'whois';
        $this->data->icon = 'ico-report';
        $this->data->widget = 'mv-report';
        $this->data->displayBookmark = true;

        $this->data->report_type = 'whois';
        $this->data->report_where = array(
            'report_type' => $this->data->report_type,
            'is_retailer' => false,
            'report_function' => '',
            'marketplace' => '',
            'merchant_id' => '',
            'time_frame' => '24'
        );
        
        $this->data->totalRetailers = count($this->subscriber_retailer_addons);
    }
	
    /**
     * Breaking up old /whois and creating separate retailers page.
     * 
     * @author Christophe
     */
    public function retailers($id = false)
    {
        $this->data->report_name = 'Retailers Who Sell Your Products';
        $this->data->file_name = str_replace(' ', '_', $this->data->report_name);
        $this->crawl_range = $this->crawl_data_m->last_crawl_range();
        $this->data->gData = "[]";
        
        if ($id)
        {
        	$id = base64_decode(urldecode($id));
        }
        
        $this->setDefaultValues($id, 'retailer');
        $this->getMarketPlaceData();
        
        $this->javascript('views/whois_selling.js.php');        
    }	

    /**
     * Who is Selling - Marketplaces
     * 
     * @author unknown
     * @param int $id
     */
    public function index($id = false) 
    {
    		$this->data->report_name = 'Market &amp; Retailers';
    		$this->data->file_name = str_replace(' ', '_', $this->data->report_name);
    		$this->crawl_range = $this->crawl_data_m->last_crawl_range();
        $this->data->gData = "[]";
            
    		if ($id) 
    		{
    		    $id = base64_decode(urldecode($id));
    		}
            
    		$this->setDefaultValues($id);
    		$this->getMarketPlaceData();

        $this->javascript('views/whois_selling.js.php');
    }

    /**
     * 
     * @author unknown
     * @param unknown_type $id
     * @param unknown_type $whois_switch
     */
    private function setDefaultValues($id = 0, $whois_switch = 'marketplace') 
    {
        $this->session->set_userdata("report_type", "whois");
        
        $this->data->productArr = getProductsDrp($this->store_id);
        $this->data->proMerchants = getProductMerchant($this->store_id);
        $this->data->markertArr = getMarketArray();
        $this->data->is_report = false;
        $this->data->report = 'default';
        
        $this->data->is_post = false;
        $this->data->my = "whois";
        $this->data->merchantReport = '';
        
        $this->data->report_id = 0;
        $this->data->date_from = 'Start';
        $this->data->date_to = 'Stop';
        $this->data->time_frame = '';
        $this->data->products_ids = $this->data->markets = $this->data->merchants = array();
        $this->data->whoisSwitch = $whois_switch;
        
        if ($id == 0) 
        {
            $post = array(
            	'report_type' => 'whois',
            	'report' => 'default',
            	'date_from' => 'Start',
            	'date_to' => 'Stop',
            	'time_frame' => '',
            	'products_ids' => array(),
            	'merchants' => array(),
            	'markets' => array(),
            	'whoisSwitch' => $whois_switch
            );
            
            $data = $this->input->post_default($post);
            
            foreach ($data as $key => $value)
            {
                $this->data->$key = $value;
            }
        }
        else 
        {
            $this->data->report_id = $id;
            $this->data->report_info = $this->Report->get_save_report_by_id($id);
            
            $report_where = json_decode($this->data->report_info['report_where'], true);
            
            if (!empty($report_where)) 
            {
                foreach ($report_where as $key => $value)
                {
                    $this->data->$key = $value;
                }
            }
            
            if ($this->data->date_from !== 'Start') 
            {
            		$this->data->date_from = date('Y-m-d', $this->data->date_from);
            }
            	
            if ($this->data->date_to !== 'Stop')
            {
            		$this->data->date_to = date('Y-m-d', $this->data->date_to);
            }
        }
        
        $this->data->marketplace_keys = array();
        
        $tempProducts = array();
        
        if (!empty($this->data->products_ids)) 
        {
            foreach ($this->data->products_ids as $indexKey => $nV) 
            {
                if ($nV == '') 
                {
                    unset($this->data->products_ids[$indexKey]);
                }
                else 
                {
                    $tempProducts[] = $nV;
                }
            }
        	
            $this->data->is_post = true;
        }
        
        $this->data->product_ids = $tempProducts;
        
        $this->data->product_data = array();
        $this->data->report_where = array(
            'date_from' => $this->data->date_from,
            'date_to' => $this->data->date_to,
            'api_type' => empty($this->data->api_type) ? array('all') : $this->data->api_type,
            'product_ids' => $this->data->product_ids,
            'merchants' => $this->data->merchants,
            'time_frame' => 1,
            'report_type' => $this->data->report_type,
            'cron_ids' => array(),
            'whoisSwitch' => $this->data->whoisSwitch,
            'isoverview' => true,
            'is_retailer' => false,
        );
    }

    /**
     * Get marketplace data to show on the Who Is Selling report.
     * 
     * @author unknown
     */
    private function getMarketPlaceData() 
    {
        $this->data->graphImagename = ''; // for exporting graph

        // Separate marketplaces from retailers and
        // group marketplace_products by marketplace
        $this->data->marketplaces = array();
        $this->data->retailers = array();

        if ($this->data->report_where['whoisSwitch'] === 'marketplace')
        {
            $markets = getMarketplaceArray($this->store_id);
        }
        else
        {
            $markets = getRetailerArray(FALSE, $this->store_id);
        }
        
        //var_dump($markets); exit();
        
        $markets = array_to_lower($markets);
        
        //var_dump($markets); //exit();
		
        foreach ($markets as $market) 
        {
            $crawl_info = $this->crawl_data_m->last_crawl($market);
            
            if ($crawl_info) 
            {
                $from = $crawl_info->start_datetime;
				        $to = $crawl_info->end_datetime;

				        $marketplace_products = $this->merchant_products_m->getCountByMarketplace($this->store_id, $market, $from, $to);
				        
                if (!empty($marketplace_products)) 
                {
					          $this->data->marketplace_products[] = $marketplace_products[0];
                }
            }
        }
        
        //var_dump($this->data->marketplace_products); exit();

        if (isset($this->data->marketplace_products)) 
        {
            for ($i = 0, $n = count($this->data->marketplace_products); $i < $n; $i++) 
            {
                $marketplace = $this->data->marketplace_products[$i]['marketplace'];

                if ($this->data->marketplace_products[$i]['is_retailer'] === '1') 
                {
                    $merchant = $this->merchant_products_m->getMerchantDetailsByMarketplace($marketplace);
                    
                    if (isset($merchant[0]['id'])) 
                    {
                        $this->data->marketplace_products[$i] = array_merge($this->data->marketplace_products[$i], $merchant[0]);
                        $this->data->retailers[] = $this->data->marketplace_products[$i];
                    }
                }
                else 
                {
                    $this->data->marketplaces[] = $this->data->marketplace_products[$i];
                }
                
                $this->data->marketplace_products[$marketplace] = $this->data->marketplace_products[$i];
                
                unset($this->data->marketplace_products[$i]);
            }
        }
        
        // sort by keys
        $this->data->marketplace_keys = array();
        
        if (!empty($this->data->marketplace_products)) 
        {
            ksort($this->data->marketplace_products);
            
            $this->data->marketplace_keys = array_keys($this->data->marketplace_products);
            
            // graph data
            $this->data->gData = mvFormat::whoIsSellingMyProductDefault($this->data->marketplace_products);
        }
    }

    /**
     * Report on marketplace sellers and how many products they are selling.
     * 
     * @author unknown
     * @param string $marketplace
     * @param unknown_type $id
     */
    function report_marketplace($marketplace, $id = false) 
    {
        // what is this used for? - Christophe
    		if ($id) 
    		{
            $id = base64_decode(urldecode($id));
            
            $this->setDefaultValues($id);
            $this->data->report_id = $id;
    		}
    
    		$this->data->marketplace = $marketplace;
    		$this->data->report_name = marketplace_display_name($marketplace).' Sellers';
    		$this->data->file_name = str_replace(' ', '_', $this->data->report_name.' '.date('Y-m-d'));
    		$this->data->report_where = array(
    		    'report_type' => $this->data->report_type,
            'report_function' => 'report_marketplace',
    		    'marketplace' => $marketplace
        );
    
    		$crawl = $this->crawl_data_m->last_crawl($marketplace);
    
    		$merchants = $this->merchant_products_m->getCountByMerchant($this->store_id, $marketplace, $crawl->start_datetime, $crawl->end_datetime);
    		
    		for ($i = 0; $i < count($merchants); $i++)
    		{
        		$merchant_info = $this->merchant_products_m->getMerchantDetailsById($merchants[$i]['id']);
        		
        		$merchants[$i]['marketplace_seller_url'] = $this->merchant_products_m->get_marketplace_seller_url($merchant_info, $marketplace);
    		}
    		
    		//var_dump($merchants); exit();
    		
    		$this->data->merchantList = $merchants;
    		
    		$this->data->gData = mvFormat::whoIsSellingMerchantFormatdata($this->data->merchantList);
    		
    		$this->javascript('views/whois_selling.js.php');
    }

	/**
	 * 
	 * @author unknown
	 * @param string $marketplace
	 * @param int $merchantId
	 * @param int $reportId
	 */
	function report_merchant($marketplace, $merchantId, $reportId = false) 
	{
		$this->data->retailer = $this->marketplace_m->is_retailer($marketplace);
		$this->data->marketplace = $marketplace;
		
		$merchant_details = $this->merchant_products_m->getMerchantDetailsById($merchantId);
		
		$this->data->merchant = $this->data->original_name = trim($merchant_details['original_name']);

		$this->data->show_notify_resource = $merchantId;

		$this->data->report_where = array(
			'report_type' => $this->data->report_type,
			'is_retailer' => $this->data->retailer,
			'report_function' => 'report_merchant',
			'marketplace' => $marketplace,
			'merchant_id' => $merchantId,
			'time_frame' => '24'
		);

		if ($reportId) {
			$this->data->report_id = base64_decode(urldecode($reportId));
		}

		$this->data->report_name = $this->data->retailer ? marketplace_display_name($marketplace).' Product Listing' : marketplace_display_name($marketplace).' Seller: '.marketplace_display_name($this->data->merchant).' Product Listing';
		$this->data->file_name = str_replace(' ', '_', $this->data->report_name);

		//view data
		$this->data->time_frame = '24';
		$lastCrawl = $this->crawl_data_m->last_crawl($marketplace);
		
		//var_dump($lastCrawl); exit();
		
		$this->crawl_range = $this->crawl_data_m->last_crawl_range();
		
		$this->data->crawl_range = $this->crawl_range;
		
		if ($this->config->item('environment') == 'local')
		{
			$crawl_range_from_int = strtotime('2015-08-01 00:00:00');
			$crawl_range_to_int = strtotime('2015-08-02 00:00:00');
		}
		else
		{
			$crawl_range_from_int = strtotime($this->crawl_range['from']);
			$crawl_range_to_int = strtotime($this->crawl_range['to']);
		}		

		//TODO
		//this should occur in the reports model...
		//now setup the product data
		$productQuery = "SELECT
				ptn.um as hashKey,
				cmn.seller_id,
				p.upc_code,
				p.sku,
				p.title,
				p.retail_price,
				p.wholesale_price,
				cmn.merchant_name,
				cmn.original_name,
				ptn.dt
			FROM
				{$this->_table_products_trends} ptn
			INNER JOIN {$this->_table_crowl_merchant_name} cmn ON cmn.id = ptn.mid
			LEFT JOIN {$this->_table_products} p ON p.id = ptn.pid
			WHERE p.store_id IN (" . getStoreIdList($this->store_id, TRUE) . ")
				AND ptn.ar = '$marketplace'
				AND cmn.id =  $merchantId
				AND ptn.dt >= $crawl_range_from_int
				AND ptn.dt <= $crawl_range_to_int
			GROUP BY hashKey";
				
		//echo "<PRE>";
		//echo $productQuery;
		//exit;
		
		$products = $this->db->query($productQuery)->result();
		
		$finalProductsArray = array();
		$gData = array('violoation' => 0, 'non_violoation' => 0);
		
		$hashes = array();
		
		if (empty($products))
		{
		    
		}
		else
		{
		    foreach ($products as $product)
		    {
		    	$hashes[] = $product->hashKey;
		    	$products_assoc[$product->hashKey] = $product;
		    }
		    
		    $priceTrends = $this->ProductsTrends->get_all_by_hashkeys_and_date_range($hashes, strtotime($lastCrawl->start_datetime), strtotime($lastCrawl->end_datetime));
		    
		    foreach($priceTrends->result_object() as $priceTrend)
		    {
		    	if ((float)$priceTrend->mpo >= (float)$priceTrend->ap)
		    	{
		    		$gData['non_violoation']++;
		    	}
		    	else
		    	{
		    		$gData['violoation']++;
		    	}
		    		
		    	$violationArray = array(
		    			'productId' 	=> (int)   $priceTrend->pid,
		    			'upc_code' 		=> (string)$priceTrend->upc,
		    			'retail' 		=> $priceTrend->rp ? (float)$priceTrend->rp : $products_assoc[$priceTrend->um]->retail_price,
		    			'wholesale' 	=> $priceTrend->wp ? (float)$priceTrend->wp : $products_assoc[$priceTrend->um]->wholesale_price,
		    			'price' 		=> (float) $priceTrend->mpo,
		    			'map' 			=> (float) $priceTrend->ap,
		    			'title' 		=> (string)$priceTrend->t,
		    			'title2' 		=> (string)$products_assoc[$priceTrend->um]->title,
		    			'marketplace' 	=> (string)$priceTrend->ar,
		    			'url' 			=> (string)$priceTrend->l,
		    			'timestamp'		=> (int)   $priceTrend->dt,
		    			'hash_key'		=> (string)$priceTrend->um,
		    			'hashKey'		=> (string)$priceTrend->um,
		    			'merchant_id' 	=> (string)$products_assoc[$priceTrend->um]->seller_id,
		    			'original_name' => (string)$products_assoc[$priceTrend->um]->original_name,
		    			'sku'		 	=> (string)$products_assoc[$priceTrend->um]->sku,
		    			'date' 			=> (string)date('m/d/Y G:i:s', (int)$priceTrend->dt),
		    			'shot' 			=> (string)$priceTrend->ss
		    	);
		    		
		    	$finalProductsArray[$priceTrend->pid] = $violationArray;
		    }		    
		}

		$googleDataArray = array();
		$googleDataArray[0] = array('State', 'Count');
		$googleDataArray[] = array('Non Violation', $gData['non_violoation']);
		$googleDataArray[] = array('Violation', $gData['violoation']);
		$gData['googleData'] = $googleDataArray;
		$gData['type'] = 'pie';

		$this->data->Data = $finalProductsArray;
		$this->data->gData = $gData;

		// violator notification data
		$this->data->merchant_id = $merchantId;
		$this->data->show_notify_resource = $merchantId;
		$this->data->violator_notification = $this->store_m->get_violator_notification_by_seller($merchantId, $this->store_id);
		$this->data->smtp = $this->store_m->get_store_smtp_by_store($this->store_id);
		$this->data->default_email_from = $this->Store->get_smtp_email($this->data->smtp);

		//array_push($this->javascript_files, 'views/merchant_report.php');
	}
	
	/*
	*
	* genratePieChartImage
	*
	*
	*
	*/
	function genratePieChartImage($dataAr) {
		//debug('DATA',$dataAr,2);
		$s1 = array();
		$s2 = array();
		foreach ($dataAr as $key => $val) {
			$s1[] = $val;
			$s2[] = $key;
		}

		// Dataset definition
		require_once(APPPATH.'3rdparty/pchart/pChart/pData.class');
		require_once(APPPATH.'3rdparty/pchart/pChart/pChart.class');
		$path = $this->config->item('csv_upload_path');
		$DataSet = new pData;
		$DataSet->AddPoint($s1, "Serie1");
		$DataSet->AddPoint($s2, "Serie2");
		$DataSet->AddAllSeries();
		$DataSet->SetAbsciseLabelSerie("Serie2");

		// Initialise the graph
		$Test = new pChart(1000, 458);
		$Test->drawBackground(232, 234, 234);
		$Test->loadColorPalette(APPPATH.'3rdparty/pchart/softtones.txt');
		// Draw the pie chart
		$Test->setFontProperties(APPPATH.'3rdparty/pchart/Fonts/tahoma.ttf', 8);
		$Test->drawBasicPieGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 480, 229, 175);
		$Test->drawPieLegend(650, 75, $DataSet->GetData(), $DataSet->GetDataDescription(), 232, 234, 234);
		$imgName = uniqid('graph_').'.png';
		$Test->Render($path.$imgName);

		return upload_to_amazon_graphImage($imgName, $path);
	}

	function generateGraphImage($finalData = array(), $hourflag, $title = 'Sticky Charts', $x_axis_format = 'date') {

		$path = $this->config->item('csv_upload_path');

		if (isset($finalData['data']) && count($finalData['data']) > 0) {
			$DataSet = new pData;
			$in = 0;
			foreach ($finalData['data'] as $seriesData) {
				$in++;
				$seriesIndex = 'Serie'.$in;
				$DataSet->AddPoint($seriesData['data'], $seriesIndex);
				$DataSet->SetSerieName($seriesData['name'], $seriesIndex);
				$DataSet->AddSerie($seriesIndex);

				//$DataSet->AddPoint(array(23,432,43,153,234),"Serie2");
			}

			$xAxisArray = array();
			$in++;
			$seriesIndex = 'Serie'.$in;
			$catCount = count($finalData['cat']);
			if ($catCount <= 10)
				$DataSet->SetXAxisFormat($x_axis_format);
			foreach ($finalData['cat'] as $catD) {
				if ($catCount > 10) {
					$xAxisArray[] = '';
				}else {
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
			$Test->setFontProperties(APPPATH.'3rdparty/pchart/Fonts/tahoma.ttf', 8);
			$Test->setGraphArea(40, 30, 950, 400);
			$Test->drawGraphArea(109, 110, 114, false);
			$Test->drawGrid(4, false, 0, 0, 0, 50);

			// Draw the 0 line
			$Test->setFontProperties(APPPATH.'3rdparty/pchart/Fonts/tahoma.ttf', 6);
			// Draw the line graph
			$sCount = count($finalData['data']);
			if ($sCount > 0) {
				for ($m = 0; $m < $sCount; $m++) {
					$color = Color_handler::get_next($m);
					$rgb = $color->get_rgb();
					$Test->setColorPalette($m, $rgb['r'], $rgb['g'], $rgb['b']);
				}
			}
			$Test->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(), SCALE_NORMAL, 0, 0, 0, TRUE, 0, 0, TRUE);
			$Test->drawBarGraph($DataSet->GetData(), $DataSet->GetDataDescription());

			// Finish the graph
			$Test->setFontProperties(APPPATH.'3rdparty/pchart/Fonts/tahoma.ttf', 8);
			$Test->setFontProperties(APPPATH.'3rdparty/pchart/Fonts/tahoma.ttf', 10);
			$imgName = uniqid('graph_').'.png';
			$Test->Render($path.$imgName);

			return upload_to_amazon_graphImage($imgName, $path);
		}
	}

	/**
	 * @deprecated
	 */	
	//function edit($id) {
	//	$old_id = $id;
	//	if ($id != '') {
	//		$id = base64_decode(urldecode($id));
	//
	//		$prodSavedRpt = $this->Report->get_save_report_by_id($id);
	//		if (isset($prodSavedRpt['report_where'])) {
	//			$report_where = json_decode($prodSavedRpt['report_where'], TRUE);
	//		}
	//		if ($report_where['reportMarketPlace'] != '' && $report_where['reportMerchantName'] == '') {
	//			redirect(base_url().'whois/report_marketplace/'.$report_where['reportMarketPlace'].'/'.$old_id);
	//		} else if ($report_where['reportMarketPlace'] != '' && $report_where['reportMerchantName'] != '') {
	//				$merchant_name = explode(',', $report_where['reportMerchantName']);
	//				redirect(base_url().'whois/report_merchant/'.$report_where['reportMarketPlace'].'/'.urlencode($merchant_name[0]).'/'.$old_id);
	//			}
	//	}
	//	$this->setDefaultValues($id);
	//
	//	if (count($this->data->products) == 0)
	//		$this->getDefaultData($this->data->markets[0]);
	//	else
	//		$this->getDefaultData();
	//
	//	$this->_view = $this->_controller . '/report_marketplace';
	//}

	/**
	 * function report
	 *
	 * @deprecated
	 */
	//function report($marketPlace = 'all', $merchantReport = '') {
	//	$this->setDefaultValues();
	//	$this->getDefaultData($marketPlace);
	//	$this->data->is_report = true;
	//	$this->data->marketPlace = $marketPlace;
	//	$this->data->merchantReport = $merchantReport;
	//	$this->data->report_where['api_type'] = array($marketPlace);
	//
	//	$this->load->view('front/myproducts/market_report', $this->data);
	//}

	/**
	 * function byproduct
	 *
	 * @deprecated
	 */
	//function byproduct() {
	//	$this->setDefaultValues();
	//	$this->getDefaultData();
	//	$this->data->report = 'byproduct';
	//	$this->load->view('front/myproducts/market_report', $this->data);
	//}

	/**
	 * function bymarket
	 *
	 * @deprecated
	 */
	//function bymarket() {
	//	$this->setDefaultValues();
	//	$this->getDefaultData();
	//	$this->data->report = 'bymarket';
	//	$this->load->view('front/myproducts/market_report', $this->data);
	//}

	/**
	 * function bydate
	 *
	 * @deprecated
	 */
	//function bydate() {
	//	$this->setDefaultValues();
	//	$this->getDefaultData();
	//	$this->data->report = 'bydate';
	//	$this->load->view('front/myproducts/market_report', $this->data);
	//}

	/**
	 * function merchant_report
	 *
	 * @deprecated
	 */
	//function merchant_report($marketplace) {
	//
	//	if (strtolower($marketplace) == 'shopping.com') {
	//		$marketplace = 'shopping';
	//	}
	//	$this->data->marketplace = $marketplace;
	//
	//	// marketplace data
	//	$this->data->merchantProducts = $this->merchant_products_m->getCountByMerchant($this->store_id, $marketplace);
	//	// echo $this->db->last_query();
	//	//  echo $this->db->last_query();
	//	exit($this->load->view('front/myproducts/merchant_report', $this->data, true));
	//}

	/**
	 * function _getDetails
	 *
	 * @deprecated
	 */
	//private function _getDetails($graphImageName = '', $marketplace = '', $merchant = '', $graph_data = '') {
	//	// Graph Image
	//	//echo "Graph : ".$graphImageName;
	//
	//	if ($graphImageName == 'noimage') {
	//		$graphImageName = '';
	//	}
	//	$this->data->orginal_name = '';
	//	$this->data->marketplace = $marketplace;
	//	$this->data->merchant = $merchant;
	//
	//	if (trim($merchant)) {
	//		$tmp = explode(',', $merchant);
	//		$this->data->orginal_name = $tmp[1];
	//		$merchant = $tmp[0];
	//	}
	//
	//	if ($graphImageName != '') {
	//		$graphImageName = base64_decode(urldecode($graphImageName));
	//		$this->data->graph_image_name = $graphImageName;
	//	}else {
	//		$this->data->graph_image_name = '';
	//	}
	//
	//	$this->data->headerDate = '';
	//	$this->data->title = '';
	//
	//	if (strtolower($marketplace) == 'shopping.com') {
	//		$marketplace = 'shopping';
	//	}
	//
	//	if (!trim($marketplace)) {
	//		$this->data->marketplace_products = $this->merchant_products_m->getCountByMarketplace($this->store_id);
	//		$report_data = get24ReportTitleAndDate(
	//			array('time_frame' => 24,
	//				'title' => "Who's is selling my products in",
	//				'date_from' => strtotime('-24 hours'),
	//				'date_to' => time()
	//			)
	//		);
	//	} elseif (!trim($merchant)) { // merchant data
	//
	//		$this->data->merchantProducts = myproducts_group_by_market($this->merchant_products_m->getCountByMerchant($this->store_id, $marketplace));
	//		$report_data = get24ReportTitleAndDate(
	//			array('time_frame' => 24,
	//				'title' => "Who's is selling my products in ".marketplace_display_name($marketplace).' in',
	//				'date_from' => strtotime('-24 hours'),
	//				'date_to' => time()
	//			)
	//		);
	//
	//	}else { // products data
	//		$report_data = get24ReportTitleAndDate(
	//			array('time_frame' => 24,
	//				'title' =>  $this->data->orginal_name." is selling my products in ".marketplace_display_name($marketplace).' in',
	//				'date_from' => strtotime('-24 hours'),
	//				'date_to' => time()
	//			)
	//		);
	//		// get dynamo db data
	//		if ($graph_data) {
	//			$graph_data = urldecode($graph_data);
	//			$this->data->graph_image_name = $this->genratePieChartImage(unserialize($graph_data));
	//		}
	//
	//		$merchant = urldecode($merchant);
	//		if (strtolower($marketplace) == 'shopping.com') {
	//			$marketplace = 'shopping';
	//		}
	//		$request = myproducts_products_request($marketplace, $merchant, $this->store_id, 1);
	//		$merchant_details = $this->merchant_products_m->getMerchantDetailsByName($merchant);
	//
	//		$this->data->marketplace = $marketplace;
	//		$this->data->merchant = trim($merchant);
	//		$this->data->original_name = clearnSellerName($merchant_details[0]['original_name']);
	//
	//		$result = $this->report_m->productPricingReport24($request, true);
	//		$this->data->productsData = $result['data'];
	//
	//	}
	//
	//	$this->data->headerDate = $report_data['date'];
	//	$this->data->title = $report_data['title'];
	//
	//	$this->data->merchantLogo = $this->account_m->get_merchant_thumb($this->store_id);
	//
	//}

	/**
	 * function send_email
	 * @deprecated use reports::email()
	 */
	//function send_email($graphImageName = '', $marketplace = '', $merchant = '', $graph_data = '') {
	//	// emails
	//	$receivers = join(',', $this->input->post('email_address'));
	//
	//	$this->_getDetails($graphImageName, $marketplace, $merchant, $graph_data);
	//	// email body
	//	$this->data->viewFooter=1;//added to remove footer text if view is  called from somewhere else
	//	$html = $this->load->view('front/myproducts/email', $this->data, true);
	//
	//	template_email_send('general', $this->user_id, "Who's Selling My Products", $receivers, $html);
	//
	//	exit('1');
	//}

	/**
	 * function merchant_products
	 *
	 * @deprecated
	 */
	//function merchant_products() {
	//	unset($this->layout);
	//
	//	$marketplace = $this->input->post('marketplace');
	//	$merchant = $this->input->post('merchant');
	//	$original_name = $this->input->post('original_name');
	//	if (strtolower($marketplace) == 'shopping.com') {
	//		$marketplace = 'shopping';
	//	}
	//	$request = myproducts_products_request($marketplace, $merchant, $this->store_id, 1);
	//
	//	$this->data->merchant = $merchant;
	//	$this->data->original_name = $original_name;
	//
	//	//debug('Request',$request ,2);
	//	// get dynamo db data
	//	$result = $this->report_m->productPricingReport24($request, true);
	//	//p_array($result);
	//	$upc_data = array();
	//
	//	if (count($result) > 0) {
	//		foreach ($result['data'] as $data) {
	//			$upc_data[$data['upc']] = $data;
	//			//$upc_data[$data['upc']][] = $data;
	//		}
	//	}
	//
	//	$this->data->upc_data = $upc_data;
	//
	//	$this->load->view('front/myproducts/product_report', $this->data);
	//}

	/**
	 * function merchant_product_drill
	 *
	 * @deprecated
	 */
	//function merchant_product_drill() {
	//	unset($this->layout);
	//
	//	$marketplace = $this->input->post('marketplace');
	//	$merchant = $this->input->post('merchant');
	//	$to = $from = $this->input->post('date');
	//	$product_id = $this->input->post('product_id');
	//
	//	$request = myproducts_merchant_product_request($marketplace, $merchant, $product_id, $this->store_id, $to, $from);
	//
	//	//p_array($request);
	//
	//	$this->data->merchantProducts = $this->report_m->productPricingReport24($request);
	//	$this->load->view('front/myproducts/merchant_product_drill', $this->data);
	//}

	/**
	 * @deprecated
	 */
	//private function prepareData() {
	//	if (
	//		trim($this->data->date_from) &&
	//		trim($this->data->date_to) &&
	//		$this->data->date_to != 'Stop' &&
	//		$this->data->date_from != 'Start'
	//	) {
	//		$this->data->to = strtotime($this->data->date_to.' 23:59:59');
	//		$this->data->from = strtotime($this->data->date_from.' 00:00:00');
	//	} else if ($this->data->time_frame == '24') {
	//			$this->data->from = strtotime(date('d-m-Y').' 00:00:00');
	//			$this->data->to = strtotime(date('d-m-Y').' 23:59:59');
	//		} else if ($this->data->time_frame == '7') {
	//			$this->data->to = time();
	//			$this->data->from = strtotime("-6 days");
	//		} else if ($this->data->time_frame == '30') {
	//			$this->data->to = time();
	//			$this->data->from = strtotime("-29 days");
	//		}
	//	$product_list = $this->merchant_products_m->getHashKeysForDynamo(
	//		$this->data->products, $this->data->merchants, $this->data->markets
	//	);
	//	$dataArray = $this->getDateArray($this->data->to, $this->data->from);
	//	$tmp = array('total_products' => 0, 'total_listing' => 0, 'merchants' => array(), 'count' => 0, 'late_date' => '', 'date' => $dataArray);
	//	$this->data->market_place_template = array();
	//	if (isset($this->data->markets[0]) && $this->data->markets[0] != 'all') {
	//		foreach ($this->data->markets as $market_place) {
	//			$this->data->market_place_template[strtolower($market_place)] = $tmp;
	//		}
	//	}else {
	//		$this->data->market_place_template = array('google' => $tmp, 'shopping' => $tmp, 'amazon' => $tmp);
	//	}
	//	ksort($this->data->market_place_template);
	//	$this->data->marketplace_keys = array_keys($this->data->market_place_template);
	//	foreach ($this->data->products as $product_id) {
	//		$this->data->marketplace_products[$product_id] = $this->data->market_place_template;
	//	}
	//	$this->product_id = 0;
	//	foreach ($product_list as $product) {
	//		$this->updateObject($product, $this->data->from, $this->data->to);
	//	}
	//	$this->data->date_to = $this->data->to;
	//	$this->data->date_from = $this->data->from;
	//	$this->data->report_where = array(
    //      'date_from' => $this->data->date_from,
	//		'date_to' => $this->data->date_to,
	//		'api_type' => $this->data->markets,
	//		'product_ids' => $this->data->products,
	//		'merchants' => $this->data->merchants,
	//		'time_frame' => $this->data->time_frame,
	//		'report_type' => 'whois',
	//		'cron_ids' => array());
	//}

	/**
	 * @deprecated
	 */
	//private function updateObject($product, $from, $to) {
	//
	//	$hash_key = $product['hash_key'];
	//	$id = $product['id'];
	//	$merchant_name = $product['merchant_name'];
	//	$this->data->product_data[$id] = $product;
	//
	//	$response = $this->merchant_products_m->getResultFromDynamoDB($hash_key, $from, $to);
	//	$count = $response->body->Count;
	//
	//	if ($count > 0) {
	//		for ($i = 0; $i < $count; $i++) {
	//			$priceTrends = $response->body->Items->{$i};
	//			$saveDate = date('Y-m-d', (string) $priceTrends->dt->N);
	//
	//			if (!isset($this->data->marketplace_products[$id][(string) $priceTrends->ar->S])) {
	//				continue;
	//			}
	//
	//			$this->data->marketplace_products[$id][(string) $priceTrends->ar->S]['total_listing']++;
	//			$this->data->marketplace_products[$id][(string) $priceTrends->ar->S]['merchants'][$merchant_name] = $product['original_name'];
	//			$this->data->marketplace_products[$id][(string) $priceTrends->ar->S]['date'][$saveDate]['total_listing']++;
	//
	//			if ($this->data->marketplace_products[$id][(string) $priceTrends->ar->S]['count'] == 0) {
	//				$this->data->marketplace_products[$id][(string) $priceTrends->ar->S]['count']++;
	//				$this->data->marketplace_products[$id][(string) $priceTrends->ar->S]['total_products']++;
	//			}
	//
	//			if ($this->data->marketplace_products[$id][(string) $priceTrends->ar->S]['late_date'] != $saveDate) {
	//				$this->data->marketplace_products[$id][(string) $priceTrends->ar->S]['late_date'] = $saveDate;
	//				$this->data->marketplace_products[$id][(string) $priceTrends->ar->S]['date'][$saveDate]['total_products']++;
	//				$this->data->marketplace_products[$id][(string) $priceTrends->ar->S]['date'][$saveDate]['merchants'][$merchant_name] = $product['original_name'];
	//			}
	//		}
	//
	//		foreach ($this->data->market_place_template as $market_place => $template) {
	//			$this->data->marketplace_products[$id][$market_place]['count'] = 0;
	//			$this->data->marketplace_products[$id][$market_place]['late_date'] = '';
	//		}
	//	}
	//}

	/**
	 * function getDateArray
	 * @deprecated
	 */
	//private function getDateArray($to, $from) {
	//
	//	$diff = getDiffBwDates($to, $from);
	//	//echo $to.' = '.$from.' = '.$diff.'<br />';
	//
	//	if ($to > $from) {
	//		$start_date = $to;
	//	}else {
	//		$start_date = $from;
	//	}
	//	$templateArray = array('total_products' => 0, 'total_listing' => 0, 'merchants' => array());
	//	$dateArray = array();
	//	if ($diff == 0) {
	//		$date = date('Y-m-d', $start_date);
	//		$dateArray[$date] = $templateArray;
	//	}else {
	//		for ($x = 0; $x <= $diff; $x++) {
	//			if ($x == 0) {
	//				$date = date('Y-m-d', $start_date);
	//			} else if ($x == 1) {
	//					$date = date('Y-m-d', strtotime("-1 day", $start_date));
	//				}else {
	//				$date = date('Y-m-d', strtotime("-".$x." days", $start_date));
	//			}
	//
	//			$dateArray[$date] = $templateArray;
	//		}
	//	}
	//	ksort($dateArray);
	//	//p_array($dateArray);
	//	return $dateArray;
	//}

	/**
	 * @deprecated
	 */
	//private function getDefaultData($marketPlace = 'all') {
	//	if ($this->data->is_post) {
	//		$this->prepareData();
	//		// graph data
	//		$this->data->gData = mvFormat::whoIsSellingMyProductByDate($this->data->marketplace_products);
	//	}else {
	//		// marketplace data
	//		$countByMarketPlace = $this->merchant_products_m->getCountByMarketplace($this->store_id, $marketPlace);
	//		foreach ($countByMarketPlace as $val) {
	//			$this->data->marketplace_products[$val['marketplace']] = $val;
	//		}
	//		//sort by keys
	//		ksort($this->data->marketplace_products);
	//		$this->data->marketplace_keys = array_keys($this->data->marketplace_products);
	//		// graph data
	//		$this->data->gData = mvFormat::whoIsSellingMyProductDefault($this->data->marketplace_products);
	//	}
	//	// for capturing image image
	//	$this->data->graphImagename = '';
	//}

}
