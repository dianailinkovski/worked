<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Overview extends MY_Controller
{
    public function __construct() 
    {
        parent::__construct();
        
        $this->load->model("crawl_data_m", 'Crawl');
        $this->load->model("merchant_products_m", 'MProducts');
        $this->load->model("Products_m", 'Product');
        $this->load->model('violator_m', 'Violator');
        $this->load->model("report_m", 'Report');
        $this->load->model("store_m", 'Store');
        $this->load->model("Users_m", 'User');
        
        $this->load->library('reportinfo');
        
        $this->data->my = 'pricingOverview';
        
        $this->record_per_page = $this->config->item("record_per_page");
    }
    
    /**
     * AJAX call to get widget which shows average # of products sold for all merchants per day.
     * 
     * @author Christophe
     */
    public function average_products_sold()
    {
        $this->_layout = 'ajax_html';
        
        $start_date = date('Y-m-d', strtotime('-180 days'));
        $end_date = date('Y-m-d', strtotime('-1 day'));
        
        $query_str = "
            SELECT select_date, SUM(product_count) AS product_count
            FROM products_per_merchant_per_day
            WHERE store_id = {$this->store_id}
            AND select_date >= '{$start_date}'
            AND select_date <= '{$end_date}'
            GROUP BY select_date
            ORDER BY select_date ASC
        ";
        
        $product_data_points = $this->db->query($query_str)->result_array();
        
        $query_str = "
            SELECT select_date, COUNT(*) AS merchant_count
            FROM products_per_merchant_per_day
            WHERE store_id = {$this->store_id}
            AND select_date >= '{$start_date}'
            AND select_date <= '{$end_date}'
            GROUP BY select_date
            ORDER BY select_date ASC
        ";
        
        $merchant_count_data_points = $this->db->query($query_str)->result_array();
        
        $merchant_counts = array();
        
        // create new array that contains counts per day of # of merchants
        foreach ($merchant_count_data_points as $merchant_count_data_point)
        {
            $merchant_counts[$merchant_count_data_point['select_date']] = $merchant_count_data_point['merchant_count'];
        }
        
        $average_product_data_points = array();
        
        foreach ($product_data_points as $data_point)
        {
            $average_number = intval($data_point['product_count']) / intval($merchant_counts[$data_point['select_date']]);
            
            $average_number = intval($average_number);
            
            $average_product_data_points[$data_point['select_date']] = $average_number;
        }
        
        $view_data['average_product_data_points'] = $average_product_data_points;
        
        $this->load->view('overview/widgets/flot_average_products_chart', $view_data);        
    }
    
    /**
     * AJAX call to get widget which shows average # of violations for all merchants per day.
     * 
     * @author Christophe
     */
    public function average_violations_chart()
    {
        $this->_layout = 'ajax_html';
        
        $start_date = date('Y-m-d', strtotime('-180 days'));
        $end_date = date('Y-m-d', strtotime('-1 day'));
        
        $query_str = "
            SELECT select_date, SUM(violation_count) AS violation_count 
            FROM violations_per_merchant_per_day 
            WHERE store_id = {$this->store_id} 
            AND select_date >= '{$start_date}' 
            AND select_date <= '{$end_date}' 
            GROUP BY select_date 
            ORDER BY select_date ASC                                      
        ";
        
        $violation_data_points = $this->db->query($query_str)->result_array();
        
        $query_str = "
            SELECT select_date, COUNT(*) AS merchant_count 
            FROM violations_per_merchant_per_day 
            WHERE store_id = {$this->store_id}
            AND select_date >= '{$start_date}'
            AND select_date <= '{$end_date}'
            GROUP BY select_date
            ORDER BY select_date ASC
        ";
        
        $merchant_count_data_points = $this->db->query($query_str)->result_array();   

        $merchant_counts = array();
        
        // create new array that contains counts per day of # of merchants
        foreach ($merchant_count_data_points as $merchant_count_data_point)
        {
            $merchant_counts[$merchant_count_data_point['select_date']] = $merchant_count_data_point['merchant_count'];
        }
        
        $average_viol_data_points = array();
        
        foreach ($violation_data_points as $data_point)
        {
            $average_number = intval($data_point['violation_count']) / intval($merchant_counts[$data_point['select_date']]);
            
            $average_number = intval($average_number);
            
            $average_viol_data_points[$data_point['select_date']] = $average_number;
        }
        
        //var_dump($average_viol_data_points); exit();
        
        $view_data['average_viol_data_points'] = $average_viol_data_points;
        
        $this->load->view('overview/widgets/flot_average_violations_chart', $view_data);
    }
    
    /**
     * AJAX call to get chart for # of products tracked.
     * 
     * @author Christophe
     */
    public function daily_products_tracked()
    {
        $this->_layout = 'ajax_html';
        
        $start_date = date('Y-m-d', strtotime('-180 days'));
        $end_date = date('Y-m-d', strtotime('-1 day'));
        
        $query_str = "
            SELECT select_date, SUM(product_count) AS product_count 
            FROM products_per_merchant_per_day 
            WHERE store_id = {$this->store_id} 
            AND select_date >= '{$start_date}' 
            AND select_date <= '{$end_date}' 
            GROUP BY select_date 
            ORDER BY select_date ASC 
        ";
        
        //var_dump($query_str); exit();
        
        $data_points = $this->db->query($query_str)->result_array();
        
        $view_data['data_points'] = $data_points;
        
        $this->load->view('overview/widgets/flot_daily_products_tracked', $view_data);        
    }
    
    /**
     * 
     * @author unknown, Christophe
     * @param int $reportId
     */
    public function index($reportId = FALSE)
    {
        $this->load->model('crawl_data_m');
        $this->load->model('crowl_merchant_name_m');
        $this->load->model('merchant_products_m');
        $this->load->model('marketplace_m', 'Market');

        $data = $this->reportinfo->report_overview();
        
        foreach($data as $key=>$value)
        {
            $this->data->$key = $value;
        }
        
        // Get the last crawl data
        //$this->data->last_crawl = $this->crawl_data_m->last_crawl();
        // Christophe: Chris says that generally we have violation data if we look at the past
        // 48 hours of crawling (2 days is 2 param with below)
        $this->data->last_crawl = $this->crawl_data_m->last_crawl('all', 2);
        
        //var_dump($this->data->last_crawl); exit();
        
        $this->crawl_range = $this->crawl_data_m->last_crawl_range();
        $this->data->number_of_merchants = getNumberOfMerchants(
        		$this->store_id,
        		$this->crawl_range['from'],
        		$this->crawl_range['to']
        );
        
        //var_dump($this->crawl_range); exit();
        
        $this->data->marketplace_violation_total_count = 0;
        $this->data->marketplace_products = array();
        $this->data->market_violations = array();
        
        $markets = $this->Market->get_marketplaces_by_storeid_using_categories($this->store_id);
        
        //var_dump($markets); //exit();
        
        $this->data->violatedRetailers = array();
        
        foreach ($markets as $market)
        {
        		$market = $market['name'];
        
        		$crawl_info = !empty($this->data->last_crawl[$market]) ? $this->data->last_crawl[$market] : FALSE;
        		//$crawl_info = $this->crawl_data_m->last_crawl($market); // super slow
        		
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
        			  $from = $crawl_info->start_datetime;
        			  $to = $crawl_info->end_datetime;
        			}
        
        			// CS: get count of marketplace violations (gets all from products_trends_new table)
        			$marketplace_products = $this->merchant_products_m->getCountByMarketplace($this->store_id, $market, $from, $to);
        			 
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
         
        if ($this->config->item('environment') == 'local')
        {
        	$from = '2015-08-01 00:00:00';
        	$to = '2015-08-02 00:00:00';
        }
        else
        {        	      
          $from = $this->crawl_range['from'];
          $to = $this->crawl_range['to'];
        }
               
        $this->data->from = $from;
        $this->data->to = $to;
        
        // #1 trying to speed up dashboard load time by not using products_trends_new - Christophe
        $violated_retailers = $this->crowl_merchant_name_m->get_violated_retailers($this->store_id, $from, $to);
        
        $this->data->retailer_query = $this->db->last_query();
        
        foreach ($violated_retailers as $violated_retailer)
        {
            $this->data->violatedRetailers[] = TRUE;
        }
        
        $this->data->marketplaces = array();
        $this->data->retailers = array();
        $this->data->violatedMarketplaces = array();
        
        for ($i = 0; $i < count($this->data->marketplace_products); $i++)
        {
            $name = $this->data->marketplace_products[$i]['marketplace'];
        
            // check to see if retailer or marketplace
            if ($this->data->marketplace_products[$i]['is_retailer'] === '1')
            {
                //$this->data->violatedRetailers[] = TRUE;
                
                $merchant = $this->MProducts->getMerchantDetailsByMarketplace($name);
                
                if (isset($merchant[0]['id']))
                {
                	$this->data->marketplace_products[$i] = array_merge($this->data->marketplace_products[$i], $merchant[0]);
                		
                	$this->data->retailers[] = $this->data->marketplace_products[$i];
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
    }
    
    /**
     * Ajax call to get # of merchants tracked over 180 days for dashboard widget.
     * 
     * @author Christophe
     */
    public function merchants_tracked_chart()
    {
        $this->_layout = 'ajax_html';
        
        $start_date = date('Y-m-d', strtotime('-180 days'));
        $end_date = date('Y-m-d', strtotime('-1 day'));
        
        $query_str = "
            SELECT select_date, COUNT(*) AS merchant_count 
            FROM products_per_merchant_per_day 
            WHERE store_id = {$this->store_id} 
            AND select_date >= '{$start_date}' 
            AND select_date <= '{$end_date}' 
            GROUP BY select_date 
            ORDER BY select_date ASC                                      
        ";
        
        //var_dump($query_str); exit();
        
        $data_points = $this->db->query($query_str)->result_array();
        
        $view_data['data_points'] = $data_points;
        
        $this->load->view('overview/widgets/flot_merchants_tracked_chart', $view_data);
    }
    
    /**
     * Handle AJAX call to get most violated products list for dashboard.
     * 
     * @author Christophe
     */
    public function most_violated_products()
    {
        ini_set('memory_limit', '200M');
        
        $this->load->model('products_m');
        $this->load->model('crawl_data_m');
        
        $this->_layout = 'ajax_html';
                
        if ($this->config->item('environment') == 'local')
        {
            $start_date = '2015-08-04 00:00:00';
            $end_date = '2015-08-04 11:59:59';
        }
        else
        {
            $range = $this->crawl_data_m->last_crawl_range();
            
            $this->crawl_range = $this->crawl_data_m->last_crawl_range();
            
            $this->data->crawl_range = $this->crawl_range;
            
            $start_date = $this->crawl_range['from'];
            $end_date = $this->crawl_range['to'];  

            //$start_date = $range['from'];
            //$end_date = $range['to'];
            
            //$start_date = date('Y-m-d H:i:s', strtotime('-24 hours'));
            //$end_date = date('Y-m-d H:i:s');
            
            //$start_date = date('Y-m-d 00:00:00', strtotime('-30 days'));
            //$start_date = date('Y-m-d 00:00:00', strtotime('-1 day'));
            //$end_date = date('Y-m-d 11:59:59', strtotime('-1 day'));
        }
        
        $start = strtotime($start_date);
        $end = strtotime($end_date);
        
        $products = $this->products_m->get_active_tracked_products_by_store($this->store_id);
        
        $product_upcs = array();
        
        foreach ($products as $product)
        {
            $product_upcs[] = "{$product['upc_code']}";
        }
        
        $product_data_items = $this->products_m->get_trend_data_by_upcs($product_upcs, $start, $end);
        
        //var_dump(count($product_data_items)); exit();
        //var_dump($this->db->last_query()); exit();
        
        $data_rows = array();
        
        foreach ($product_data_items as $product_data_item)
        {
            //var_dump($product_data_item); exit();
            
            // @todo look to see if promotion pricing is set on product and use that instead of MAP (AP) price
            if ($product_data_item['ap'] > $product_data_item['mpo'])
            {
                if (isset($data_rows[$product_data_item['upc']]))
                {
                    $data_rows[$product_data_item['upc']] = $data_rows[$product_data_item['upc']] + 1;
                }
                else
                {
                    $data_rows[$product_data_item['upc']] = 1;
                }
            }
        }
        
        arsort($data_rows);
        
        //var_dump($data_rows); exit();
        
        $ranked_products = array();
        
        foreach ($data_rows as $upc => $violation_count)
        {
            $product = $this->products_m->get_product_array_data_by_upc($upc, $this->store_id);
            
            $product['violaton_count'] = $violation_count;
            
            $ranked_products[] = $product;
        }
        
        $view_data['data_rows'] = $data_rows;
        $view_data['ranked_products'] = $ranked_products;
        
        $this->load->view('overview/widgets/most_violated_products', $view_data);
    }
    
    /**
     * Handle AJAX call to get most volatile products list.
     * 
     * @author Christophe
     */
    public function most_volatile_products()
    {   
        ini_set('memory_limit', '200M');
        
        $this->load->model('products_m');
        $this->load->model('crawl_data_m');
        
        $this->_layout = 'ajax_html';
        
        if ($this->config->item('environment') == 'local')
        {
            $start_date = '2015-08-01 00:00:00';
            $end_date = '2015-08-02 00:00:00';
        }
        else
        {
            $range = $this->crawl_data_m->last_crawl_range();
            
            $this->crawl_range = $this->crawl_data_m->last_crawl_range();
            
            $this->data->crawl_range = $this->crawl_range;
            
            $start_date = $this->crawl_range['from'];
            $end_date = $this->crawl_range['to']; 
            
            //$start_date = $range['from'];
            //$end_date = $range['to'];
            
            //$start_date = date('Y-m-d H:i:s', strtotime('-24 hours'));
            //$end_date = date('Y-m-d H:i:s');
            
            //$start_date = date('Y-m-d 00:00:00', strtotime('-30 days'));
            //$start_date = date('Y-m-d 00:00:00', strtotime('-1 day'));
            //$end_date = date('Y-m-d 11:59:59', strtotime('-1 day'));
        }
        
        $start = strtotime($start_date);
        $end = strtotime($end_date);
        
        $products = $this->products_m->get_active_tracked_products_by_store($this->store_id);
        
        $product_upcs = array();
        
        foreach ($products as $product)
        {
        	$product_upcs[] = "{$product['upc_code']}";
        }
        
        $product_data_items = $this->products_m->get_trend_data_by_upcs($product_upcs, $start, $end);
        
        $data_rows = array();
        $trend_ids = array();
        $product_trend_ids = array();
        
        foreach ($product_data_items as $product_data_item)
        {
            if ($product_data_item['ap'] > $product_data_item['mpo'])
            {
                $price_difference = $product_data_item['ap'] - $product_data_item['mpo'];
                
                //var_dump($product_data_item['id']);
                //var_dump($price_difference);
                
                $wholesale_price = floatval($product_data_item['wp']);
                

                if (isset($data_rows[$product_data_item['upc']]))
                {
                    if ($price_difference > $data_rows[$product_data_item['upc']])
                    {
                        $data_rows[$product_data_item['upc']] = $price_difference;
                        //$product_trend_ids[$product_data_item['upc']] = $product_data_item['id'];
                        $product_trend_ids[$product_data_item['upc']] = $product_data_item['dt'];
                        $trend_ids[$price_difference] = $product_data_item['id'];
                        
                        /*
                        $data_rows[$product_data_item['upc']] = array(
                            'trend_id' => $product_data_item['id'],      
                            'price_diff' => $price_difference
                        );
                        */
                        //$data_rows[$product_data_item['upc']] = $product_data_item['id'];
                    }
                }
                else
                {
                    $data_rows[$product_data_item['upc']] = $price_difference;
                    //$product_trend_ids[$product_data_item['upc']] = $product_data_item['id'];
                    $product_trend_ids[$product_data_item['upc']] = $product_data_item['dt'];
                    $trend_ids[$price_difference] = $product_data_item['id'];
                    
                    /*
                    $data_rows[$product_data_item['upc']] = array(
                    		'trend_id' => $product_data_item['id'],
                    		'price_diff' => $price_difference
                    );
                    */
                    //$data_rows[$product_data_item['upc']] = $product_data_item['id'];
                }
            }
        }
        
        arsort($data_rows);
        
        //var_dump($data_rows); exit();
        
        $ranked_products = array();
        
        foreach ($data_rows as $upc => $price_diff)
        {
            $product = $this->products_m->get_product_array_data_by_upc($upc, $this->store_id);
            
            $ranked_products[] = array(
                'price_diff' => $price_diff,       
                'product' => $product,
                'upc' => $upc            
            );
        }
        
        //var_dump($ranked_products); exit();
        
        $view_data['ranked_products'] = $ranked_products;
        $view_data['trend_ids'] = $trend_ids;
        $view_data['product_trend_ids'] = $product_trend_ids;
        
        $this->load->view('overview/widgets/most_volatile_products', $view_data);
    }
    
    /**
     * Handle AJAX call to get notification chart for dashboard.
     * 
     * @author Christophe
     */
    public function notifications_chart($type = '')
    {
        $this->load->model('products_m');
        
        $this->_layout = 'ajax_html';
        
        // get violation counts for store for past 30 days
        //$start_date = date('Y-m-d 00:00:00', strtotime('-30 days'));
        //$start_date = '2015-06-24';
        $start_date = date('Y-m-d 00:00:00', strtotime('-180 days'));
        
        $end_date = date('Y-m-d 00:00:00', strtotime('-1 day'));
        //$end_date = '2015-07-17';
        
        $notifications = $this->products_m->get_notifications_for_date_range($this->store_id, $start_date, $end_date);
        
        $data_points = array();
        
        foreach ($notifications as $notification)
        {
            $date_str = date('M j', strtotime($notification['date']));
            
            if (isset($data_points[$date_str]))
            {
                $data_points[$date_str] = $data_points[$date_str] + 1;
            }
            else
            {
                $data_points[$date_str] = 1;
            }
        }
        
        //var_dump($data_points); exit();
        
        $view_data['data_points'] = $data_points;
           
        if ($type == 'flot')
        {
        	$this->load->view('overview/widgets/flot_notifications_chart', $view_data);
        }
        else
        {
        	$this->load->view('overview/widgets/notifications_chart', $view_data);
        }     
    }
    
    /**
     * Handle AJAX call to get violation chart for dashboard.
     * 
     * @author Christophe
     */
    public function product_violation_chart($type = '')
    {
        $this->load->model('products_m');
        
        $this->_layout = 'ajax_html';
        
        // get violation counts for store for past 30 days
        if ($this->config->item('environment') == 'local')
        {
            $start_date = '2015-06-24';
            $end_date = '2015-07-17';
        }
        else
        {
            //$start_date = date('Y-m-d', strtotime('-30 days'));
            $start_date = date('Y-m-d', strtotime('-180 days'));
            $end_date = date('Y-m-d', strtotime('-1 day'));            
        }
      
        $violations = $this->products_m->get_violations_for_date_range($this->store_id, $start_date, $end_date);
        
        //var_dump($violations); exit();
        
        $view_data['violations'] = $violations;
        
        if ($type == 'flot')
        {
            $this->load->view('overview/widgets/flot_product_violation_chart', $view_data);
        }
        else
        {
            $this->load->view('overview/widgets/product_violation_chart', $view_data);
        }
    }
}
