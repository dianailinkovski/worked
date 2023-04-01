<?php

class Violationoverview extends MY_Controller
{
	private $countProduct, $countViolation;

	function Violationoverview()
	{
		parent::__construct();

		$this->load->model('chart_m', 'Chart');
		$this->load->model("crawl_data_m");
		$this->load->model('marketplace_m', 'Market');
		$this->load->model("Users_m", 'User');
		$this->load->model("merchant_products_m");
		$this->load->model("Products_m", 'Product');
		$this->load->model("report_m", 'Report');
		$this->load->model("store_m", 'Store');
		$this->load->model("violator_m", "Violator");

		$this->javascript_files = array('reports.js.php');
		
		$this->data->graphDataType = 'chart';
		$this->data->report_name = '';
		$this->data->my = 'violationoverview';
		$this->data->icon = 'ico-report';
		$this->data->widget = 'mv-report';
		$this->data->displayBookmark = true;
		
		$this->data->report_type = 'violationoverview';
		$this->data->report_where = array('report_type' => $this->data->report_type,
				'is_retailer' => false,
				'report_function' => '',
				'marketplace' => '',
				'merchant_id' => '',
				'time_frame' => '24');		
	}

	function old_index($id = false)
	{
		// Tell the view this is the violation overview report
		$this->data->my = 'violationOverview';
		$this->data->report_name = 'Violation Overview';
		$this->data->file_name = str_replace(' ', '_', 'Violations Overview '.date('m-d-Y'));

		//if($id) $this->data->report_id = base64_decode(urldecode($id));
        if($id) $this->data->report_id = $id;

		// Get the last crawl data
		$this->data->last_crawl = $this->crawl_data_m->last_crawl();
		$this->crawl_range = $this->crawl_data_m->last_crawl_range();
		$this->data->number_of_merchants = getNumberOfMerchants(
			$this->store_id,
			$this->crawl_range['from'],
			$this->crawl_range['to']
		);
		$this->data->last_tracked_arr = $this->Report->last_tracked_image($this->crawl_range['from']);
		$this->data->last_tracked_date = trackingDateFormat($this->crawl_range['from']);

		// Get market/merchant/product violations
		$this->data->priceViolators = $this->_countPriceViolations();
//echo "<PRE>----------------------------------------------------\n";
//print_r($this->data); 
//exit;
		$this->data->totalPriceViolators = count($this->data->priceViolators);
		$this->data->violatedProducts = $this->Violator->getViolatedProducts($this->store_id);
		loadPricePoints($this->data->violatedProducts, strtotime($this->crawl_range['from']));

		// Calculate the overview statistics
		$this->data->products_monitored = $this->Product->getProductsMonitoredCount($this->store_id);
		$this->data->total_violations = count($this->data->violatedProducts);
		$this->data->marketplace_products = array();
		$this->data->market_violations = array();

		$markets = $this->Market->get_marketplaces_by_storeid_using_categories($this->store_id);
		foreach ($markets as $market)
		{
			$market = $market['name'];
			$crawl_info = ! empty($this->data->last_crawl[$market]) ? $this->data->last_crawl[$market] : FALSE;
			if ($crawl_info)
			{
				//echo "<PRE>----------------------------------------------------\n";
				//print_r($crawl_info); 
				$from = $crawl_info->start_datetime;
				$to = $crawl_info->end_datetime;

				$marketplace_products = $this->merchant_products_m->getCountByMarketplace($this->store_id, $market, $from, $to, 1);
				//print_r($marketplace_products); 
				if ( ! empty($marketplace_products))
				{
					//$market_violations = $this->Violator->getViolatedMarkets($this->store_id, '', $market, $from, $to); // this is retarded
					//print_r($market_violations); 
					$this->data->marketplace_products[] = $marketplace_products[0];
					//$this->data->market_violations[$market] = $market_violations[$market];
					$this->data->market_violations[$market] = $marketplace_products[0]['violated_products'];
				}
			}
		}
		//exit;
		
		//for saving reports - not necessary to provide info for overview
		$this->data->report_where = array('report_type' => 'violationoverview');
		$this->data->gData = array(
			'type'       => 'pie',
			'width'      => '220',
			'height'     => '150',
			'googleData' => array(
				array('State', 'Count'),
				array('Non Violation', (int)max($this->data->products_monitored - $this->data->total_violations, 0)),
				array('Violation', (int)$this->data->total_violations)
			)
		);

		// Separate marketplaces from retailers
		$this->data->marketplaces = array();
		$this->data->retailers = array();
		$this->data->violatedMarketplaces = array();
		$this->data->violatedRetailers = array();
		
		//var_dump($this->data->marketplace_products); exit();
		
		for ($i = 0, $n = count($this->data->marketplace_products); $i < $n; $i++)
		{
			$name = $this->data->marketplace_products[$i]['marketplace'];
			if ($this->data->marketplace_products[$i]['is_retailer'] === '1')
			{
				$merchant = $this->merchant_products_m->getMerchantDetailsByMarketplace($this->data->marketplace_products[$i]['marketplace']);
				if (isset($merchant[0]['id']))
				{
					$this->data->marketplace_products[$i] = array_merge($this->data->marketplace_products[$i], $merchant[0]);
					$this->data->retailers[] = $this->data->marketplace_products[$i];
					if ( ! empty($this->data->market_violations[$name]))
						$this->data->violatedRetailers[$name] = TRUE;
				}
			}
			else
			{
				$this->data->marketplaces[] = $this->data->marketplace_products[$i];
				if ( ! empty($this->data->market_violations[$name]))
					$this->data->violatedMarketplaces[$name] = TRUE;
			}
		}

//echo "<pre>";
//print_r($this->data); exit;
		$this->data->totalMarketplaces = count($this->data->marketplaces);
		$this->data->totalRetailers = count($this->data->retailers);

		$this->javascript('views/violation_overview.js.php');
	}
	
    public function merchant_profile($merchant_id)	
    {
        redirect('/merchants/profile/' . $merchant_id);
        exit();   
    }
    
    public function merchant_profile_products($merchant_id)
    {
        redirect('/merchants/profile_products/' . $merchant_id);
        exit();
    }
    
    public function merchant_profile_violations($merchant_id)
    {
        redirect('/merchants/profile_violations/' . $merchant_id);
        exit();
    }
	
    /**
     * Table of violated products.
     * 
     * @author Christophe
     */
    public function violated_products()
    {
        $this->data->violatedProducts = $this->Violator->getViolatedProducts($this->store_id);
    }
    
    /**
     * New dashboard for violation area.
     * 
     * Formerly known as violation_dashboard()
     * 
     * @author Christophe
     * @param int $reportId
     */
    public function index($reportId = FALSE)
    {
        $this->load->model("crawl_data_m", 'Crawl');
        $this->load->model("merchant_products_m", 'MProducts');
        $this->load->model("Products_m", 'Product');
        $this->load->model('violator_m', 'Violator');
        $this->load->model("report_m", 'Report');
        $this->load->model("store_m", 'Store');
        $this->load->model("Users_m", 'User');
        
        $this->last_crawl = $this->Crawl->last_crawl();
        $this->crawl_range = $this->Crawl->last_crawl_range();
                
        $this->data->my = 'pricingOverview';
        
        $this->record_per_page = $this->config->item("record_per_page");
        
    		// tell the view this is the pricing overview report
    		$this->data->report_name = 'Pricing Overview';
    		$this->data->report_type = 'pricingoverview';
    		$this->data->file_name = str_replace(' ', '_', 'Pricing Overview ' . date('Y-m-d'));
    
    		if ($reportId) 
    		{
    		    $this->_set_defaults($reportId);
    		}
    		
    		$this->data->number_of_merchants = getNumberOfMerchants(	
    		    $this->store_id,
    				$this->crawl_range['from'],
    				$this->crawl_range['to']
    		);
    		
    		$this->data->last_tracked_arr = $this->Report->last_tracked_image($this->crawl_range['from']);
    		$this->data->last_tracked_date = trackingDateFormat($this->crawl_range['from']);
    
    		// calculate the overview statistics
    		$this->data->products_monitored = $this->Product->getProductsMonitoredCount($this->store_id);
    		$this->data->total_violations = $this->Violator->countViolatedProducts($this->store_id);
    		
    		$this->data->marketplace_products = array();
    		$this->data->market_violations = array();
    
    		$markets = array_keys(get_market_lookup(TRUE,TRUE));
    		
    		foreach ($markets as $market)
    		{
    			$crawl_info = ! empty($this->last_crawl[$market]) ? $this->last_crawl[$market] : FALSE;
    			
    			// using global crawl time - change by Christophe on 9/2/2015 
    			if ($this->config->item('environment') == 'local')
    			{
    			    $crawl_info = TRUE;
    			}
    			
    			if ($crawl_info)
    			{            
            if ($this->config->item('environment') == 'local')
            {
                $from = '2015-08-01 00:00:00';
                $to = '2015-08-02 00:00:00';
            }
            else
            {
                //$from = date('Y-m-d H:i:s', strtotime('-24 hours'));
                //$to = date('Y-m-d H:i:s');
                $from = $crawl_info->start_datetime;
                $to = $crawl_info->end_datetime;
            } 
    			            
    				$marketplace_products = $this->MProducts->getCountByMarketplace($this->store_id, $market, $from, $to);
    								
    				if (!empty($marketplace_products))
    				{
                $this->data->marketplace_products[] = $marketplace_products[0];
                
                $market_violations = $this->Violator->getViolatedMarkets($this->store_id, '', $market, $from, $to);
                //$market_violations = $CI->Violator->getMarketViolations($this->store_id, '', $market);
                					
                $this->data->market_violations[$market] = $market_violations[$market];
    				}			
    			}
    		}
    
    		//for saving reports - not necessary to provide info for overview
    		$this->data->report_where = array('report_type' => 'pricingoverview');
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
    
    		// separate marketplaces from retailers
    		$this->data->marketplaces = array();
    		$this->data->retailers = array();
    		$this->data->violatedMarketplaces = array();
    		$this->data->violatedRetailers = array();
    		
    		for ($i = 0, $n = count($this->data->marketplace_products); $i < $n; $i++) 
    		{
    			$name = $this->data->marketplace_products[$i]['marketplace'];
    			
    			if ($this->data->marketplace_products[$i]['is_retailer'] === '1') 
    			{
    				$merchant = $this->MProducts->getMerchantDetailsByMarketplace($name);
    				
    				if (isset($merchant[0]['id'])) 
    				{
    					$this->data->marketplace_products[$i] = array_merge($this->data->marketplace_products[$i], $merchant[0]);
    					$this->data->retailers[] = $this->data->marketplace_products[$i];
    					
    					if ( ! empty($this->data->market_violations[$name]))
    					{
    						$this->data->violatedRetailers[$name] = TRUE;
    					}
    				}
    			}
    			else 
    			{
    				$this->data->marketplaces[] = $this->data->marketplace_products[$i];
    				
    				if ( ! empty($this->data->market_violations[$name]))
    				{
    					$this->data->violatedMarketplaces[$name] = TRUE;
    				}
    			}
    		}
    		
    		usort($this->data->marketplaces, function($a, $b) 
    		{
    			return strtolower($a['marketplace']) > strtolower($b['marketplace']);
    		});
    		
    		usort($this->data->retailers, function($a, $b) 
    		{
    			return strtolower($a['marketplace']) > strtolower($b['marketplace']);
    		});
    
    		$this->data->totalMarketplaces = count($this->data->marketplaces);
    		$this->data->totalRetailers = count($this->data->retailers);
    }
	
    /**
     * Show price violators.
     * 
     * @author unknown, Christophe
     */
    public function price_violators()
    {
        $this->data->report_name = 'Price Violators';
        
        $this->data->file_name = str_replace(' ', '_', 'Price Violators ' . date('m-d-Y'));
        
        $this->data->priceViolators = $this->_countPriceViolations();
    }
    
    /**
     * Reporting by month on product data.
     * 
     * @author Christophe
     */
    public function product_report()
    {
        ini_set('memory_limit', '512M');
        
        $this->load->model('products_m');
        
        // check to see if date_to after date_from
        if ($this->input->post('date_from') != FALSE && $this->input->post('date_to') != FALSE)
        {
            if ($this->input->post('date_to') >= $this->input->post('date_from'))
            {
                $start = $this->input->post('date_from');
                $end = $this->input->post('date_to');
            }
            else
            {
                $start = date('Y-m-d', strtotime('-1 months'));
                $end = date('Y-m-d');
            }
        }
        else
        {
            $start = date('Y-m-d', strtotime('-1 months'));
            $end = date('Y-m-d');
        }
        
        $start_time_int = strtotime($start);
        $end_time_int = strtotime($end);
        
        $violation_query = "            
            SELECT ptn.*
            FROM products_trends_new ptn
            JOIN products p ON p.id = ptn.pid
            WHERE ptn.dt >= {$start_time_int}
            AND ptn.dt <= {$end_time_int}
            AND p.store_id = {$this->store_id}
            AND ptn.mpo < ptn.ap
            AND ptn.ap > 0 
            ORDER BY ptn.dt ASC   
        ";
         
        $violations = $this->db->query($violation_query)->result_array();
        
        $monthly_data = array();
        $month_index_array = array();
        $product_id_array = array();
        
        $previous_row_day = '';
        $current_row_day = '';       
        
        for ($i = 0; $i < count($violations); $i++)
        {
            $product_id = intval($violations[$i]['pid']);
            $product_title = intval($violations[$i]['t']);
            $merchant_id = intval($violations[$i]['mid']);
            
            // see: http://php.net/manual/en/function.date.php
            $month_index = date('M, Y', $violations[$i]['dt']);
            
            $current_row_day = date('M j', $violations[$i]['dt']);
            
            if ($current_row_day != $previous_row_day)
            {
                // new day that we are going through
                // this array is used to make sure we only count 1 violation per product per merchant per day
                $single_day_tracking_array = array();
            }
            
            if (!in_array($month_index, $month_index_array))
            {
                $month_index_array[] = $month_index;
            }
            
            if (!in_array($product_id, $product_id_array))
            {
                $product_id_array[] = $product_id;
            }
            
            if (isset($monthly_data[$month_index][$product_id]))
            {
                if (!isset($single_day_tracking_array[$merchant_id][$product_id]))
                {
                    $monthly_data[$month_index][$product_id] = $monthly_data[$month_index][$product_id] + 1;
                    
                    $single_day_tracking_array[$merchant_id][$product_id] = TRUE;
                }
            }
            else
            {
                $monthly_data[$month_index][$product_id] = 1;
                
                $single_day_tracking_array[$merchant_id][$product_id] = TRUE;
            }
            
            $previous_row_day = $current_row_day;
        }
                
        /*
        var_dump($month_index_array);
        var_dump($product_id_array);
        var_dump($monthly_data); 
        exit();
        */
        
        $product_names = array();
        
        foreach ($product_id_array as $product_id)
        {
            $product = $this->products_m->get_product_by_id($product_id);
            
            $product_names[$product_id] = $product['title'];
        }       
        
        $this->data->month_index_array = $month_index_array;
        $this->data->product_id_array = $product_id_array;
        $this->data->monthly_data = $monthly_data;
        $this->data->product_names = $product_names;
        
        $this->data->report_name = 'Product Violation Summary';
        $this->data->report_where = array(
        		'fromDate' => $start,
        		'toDate' => $end,
        		'report_type' => 'violationoverview',
        		'report_function' => 'product_report',
            'time_frame' => ''
        );
        
        $this->data->time_frame = $this->input->post('time_frame') != FALSE ? $this->input->post('time_frame') : '';
        $this->data->display = TRUE;
        $this->data->is_first = '';
        $this->data->date_from = $start;
        $this->data->date_to = $end;
    }
    
    /**
     * Violation summary based on merchant violators.
     * 
     * @author Christophe
     */
    public function merchant_report()
    {
        ini_set('memory_limit', '512M');

        $this->load->model('merchant_products_m');
        $this->load->model('merchants_m');

        // check to see if date_to after date_from
        if ($this->input->post('date_from') != FALSE && $this->input->post('date_to') != FALSE)
        {
            if ($this->input->post('date_to') >= $this->input->post('date_from'))
            {
                $start = $this->input->post('date_from');
                $end = $this->input->post('date_to');
            }
            else
            {
                $start = date('Y-m-d', strtotime('-1 months'));
                $end = date('Y-m-d');
            }
        }
        else
        {
            $start = date('Y-m-d', strtotime('-1 months'));
            $end = date('Y-m-d');
        }

        $start_time_int = strtotime($start);
        $end_time_int = strtotime($end);

        $violation_query = "
            SELECT ptn.*
            FROM products_trends_new ptn
            JOIN products p ON p.id = ptn.pid
            WHERE ptn.dt >= {$start_time_int}
            AND ptn.dt <= {$end_time_int}
            AND p.store_id = {$this->store_id}
            AND ptn.mpo < ptn.ap
            AND ptn.ap > 0
            ORDER BY ptn.dt ASC
        ";
         
        $violations = $this->db->query($violation_query)->result_array();

        $monthly_data = array();
        $month_index_array = array();
        $merchant_id_array = array();
        
        $previous_row_day = '';
        $current_row_day = '';

        for ($i = 0; $i < count($violations); $i++)
        {
            $product_id = intval($violations[$i]['pid']);
            $product_title = intval($violations[$i]['t']);
            $merchant_id = intval($violations[$i]['mid']);

            $month_index = date('M, Y', $violations[$i]['dt']);

            if (!in_array($month_index, $month_index_array))
            {
                $month_index_array[] = $month_index;
            }

            if (!in_array($merchant_id, $merchant_id_array))
            {
                $merchant_id_array[] = $merchant_id;
            }
            
            $current_row_day = date('M j', $violations[$i]['dt']);
            
            if ($current_row_day != $previous_row_day)
            {
                // new day that we are going through
                // this array is used to make sure we only count 1 violation per product per merchant per day
                $single_day_tracking_array = array();
            }

            if (isset($monthly_data[$month_index][$merchant_id]))
            {
                if (!isset($single_day_tracking_array[$merchant_id][$product_id]))
                {
                    $monthly_data[$month_index][$merchant_id] = $monthly_data[$month_index][$merchant_id] + 1;
                    
                    $single_day_tracking_array[$merchant_id][$product_id] = TRUE;
                }
            }
            else
            {
                $monthly_data[$month_index][$merchant_id] = 1;
                
                $single_day_tracking_array[$merchant_id][$product_id] = TRUE;
            }
            
            $previous_row_day = $current_row_day;
        }

        $merchant_names = array();
        $merchant_websites = array();

        foreach ($merchant_id_array as $merchant_id)
        {
            $merchant = $this->merchant_products_m->getMerchantDetailsById($merchant_id);
            
            $merchant_names[$merchant_id] = $this->merchants_m->get_merchant_human_name($merchant);
            
            if ($merchant['original_name'] != $merchant['marketplace'])
            {
                $merchant['marketplace_url'] = $this->merchant_products_m->get_marketplace_seller_url($merchant, $merchant['marketplace']);
            }
            
            if ($merchant['original_name'] == $merchant['marketplace'] || $merchant['seller_id'] == $merchant['marketplace'])
            {
                $link_title = $merchant['merchant_url'];
                $merchant_url = $merchant['merchant_url'];
            }
            else
            {
                $link_title = ucfirst($merchant['marketplace']) . ' Seller Page';
                $merchant_url = $merchant['marketplace_url'];
            }
            
            $a_html = '<a href="' . $merchant_url . '" target="_blank">' . $link_title . '</a>';
            
            $merchant_websites[$merchant_id] = $a_html;
        }

        $this->data->month_index_array = $month_index_array;
        $this->data->merchant_id_array = $merchant_id_array;
        $this->data->monthly_data = $monthly_data;
        $this->data->merchant_names = $merchant_names;
        $this->data->merchant_websites = $merchant_websites;

        $this->data->report_name = 'Merchant Violation Summary';
        $this->data->report_where = array(
            'fromDate' => $start,
            'toDate' => $end,
            'report_type' => 'violationoverview',
            'report_function' => 'merchant_report',
            'time_frame' => ''
        );

        $this->data->time_frame = $this->input->post('time_frame') != FALSE ? $this->input->post('time_frame') : '';
        $this->data->display = TRUE;
        $this->data->is_first = '';
        $this->data->date_from = $start;
        $this->data->date_to = $end;
    }
	
    /**
     * Show violations by marketplace.
     * 
     * @author Christophe
     */
    public function violations_by_marketplace($id = FALSE)
    {
        // Tell the view this is the violation overview report
        $this->data->my = 'violationOverview';
        $this->data->report_name = 'Violation Overview';
        $this->data->file_name = str_replace(' ', '_', 'Violations Overview '.date('m-d-Y'));
        
        //if($id) $this->data->report_id = base64_decode(urldecode($id));
        if($id) $this->data->report_id = $id; // Christophe: what is this $id used for??
        
        // Get the last crawl data
        //$this->data->last_crawl = $this->crawl_data_m->last_crawl();
        // Christophe: Chris says that generally we have violation data if we look at the past
        // 48 hours of crawling (2 days is 2 param with below)
        $this->data->last_crawl = $this->crawl_data_m->last_crawl('all', 2); 
        
        $this->crawl_range = $this->crawl_data_m->last_crawl_range();
        $this->data->number_of_merchants = getNumberOfMerchants(
        	$this->store_id,
        	$this->crawl_range['from'],
        	$this->crawl_range['to']
        );
        
        //var_dump($this->data->last_crawl); exit();
        
        $this->data->last_tracked_arr = $this->Report->last_tracked_image($this->crawl_range['from']);
        $this->data->last_tracked_date = trackingDateFormat($this->crawl_range['from']);
        
        // Get market/merchant/product violations
        //$this->data->priceViolators = $this->_countPriceViolations();
        //$this->data->totalPriceViolators = count($this->data->priceViolators);
        
        $this->data->violatedProducts = $this->Violator->getViolatedProducts($this->store_id);
        
        // what does this do? - Christophe
        loadPricePoints($this->data->violatedProducts, strtotime($this->crawl_range['from']));
        
        // Calculate the overview statistics
        $this->data->products_monitored = $this->Product->getProductsMonitoredCount($this->store_id);
        $this->data->total_violations = count($this->data->violatedProducts);
        
        $this->data->marketplace_products = array();
        $this->data->market_violations = array();
        
        $markets = $this->Market->get_marketplaces_by_storeid_using_categories($this->store_id);
        
        //var_dump($markets); exit();
        
        //var_dump($this->data->last_crawl); exit();
        
        foreach ($markets as $market)
        {
            // check only marketplaces
            if ($market['is_retailer'] === '0')
            {
                $market = $market['name'];
                
                $crawl_info = ! empty($this->data->last_crawl[$market]) ? $this->data->last_crawl[$market] : FALSE;
                
                if ($this->config->item('environment') == 'local')
                {
                    $crawl_info = TRUE;
                }
                
                if ($crawl_info)
                {
                  if ($this->config->item('environment') == 'local')
                  {
                    	$from = '2015-08-01 00:00:00';
                    	$to = '2015-08-02 00:00:00';
                  }
                  else
                  {
                    	//$from = date('Y-m-d H:i:s', strtotime('-24 hours'));
                    	//$to = date('Y-m-d H:i:s');
                    	
                    	$from = $crawl_info->start_datetime;
                    	$to = $crawl_info->end_datetime;
                  }
                
                	// CS: get count of marketplace violations (gets all from products_trends_new table)
                	$marketplace_products = $this->merchant_products_m->getCountByMarketplace($this->store_id, $market, $from, $to, 1);
                	
                	//print_r($marketplace_products);
                	if (!empty($marketplace_products))
                	{
                		//$market_violations = $this->Violator->getViolatedMarkets($this->store_id, '', $market, $from, $to); // this is retarded
                		//print_r($market_violations);
                		$this->data->marketplace_products[] = $marketplace_products[0];
                		//$this->data->market_violations[$market] = $market_violations[$market];
                		$this->data->market_violations[$market] = $marketplace_products[0]['violated_products'];
                	}
                }
            }
        }	
         
        //exit();   
        
        $this->data->marketplaces = array();
        $this->data->retailers = array();
        $this->data->violatedMarketplaces = array();
        $this->data->violatedRetailers = array();
        
        //var_dump($this->data->marketplace_products);
        
        for ($i = 0, $n = count($this->data->marketplace_products); $i < $n; $i++)
        {
            $name = $this->data->marketplace_products[$i]['marketplace'];
        		
        		if ($this->data->marketplace_products[$i]['is_retailer'] === '1')
        		{
        		    
        	      $merchant = $this->merchant_products_m->getMerchantDetailsByMarketplace($this->data->marketplace_products[$i]['marketplace']);
    			
    			      if (isset($merchant[0]['id']))
        				{
        				    $this->data->marketplace_products[$i] = array_merge($this->data->marketplace_products[$i], $merchant[0]);
        				    
        				    $this->data->retailers[] = $this->data->marketplace_products[$i];
        				
        				    if (!empty($this->data->market_violations[$name]))
        				    {
        	              $this->data->violatedRetailers[$name] = TRUE;
        				    }
                }
                
            }
            else
            {
                $this->data->marketplaces[] = $this->data->marketplace_products[$i];
        
                if (!empty($this->data->market_violations[$name]))
                {
                    $this->data->violatedMarketplaces[$name] = TRUE;
                }
            }
        }
        
        //var_dump($this->data->violatedRetailers);
        //var_dump($this->data->marketplaces);
        //var_dump($this->data->violatedMarketplaces);
        //exit();
    }
    
    /**
     * Page to see violations for a single retailer.
     * 
     * Note: code brought over from report_marketplace()
     * 
     * @author Christophe
     * @param string $market
     * @param int $id
     */
    public function violations_by_retailer($market, $id = false)
    {
        $this->load->model('products_m');
        $this->load->model('crowl_m');        
        
        $this->data->market = $this->Market->get_marketplace_by_name($market);
        $this->data->market_shortname = $market;
        
        if ($id)
        { 
            $this->data->report_id = base64_decode(urldecode($id));
        }
        
        //var_dump($this->data->market); exit();
        
        // merchant is different DB table that marketplaces -- Christophe
        $merchant_info = $this->merchant_products_m->getMerchantDetailsBySellerId($market, $market);
        
        //var_dump($merchant_info); exit();
        
        $this->data->report_name = $this->data->market['display_name'] . ' Violations';
        $this->data->file_name = str_replace(' ', '_', $this->data->report_name);
        $this->data->my = 'pricingviolator';
        $this->data->icon = 'ico-report';
        $this->data->widget = 'mv-report';
        $this->data->displayBookmark = true;

        $this->data->report_info = array('report_name' => $this->data->report_name);
        $this->data->report_where = array(
        		'report_function' => 'report_marketplace',
        		'marketplace' => $market,
        		'report_type' => 'violationdetails'
        );
        
        $this->data->crawl_start = $this->Violator->crawlStart;
        $this->data->crawl_end = $this->Violator->crawlEnd;
        
        //var_dump($this->data->crawl_start); exit();
        
        $this->data->date_from = date('Y-m-d', strtotime($this->Violator->crawlStart));
        $this->data->date_to = date('Y-m-d', strtotime($this->Violator->crawlEnd));
        
        $this->data->show_most_recent = TRUE; // default value
        
        if (!empty($_POST))
        {
            //var_dump($_POST); exit();
            
            $this->data->crawl_start = $_POST['date_from'] . ' 00:00:00';
            $this->data->crawl_end = $_POST['date_to'] . ' 23:59:59';
            
            $this->data->date_from = date('Y-m-d', strtotime($this->data->crawl_start));
            $this->data->date_to = date('Y-m-d', strtotime($this->data->crawl_end));
            
            $this->data->show_most_recent = isset($_POST['show_most_recent']) ? TRUE : FALSE;
        }
        
        $start_time_int = strtotime($this->data->crawl_start);
        $end_time_int = strtotime($this->data->crawl_end);
         
        $merchant_id = intval($merchant_info['id']);
        
        $violation_query = "
            SELECT *
            FROM products_trends_new
            WHERE mid = {$merchant_id}
            AND dt >= {$start_time_int}
            AND dt <= {$end_time_int}
            AND mpo < ap
            ORDER BY dt DESC
        ";
        
        //var_dump($violation_query); exit();
         
        $priceTrends = $this->db->query($violation_query)->result_array();
        
        //var_dump($priceTrends); exit();
        //echo count($priceTrends); exit();
         
        $finalViolationsArray = array();
        
        $duplicate_check_array = array();
         
        foreach ($priceTrends as $priceTrend)
        {
            //safety hack to not show incorrect violations
            if ((float)$priceTrend['mpo'] >= (float)$priceTrend['ap'])
            {
        	      continue;
            }
            
            $product_id = intval($priceTrend['pid']);
            
            if ($this->data->show_most_recent)
            {
                if (isset($duplicate_check_array[$product_id]))
                {
                    continue;
                }
            }
        
        	  $merchant_name = empty($merchant_info) || $merchant_info == FALSE ? 'N/A' : $merchant_info['merchant_name'];
                	  
        	  $product = $this->products_m->get_product_by_id($priceTrend['pid']);
        	  
        	  // Christophe: database queries to product_trends_new are slow, so faster to put processing
        	  // here with PHP, and just go through all rows
        	  if (intval($product['store_id']) != intval($this->store_id))
        	  {
        	      continue;
        	  }
        	  
        	  $violationArray = array(
        	      'productId' => (int)$priceTrend['pid'],
        	      'upc_code'  => (string)$priceTrend['upc'],
        	      'retail'    => (float) $priceTrend['rp'],
        	      'wholesale' => (float)$priceTrend['wp'],
        	      'price' 		=> (float)$priceTrend['mpo'],
        			  'map' 			=> (float)$priceTrend['ap'],
                'title' 		=> (string)$product['title'], //$priceTrend->t
        				'marketplace' 	=> (string)$priceTrend['ar'],
        				'url' 			=> (string)$priceTrend['l'],
        				'timestamp'		=> (int)$priceTrend['dt'],
        				'hash_key'		=> (string)$priceTrend['um'],
        				'merchant_id' 	=> (string)$priceTrend['mid'],
        				'original_name' => $merchant_name,
        				'date' 			=> (string)date('m/d/Y G:i:s', (int)$priceTrend['dt']),
        				'shot' 			=> (string)$priceTrend['ss']
        		);
        
        		$finalViolationsArray[] = $violationArray;
        		
        		$duplicate_check_array[$product_id] = TRUE;
        }
        													 
        //var_dump($finalViolationsArray); exit();
	  
        $this->data->violations = $finalViolationsArray;
        
        $this->load->view('violationoverview/violations_by_retailer', $this->data, true);        
    }
    
    /**
     * 
     * @author Christophe
     * @param int $id
     */
    public function violations_by_retailers($id = FALSE)
    {
        $this->load->model('crowl_merchant_name_m');
        
        // Tell the view this is the violation overview report
        $this->data->my = 'violationOverview';
        $this->data->report_name = 'Violation Overview';
        $this->data->file_name = str_replace(' ', '_', 'Violations Overview '.date('m-d-Y'));
        
        //if($id) $this->data->report_id = base64_decode(urldecode($id));
        if ($id) $this->data->report_id = $id;
        
        // Get the last crawl data
        $this->data->last_crawl = $this->crawl_data_m->last_crawl();
        $this->crawl_range = $this->crawl_data_m->last_crawl_range();
        
        $this->data->crawl_range = $this->crawl_range;
        
        $this->data->number_of_merchants = getNumberOfMerchants(
        		$this->store_id,
        		$this->crawl_range['from'],
        		$this->crawl_range['to']
        );
        
        $this->data->last_tracked_arr = $this->Report->last_tracked_image($this->crawl_range['from']);
        $this->data->last_tracked_date = trackingDateFormat($this->crawl_range['from']);
        
        if ($this->config->item('environment') == 'local')
        {
            $crawl_range_from_int = strtotime('2015-12-19 00:00:00');
            $crawl_range_to_int = strtotime('2015-12-15 00:00:00');
        }
        else
        {
            $crawl_range_from_int = strtotime($this->crawl_range['from']);
            $crawl_range_to_int = strtotime($this->crawl_range['to']);
        }
        
        $violation_query = "
            SELECT cpl.*
            FROM crowl_product_list_new cpl
            INNER JOIN products p ON p.upc_code = cpl.upc  
            INNER JOIN marketplaces m ON m.name = cpl.marketplace                      
            WHERE cpl.violated = 1
            AND m.is_retailer = 1
            AND m.is_active = 1
            AND p.store_id = {$this->store_id}
            AND cpl.last_date >= {$crawl_range_from_int}
            AND cpl.last_date <= {$crawl_range_to_int}
        ";
        
        //var_dump($violation_query); exit();
        
        // local test
        /*
        $violation_query = "
            SELECT cpl.*
            FROM crowl_product_list_new cpl
            WHERE cpl.violated = 1
            LIMIT 500
        ";
        */        
         
        $violated_products = $this->db->query($violation_query)->result_array();
        
        //var_dump($violated_products); exit();
        
        $merchant_ids = array();
        
        $retailers = array();
        
        foreach ($violated_products as $violated_product)
        {
            $merchant_id = intval($violated_product['merchant_name_id']);
            
            if (!in_array($merchant_id, $merchant_ids))
            {
                $merchant_ids[] = $merchant_id;
                
                $merchant = $this->crowl_merchant_name_m->get_merchant_by_id($merchant_id);
                
                $retailers[$merchant_id] = array(
                    'merchant_name_short' => $violated_product['marketplace'],
                    'merchant_name' => ucfirst($merchant['original_name'] . '.com'), 
                    'product_violation_count' => 1,
                    'last_track_date' => $violated_product['last_date']
                );
            }
            else
            {   
                $retailers[$merchant_id]['product_violation_count'] = $retailers[$merchant_id]['product_violation_count'] + 1;
            }
            
            if ($violated_product['last_date'] > $retailers[$merchant_id]['last_track_date'])
            {
                $retailers[$merchant_id]['last_track_date'] = $violated_product['last_date'];
            }
        }
        
        //var_dump($retailers); exit();
        
        $this->data->retailers = $retailers;
    }
	
    /**
     * Violations by retailers.
     * 
     * @author Christophe
     */
    public function violations_by_retailers_old($id = false)
    {
        // Tell the view this is the violation overview report
        $this->data->my = 'violationOverview';
        $this->data->report_name = 'Violation Overview';
        $this->data->file_name = str_replace(' ', '_', 'Violations Overview '.date('m-d-Y'));
        
        //if($id) $this->data->report_id = base64_decode(urldecode($id));
        if($id) $this->data->report_id = $id;
        
        // Get the last crawl data
        $this->data->last_crawl = $this->crawl_data_m->last_crawl();
        $this->crawl_range = $this->crawl_data_m->last_crawl_range();
        
        $this->data->crawl_range = $this->crawl_range;
        
        $this->data->number_of_merchants = getNumberOfMerchants(
        	$this->store_id,
        	$this->crawl_range['from'],
        	$this->crawl_range['to']
        );
        $this->data->last_tracked_arr = $this->Report->last_tracked_image($this->crawl_range['from']);
        $this->data->last_tracked_date = trackingDateFormat($this->crawl_range['from']);
        
        // Get market/merchant/product violations
        $this->data->priceViolators = $this->_countPriceViolations();
        
        $this->data->totalPriceViolators = count($this->data->priceViolators);
        $this->data->violatedProducts = $this->Violator->getViolatedProducts($this->store_id);
        loadPricePoints($this->data->violatedProducts, strtotime($this->crawl_range['from']));
        
        // Calculate the overview statistics
        $this->data->products_monitored = $this->Product->getProductsMonitoredCount($this->store_id);
        $this->data->total_violations = count($this->data->violatedProducts);
        $this->data->marketplace_products = array();
        $this->data->market_violations = array();
        
        $markets = $this->Market->get_marketplaces_by_storeid_using_categories($this->store_id);
        
        //var_dump($markets); exit();
        
        foreach ($markets as $market)
        {
            $market = $market['name'];
            
            $crawl_info = ! empty($this->data->last_crawl[$market]) ? $this->data->last_crawl[$market] : FALSE;
            
            //var_dump($crawl_info);
            
            // local env debug
            if ($this->config->item('environment') == 'local')
            {
                $crawl_info = TRUE;
            }
            
            if ($crawl_info)
            {            
                // local env debug
                if ($this->config->item('environment') == 'local')
                {
                    $from = $this->data->crawl_start = $this->Violator->crawlStart;
                    $to = $this->data->crawl_end = $this->Violator->crawlEnd;
                }
                else
                {
                    $from = $this->crawl_range['from'];
                    $to = $this->crawl_range['to'];
                    
                    //$from = $crawl_info->start_datetime;
                    //$to = $crawl_info->end_datetime;
                }
                
                $marketplace_products = $this->merchant_products_m->getCountByMarketplace($this->store_id, $market, $from, $to, 1);
                
                //print_r($marketplace_products);
                
                if (!empty($marketplace_products))
                {
                    //$market_violations = $this->Violator->getViolatedMarkets($this->store_id, '', $market, $from, $to); // this is retarded
                    //print_r($market_violations);
                    $this->data->marketplace_products[] = $marketplace_products[0];
                    
                    //$this->data->market_violations[$market] = $market_violations[$market];
                    $this->data->market_violations[$market] = $marketplace_products[0]['violated_products'];
                }
            }
        }
        
        //var_dump($this->data->marketplace_products); exit();
        
        $this->data->marketplaces = array();
        $this->data->retailers = array();
        $this->data->violatedMarketplaces = array();
        $this->data->violatedRetailers = array();
        
        for ($i = 0, $n = count($this->data->marketplace_products); $i < $n; $i++)
        {
            $name = $this->data->marketplace_products[$i]['marketplace'];
            
            if ($this->data->marketplace_products[$i]['is_retailer'] === '1')
            {
                $merchant = $this->merchant_products_m->getMerchantDetailsByMarketplace($this->data->marketplace_products[$i]['marketplace']);
                 
                if (isset($merchant[0]['id']))
                {
                    $this->data->marketplace_products[$i] = array_merge($this->data->marketplace_products[$i], $merchant[0]);
                    
                    $this->data->retailers[] = $this->data->marketplace_products[$i];
                    
                    if (!empty($this->data->market_violations[$name]))
                    {
                        $this->data->violatedRetailers[$name] = TRUE;
                    }
                }
            }
            else
            {
                $this->data->marketplaces[] = $this->data->marketplace_products[$i];
            
                if (!empty($this->data->market_violations[$name]))
                {
            		    $this->data->violatedMarketplaces[$name] = TRUE;
                }
            }
        }   

        //$this->data->crawl_start = $this->Violator->crawlStart;
        //$this->data->crawl_end = $this->Violator->crawlEnd;
    }

    /**
     * 
     * @author unknown
     * @return array
     */
    protected function _countPriceViolations()
    {
        $violators = $this->Violator->lastCrawlViolators($this->store_id);
        
        $email_levels = array(
            1	=> "First",
            2	=> "Second",
            3	=> "Third",
            4	=> "Fourth",
            5	=> "Fifth",
            6	=> "Sixth",
            7	=> "Seventh",
            8	=> "Eighth",
            9	=> "Ninth",
            10	=> "Tenth"
        );
        
        if ( ! empty($violators))
        {
            foreach ($violators['violators'] as $id => $violations)
            {
                $crowl_merchant = $this->merchant_products_m->getMerchantDetailsById($id);
                
                $violators['violators'][$id] = array(
                	'crowl_merchant' => $crowl_merchant,
                	'total_violations' => isset($violations['products']) ? $violations['products'] : 0,
                	'total_products' => isset($violations['violations']) ? $violations['violations'] : 0,
                	'violation_status' => 'None',
                	'repeat_vioaltor' => 'N',
                	'last_violator' => ''
                );
                				
                $_violations = $this->Violator->getSellerViolations($this->store_id, $id, 0, strtotime('now'));
                
                //var_dump($_violations); exit();
                
                if (!empty($_violations[0]->last_date)) 
                {
                    $violators['violators'][$id]['last_violator'] = date('m/d/Y', $_violations[0]->last_date);
                }				
                
                $last_history = $this->Violator->getLastViolationNotificationHistory($this->store_id, $id);
                
                if ($last_history !== FALSE ) 
                {
                    $violators['violators'][$id]['violation_status'] = " ".$email_levels[$last_history['email_level']];
                    $violators['violators'][$id]['repeat_vioaltor'] = ($last_history['email_repeat']>1 ? $last_history['email_repeat']:"N");
                }
                
                $first_notification = $this->Violator->getViolationStreak($this->store_id, $id);
                              
                if ($first_notification)
                {
                    $violators['violators'][$id]['first_violation_date'] = date('m/d/Y', strtotime($first_notification['streak_start']));
                }
                else
                {
                    $violators['violators'][$id]['first_violation_date'] = 'N/A';    
                }
            }
        }
        
        return $violators['violators'];
    }

	/**
	 * 
	 * @author unknown
	 */
	function graphDefaultReports()
	{
		echo "cp:".$this->countProduct."<br>";
		echo  "cv: ".$this->countViolation."<br>";

		$name = $this->input->post('name');
		switch ($name)
		{
		case 'overview':
			$totalProducts  = $this->countProduct;
			$total_violations = $this->countViolation;
			$notViolation = $totalProducts - $total_violations;
			$notViolation = ($notViolation < 0)?0:$notViolation;
			$myData = array($total_violations.' In violation'=>0, $notViolation.' Not in violation'=>0);

			if
			($totalProducts > 0)
			{
				$myData[$total_violations.' In violation'] = $total_violations;
				$myData[$notViolation.' Not in violation'] = $notViolation;
			}
			break;
		}

		echo json_encode($myData);
		exit;
	}
	
    /**
     * Page that shows all current retailer violations.
     * 
     * @author Christophe
     */
    public function all_retailer_violations()
    {
        //$this->data->violatedProducts = $this->Violator->getViolatedProducts($this->store_id);
        
        //var_dump($this->db->last_query()); exit();
        
        //var_dump(count($this->data->violatedProducts)); exit();
  
        $this->data->report_name = 'Retailer Violations';
        $this->data->file_name = str_replace(' ', '_', $this->data->report_name);
        $this->data->my = 'pricingviolator';
        $this->data->icon = 'ico-report';
        $this->data->widget = 'mv-report';
        $this->data->displayBookmark = true;
        $this->data->report_info = array('report_name' => $this->data->report_name);
        $this->data->report_where = array(
        		'report_function' => 'all_retailer_violations',
        		'report_type' => 'violationdetails');
        
        $violations = $this->Violator->get_retailer_violations_by_store($this->store_id);
        
        //var_dump(count($violations)); exit();
        
        $this->data->violations = $violations;
        
        $this->data->crawl_start = $this->Violator->crawlStart;
        $this->data->crawl_end = $this->Violator->crawlEnd;
        
        //$this->load->view('violationoverview/report_marketplace', $this->data, true);        
    } 

    /**
     * Show violations that are happening on a specific marketplace.
     * 
     * @author unknown, Christophe
     * @param string $market
     * @param int $id
     */
    function report_marketplace($market, $id = false)
    {
        $this->load->model('products_m');
        
        $this->data->market = $this->Market->get_marketplace_by_name($market);
        
        if($id) $this->data->report_id = base64_decode(urldecode($id));
        
        $this->data->report_name =$this->data->market['display_name'] . ' Marketplace Violations';
        $this->data->file_name = str_replace(' ', '_', $this->data->report_name);
        $this->data->my = 'pricingviolator';
        $this->data->icon = 'ico-report';
        $this->data->widget = 'mv-report';
        $this->data->displayBookmark = true;
        $this->data->report_info = array('report_name' => $this->data->report_name);
        $this->data->report_where = array(
            'report_function' => 'report_marketplace',
            'marketplace' => $market,
            'report_type' => 'violationdetails'
        );
        
        // CS: checkthis
        //$this->data->violations = $this->Violator->getViolatedMarketsProducts($this->store_id, $market);
        
        $violation_query = "
            SELECT DISTINCT merchant_name_id
            FROM crowl_product_list_new cpl
            INNER JOIN products p ON p.upc_code = cpl.upc
            WHERE p.store_id = {$this->store_id}
            AND cpl.marketplace = '{$market}'
        ";
        
        $marketplace_merchants = $this->db->query($violation_query)->result_array();
        
        //var_dump($marketplace_merchants); exit();
        
        $merchant_ids = array();
        
        foreach ($marketplace_merchants as $merchant)
        {
            $merchant_ids[] = intval($merchant['merchant_name_id']);
        }
        
        $merchant_ids_str = implode($merchant_ids, ', ');
        
        $this->data->merchant_ids_str = $merchant_ids_str;
        $this->data->market_name = $market;
        
        //$this->data->crawl_start = $this->Violator->crawlStart;
        //$this->data->crawl_end = $this->Violator->crawlEnd;
        
        if ($this->config->item('environment') == 'local')
        {
            $this->data->crawl_start = '2015-08-01 00:00:00';
            $this->data->crawl_end = '2015-08-02 00:00:00';
        }
        else
        {
            $this->data->last_crawl = $this->crawl_data_m->last_crawl('all', 2);
            
            $crawl_info = ! empty($this->data->last_crawl[$market]) ? $this->data->last_crawl[$market] : FALSE;
            
            if ($crawl_info)
            {
                $this->data->crawl_start = $crawl_info->start_datetime;
                $this->data->crawl_end = $crawl_info->end_datetime;
            }
            else
            {
                $this->data->crawl_start = date('Y-m-d H:i:s', strtotime('-48 hours'));
                $this->data->crawl_end = date('Y-m-d H:i:s');
            }          
        }
        
        //var_dump($this->data->crawl_start);
        //var_dump($this->data->crawl_end);
         
        $start_time_int = strtotime($this->data->crawl_start);
        $end_time_int = strtotime($this->data->crawl_end);
         
        /*
        $violation_query = "
            SELECT *
            FROM products_trends_new
            WHERE ar = '{$market}'
            AND dt >= {$start_time_int}
            AND dt <= {$end_time_int}
            AND mpo < ap
        ";
        */
        
        $violation_query = "
            SELECT ptn.*, p.*
            FROM products_trends_new ptn
            JOIN products p ON p.id = ptn.pid
            WHERE ptn.mid IN ({$merchant_ids_str})
            AND ptn.dt >= {$start_time_int}
            AND ptn.dt <= {$end_time_int}
            AND ptn.mpo < ptn.ap
            AND p.store_id = {$this->store_id}
        ";
        
        $this->data->violation_query = $violation_query; // for debug
        //$this->data->violation_query = '';
         
        $priceTrends = $this->db->query($violation_query)->result_array();
        
        $finalViolationsArray = array();
         
        foreach ($priceTrends as $priceTrend)
        {
            // safety hack to not show incorrect violations
            // is MAP price greatar than price recorded at
            if ((float)$priceTrend['mpo'] > (float)$priceTrend['ap'])
            {
            	continue;
            }
            
            $merchant_info = $this->merchant_products_m->getMerchantDetailsById($priceTrend['mid']);
            
            $merchant_info['marketplace_seller_url'] = $this->merchant_products_m->get_marketplace_seller_url($merchant_info, $market);
            
            if (empty($merchant_info) || $merchant_info == FALSE)
            {
                $merchant_name = ucfirst($market) . ' Seller';
            }
            else
            {
                if (isset($merchant_info['merchant_name']))
                {
                    $merchant_name = $merchant_info['merchant_name'];
                }
                else if (isset($merchant_info['original_name']))
                {
                    $merchant_name = $merchant_info['original_name'];
                }
                else
                {
                    $merchant_name = ucfirst($market) . ' Seller';
                }
            }
             
            //$product = $this->products_m->get_product_by_id($priceTrend['pid']);
             
            // Christophe: database queries to product_trends_new are slow, so faster to put processing
            // here with PHP, and just go through all rows
            /*
            if (intval($product['store_id']) != intval($this->store_id))
            {
            	continue;
            }
            */
             
            $violationArray = array(
                'merchant' => $merchant_info, 
                'merchant_id' => (int)$priceTrend['mid'],                           
            		'productId' => (int)$priceTrend['pid'],
            		'upc_code'  => (string)$priceTrend['upc'],
            		'retail'    => (float) $priceTrend['rp'],
            		'wholesale' => (float)$priceTrend['wp'],
            		'price' 		=> (float)$priceTrend['mpo'],
            		'map' 			=> (float)$priceTrend['ap'],
            		//'title' 		=> (string)$product['title'], //$priceTrend->t
                'title' 		=> (string)$priceTrend['title'],
            		'marketplace' 	=> (string)$priceTrend['ar'],
            		'url' 			=> (string)$priceTrend['l'],
            		'timestamp'		=> (int)$priceTrend['dt'],
            		'hash_key'		=> (string)$priceTrend['um'],
            		'merchant_id' 	=> (string)$priceTrend['mid'],
            		'original_name' => $merchant_name,
            		'date' 			=> (string)date('m/d/Y G:i:s', (int)$priceTrend['dt']),
            		'shot' 			=> (string)$priceTrend['ss']
            );
            
            $finalViolationsArray[] = $violationArray;
        }
        
        //var_dump($finalViolationsArray); exit();
         
        $this->data->violations = $finalViolationsArray;        
        
        //echo count($priceTrends); exit();
        
        /*
        if ($this->Market->is_retailer($market))
        {
        	$merchant_info = $this->merchant_products_m->getMerchantDetailsBySellerId($market, $market);
        	if ( ! empty($merchant_info['id']))
        	{
        		$merchant = $merchant_info['id'];
        		$this->data->violator_notification = $this->Store->get_violator_notification_by_seller($merchant, $this->store_id);
        		$this->data->show_notify_resource = $merchant;
        		$this->data->original_name = trim($merchant_info['original_name']);
        		$this->data->smtp = $this->Store->get_store_smtp_by_store($this->store_id);
        	}
        }
        */
        
        $this->load->view('violationoverview/report_marketplace', $this->data, true);
    }

    /**
     * This is to show records for violators & products found during last crawl
     * 
     * @author unknown
     * @param int $merchantId
     * @param int $id
     */
    public function violator_report($merchantId, $id = false)
    {   
        if ($id) 
        {
            $this->data->report_id = base64_decode(urldecode($id));
        }
        
        $this->data->merchant = $this->merchant_products_m->getMerchantDetailsById($merchantId);
        
        //var_dump($this->data->merchant); exit();
        
        if (empty($this->data->merchant))
        {
            $this->session->set_flashdata('error_msg', 'Merchant data is not available at this time.');
            
            redirect('/violationoverview');
            exit();
        }
        
        $this->data->report_name = trim($this->data->merchant['original_name']).' Violated Products';
        $this->data->file_name = str_replace(' ', '_', $this->data->report_name.' '.date('Y-m-d'));        
        
        $this->data->my = 'pricingviolator';
        $this->data->icon = 'ico-report';
        $this->data->widget = 'mv-report';
        $this->data->displayBookmark = TRUE;
        
        $this->data->violations = $this->Violator->getViolatorReport($this->store_id, $this->data->merchant);
        
        $this->data->report_where = array(
        	'fromDate' => 'Start',
        	'toDate' => 'Stop',
        	'cron_ids' => array(),
        	'time_frame' => 1,
        	'report_type' => 'violationoverview',
        	'report_function' => 'violator_report',
        	'user_id' => (int)$this->data->merchant['id']
        );
        
        // violator notification data
        $this->data->violator_notification = $this->Store->get_violator_notification_by_seller($this->data->merchant['id'], $this->store_id);
        
        $this->data->show_notify_resource = $merchantId;
        
        $this->data->original_name = trim($this->data->merchant['original_name']);
        
        $this->data->smtp = $this->Store->get_store_smtp_by_store($this->store_id);
        
        $this->load->view('violationoverview/violator_report', $this->data, TRUE);
    }
	
	/**
	 * Show violations for a product.
	 * 
	 * @author Christophe
	 * @param int $product_id
	 */
	function violated_product($product_id)
	{
	    $this->load->model('products_m');
	    $this->load->model('crowl_m');
	    
	    $product_id = intval($product_id);
	    
	    $product = $this->products_m->get_product_by_id($product_id);
	    
	    if (empty($product))
	    {
	        $this->session->set_flashdata('error_msg', 'Error: Product not found or can not be accessed at this time.');
	         
	        redirect('/');
	        exit();
	    }
	    
	    // check to see if user can access product
	    if ($this->store_id != intval($product['store_id']))
	    {
	        $this->session->set_flashdata('error_msg', 'Error: Your account does not have access to this item.');
	        
	        redirect('/');
	        exit();
	    }
	    
	    $this->data->report_name = $product['title'].' Violations';
	    $this->data->file_name = str_replace(' ', '_', $this->data->report_name.'_'.date('Y-m-d'));
	    
	    $now = time();
	    
	    $this->data->report_where = array(
	    		'fromDate' => 'Start',
	    		'toDate' => 'Stop',
	    		'time_frame' => 1,
	    		'report_type' => 'violationoverview',
	    		'report_function' => 'violated_product',
	    		'product_id' => $product_id
	    );
	    
	    $this->data->my = 'pricingviolator';
	    $this->data->icon = 'ico-report';
	    $this->data->widget = 'mv-report';
	    $this->data->displayBookmark = true;
	     
	    $this->data->product = $product;
	    
	    $this->data->crawl_start = $this->Violator->crawlStart;
	    $this->data->crawl_end = $this->Violator->crawlEnd;
	    
	    $start_time_int = strtotime($this->data->crawl_start);
	    $end_time_int = strtotime($this->data->crawl_end);
	    
	    $violation_query = "
        SELECT * 
        FROM products_trends_new
        WHERE upc = '{$product['upc_code']}'
        AND dt >= {$start_time_int}
        AND dt <= {$end_time_int}
        AND mpo < ap  
	    ";
	    
	    $priceTrends = $this->db->query($violation_query)->result_array();
	    
	    $finalViolationsArray = array();
	    
	    foreach ($priceTrends as $priceTrend)
	    {
        //safety hack to not show incorrect violations
        if ((float)$priceTrend['mpo'] >= (float)$priceTrend['ap'])
        {
        	continue;
        }
        
        // get merchant details
        $merchant = $this->crowl_m->get_merchant_from_id($priceTrend['mid']);
        
        $merchant_name = empty($merchant) || $merchant == FALSE ? 'N/A' : $merchant['merchant_name'];
        
        $violationArray = array(
        		'productId' 	=> (int)$priceTrend['pid'],
        		'upc_code' 		=> (string)$priceTrend['upc'],
        		'retail' 		=> (float) $priceTrend['rp'],
        		'wholesale' 	=> (float)$priceTrend['wp'],
        		'price' 		=> (float)$priceTrend['mpo'],
        		'map' 			=> (float)$priceTrend['ap'],
        		'title' 		=> (string)$product['title'], //$priceTrend->t
        		'marketplace' 	=> (string)$priceTrend['ar'],
        		'url' 			=> (string)$priceTrend['l'],
        		'timestamp'		=> (int)$priceTrend['dt'],
        		'hash_key'		=> (string)$priceTrend['um'],
        		'merchant_id' 	=> (string)$priceTrend['mid'],
        		'original_name' => $merchant_name,
        		'date' 			=> (string)date('m/d/Y G:i:s', (int)$priceTrend['dt']),
        		'shot' 			=> (string)$priceTrend['ss']
        );
        
        $finalViolationsArray[] = $violationArray;
	    }
	    
	    //var_dump($finalViolationsArray); exit();
	    
	    $this->data->violations = $finalViolationsArray;
	}
	
    /**
     * Way for user to see violation message that was sent out.
     * 
     * @author Christophe
     * @param int $notice_id
     */
    public function sent_notice_message($notice_id)
    {
        $this->load->model('violator_m');
        
        $notice_id = intval($notice_id);
        
        $notice = $this->violator_m->get_notification_by_id($notice_id);
        
        if (intval($notice['store_id']) != intval($this->store_id))
        {
            echo '<html><body>Access denied for this message.</body></html>';
        }
        else 
        {
            echo '<html><body><div style="background-color:#fff;">' . $notice['full_message'] . '</div></body></html>';
        }
        
        exit();
    }
	
    /**
     * Page where users can see a log of violation notices that were sent out.
     * 
     * @author Christophe
     */
    public function sent_notices()
    {
        $this->load->model('violator_m');
        $this->load->model('crowl_merchant_name_m');
        
        //$start = date('Y-m-d H:i:s', strtotime('-5 months'));
        //$end = date('Y-m-d H:i:s');
        
        // check to see if date_to after date_from
        if ($this->input->post('date_from') != FALSE && $this->input->post('date_to') != FALSE)
        {
            if ($this->input->post('date_to') >= $this->input->post('date_from'))
            {           
                $start = $this->input->post('date_from') . ' 00:00:00';
                $end = $this->input->post('date_to') . ' 23:59:59'; 
                
                $date_from = $this->input->post('date_from');
                $date_to = $this->input->post('date_to');
            }
            else
            {
                $start = date('Y-m-d', strtotime('-1 months')) . ' 00:00:00';
                $end = date('Y-m-d') . ' 23:59:59'; 

                $date_from = date('Y-m-d', strtotime('-1 months'));
                $date_to = date('Y-m-d');
            }
        }
        else
        {
            $start = date('Y-m-d', strtotime('-1 months')) . ' 00:00:00';
            $end = date('Y-m-d') . ' 23:59:59';   

            $date_from = date('Y-m-d', strtotime('-1 months'));
            $date_to = date('Y-m-d');
        }
        
        // get sent notice records from the violator_notifications_history table
        $notices = $this->violator_m->get_notifications_by_store($this->store_id, $start, $end);
        
        $final_notices_array = array();
        
        for ($i = 0; $i < count($notices); $i++)
        {
            if ($notices[$i]['email_to'] != 'support@trackstreet.com')
            {
                $merchant_id = $notices[$i]['crowl_merchant_name_id'];
                
                $merchant = $this->crowl_merchant_name_m->get_merchant_by_id($merchant_id);
                
                $notices[$i]['merchant'] = $merchant;
                $notices[$i]['merchant_id'] = $merchant_id;
                
                $final_notices_array[] = $notices[$i];
            }
        }
        
        $view_data['notices'] = $final_notices_array;
        
        $view_data['time_frame'] = $this->input->post('time_frame') != FALSE ? $this->input->post('time_frame') : '';
        $view_data['display'] = TRUE;
        $view_data['is_first'] = '';
        $view_data['date_from'] = $date_from;
        $view_data['date_to'] = $date_to;
        
        $this->load->view('violationoverview/sent_notices', $view_data);
    }	
}