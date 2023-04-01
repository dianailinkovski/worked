<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('report_types.php');

class Reports extends Report_types 
{
	function __construct()
	{
		parent::__construct();
		
		$this->time = microtime(true);

		$this->load->model('account_m', 'Account');
		$this->load->model('crawl_data_m', 'Crawl');
		$this->load->model('chart_m', 'Chart');
		$this->load->model('marketplace_m', 'Marketplace');
		$this->load->model("Users_m", 'User');
		$this->load->model("Products_m", 'Product');
		$this->load->model("report_m", 'Report');
		$this->load->model("store_m", 'Store');

		$this->_view = $this->_controller . '/index';

		$this->_default_report();
        
    $this->adjusted_for_demo = FALSE;
	}

	private function _default_report()
	{
		$this->data->report_type = "productpricing";
		$this->session->set_userdata("report_type", $this->data->report_type);

		$this->data->my = 'productpricing';
		$this->data->report_chart = 'line';
		$this->data->icon = 'ico-report';
		$this->data->widget = 'mv-report';
		$this->data->date_from = 'Start';
		$this->data->date_to = 'Stop';
		$this->data->mode = '';
		$this->data->report_name = $this->data->file_name = '';
		$this->data->flagDates24 = false;

		$this->data->report_where = array();
		$this->data->product_ids = array();
		$this->data->searchProducts = array();
		$this->data->merchants = array();
		$this->data->markets = array();
		$this->data->displayBookmark = true;
		$this->data->Data = null;
		$this->data->show_comparison = false;

		// for filter by merchant/marketplace
		$this->data->proMerchants = array();
		//$this->data->markertArr = getMarketArray($this->store_id);
		$this->data->marketplaceArr = getMarketplaceArray($this->store_id);
		$this->data->retailerArr = getRetailerArray(false,$this->store_id);
		$this->data->all_markets = (boolean)$this->input->post('all_markets');
		$this->data->all_retailers = ((boolean)$this->input->post('all_retailers') and ! empty($this->subscriber_retailer_addons));

		//display data
		$this->data->submitted = (boolean)$this->input->post('formSubmit');
		$this->data->time_frame = $this->data->submitted ? $this->input->post('time_frame') : '';
		$this->data->dateStartField = (!$this->data->submitted || ($this->data->submitted && $this->data->time_frame != '')) ? 'Start' : $this->input->post('date_from');
		$this->data->dateEndField = (!$this->data->submitted || ($this->data->submitted && $this->data->time_frame != '')) ? 'Stop' : $this->input->post('date_to');

		$this->data->noRecord = $this->config->item('no_record');
	}


	function index()
	{
		$this->byproduct();
		$this->data->listBOXDATA = json_encode($this->Product->getlqxListProducts($this->store_id));
	}


	/**
	 * Display a price report
	 *
	 * @see reports::_default_report()
	 */
	function show()
	{
		if ( ! $this->data->submitted) redirect(site_url('reports'));

		$this->data->emails = $this->session->userdata('user_email');

		$data = $this->input->post(array(
			'report_id',
			'report_type',
			'by_which',
			'time_frame',
			'date_from',
			'date_to',
			'show_comparison',
		));
		$data['show_comparison'] = (boolean)$data['show_comparison'];
		foreach ($data as $key => $value)
			$this->data->$key = $value;

		$this->data->graphDataType = 'chart';
		$this->data->productNames = array();
		if ( ($product_name = $this->input->post('product_name')) )
		{
			if (is_array($product_name))
			{
				for
				($i=0, $n=sizeof($product_name); $i<$n; $i++)
				{
					$this->data->report_name .= $product_name[$i].' ';
				}
				$this->data->report_name .= 'Product Pricing';
			}
			$this->data->file_name = str_replace(' ', '_', $this->data->report_name.' '.date('Y-m-d'));
			$this->data->productNames = $this->input->post('product_name');
		}

		if ($this->input->post('products'))
		{
			$this->data->report_where['product_ids'] = $this->data->product_ids = $this->input->post('products');
		} elseif ($this->input->post('group_id'))
		{
			$this->data->report_where['group_id'] = $this->data->group_id = $this->input->post('group_id');
			$this->data->product_groups = $this->Product->getGroups($this->store_id, 999);
			$group_prods = $this->Product->getProductsByGroupId((int)$this->input->post('group_id'));
			if ( ! empty($group_prods))
			{
				foreach ($group_prods as $prod)
				{
					$prod = $this->Product->getProductsByID($this->store_id, $prod['product_id']);
					$this->data->product_ids[] = $prod[0]['id'];
					$this->data->productNames[] = $prod[0]['title'];
				}
			}
		}

		$this->data->product_ids = array_values(array_filter($this->data->product_ids));

		$this->data->proMerchants = getProductMerchant($this->store_id);
		$this->data->merchants = $this->input->post_default('merchants', array());
		if ( ! empty($this->data->merchants))
		{
			$this->data->report_where['merchants'] = $this->data->merchants;
		}

		$this->data->markets = $this->input->post_default('markets', array());
        if ( ! empty($this->data->markets) )
		{
			$this->data->report_where['markets'] = $this->data->markets;
		}

		$this->_prep_report();
        $this->_build_options_array($this->data->by_which);
	}


	private function _prep_report()
	{
		//  $lookup_markets = $this->data->markets;
		//  if ( ! $this->data->all_markets AND $this->data->all_retailers)
		//   $lookup_markets = array_merge($lookup_markets, $this->data->retailerArr);
		//  elseif ( ! $this->data->all_retailers AND $this->data->all_markets)
		//   $lookup_markets = array_merge($lookup_markets, $this->data->marketplaceArr);
        if ( ! empty($this->data->markets) ) {
            $lookup_markets = $this->data->markets;
        } else {
            $lookup_markets = array_merge($this->data->marketplaceArr, $this->data->retailerArr);
        }

        if ( ! empty($this->data->product_ids[0]))
		{
			$this->data->competitor_store_id = false;
			if ($this->data->by_which === 'bycompetition') 
            { // this is a competitor's product
				$this->data->competitor_products = $this->Product->getProductsById(null, $this->data->product_ids);
				$this->data->competitor_store_id = array();
				for	($i = 0, $n = count($this->data->competitor_products); $i < $n; $i++)
				{
					$this->data->competitor_store_id[] = $this->data->competitor_products[$i]['store_id'];
				}
				$this->data->competitor_store_id = array_unique($this->data->competitor_store_id);
				$this->data->proMerchants = getProductMerchant($this->data->competitor_store_id);

				if ($this->data->show_comparison)
                { // and it's a comparison
					$competitor_map = $this->Store->getCompetitorMap($this->store_id, $this->data->product_ids);
					$this->data->competitor_store_id[] = $this->store_id;
					$products_wo_comparisons = $this->data->product_ids;
					$this->data->competitor_map = array();
					foreach($competitor_map as $bpp)
					{
						$this->data->product_ids[] = $bpp['owner_brand_product'];
						$owned_product = $this->Product->getProductsById(null, $bpp['owner_brand_product']);
						if ( ! empty($owned_product[0]['id']))
						{
							$this->data->competitor_map[$bpp['competitor_brand_product']] = $owned_product[0];
						}
					}
					$products_w_comparisons = $this->data->product_ids;
				}
			}

			$this->data->report_where = array(
				'report_type' => 'productpricing',
				'by_which' => $this->data->by_which,
				'api_type' => $lookup_markets,
				'time_frame' => $this->data->time_frame,
				'date_from' => $this->data->date_from,
				'date_to' => $this->data->date_to,
				'cron_ids' => '',
				'product_ids' => $this->data->product_ids,
				'merchants' => $this->data->merchants,
				'competitor_store_id' => $this->data->competitor_store_id,
				'show_comparison' => $this->data->show_comparison,
				'competitor_map' => isset($this->data->competitor_map) ? $this->data->competitor_map : array()
			);

			//specific dates meant that they should be just that
			//time_frame values are relative to the current date
			if ($this->data->date_from !== 'Start' && $this->data->date_to !== 'Stop')
			{
				$this->data->report_where['date_from'] = strtotime($this->data->date_from);
                if ( $this->adjusted_for_demo ) { //adjusted for demo
                    $this->data->report_where['date_from'] -= 365*24*60*60;
                }
				$this->data->report_where['date_to'] = strtotime($this->data->date_to);
				$this->data->report_where['time_frame'] = $this->data->time_frame = '';
				if ($this->data->report_type === 'pricingviolation')
				{
					$this->data->report_where['date_from'] = $this->date_from = strtotime($this->data->date_from." 00:00:00");
                    if ( $this->adjusted_for_demo ) { //adjusted for demo
                        $this->data->report_where['date_from'] -= 365*24*60*60;
                    }
					$this->data->report_where['date_to'] = $this->datadate_to = strtotime($this->data->date_to." 23:59:59");
				}
				if ($this->data->date_from == $this->data->date_to)
				{
					$this->data->flagDates24 = true;
					$this->data->time_frame = '24';
				}
			} else
			{
				$tF = getTimeFrame($this->data->time_frame);
				$this->data->date_from = $tF['date_from'];
                if ( $this->adjusted_for_demo ) { //adjusted for demo
                    $this->data->date_from -= 365*24*60*60;
                }	
				$this->data->date_to = $tF['date_to'];
				$this->data->report_where['date_from'] = $this->data->date_from;
				$this->data->report_where['date_to'] = $this->data->date_to;
			}

			//get cron_ids for 24 hour scatter
			if ($this->data->time_frame == '24' || $this->data->flagDates24)
			{
				$this->data->report_chart = 'scatter';
				if ($this->data->flagDates24)
				{
					$this->data->report_where['cron_ids'] = getLast24HoursCronIds($this->data->report_where['date_from'], '', $this->data->report_where['api_type']);
				} else
				{
					$this->data->report_where['cron_ids'] = getLast24HoursCronIds('', '', $this->data->report_where['api_type']);
				}
			}
			//add group_id if present
			if (isset($this->data->group_id)) $this->data->report_where['group_id'] = $this->data->group_id;

			// everything is prepped, let's lookup the data for the chart and table
			if ($this->data->time_frame == '24' || $this->data->flagDates24)
			{
				$response = $this->Report->productPricingReport24($this->data->report_where);
				$this->data->Data = $response['data'];
				$this->data->gData = $this->Chart->prepGoogleData($response['data'], $this->data->report_where, $this->data->report_chart);
			} else
			{
				$this->data->Data = $this->Report->productPricingHistory($this->data->report_where);
				$this->data->gData = $this->Chart->prepGoogleData($this->data->Data, $this->data->report_where, $this->data->report_chart);
			}

			$this->session->set_userdata('report_where', $this->data->report_where);
		}

		if ($this->data->show_comparison and isset($products_wo_comparisons))
		{ // don't populate product fields with the compared
			$this->data->product_ids = $products_wo_comparisons;
		}

		for ($i=0, $n=sizeof($this->data->product_ids); $i<$n; $i++)
		{
			$this->data->searchProducts[$this->data->product_ids[$i]] = $this->data->productNames[$i];
		}

		//
		// Set retailer/marketplace information
		$this->data->marketRetailer = array();
		$this->data->retailersExist = array();
		$this->data->marketplacesExist = array();

		if ($this->data->report_chart === 'line')
		{
            if ( !empty($this->data->Data) ) {
                foreach ($this->data->Data as $prodId => $marketArr)
                {
                    foreach ($marketArr as $marketName => $productData)
                    {
                        if ( ! isset($this->data->marketRetailer[$marketName]))
                            $this->data->marketRetailer[$marketName] = $this->Marketplace->is_retailer($marketName);
                        if ($this->data->marketRetailer[$marketName])
                            $this->data->retailersExist[$prodId] = true;
                        else
                            $this->data->marketplacesExist[$prodId] = true;
                    }
                }
            }
		}
		elseif ($this->data->report_chart === 'scatter')
		{
            if ( !empty($this->data->Data) ) {
                foreach ($this->data->Data as $prodId => $productData)
                {
                    for ($i = 0, $n = count($productData); $i < $n; $i++)
                    {
                        $marketName = strtolower($productData[$i]['marketplace']);
                        if ( ! isset($this->data->marketRetailer[$marketName]))
                            $this->data->marketRetailer[$marketName] = $this->Marketplace->is_retailer($marketName);
                        if ($this->data->marketRetailer[$marketName])
                            $this->data->retailersExist[$prodId] = true;
                        else
                            $this->data->marketplacesExist[$prodId] = true;
                    }
                }
            }
		}

		//
		// Set up map between competitor product data and user product data
		$this->data->comparison_data = array();
		if ($this->data->show_comparison)
		{
			$color_index = 0;
			$this->data->color_index = array();

			if ( ! empty($this->data->competitor_map))
			{
				//
				// If this is a scatter chart we need to add the crawl id for all the data
				if ($this->data->report_chart === 'scatter')
				{
                    if ( !empty($this->data->Data) ) {
                        foreach ($this->data->Data as $prodId=>$productData)
                        {
                            for ($i = 0, $n = count($productData); $i < $n; $i++)
                            {
                                if (isset($productData[$i]['marketplace']))
                                {
                                    $api_type = explode('.', $productData[$i]['marketplace']);
                                    $api_type = $api_type[0];
                                    $crawl = $this->Crawl->get_crawl_by_time($productData[$i]['dt'], $api_type, 'id');
                                    $this->data->Data[$prodId][$i]['crawl_id'] = isset($crawl['id']) ? $crawl['id'] : false;
                                }
                            }
                        }
                    }
				}

				//
				// Now loop through and set the comparison data
				foreach ($products_w_comparisons as $prodId)
				{
					$this->data->color_index[$prodId] = $color_index++;
					//
					// Check if this competitor product is associated with user product
					$comparison_id = false;
					if (isset($this->data->competitor_map[$prodId]))
						$comparison_id = $this->data->competitor_map[$prodId]['id'];

					if ($comparison_id and isset($this->data->Data[$comparison_id]))
					{
						$comparison_data = $this->data->Data[$comparison_id];
						//
						// Map values via productid->marketplace->crawltime
						if ( ! empty($comparison_data))
						{

							if ($this->data->report_chart === 'line')
							{
								foreach ($comparison_data as $market => $market_prod_data)
								{
									for ($i = 0, $n = count($market_prod_data); $i < $n; $i++)
									{
										if (isset($market_prod_data[$i]['dt']))
										{
											$this->data->comparison_data[$prodId][$market][$market_prod_data[$i]['dt']] = $market_prod_data[$i];
										}
									}
								}
							}
							elseif ($this->data->report_chart === 'scatter')
							{
								for ($i = 0, $n = count($comparison_data); $i < $n; $i++)
								{
									if (isset($comparison_data[$i]['marketplace']))
									{
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
	}


	function view($reportId)
	{
		//$reportId = base64_decode(urldecode($reportId));
		if (empty($reportId))
			redirect('savedreports');

		$savedReport = $this->Report->get_save_report_by_id($reportId, false);
		$report_where = (isset($prodSavedRpt['report_where'])) ? json_decode($prodSavedRpt['report_where'], TRUE) : array();

		if (empty($savedReport))
			redirect('savedreports');

		// The saved report exists, let's load it
		$this->data->report_id = $reportId;
		$this->data->graphDataType = 'chart';
		$this->data->submitted = TRUE;
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

		$this->data->report_name = $savedReport->report_name;
		$this->data->show_comparison = isset($report_where['show_comparison']) ? (boolean)$report_where['show_comparison'] : FALSE;
		// @TODO add comparison support

		$this->data->product_ids = (isset($report_where['product_ids'])) ? $report_where['product_ids'] : array();
		if ( ! empty($this->data->product_ids))
		{
			foreach ($this->data->product_ids as $product)
			{
				$prod = $this->Product->get($product);
				$this->data->productNames[] = $prod->title;
			}
		}

		if ($this->data->by_which === 'bygroup')
		{
			$this->data->group_id = isset($report_where['group_id']) ? $report_where['group_id'] : NULL;
			$this->data->product_groups = $this->Product->getGroups($this->store_id);
		}

		$this->data->product_ids = array_values(array_filter($this->data->product_ids));
		$this->data->proMerchants = getProductMerchant($this->store_id);
		$this->data->markets = isset($report_where['api_type']) ? $report_where['api_type'] : array();
		$this->data->merchants = isset($report_where['merchants']) ? $report_where['merchants'] : array();
		if (empty($this->data->markets))
		{
			$this->data->all_markets = TRUE;
			$this->data->all_retailers = TRUE;
		}
		if (empty($this->data->merchants))
			$this->data->all_merchants = TRUE;

		$this->session->set_userdata('report_where', $report_where);

		$this->_prep_report();
		$this->_build_options_array($this->data->by_which);
	}

    /**
     * Email a report to emails entered into export modal form.
     * 
     * @author unknown
     * @param string $imageName
     */
    public function email($imageName = '')
    {
        $this->_response_type('json');

        // Get the request info
        $this->data->report_where = json_decode(html_entity_decode($this->input->post('report_where')), true);
        $this->data->report_where['fromDate'] = isset($this->data->report_where['date_from']) ? $this->data->report_where['date_from'] : strtotime(date('Y-m-d 00:00:00'));
        $this->data->report_where['toDate'] = isset($this->data->report_where['date_to']) ? $this->data->report_where['date_to'] : strtotime(date('Y-m-d 23:59:59'));
        $this->data->report_where['time_frame'] = isset($this->data->report_where['time_frame']) ? $this->data->report_where['time_frame'] : '24';

        $moreInfo = $this->Store->get_store_track($this->store_id);
        
        $this->data->merchant_logo = get_merchant_logo_url($moreInfo->brand_logo);

        $this->data->file_name = $this->input->post('file_name');
        $this->data->title = $this->data->headerDate = '';

        $this->data->time_frame = $this->data->report_where['time_frame'];
        $this->data->product_ids = isset($this->data->report_where['product_ids']) ? $this->data->report_where['product_ids'] : array();
        $this->data->retailer = (int)$this->input->post('is_retailer');

        $rptInfo = getTitleReporting($this->data->report_where, 'Y');
        
        $this->data->reportType = $this->input->post('report_type');
        
        switch ($this->data->reportType)
        {
            case 'productpricing':
                $this->data->title = $emailSubject = 'Price Over Time';
                $this->data->headerDate = $rptInfo['date'];
                break;
            case 'dns_list':
                $this->data->title = $emailSubject = 'Do Not Sell List Report';
                $this->data->headerDate = $rptInfo['date'];
                break;
            case 'pricingoverview':
                $emailSubject = 'Pricing Overview';
                $this->data->title = 'Today\'s Pricing';
                $this->data->headerDate = $rptInfo['date'];
                break;
            case 'marketviolator':
                $emailSubject = 'Violations Detail';
                $this->data->title = $this->data->file_name;
                $this->data->headerDate = $rptInfo['date'];
                break;
            case 'violationoverview':
                $emailSubject = 'Violations Overview';
                $this->data->title = $this->data->file_name;
                $this->data->headerDate = $rptInfo['date'];
                break;
            case 'violator':
                $emailSubject = 'Price Violator';
                $this->data->title = $this->data->file_name;
                $this->data->headerDate = $rptInfo['date'];
                break;
            case 'whois':
                $emailSubject = $this->data->file_name;
                $this->data->title = $this->data->file_name;
                $this->data->headerDate = $rptInfo['date'];
                break;
            default:
                $emailSubject = 'Price Violations';
                $this->data->title = $rptInfo['title'];
                
                if (!empty($this->data->report_where['group_id']))
                {
                    $group = $this->Product->getGroupByID($this->data->report_where['group_id']);
                    
                    if (!empty($group['name']))
                    {
                        $this->data->title = $group['name'];
                    }
                }

                $this->data->headerDate = 'Dates: ' . $rptInfo['date'];
        }

        if (isset($_POST['report_name'])) 
        {
            $this->data->title = $this->input->post('report_name') . ' - ' . $this->data->title;
        }

        // Get the images used
        $graphData = $this->input->post('graph_data');
        
        if ($imageName == '' and $graphData)
        {
            $imageName = $this->generateImage($graphData, $this->store_id);
        }

        $this->data->graph_image_name = empty($imageName) ? '' : $imageName;

        // Generate the content
        require_once APPPATH . 'libraries/mvexport.php';
        
        $content = $this->input->post('export_content');

        $this->mvexport = new mvexport($content);
        
        $this->data->report = $this->mvexport->getReport('.reportTable');

        // Create the email
        $template = 'general';
        $email_addresses = $this->input->post('email_addresses');
        
        if (is_array($email_addresses) ) 
        {
            $receivers = implode(',', array_flip(array_flip($this->input->post('email_addresses'))));
        } 
        else 
        {
            $receivers = $email_addresses;
        }

        $htmlContent = $this->load->view('reports/_email', $this->data, TRUE);
        $textContent = $this->load->view('reports/_email_txt', $this->data, TRUE);

        $this->data = template_email_send($template, $this->user_id, $emailSubject, $receivers, $htmlContent, $textContent);
    }

	function pdfExport($imageName = '')
	{
		ini_set('memory_limit', '1024M');
		//
		// Get the request info
		//
        $this->data->report_where = json_decode(html_entity_decode($this->input->post('report_where')), true);
		$this->data->report_where['fromDate'] = isset($this->data->report_where['date_from']) ? $this->data->report_where['date_from'] : strtotime(date('Y-m-d 00:00:00'));
		$this->data->report_where['toDate'] = isset($this->data->report_where['date_to']) ? $this->data->report_where['date_to'] : strtotime(date('Y-m-d 23:59:59'));
		$this->data->report_where['time_frame'] = isset($this->data->report_where['time_frame']) ? $this->data->report_where['time_frame'] : '24';
		$overviews = array(
			'pricingoverview' => TRUE,
			'violationoverview' => TRUE
		);
		if ( isset($this->data->report_where) && !isset($overviews[$this->data->report_where['report_type']])) {
            if ( isset($this->data->report_where['product_ids']) ) {
                $this->data->product_ids = $this->data->report_where['product_ids'];
            }
        }

		//
		// Determine the title
		//
		$reportInfo = getTitleReporting($this->data->report_where, 'Y', true);
        
		$this->data->headerDate = $reportInfo['date'];
		$this->data->title = $reportInfo['title'];

		if(($report_title = $this->input->post('report_title')))
			$this->data->title = $report_title;
		elseif( ! empty($this->data->report_where['group_id']))
		{
			$group = $this->Product->getGroupByID($this->data->report_where['group_id']);
			if ( ! empty($group['name']))
				$this->data->title = $group['name'];
		}
        
        if ( isset($_POST['report_name']) ) {
            $this->data->title = $this->input->post('report_name')." - ".$this->data->title;
        }
        
		//
		// Get the images used
		//

		$graphData = $this->input->post('graph_data');
		if
		($imageName == '' and $graphData)
		{
			$imageName = $this->generateImage($graphData, $this->store_id);
		}

		$this->data->graph_image_name = $imageName ? $imageName : '';
		$this->data->merchant_logo = $this->Account->get_merchant_thumb($this->store_id);

		//
		// Generate the content
		//

		require_once APPPATH.'libraries/mvexport.php';
		$this->mvexport = new mvexport($this->input->post('export_content'));
		$this->data->report = $this->mvexport->getReport('.reportTable');
		$phtml = $this->load->view('reports/_pdf', $this->data, TRUE);

		//
		// Determine the filename
		//
		$fn = $this->input->post('file_name');
		if ( ! $fn)
			$fn = $this->data->title;

		//
		// Create the pdf
		//

		$this->writePDF($phtml, $fn.'.pdf');
		exit;
	}


	function writePDF($html, $fileName)
	{
		$this->load->helper('pdf');
		return tcpdf_write($html, $fileName, tcpdf_options('reports'));
	}


	function autocomplete($query)
	{
		$Items = $this->Product->getMultiSelectProduct($this->store_id, $query);
		echo json_encode($Items);
		exit();
	}


	/* to correct everything that is wrong with scatter chart */
	function dailyPriceHistory($data, $report_where)
	{
		$this->data->report_where = $report_where;

		$hourFlag = (isset($this->data->report_where['time_frame']) && $this->data->report_where['time_frame'] == '24') ? true : false;
		$products = $this->data->report_where['product_ids'];

		echo "returning dailyPriceHistory()<br>\n";return;

		$i = 0;
		if
		($hourFlag)
		{
			//this just gets from-to vals
			$from = $this->data->report_where['date_from'];
			$to = $this->data->report_where['date_to'];

			$skipColorIndex = array();
			$ind = 0;
			foreach
			($products as $key=>$productId)
			{
				$colorCount = 0;
				for
				($i=0, $n=sizeof($this->data->Data); $i<$n; $i++)
				{
					if
					($productId == $this->data->Data[$i]['prod_id'])
					{
						$colorCount++;
						$tempTime = $this->data->Data[$i]['timestamp'];
						$graph_data_temp[$productId][$tempTime]['price'][] = $this->data->Data[$i]['price'];
						$graph_data_temp[$productId][$tempTime]['retail'][] = $this->data->Data[$i]['retail'];
						$graph_data_temp[$productId][$tempTime]['map'][] = $this->data->Data[$i]['timestamp'];
						if
						(isset($graph_data[$productId]) && array_key_exists($key, $graph_data[$productId]))
						{
							$graph_data[$productId][$key]['count'] = $graph_data[$productId][$key]['count'] + 1;
							$graph_data[$productId][$key]['price'] = $graph_data[$productId][$key]['price'] + $this->data->Data[$i]['price'];
							$graph_data[$productId][$key]['wholesaleprice'] = $graph_data[$productId][$key]['wholesaleprice'] + $this->data->Data[$i]['wholesale'];
							$graph_data[$productId][$key]['map'] = $graph_data[$productId][$key]['map'] + $this->data->Data[$i]['timestamp'];
							$graph_data[$productId][$key]['retailprice'] = $graph_data[$productId][$key]['retailprice'] + $this->data->Data[$i]['retail'];
						}else
						{
							$graph_data[$productId][$key] = array('count' => 1,
								'price' => $this->data->Data[$i]['price'],
								'wholesaleprice' => $this->data->Data[$i]['wholesale'],
								'map' => $this->data->Data[$i]['timestamp'],
								'retailprice' => $this->data->Data[$i]['retail']);
						}
					}
				}
				if
				($colorCount == '0')
				{
					$skipColorIndex[] = $ind;
				}
				$ind++;
			}//end foreach($products);

			//$cat is for date range...
			//$cat = array_unique($cat);
			$tempArray = array();
			// For colors Handling
			if
			(count($skipColorIndex) > 0)
			{
				$tmpColor = array();
				foreach ($colors as $cKeyC => $cValC)
				{
					if
					(in_array($cKeyC, $skipColorIndex))
					{
						unset($colors[$cKeyC]);
					}else
					{
						$tmpColor[] = $cValC;
					}
				}
				unset($colors);
				$colors = $tmpColor;
			}

			/*For making Google chart Data of scatter graph*/
			$googledataColorArrayTemp = $googleDataArrayTemp = array();
			$googleDataArrayTemp[0][] = 'Date';
			$maxValueTemp = 0;
			$prCountTemp = count($products);
			$googleCat = array();
			foreach ($cat as $val)
			{
				$valExplode = explode('-', $val);
				$val = isset($valExplode[0]) ? $valExplode[0] : $val;
				$datestr = $val;
				if
				(!in_array($datestr, $googleCat))
				{
					$googleCat[] = $datestr;
				}
			}
			sort($googleCat);
			$googleDataArrayTemp = array();
			$googleDataArrayTemp[0][] = 'Date';
			for ($prcounter = 0; $prcounter < $prCountTemp; $prcounter++)
			{
				$googleDataArrayTemp[0][] = 'Price: '.getProductsTitle($products[$prcounter]);
				$googleDataArrayTemp[0][] = 'MAP: '.getProductsTitle($products[$prcounter]);
				$googleDataArrayTemp[0][] = 'Retail: '.getProductsTitle($products[$prcounter]);
				if
				(isset($colors[$prcounter]))
				{
					$googledataColorArrayTemp[] = $colors[$prcounter];
					$googledataColorArrayTemp[] = $colors[$prcounter];
					$googledataColorArrayTemp[] = $colors[$prcounter];
				}else
				{
					$googledataColorArrayTemp[] = '#FD9B00';
					$googledataColorArrayTemp[] = '#FD9B00';
					$googledataColorArrayTemp[] = '#FD9B00';
				}
			}
			$secondsCategories = getSecondsArray($googleCat[0],$googleCat[count($googleCat)-1]);//for new change
			$categoriesCount = count($secondsCategories);
			$rowsCount = 0;
			$previousvalues = array();
			for
			($m=0;$m<$categoriesCount;$m++)
			{
				++$rowsCount;
				$newDate = date('Y-m-d H:i:s',$secondsCategories[$m]);
				$newDate = explode(' ',$newDate);
				$newDate1 = explode('-',$newDate[0]);
				$newDate2 = explode(':',$newDate[1]);
				$googleDataArrayTemp[$rowsCount][] = 'new  Date('.$newDate1[0].','.($newDate1[1]-1).','.$newDate1[2].','.$newDate2[0].','.$newDate2[1].','.$newDate2[2].')';
				foreach
				($products as $key=>$value)
				{
					if
					(isset($graph_data_temp[$value][$secondsCategories[$m]]))
					{
						$priceValue = $graph_data_temp[$value][$secondsCategories[$m]]['price'][0];
						$mapValue   = $graph_data_temp[$value][$secondsCategories[$m]]['map'][0];
						$retailValue= $graph_data_temp[$value][$secondsCategories[$m]]['retail'][0];
					}else if
						(isset($previousvalues[$value]['map']))
						{
							$priceValue = null;
							$mapValue   = $previousvalues[$value]['map'];
							$retailValue= $previousvalues[$value]['retail'];
						}else
					{
						$priceValue = null;
						$mapValue   = null;
						$retailValue= null;
					}
					$previousvalues[$value]['price']  = $priceValue;
					$previousvalues[$value]['map']    = $mapValue;
					$previousvalues[$value]['retail']   = $retailValue;
					$googleDataArrayTemp[$rowsCount][] = (float)$priceValue;
					$googleDataArrayTemp[$rowsCount][] = (float)$mapValue;
					$googleDataArrayTemp[$rowsCount][] = (float)$retailValue;
				}
			}
			//debug('Google Array temp',$googleDataArrayTemp,2);
			/*************************************************/
			foreach ($graph_data as $key => $dArr)
			{
				$priceD = array();
				$whpriceD = array();
				$repriceD = array();
				$k = 1;
				foreach ($cat as $ckey => $cval)
				{
					if
					(!isset($dArr[$cval]))
					{
						unset($cat[$ckey]);
					}else
					{
						if
						(!in_array($cat[$ckey], $tempArray))
						{
							$tempArray[] = $cat[$ckey];
						}
						$arr = $dArr[$cval];
						$cnt = $arr['count'];
						$priceD[] = $arr['price'] / $cnt;
						$whpriceD[] = $arr['map'] / $cnt;
						$repriceD[] = $arr['retailprice'] / $cnt;
					}
				}
				$series[] = array('name' => getProductsTitle($key), 'data' => $priceD, 'dashStyle' => 'Dot', 'color' => $colors[$i]);
				$series[] = array('name' => getProductsTitle($key), 'data' => $whpriceD, 'dashStyle' => 'Solid', 'color' => $colors[$i]);
				$series[] = array('name' => getProductsTitle($key), 'data' => $repriceD, 'dashStyle' => 'Dash', 'color' => $colors[$i]);
				$i++;
			}
			$cat = $tempArray;
			$mcat = array();
			foreach ($cat as $val)
			{
				$valExplode = explode('-', $val);
				$val = isset($valExplode[0]) ? $valExplode[0] : $val;
				$mcat[] = date('n/j/Y h:i A', $val);
			}
			$cat = $mcat;
		}
		return $graph_data;
	}//dailyPriceHistory()

	function priceHistory($gData, $report_where)
	{
		$this->data->report_where = $report_where;

		$cat = $oItem = $item = $graph_data = $series = $graph_data_temp = $tempcat = array();
		$hourFlag = (isset($this->data->report_where['time_frame']) && $this->data->report_where['time_frame'] == '24') ? true : false;
		$products = $this->data->report_where['product_ids'];
		// @TODO Universal colors
		$colors = array(
			"#FBB925",
			"#00AEEF",
			"#BF302D",
			"#83B519",
			"#FD9B00"
		);

		$i = 0;
		//debug('DateInfo', $this->data->report_where, 2);
		//debug('Products',$products,2);
		//debug('GraphData',$gData,2);
		if
		($hourFlag)
		{
			$cat = last24HourCat($gData);
			$skipColorIndex = array();
			$ind = 0;
			foreach ($products as $key => $val)
			{
				$colorCount = 0;
				foreach ($gData as $key => $data)
				{
					foreach ($data as $inData)
					{
						if
						($val == $inData['prod_id'])
						{
							$colorCount++;
							$price = $inData['price'];
							$wholesalePrice = $inData['wholesale'];
							$retailPrice = $inData['retail'];
							$map = $inData['map'];
							$tempTime = $inData['timestamp'];
							$tempcat[] = $tempTime;
							$graph_data_temp[$val][$tempTime]['price'][] = $price;
							$graph_data_temp[$val][$tempTime]['retail'][] = $retailPrice;
							$graph_data_temp[$val][$tempTime]['map'][] = $map;
							if
							(isset($graph_data[$val]) && array_key_exists($key, $graph_data[$val]))
							{
								$graph_data[$val][$key]['count'] = $graph_data[$val][$key]['count'] + 1;
								$graph_data[$val][$key]['price'] = $graph_data[$val][$key]['price'] + $price;
								$graph_data[$val][$key]['wholesaleprice'] = $graph_data[$val][$key]['wholesaleprice'] + $wholesalePrice;
								$graph_data[$val][$key]['map'] = $graph_data[$val][$key]['map'] + $map;
								$graph_data[$val][$key]['retailprice'] = $graph_data[$val][$key]['retailprice'] + $retailPrice;
							}else
							{
								$graph_data[$val][$key] = array('count' => 1,
									'price' => $price,
									'wholesaleprice' => $wholesalePrice,
									'map' => $map,
									'retailprice' => $retailPrice
								);
							}
						}
					}
				}
				if
				($colorCount == '0')
				{
					$skipColorIndex[] = $ind;
				}
				$ind++;
			}
			//debug('Grapgh Data',$graph_data,2);
			$cat = array_unique($cat);
			$tempArray = array();
			sort($cat);
			// For colors Handling
			if
			(count($skipColorIndex) > 0)
			{
				$tmpColor = array();
				foreach ($colors as $cKeyC => $cValC)
				{
					if
					(in_array($cKeyC, $skipColorIndex))
					{
						unset($colors[$cKeyC]);
					}else
					{
						$tmpColor[] = $cValC;
					}
				}
				unset($colors);
				$colors = $tmpColor;
			}
			//debug('Graph Data',$graph_data,2);
			//debug('Temp Graph Data',$graph_data_temp,2);
			//debug('Temp Cat',array_unique($tempcat),2);
			/*For making Google chart Data of scatter graph*/
			$googledataColorArrayTemp = array();
			$googleDataArrayTemp = array();
			$googleDataArrayTemp[0][] = 'Date';
			$maxValueTemp = 0;
			$prCountTemp = count($products);
			//debug('Products',$products,2);
			//debug('CAT',$cat,2);
			$googleCat = array();
			foreach ($cat as $val)
			{
				$valExplode = explode('-', $val);
				$val = isset($valExplode[0]) ? $valExplode[0] : $val;
				$datestr = $val;
				if
				(!in_array($datestr, $googleCat))
				{
					$googleCat[] = $datestr;
				}
			}
			//$googleCat = array_unique($googleCat);
			sort($googleCat);
			//debug('GOOGLE CAT',$googleCat,2);
			$googleDataArrayTemp = array();
			$googleDataArrayTemp[0][] = 'Date';
			for ($prcounter = 0; $prcounter < $prCountTemp; $prcounter++)
			{
				$googleDataArrayTemp[0][] = 'Price: '.getProductsTitle($products[$prcounter]);
				$googleDataArrayTemp[0][] = 'MAP: '.getProductsTitle($products[$prcounter]);
				$googleDataArrayTemp[0][] = 'Retail: '.getProductsTitle($products[$prcounter]);
				if
				(isset($colors[$prcounter]))
				{
					$googledataColorArrayTemp[] = $colors[$prcounter];
					$googledataColorArrayTemp[] = $colors[$prcounter];
					$googledataColorArrayTemp[] = $colors[$prcounter];
				}else
				{
					$googledataColorArrayTemp[] = '#FD9B00';
					$googledataColorArrayTemp[] = '#FD9B00';
					$googledataColorArrayTemp[] = '#FD9B00';
				}
			}
			$secondsCategories = getSecondsArray($googleCat[0],$googleCat[count($googleCat)-1]);//for new change
			//debug('Second Categories',$secondsCategories,2);
			//debug('GRaph temp data',$graph_data_temp,2);
			$categoriesCount = count($secondsCategories);
			$rowsCount = 0;
			$previousvalues = array();
			for
			($m=0;$m<$categoriesCount;$m++)
			{
				++$rowsCount;
				$newDate = date('Y-m-d H:i:s',$secondsCategories[$m]);
				$newDate = explode(' ',$newDate);
				$newDate1 = explode('-',$newDate[0]);
				$newDate2 = explode(':',$newDate[1]);
				$googleDataArrayTemp[$rowsCount][] = 'new  Date('.$newDate1[0].','.($newDate1[1]-1).','.$newDate1[2].','.$newDate2[0].','.$newDate2[1].','.$newDate2[2].')';
				foreach
				($products as $key=>$value)
				{
					if
					(isset($graph_data_temp[$value][$secondsCategories[$m]]))
					{
						$priceValue = $graph_data_temp[$value][$secondsCategories[$m]]['price'][0];
						$mapValue   = $graph_data_temp[$value][$secondsCategories[$m]]['map'][0];
						$retailValue= $graph_data_temp[$value][$secondsCategories[$m]]['retail'][0];
					}else if
						(isset($previousvalues[$value]['map']))
						{
							$priceValue = null;
							$mapValue   = $previousvalues[$value]['map'];
							$retailValue= $previousvalues[$value]['retail'];
						}else
					{
						$priceValue = null;
						$mapValue   = null;
						$retailValue= null;
					}
					$previousvalues[$value]['price']  = $priceValue;
					$previousvalues[$value]['map']    = $mapValue;
					$previousvalues[$value]['retail']   = $retailValue;
					$googleDataArrayTemp[$rowsCount][] = (float)$priceValue;
					$googleDataArrayTemp[$rowsCount][] = (float)$mapValue;
					$googleDataArrayTemp[$rowsCount][] = (float)$retailValue;
				}
			}
			//debug('Google Array temp',$googleDataArrayTemp,2);
			/*************************************************/
			foreach ($graph_data as $key => $dArr)
			{
				$priceD = array();
				$whpriceD = array();
				$repriceD = array();
				$k = 1;
				foreach ($cat as $ckey => $cval)
				{
					if
					(!isset($dArr[$cval]))
					{
						unset($cat[$ckey]);
					}else
					{
						if
						(!in_array($cat[$ckey], $tempArray))
						{
							$tempArray[] = $cat[$ckey];
						}
						$arr = $dArr[$cval];
						$cnt = $arr['count'];
						$priceD[] = $arr['price'] / $cnt;
						$whpriceD[] = $arr['map'] / $cnt;
						$repriceD[] = $arr['retailprice'] / $cnt;
					}
				}
				$series[] = array('name' => getProductsTitle($key), 'data' => $priceD, 'dashStyle' => 'Dot', 'color' => $colors[$i]);
				$series[] = array('name' => getProductsTitle($key), 'data' => $whpriceD, 'dashStyle' => 'Solid', 'color' => $colors[$i]);
				$series[] = array('name' => getProductsTitle($key), 'data' => $repriceD, 'dashStyle' => 'Dash', 'color' => $colors[$i]);
				$i++;
			}
			//debug('cat',$cat,2);
			$cat = $tempArray;
			$mcat = array();
			foreach ($cat as $val)
			{
				$valExplode = explode('-', $val);
				$val = isset($valExplode[0]) ? $valExplode[0] : $val;
				$mcat[] = date('n/j/Y h:i A', $val);
			}
			$cat = $mcat;
			//debug('cat After ',$cat,2);
		}else
		{
			$cat = createDateRangeArrayNew($this->data->report_where['date_from'], $this->data->report_where['date_to']);
			//debug('Category',$cat,2);
			$skipColorIndex = array();
			$ind = 0;
			foreach ($products as $key => $val)
			{
				$colorCount = 0;
				foreach ($gData as $data)
				{
					if
					($val == $data['prod_id'])
					{
						$colorCount++;
						$date = date('Y-m-d', $data['dt']);
						$price = $data['price'];
						$wholesalePrice = $data['wholesale'];
						$retailPrice = $data['retail'];
						$map = $data['map'];
						if
						(isset($graph_data[$val]) && array_key_exists($date, $graph_data[$val]))
						{
							$graph_data[$val][$date]['count'] = $graph_data[$val][$date]['count'] + 1;
							$graph_data[$val][$date]['price'] = $graph_data[$val][$date]['price'] + $price;
							$graph_data[$val][$date]['wholesaleprice'] = $graph_data[$val][$date]['wholesaleprice'] + $wholesalePrice;
							$graph_data[$val][$date]['map'] = $graph_data[$val][$date]['map'] + $map;
							$graph_data[$val][$date]['retailprice'] = $graph_data[$val][$date]['retailprice'] + $retailPrice;
						}else
						{
							$graph_data[$val][$date] = array('count' => 1,
								'price' => $price,
								'wholesaleprice' => $wholesalePrice,
								'map' => $map,
								'retailprice' => $retailPrice
							);
						}
					}
				}
				if
				($colorCount == '0')
				{
					$skipColorIndex[] = $ind;
				}
				$ind++;
			}
			// debug('GraphData',$graph_data,2);
			$cat = array_unique($cat);
			//debug('CategorUNIQUE',$cat,2);
			$tempArray = array();
			sort($cat);
			// For colors Handling
			if
			(count($skipColorIndex) > 0)
			{
				$tmpColor = array();
				foreach ($colors as $cKeyC => $cValC)
				{
					if
					(in_array($cKeyC, $skipColorIndex))
					{
						unset($colors[$cKeyC]);
					}else
					{
						$tmpColor[] = $cValC;
					}
				}
				unset($colors);
				$colors = $tmpColor;
			}
			foreach ($graph_data as $key => $dArr)
			{
				$priceD = array();
				$whpriceD = array();
				$repriceD = array();
				$k = 1;
				foreach ($cat as $ckey => $cval)
				{
					if
					(!isset($dArr[$cval]))
					{
						unset($cat[$ckey]);
					}else
					{
						if
						(!in_array($cat[$ckey], $tempArray))
						{
							$tempArray[] = $cat[$ckey];
						}
						$arr = $dArr[$cval];
						$cnt = $arr['count'];
						$priceD[] = $arr['price'] / $cnt;
						$whpriceD[] = $arr['map'] / $cnt;
						$repriceD[] = $arr['retailprice'] / $cnt;
					}
				}
				$series[] = array('name' => getProductsTitle($key), 'data' => $priceD, 'dashStyle' => 'Dot', 'color' => $colors[$i]);
				$series[] = array('name' => getProductsTitle($key), 'data' => $whpriceD, 'dashStyle' => 'Solid', 'color' => $colors[$i]);
				$series[] = array('name' => getProductsTitle($key), 'data' => $repriceD, 'dashStyle' => 'Dash', 'color' => $colors[$i]);
				$i++;
			}
			$cat = $tempArray;
			foreach ($cat as $val)
			{
				$mcat[] = date('n/j', strtotime($val));
			}
			$cat = $mcat;
		}
		//debug('Categor AAAAA',$cat,2);
		if
		(count($cat) > 10)
		{
			$newCat = array();
			foreach ($cat as $ind => $val)
			{
				$newCat[] = ' ';
			}
			$cat = $newCat;
		}
		//debug('cat',$cat,2);
		$fArray = array(
			'data' => $series,
			'y_title' => 'Price',
			'x_title' => 'Date',
			'cat' => $cat,
			'type' => 'line'
		);
		/* For Google Charts */
		$googledataColorArray = array();
		$googleDataArray = array();
		$googleDataArray[0][] = 'Date';
		$maxValue = 0;
		$prCount = count($products);
		//debug('Products',$products,2);
		for ($prcounter = 0; $prcounter < $prCount; $prcounter++)
		{
			$googleDataArray[0][] = 'Avg Price: '.getProductsTitle($products[$prcounter]);
			$googleDataArray[0][] = 'MAP: '.getProductsTitle($products[$prcounter]);
			$googleDataArray[0][] = 'Retail: '.getProductsTitle($products[$prcounter]);
			if
			(isset($colors[$prcounter]))
			{
				$googledataColorArray[] = $colors[$prcounter];
				$googledataColorArray[] = $colors[$prcounter];
				$googledataColorArray[] = $colors[$prcounter];
			}else
			{
				$googledataColorArray[] = '#FD9B00';
				$googledataColorArray[] = '#FD9B00';
				$googledataColorArray[] = '#FD9B00';
			}
		}
		foreach ($cat as $keyCat => $vCat)
		{
			$googleDataArray[$keyCat + 1][] = $vCat;
			foreach ($series as $seriesKey => $seriesData)
			{
				$tempData = isset($seriesData['data'][$keyCat]) ? $seriesData['data'][$keyCat] : 0;
				$tempData = number_format($tempData, 2);
				$googleDataArray[$keyCat + 1][] = (float) $tempData;
				if
				($tempData > $maxValue)
				{
					$maxValue = $tempData;
				}
			}
		}
		//debug('Final Array',$fArray,2);
		//debug('Google Array',$googleDataArray,2);
		if
		($hourFlag)
		{
			$fArray['googleData'] = $googleDataArrayTemp;
			$fArray['googleDataColors'] = $googledataColorArrayTemp;
			$fArray['maxValue'] = $maxValueTemp;
			$fArray['type'] = 'scatter';
		}else
		{
			$fArray['googleData'] = $googleDataArray;
			$fArray['googleDataColors'] = $googledataColorArray;
			$fArray['maxValue'] = $maxValue;
		}
		/* END FOR Google Charts */
		//$graphImagename = $this->generateGraphImage($fArray, $hourFlag, 'Price Over Time');
		$fArray['graphImageName'] = '';
		return ($fArray);
	}


	function priceViolation($gData, $report_where, $skip_graph_image = false)
	{
		$this->data->report_where = $report_where;
		$cat = array();
		$oItem = array();
		$item = array();
		$graph_data = array();
		$hourFlag = (isset($this->data->report_where['time_frame']) && $this->data->report_where['time_frame'] == '24') ? true : false;
		$products = $this->data->report_where['product_ids'];
		// @TODO Universal colors
		$colors = array(
			"#FBB925",
			"#00AEEF",
			"#BF302D",
			"#83B519",
			"#FD9B00"
		);
		$series = array();
		$graph_data_temp = array();
		$tempcat = array();
		//echo "populating gData from priceViolation();<br>\n";
		$i = 0;
		//debug('Request in GRaph',$this->data->report_where,2);
		//debug('GraphData',$gData,2);
		//debug('REQUEST INFO',$this->data->report_where,2);
		$yTitle = 'Violations';
		if
		($hourFlag)
		{
			$cat = last24HourCat($gData, true);
			//debug("CAT",$cat,2);
			//debug("Products",$products,2);
			//debug("gData",$gData,2);//exit;
			//debug('request',$this->data->report_where,2);
			$skipColorIndex = array();
			$ind = 0;
			foreach ($products as $key1 => $val)
			{
				$colorCount = 0;
				foreach ($gData as $key => $data)
				{
					$key = explode('-', $key);
					foreach ($data as $inData)
					{
						if
						($val == $inData['prod_id'])
						{
							$colorCount++;
							$tempTime = $inData['dt'];
							$price = $inData['price'];
							$alarmPrice = $inData['alarm_price'];
							$market = $inData['market_place'];
							if
							(isset($graph_data[$val]) && array_key_exists($key[0], $graph_data[$val]) && array_key_exists($key[1], $graph_data[$val][$key[0]]))
							{
								$graph_data[$val][$key[0]][$key[1]]['count'] = $graph_data[$val][$key[0]][$key[1]]['count'] + 1;
								$graph_data[$val][$key[0]][$key[1]]['price'] = $graph_data[$val][$key[0]][$key[1]]['price'] + $price;
								$graph_data[$val][$key[0]][$key[1]]['alarmPrice'] = $graph_data[$val][$key[0]][$key[1]]['alarmPrice'] + $alarmPrice;
							}else
							{
								$graph_data[$val][$key[0]][$key[1]] = array('count' => 1,
									'price' => $price,
									'alarmPrice' => $alarmPrice,
									'market_place' => $market
								);
							}
						}
					}
				}
				if
				($colorCount == '0')
				{
					$skipColorIndex[] = $ind;
				}
				$ind++;
			}
			$cat = array_unique($cat);
			$tempArray = array();
			sort($cat);
			//debug('Graph Data',$graph_data,2);
			//debug('Cat',$cat,2);exit;
			// For colors Handling
			if
			(count($skipColorIndex) > 0)
			{
				$tmpColor = array();
				foreach ($colors as $cKeyC => $cValC)
				{
					if
					(in_array($cKeyC, $skipColorIndex))
					{
						unset($colors[$cKeyC]);
					}else
					{
						$tmpColor[] = $cValC;
					}
				}
				unset($colors);
				$colors = $tmpColor;
			}
			foreach ($graph_data as $key => $dArr)
			{
				$keyComp = key($dArr);
				$priceD = array();
				$alpriceD = array();
				$priceD1 = array();
				$priceD2 = array();
				$priceD3 = array();
				$k = 1;
				foreach ($cat as $ckey => $cval)
				{
					$keyOther = $cval;
					//echo date('Y-m-d H:i:s',$keyComp)."===============".date('Y-m-d H:i:s',$keyOther);
					if
					(!isset($dArr[$cval]))
					{
						continue;
					}else
					{
						if
						(!in_array($cat[$ckey], $tempArray))
						{
							$tempArray[] = $cat[$ckey];
						}
						$arr = $dArr[$cval];
						//debug('ARR',$arr,2);
						/* Added for adjusting data for google charts */
						$priceD1[] = (isset($arr['amazon'])) ? $arr['amazon']['count'] : 0;
						$priceD2[] = (isset($arr['google'])) ? $arr['google']['count'] : 0;
						$priceD3[] = (isset($arr['shopping'])) ? $arr['shopping']['count'] : 0;
					}
				}
				if
				(in_array('all', $this->data->report_where['api_type']))
				{
					$series[] = array('name' => getProductsTitle($key, 'Amazon'), 'data' => $priceD1, 'color' => $colors[0], 'id' => $key);
					$series[] = array('name' => getProductsTitle($key, 'Google'), 'data' => $priceD2, 'color' => $colors[1], 'id' => $key);
					$series[] = array('name' => getProductsTitle($key, 'Shopping.com'), 'data' => $priceD3, 'color' => $colors[2], 'id' => $key);
				}else
				{
					if
					(in_array('Amazon', $this->data->report_where['api_type']) || in_array('amazon', $this->data->report_where['api_type']))
					{
						$series[] = array('name' => getProductsTitle($key, 'Amazon'), 'data' => $priceD1, 'color' => $colors[0], 'id' => $key);
					}
					if
					(in_array('Google', $this->data->report_where['api_type']) || in_array('google', $this->data->report_where['api_type']))
					{
						$series[] = array('name' => getProductsTitle($key, 'Google'), 'data' => $priceD2, 'color' => $colors[1], 'id' => $key);
					}
					if
					(in_array('Shopping.com', $this->data->report_where['api_type']) || in_array('shopping.com', $this->data->report_where['api_type']) || in_array('shopping', $this->data->report_where['api_type']) || in_array('Shopping', $this->data->report_where['api_type']))
					{
						$series[] = array('name' => getProductsTitle($key, 'Shoping.com'), 'data' => $priceD3, 'color' => $colors[2], 'id' => $key);
					}
				}
				$i++;
			}
			//debug('cat',$cat,2);
			$cat = $tempArray;
			$mcat = array();
			foreach ($cat as $val)
			{
				$valExplode = explode('-', $val);
				$val = isset($valExplode[0]) ? $valExplode[0] : $val;
				$mcat[] = date('n/j/Y h:i A', $val);
			}
			$cat = $mcat;
		}else
		{
			$yTitle = 'Violations';
			$cat = createDateRangeArray($this->data->report_where['date_from'], $this->data->report_where['date_to']);
			$skipColorIndex = array();
			$ind = 0;
			$marketPlaceAr = array();
			foreach ($products as $key => $val)
			{
				$colorCount = 0;
				foreach ($gData as $data)
				{
					if
					($val == $data['prod_id'])
					{
						$colorCount++;
						$marketplace = strtolower($data['market_place']);
						$date = date('m/d/y', $data['dt']);
						if
						(isset($graph_data[$val]) && array_key_exists($date, $graph_data[$val]))
						{
							$graph_data[$val][$date][$marketplace] += $data['violation_count'];
						}else
						{
							$graph_data[$val][$date][$marketplace] = $data['violation_count'];
						}
					}
				}
				if
				($colorCount == '0')
				{
					$skipColorIndex[] = $ind;
				}
				$ind++;
			}
			$cat = array_unique($cat);
			$tempArray = array();
			sort($cat);
			//debug('Market Place',$graph_data,2);
			//For handling Colors
			if
			(count($skipColorIndex) > 0)
			{
				$tmpColor = array();
				foreach ($colors as $cKeyC => $cValC)
				{
					if
					(in_array($cKeyC, $skipColorIndex))
					{
						unset($colors[$cKeyC]);
					}else
					{
						$tmpColor[] = $cValC;
					}
				}
				unset($colors);
				$colors = $tmpColor;
			}
			foreach ($graph_data as $key => $dArr)
			{
				$priceD1 = array();
				$priceD2 = array();
				$priceD3 = array();
				foreach ($cat as $ckey => $cval)
				{
					if
					(!isset($dArr[$cval]))
					{
						unset($cat[$ckey]);
					}else
					{
						if
						(!in_array($cat[$ckey], $tempArray))
						{
							$tempArray[] = $cat[$ckey];
						}
						$m = 0;
						//debug('abc',$dArr,2);
						$priceD1[] = (isset($dArr[$cval]['amazon'])) ? $dArr[$cval]['amazon'] : 0;
						$priceD2[] = (isset($dArr[$cval]['google'])) ? $dArr[$cval]['google'] : 0;
						$priceD3[] = (isset($dArr[$cval]['shopping.com'])) ? $dArr[$cval]['shopping.com'] : 0;
					}
				}
				$series[] = array('name' => getProductsTitle($key), 'data' => $priceD1, 'color' => $colors[0], 'id' => $key); //'dashStyle' => 'Dot', 'color' => $colors[$i],
				$series[] = array('name' => getProductsTitle($key), 'data' => $priceD2, 'color' => $colors[1], 'id' => $key); //'dashStyle' => 'Dot', 'color' => $colors[$i],
				$series[] = array('name' => getProductsTitle($key), 'data' => $priceD3, 'color' => $colors[2], 'id' => $key); //'dashStyle' => 'Dot', 'color' => $colors[$i],
				$i++;
			}
			$cat = $tempArray;
			foreach ($cat as $val)
			{
				$mcat[] = date('n/j', strtotime($val));
			}
			$cat = $mcat;
		}
		//debug('series',$series,2);
		//debug('cat',$cat,2);
		/*debug('cat',$cat,2);
      debug('series',$series,2);
     exit; */
		if
		(count($cat) > 10)
		{
			$newCat = array();
			foreach ($cat as $ind => $val)
			{
				$newCat[] = ' ';
			}
			$cat = $newCat;
		}
		$fArray = array(
			'data' => $series,
			'y_title' => (isset($yTitle)) ? $yTitle : 'Violations',
			'x_title' => 'Date',
			'cat' => $cat,
			'type' => 'scatter'
		);
		/* For Google Charts */
		$googledataColorArray = array();
		$googleDataArray = array();
		$googleDataArray[0][] = 'Date';
		$maxValue = 0;
		$prCount = count($products);
		for ($prcounter = 0; $prcounter < $prCount; $prcounter++)
		{
			if
			(in_array('all', $this->data->report_where['api_type']))
			{
				$googleDataArray[0][] = 'Amazon: '.getProductsTitle($products[$prcounter]);
				$googleDataArray[0][] = 'Google: '.getProductsTitle($products[$prcounter]);
				$googleDataArray[0][] = 'Shopping.com: '.getProductsTitle($products[$prcounter]);
				$googledataColorArray[] = $colors[$prcounter];
				$googledataColorArray[] = $colors[$prcounter];
				$googledataColorArray[] = $colors[$prcounter];
			}else
			{
				if
				(in_array('Amazon', $this->data->report_where['api_type']) || in_array('amazon', $this->data->report_where['api_type']))
				{
					$googleDataArray[0][] = 'Amazon: '.getProductsTitle($products[$prcounter]);
					$googledataColorArray[] = $colors[$prcounter];
				}
				if
				(in_array('Google', $this->data->report_where['api_type']) || in_array('google', $this->data->report_where['api_type']))
				{
					$googleDataArray[0][] = 'Google: '.getProductsTitle($products[$prcounter]);
					$googledataColorArray[] = $colors[$prcounter];
				}
				if
				(in_array('Shopping.com', $this->data->report_where['api_type']) || in_array('shopping.com', $this->data->report_where['api_type']) || in_array('shopping', $this->data->report_where['api_type']) || in_array('Shopping', $this->data->report_where['api_type']))
				{
					$googleDataArray[0][] = 'Shopping.com: '.getProductsTitle($products[$prcounter]);
					$googledataColorArray[] = $colors[$prcounter];
				}
			}
		}
		foreach ($cat as $keyCat => $vCat)
		{
			/*For Scatter chart*/
			$newDate = date('Y-m-d H:i:s',strtotime($vCat));
			$newDate = explode(' ',$newDate);
			$newDate1 = explode('-',$newDate[0]);
			$newDate2 = explode(':',$newDate[1]);
			if
			($hourFlag)
			{
				$googleDataArray[$keyCat + 1][] = 'new  Date('.$newDate1[0].','.($newDate1[1]-1).','.$newDate1[2].','.$newDate2[0].','.$newDate2[1].','.$newDate2[2].')';
			}else
			{
				$googleDataArray[$keyCat + 1][] = $vCat; //uncomment for column chart and comment the above line
			}
			foreach ($series as $seriesKey => $seriesData)
			{
				$valueForDisplay = isset($seriesData['data'][$keyCat]) ? $seriesData['data'][$keyCat] : 0;
				$googleDataArray[$keyCat + 1][] = $valueForDisplay;
				if
				($valueForDisplay > $maxValue)
				{
					$maxValue = $valueForDisplay;
				}
			}
		}
		//debug('Final Array',$fArray,2);
		//debug('Google Array',$googleDataArray,2);
		$fArray['googleData'] = $googleDataArray;
		$fArray['googleDataColors'] = $googledataColorArray;
		$fArray['maxValue'] = $maxValue;
		/* END FOR Google Charts */
		$graphImagename = '';
		if
		(!$skip_graph_image)
		{
			//$graphImagename = $this->generateGraphImage($fArray, $hourFlag, 'Violation Report');
		}
		$fArray['graphImageName'] = $graphImagename;
		//debug('DATA',$fArray,2);
		return ($fArray);
	}

	function capture_image()
	{
		define('BATIK_PATH', BASEPATH.'batik/batik-rasterizer.jar');
		$filePath = dirname(BASEPATH)."/charts";
		///////////////////////////////////////////////////////////////////////////////
		ini_set('magic_quotes_gpc', 'off');
		$type = $_POST['type'];
		$svg = (string) $_POST['svg'];
		$filename = (string) $_POST['filename'];
		// prepare variables
		if
		(!$filename)
			$filename = 'chart';
		if
		(get_magic_quotes_gpc())
		{
			$svg = stripslashes($svg);
		}
		$tempName = md5(rand());
		// allow no other than predefined types
		if
		($type == 'image/png')
		{
			$typeString = '-m image/png';
			$ext = 'png';
		} elseif
		($type == 'image/jpeg')
		{
			$typeString = '-m image/jpeg';
			$ext = 'jpg';
		} elseif
		($type == 'application/pdf')
		{
			$typeString = '-m application/pdf';
			$ext = 'pdf';
		} elseif
		($type == 'image/svg+xml')
		{
			$ext = 'svg';
		}
		$outfile = "$filePath/$tempName.$ext";
		if
		(isset($typeString))
		{
			// size
			if
			($_POST['width'])
			{
				$width = (int) $_POST['width'];
				if
				($width)
					$width = "-w $width";
			}
			// generate the temporary file
			if
			(!file_put_contents("uploaded_files/$tempName.svg", $svg))
			{
				die("Couldn't create temporary file. Check that the directory permissions for
							the /temp directory are set to 777.");
			}
			// do the conversion
			$output = shell_exec("\"java -jar ".BATIK_PATH." $typeString -d $outfile $width $filePath/$tempName.svg\"");
			// catch error
			if
			(!is_file($outfile) || filesize($outfile) < 10)
			{
				echo "<pre>$output</pre>";
				echo "Error while converting SVG. ";
				if
				(strpos($output, 'SVGConverter.error.while.rasterizing.file') !== false)
				{
					echo "SVG code for debugging: <hr/>";
					echo htmlentities($svg);
				}
			}
			// stream it
			else
			{
				header("Content-Disposition: attachment; filename=$filename.$ext");
				header("Content-Type: $type");
				echo file_get_contents($outfile);
			}
			// delete it
			unlink("$filePath/$tempName.svg");
			//unlink($outfile);
			// SVG can be streamed directly back
		} else if
			($ext == 'svg')
			{
				header("Content-Disposition: attachment; filename=$filename.$ext");
				header("Content-Type: $type");
				echo $svg;
			}else
		{
			echo "Invalid type";
		}

		exit;
	}


	function generateGraphImage($finalData = array(), $hourflag, $title='Sticky Charts', $x_axis_format = 'date')
	{
		$path = $this->config->item('csv_upload_path');
		if
		(isset($finalData['data']) && count($finalData['data']) > 0)
		{

			// load the pData and pChart class if necessary
			if ( ! class_exists('pData'))
				require(APPPATH.'3rdparty/pchart/pChart/pData.class');
			if ( ! class_exists('pChart'))
				require(APPPATH.'3rdparty/pchart/pChart/pChart.class');

			$DataSet = new pData;
			$in = 0;
			foreach ($finalData['data'] as $seriesData)
			{
				$in++;
				$seriesIndex = 'Serie'.$in;
				$DataSet->AddPoint($seriesData['data'], $seriesIndex);
				$DataSet->SetSerieName($seriesData['name'], $seriesIndex);
				$DataSet->AddSerie($seriesIndex);
			}
			$xAxisArray = array();
			$in++;
			$seriesIndex = 'Serie'.$in;
			$catCount = count($finalData['cat']);
			if
			($catCount <= 10)
				$DataSet->SetXAxisFormat($x_axis_format);
			foreach ($finalData['cat'] as $catD)
			{
				if
				($catCount > 10)
				{
					$xAxisArray[] = '';
				}else
				{
					$xAxisArray[] = strtotime($catD);
				}
			}
			$DataSet->SetYAxisFormat("number");
			$DataSet->AddPoint($xAxisArray, $seriesIndex);
			$DataSet->SetAbsciseLabelSerie($seriesIndex);
			$DataSet->SetYAxisName($finalData['y_title']);
			$DataSet->SetXAxisName($finalData['x_title']);
			//debug('DATA ',$DataSet->GetData(),2);
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
			if
			($title == 'Violation Report')
			{
				$sCount = count($finalData['data']);
				if
				($sCount > 0)
				{
					for ($m = 0; $m < $sCount; $m++)
					{
						$color = Color_handler::get_next($m);
						$rgb = $color->get_rgb();
						$Test->setColorPalette($m, $rgb['r'], $rgb['g'], $rgb['b']);
					}
				}
				$Test->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(), SCALE_NORMAL, 0, 0, 0, TRUE, 0, 0, TRUE);
				$Test->drawBarGraph($DataSet->GetData(), $DataSet->GetDataDescription());
			}else
			{
				$sCount = count($finalData['data']);
				if
				($sCount > 0)
				{
					for ($m = 0; $m < $sCount; $m++)
					{
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
			$Test->setFontProperties(APPPATH.'3rdparty/pchart/Fonts/tahoma.ttf', 8);
			//$Test->drawLegend(90,35,$DataSet->GetDataDescription(),255,255,255);
			$Test->setFontProperties(APPPATH.'3rdparty/pchart/Fonts/tahoma.ttf', 10);
			//$Test->drawTitle(60,22,$title,50,50,50,585);
			$imgName = uniqid('graph_').'.png';
			$Test->Render($path.$imgName);
			return upload_to_amazon_graphImage($imgName, $path);
		}
	}


	/* function market is to query reports by marketplace */
	function market($market, $prodId, $dateStart, $dateEnd = NULL, $report_type = NULL, $id = false)
	{
		$this->data->by_which = $report_type ? $report_type : 'bymarket';

		$this->data->competitor_store_id = false;
		if ($this->data->by_which === 'bycompetition')
			{ // this is a competitor's product
			$this->data->competitor_product = $this->Product->get_by('id', $prodId);
			$this->data->competitor_store_id = $this->data->competitor_product->store_id;
			$this->data->proMerchants = getProductMerchant($this->data->competitor_store_id);
			$lookup_id = $this->data->competitor_store_id;
		}
		else
		{
			$lookup_id = $this->store_id;
		}
		$searchProduct = $this->Product->getProductsById($lookup_id, $prodId);

		if ( ! isset($searchProduct[0])) redirect('reports');

		$this->layout = 'frontend_inner';
		$this->data->time_frame = '24';

		if( ! $dateEnd) $this->data->flagDates24 = true;
		$this->data->my = 'productpricing';
		$this->data->productArr = getProductsDrp($this->store_id);
		$this->data->proMerchants = getProductMerchant($this->store_id);
		//$this->data->markertArr = getMarketArray();
		$this->data->product_ids = array($prodId);

		$this->data->searchProducts = array($prodId => $searchProduct[0]['title']);
		$this->data->date_from = $dateStart;
		$this->data->date_to = ($dateEnd) ? $dateEnd : $dateStart;
		$this->data->submitted = true;
		$this->data->all_markets = false;
		$this->data->all_retailers = false;

		if
		($this->data->date_from !== 'Start' && $this->data->date_to !== 'Stop')
		{
			$this->data->dateStartField = $this->data->date_from;
			$this->data->dateEndField = $this->data->date_to;
			$this->data->date_from = strtotime($this->data->date_from);
			$this->data->date_to = ( ! $dateEnd) ? strtotime($this->data->date_to.' 23:59:59'): strtotime($this->data->date_to);
			$this->data->time_frame = '';
			if
			($this->data->date_from == $this->data->date_to)
			{
				$this->data->flagDates24 = true;
				$this->data->time_frame = '24';
			}
		}else
		{
			$tF = getTimeFrame($this->data->time_frame);
			$this->data->date_from = $tF['date_from'];
			$this->data->date_to = $tF['date_to'];
		}

		//the saved report Id
		if($id){
		$this->data->report_id = $this->input->post('report_id');
		}

		if ( ($product_name = $this->input->post('product_name')) )
		{
			if
			(is_array($product_name))
			{
				for
				($i=0, $n=sizeof($product_name); $i<$n; $i++)
				{
					$this->data->report_name .= $product_name[$i].' ';
				}
				$this->data->report_name .= 'Product Pricing';
			}
			$this->data->file_name = str_replace(' ', '_', $this->data->report_name.' '.date('Y-m-d'));
			$this->data->productNames = $this->input->post('product_name');
		}

		$this->data->merchants = ($this->input->post('merchants')) ? $this->input->post('merchants') : array();
		$this->data->markets = array(ucfirst($market));
		$this->data->show_comparison = false;

		$this->data->report_where = array('date_from' => $this->data->date_from,
			'date_to' => $this->data->date_to,
			'api_type' => $this->data->markets,
			'product_ids' => $this->data->product_ids,
			'merchants' => $this->data->merchants,
			'time_frame' => $this->data->time_frame,
			'store_id' => $this->store_id,
			'competitor_store_id' => array($this->data->competitor_store_id),
			'show_comparison' => $this->data->show_comparison);

		//get cron_ids for 24 hour scatter
		$this->data->report_chart = 'scatter';
		if
		($this->data->flagDates24)
		{
			$this->data->report_where['cron_ids'] = getLast24HoursCronIds($this->data->date_from, '', $this->data->report_where['api_type']);
		}else
		{
			$this->data->report_where['cron_ids'] = getLast24HoursCronIds('', '', $this->data->report_where['api_type']);
		}

		$response = $this->Report->productPricingReport24($this->data->report_where);

		$this->data->Data = $response['data'];
		$this->data->gData = $this->Chart->prepGoogleData($response['data'], $this->data->report_where, $this->data->report_chart);
		$this->data->submitted = true;

		$this->session->set_userdata('report_where', $this->data->report_where);
		$this->data->graphDataType = 'chart';

		//
		// Set retailer/marketplace information
		//
		$this->data->marketRetailer = array();
		$this->data->retailersExist = array();
		$this->data->marketplacesExist = array();

		if
		($this->data->report_chart === 'line')
		{
			foreach
			($this->data->Data as $prodId => $marketArr)
			{
				foreach
				($marketArr as $marketName => $productData)
				{
					if
					( ! isset($this->data->marketRetailer[$marketName]))
						$this->data->marketRetailer[$marketName] = $this->Marketplace->is_retailer($marketName);
					if
					($this->data->marketRetailer[$marketName])
						$this->data->retailersExist[$prodId] = true;
					else
						$this->data->marketplacesExist[$prodId] = true;
				}
			}
		}
		elseif
		($this->data->report_chart === 'scatter')
		{
			foreach
			($this->data->Data as $prodId => $productData)
			{
				for
				($i = 0, $n = count($productData); $i < $n; $i++)
				{
					$marketName = strtolower($productData[$i]['marketplace']);
					if
					( ! isset($this->data->marketRetailer[$marketName]))
						$this->data->marketRetailer[$marketName] = $this->Marketplace->is_retailer($marketName);
					if
					($this->data->marketRetailer[$marketName])
						$this->data->retailersExist[$prodId] = true;
					else
						$this->data->marketplacesExist[$prodId] = true;
				}
			}
		}

		$this->_build_options_array($this->data->by_which);
		//$this->data->headerHTML = $this->load->view("front/reports_new_header", $this->data, true);
	}


	/**
	 * Handle exporting a report as an Excel file.
	 * 
	 * Note: This exports HTML which only Excel can open. Will not work with Google Sheets or 
	 * with Apple Numbers.
	 *  
	 * @author unknown
	 */
	function excel()
	{
    ini_set('memory_limit', '512M');
    ini_set('max_execution_time', 900);
	    
		$this->data->report_title = $this->input->post('report_name');
		$this->data->report_where = json_decode(html_entity_decode($this->input->post('report_where')), true);
		$this->data->time_frame = isset($this->data->report_where['time_frame']) ? $this->data->report_where['time_frame']:"";
        
    if (!empty($this->data->report_where['group_id']) )
		{
			$group = $this->Product->getGroupByID($this->data->report_where['group_id']);
			
			if (!empty($group['name']))
			{
				$this->data->report_title = $group['name'];
			}
		}
		
		$this->data->report_type = $this->input->post('report_type');
    $this->data->fileName = $this->input->post('file_name');
        
    /*
    if ( $this->data->fileName == '' ) {
        $this->data->fileName = str_replace(' ', '_', $this->data->report_title).'_'.date('m-d-Y');
    }
    */

		// get the images used
		$graphData = $this->input->post('graph_data');
		
		if ($graphData)
		{
			$imageName = $this->generateImage($graphData, $this->store_id);
		}

		$this->data->graph_image_name = empty($imageName) ? '' : $imageName;

		// generate the content and create the excel file
		require_once APPPATH . 'libraries/mvexport.php';
		
		// I guess Excel can read HTML?? - Christophe 8/24/2015
		//var_dump($this->input->post('export_content')); exit();
		
		$this->mvexport = new mvexport($this->input->post('export_content'));

		$this->data->report = $this->mvexport->getReport('.reportTable');

		$this->_layout = NULL;
		
		$this->_view = $this->_controller . '/' . $this->_method;
	}


	/**
	 *
	 * function automate
	 * provides view that holds report criteria to save reports
	 *
	 */
	function automate()
	{
		$this->_layout = NULL;
		$d = date("m/d/y");

		$data = array();
		$report_id = $this->input->post('id');

		if
		((int)$report_id)
		{
			//populating data from saved report
			$data['report_info'] = $this->Report->get_save_report_by_id($report_id);
			$data['report_info']['report_where'] = json_decode(html_entity_decode($data['report_info']['report_where']), true);

			if
			(!isset($data['report_info']['report_datetime'])) $data['report_info']['report_datetime'] = $d;

			$data['frequency'] = (int)$data['report_info']['report_recursive_frequency'];
			$data['reportDate'] = strtotime($data['report_info']['report_datetime']);
		}else
		{
			//populating data from current report
			$data['frequency'] = (int)$this->input->post('report_recursive_frequency')>0 ? true : false;
			$data['reportDate'] = strtotime($d);
			$data['reportDate'] = time();
			$data['report_info'] = array(
				'report_name' => $this->input->post('report_name'),
				'controller' => $this->input->post('controller'),
				'controller_function' => $this->input->post_default('controller_function', 'index'),
				'product_id' => $this->input->post('product_id'),
				'report_datetime' => $d,
				'report_recursive_frequency' => (int)$this->input->post('report_recursive_frequency'),
				'report_where' => json_decode(html_entity_decode($this->input->post('report_where')), true)
			);

			if
			($data['report_info']['controller'] === 'whois')
			{
				$data['report_info']['report_where']['reportMarketPlace'] = $this->input->post('reportMarketPlace');
				$data['report_info']['report_where']['reportMerchantName'] = $this->input->post('reportMerchantName');
			}
		}

		$this->data = $data;
		$this->_view = $this->_controller . '/automate';
	}

	function generateImage($content = '', $store_id = null)
	{
		$graph = false;
		if ($content)
			$graph = $content;
		elseif (isset($_REQUEST['content']))
			$graph = $_REQUEST['content'];

		if ( ! $graph)
			return false;

		$script_path = $this->config->item('file_root').'js/phantomjs/';
		$script_name = 'svg.js';

		$image = uniqid('graph_').'.png';
		$path = $this->config->item('csv_upload_path');

		$cmd = "phantomjs ".$script_path.$script_name." ".$path.$image." '".$graph."'";
		$ex = exec($cmd);

		$subfolder = isset($store_id) ? $store_id : '';

		return upload_to_amazon_graphImage($image, $path, $subfolder);
	}


}


/* End of file reports.php */
/* Location: ./system/application/libraries/reports.php */