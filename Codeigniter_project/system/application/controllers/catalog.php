<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 */
class Catalog extends MY_Controller
{

	function __construct(){
		parent::__construct();

		$this->load->library('form_validation');

		$this->load->model('marketplace_m', 'Marketplace');
		$this->load->model("products_m", 'Product');
		$this->load->model("products_deleted_m", 'ProductsDeleted');
		$this->load->model("report_m", 'Report');
		$this->load->model("store_m", 'Store');
		$this->load->model('upc_m', 'UPC');

		//output info
		// old: $this->javascript('views/catalog.js.php');
	}

	function product(){
		$post = json_decode($_POST['data']);
		$start_index = $_POST['start_index'];
		$html = '';
		for ($i = $start_index; $i < count($post); $i++){
			$html .= '<tr>';
			for ($j = 0; $j < count($post[$i]); $j++){
				$html .= '<input type="hidden" name="hid_data_'.$i.'_'.$j.'" id="hid_data_'.$i.'_'.$j.'" value="'.htmlentities($post[$i][$j]).'" />';
				$html .= '<td class="edit" id="'.$i.'_'.$j.'">'.htmlentities($post[$i][$j]).'</td>';
			}
			$html .= '</tr>';
		}
		$this->data->html = $html;
		echo json_encode($this->data);
		exit;
	}

	/**
	 * Top page where they can see all of their products.
	 * 
	 * @author Christophe
	 */
	function index()
	{   
	    $show_archived_products_cookie = $this->input->cookie('show_archived_products', TRUE);
	    
	    $show_archived_products = $show_archived_products_cookie == FALSE ? FALSE : TRUE;
	    
	    $this->db->select('*');
	    $this->db->from($this->_table_products);
	    $this->db->where('deleted_at', NULL);
	    $this->db->where('store_id', $this->store_id);
	    
	    if ($show_archived_products == FALSE)
	    {
	        $this->db->where('is_archived', 0);
	    }
	    
	    $this->db->or_where('deleted_at', '0000-00-00 00:00:00');
	    
	    $query = $this->db->get();
	    
	    $result = $query->result_array();

	    $products = array();
	    
	    for ($i = 0, $n = count($result); $i < $n; $i++)
	    {
	    	$row = array();
	    	
	    	$row['check'] = 0;
	    	$row['upc_code'] = $result[$i]['upc_code'];
	    	$row['title'] = html_entity_decode($result[$i]['title']);
	    	$row['retail_price'] = $result[$i]['retail_price'];
	    	$row['price_floor'] = $result[$i]['price_floor'] > 0 ? $result[$i]['price_floor'] : '';
	    	$row['wholesale_price'] = $result[$i]['wholesale_price'];
	    	
	    	if (strlen($result[$i]['sku']) > 0) 
	    	{
	    		$row['sku'] = $result[$i]['sku'];
	    	} 
	    	else 
	    	{
	    		$row['sku'] = '';
	    	}
	    	
	    	$row['is_tracked'] = $result[$i]['is_tracked'];
	    	$row['is_archived'] = $result[$i]['is_archived'];
	    	$row['id'] = $result[$i]['id'];
	    	$row['store_id'] = $result[$i]['store_id'];
	    
	    	// Get Pricing
	    	foreach (array('retail_price','price_floor','wholesale_price') as $pricing_type) 
	    	{
	    		$this->db->where('product_id', $result[$i]['id'])
	    		->where('pricing_type', $pricing_type)
	    		->order_by('pricing_start', 'desc')
	    		->limit(1);
	    		
	    		$pricing_result = $this->db->get('products_pricing');
	    		
	    		if ($pricing_result->num_rows() == 1) 
	    		{
	    			$row_array = $pricing_result->row_array();
	    			
	    			$row[$pricing_type] = $row_array['pricing_value'];
	    		}
	    	}
	    	
	    	$products[$i] = $row;
	    }
	    
	    $view_data['show_archived_products'] = $show_archived_products;
	    $view_data['products'] = $products;
	    
	    //var_dump($products); exit();
	    
	    // for report exporting
	    $view_data['report_name'] = 'Product Catalog';
	    $view_data['file_name'] = str_replace(' ', '_', $view_data['report_name']);
	    $view_data['my'] = 'catalog';
	    $view_data['controller'] = 'catalog';
	    $view_data['method'] = 'index';
	    $view_data['icon'] = 'ico-report';
	    $view_data['widget'] = 'mv-report';
	    $view_data['displayBookmark'] = true;
	    $view_data['report_where'] = array(
	    		'report_function' => 'report_catalog',
	    		'marketplace' => '',
	    		'report_type' => 'pricingoverview'
	    );
	    
	    $view_data['report_info'] = array('report_name' => $view_data['report_name']);
	    
	    $this->load->view('catalog/index', $view_data);
	}

	protected function _pricing($type = NULL)
	{
		if (!$type)
		{
			$type = $this->input->post_default('price_type', 'price_floor');
		}
		
		$this->db->where('db_name', $type);
		
		$price_column_res = $this->Store->get_columns_by_store($this->store_id);

		// debug - Christophe 11/1/2015
		/*
		var_dump($type);
		var_dump($price_column_res); 
		var_dump($this->db->last_query());
		exit();
		*/
		
		if (!isset($price_column_res[0]))
		{
			throw new UnexpectedValueException('Cannot find column data for type ' . $type);
		}
		
		$this->data->price_type = $type;
		$this->data->price_column = $price_column_res[0];
	}

	public function catalog_list(){
		$this->_layout = NULL;
		$this->_view = $this->_controller . '/' . '_catalog_list';

		$this->index();
	}
	
	/**
	 * Page where user can see active promotional pricing on products.
	 * 
	 * @author Christophe
	 */
	public function promotional_pricing()
	{
	    $this->db->select('PP.*, P.*');
	    $this->db->from('products_pricing_promotional PP');
	    $this->db->join($this->_table_products . ' P', 'P.id = PP.product_id');
	    $this->db->where('P.deleted_at', NULL);
	    $this->db->where('P.store_id', $this->store_id);
	    $this->db->where('P.is_archived', 0);
	     
	    $query = $this->db->get();
	     
	    $result = $query->result_array();
	    
	    //var_dump($result); exit();
	    
	    $products = array();
	     
	    for ($i = 0; $i < count($result); $i++)
	    {
            $row = array();
             
            $row['check'] = 0;
            $row['upc_code'] = $result[$i]['upc_code'];
            $row['price'] = $result[$i]['price'];
            $row['uuid'] = $result[$i]['uuid'];
            $row['period_start'] = $result[$i]['period_start'];
            $row['period_end'] = $result[$i]['period_end'];
            $row['title'] = html_entity_decode($result[$i]['title']);
            $row['price_floor'] = $result[$i]['price_floor'] > 0 ? $result[$i]['price_floor'] : '';
             
            if (strlen($result[$i]['sku']) > 0)
            {
                			$row['sku'] = $result[$i]['sku'];
            }
            else
            {
                $row['sku'] = '';
            }
             
            $row['id'] = $result[$i]['id'];
            $row['store_id'] = $result[$i]['store_id'];
            
            // Get Pricing
            foreach (array('retail_price','price_floor','wholesale_price') as $pricing_type)
            {
                $this->db->where('product_id', $result[$i]['id'])
                ->where('pricing_type', $pricing_type)
                ->order_by('pricing_start', 'desc')
                ->limit(1);
                 
                $pricing_result = $this->db->get('products_pricing');
                 
                if ($pricing_result->num_rows() == 1)
                {
                    $row_array = $pricing_result->row_array();
                     
                    $row[$pricing_type] = $row_array['pricing_value'];
                }
            }
             
            $products[$i] = $row;
	    }
	    
	    $view_data['products'] = $products;
	    
	    $this->load->view('catalog/promotional_pricing', $view_data);
	}
	
    /**
     * Page with form where user can add promotional pricing periods.
     * 
     * @author Christophe
     */
    public function add_promotional_pricing()
    {
        $this->load->model('products_m');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('product_id', 'Product', 'trim|required|xss_clean');
        $this->form_validation->set_rules('price', 'Price', 'trim|required|xss_clean');
        $this->form_validation->set_rules('start_date', 'Start Date', 'trim|required|xss_clean');
        $this->form_validation->set_rules('end_date', 'End Date', 'trim|required|xss_clean');
        
        if ($this->form_validation->run() === FALSE)
        {
        	// validation failed, or first load
        }
        else
        {   
            // check to see if user can add for product
            $product_id = intval($this->input->post('product_id', TRUE));
            
            $product = $this->products_m->get_product_by_id($product_id);
            
            // check to see if user has access to edit this product
            if ($this->role_id != 2 || $this->store_id != intval($product['store_id']))
            {                
                $this->session->set_flashdata('error_msg', 'Error: Your account does not have access to this item.');
                
                redirect('/catalog');
                exit();
            }
            
            $period_start = $this->input->post('start_date', TRUE) . ' 00:00:00';
            $period_end = $this->input->post('end_date', TRUE) . ' 00:00:00';
            
            // check to make sure there is not already the same pricing period for a product
            $overlap = $this->products_m->get_overlapping_promotional_pricing($product_id, $period_start, $period_end);
            
            if (!empty($overlap))
            {
                $this->session->set_flashdata('error_msg', 'Error: A promotional pricing entry already exists for this product and overlaps with the time selected.');
                
                redirect('/catalog/add_promotional_pricing');
                exit();
            }
            
            // check to make sure start is before end
            if (strtotime($period_start) >= strtotime($period_end))
            {
                $this->session->set_flashdata('error_msg', 'Error: End date needs to come after start date.');
                
                redirect('/catalog/add_promotional_pricing');
                exit();
            }
            
        		$insert_data = array(
        				'uuid' => uuid(),
                'product_id' => $product_id,
        		    'price' => $this->input->post('price', TRUE),
        		    'period_start' => $period_start,
        		    'period_end' => $period_end,
        				'created' => date('Y-m-d H:i:s'),
        				'modified' => date('Y-m-d H:i:s')
        		);
        
        		$product_id = $this->products_m->insert_product_promotional_pricing($insert_data);

        		$this->session->set_flashdata('success_msg', 'New promotional pricing has been successfully added.');
        		
        		redirect('/catalog/promotional_pricing');
        		exit();
        }
                
        $view_data = array();
        
        $this->load->view('catalog/add_promotional_pricing', $view_data);
    }
    
    /**
     * Delete a row in the promotion pricing table.
     * 
     * @param string $pricing_uuid
     */
    public function delete_promotional($pricing_uuid)
    {
        $this->load->model('products_m');
        
        $promo_pricing = $this->products_m->get_promotional_pricing_by_uuid($pricing_uuid);
        
        if (empty($promo_pricing))
        {
            $this->session->set_flashdata('error_msg', 'Error: Promo pricing record not found.');
            
            redirect('/catalog/promotional_pricing');
            exit();
        }
        
        // check to see if they can delete
        $product_id = intval($this->input->post('product_id', TRUE));
        
        $product = $this->products_m->get_product_by_id($promo_pricing['product_id']);
        
        // check to see if user has access to edit this product
        if ($this->role_id != 2 || $this->store_id != intval($product['store_id']))
        {
            $this->session->set_flashdata('error_msg', 'Error: Your account does not have access to this item.');
        
            redirect('/catalog/promotional_pricing');
            exit();
        }
        
        // delete row
        $this->products_m->delete_promotional_pricing($promo_pricing['id']);
        
        $this->session->set_flashdata('success_msg', 'Promo pricing has been successfully deleted.');
        
        redirect('/catalog/promotional_pricing');
        exit();
    }
	
	/**
	 * Check to see if user has a saved state for their products table.
	 * 
	 * @author Christophe
	 */
	public function products_table_state_load()
	{
	    $this->load->model('users_m');
	     
	    $dt_state_save = $this->users_m->get_datatables_state_save($this->user_id, $this->store_id, 'products');
	    
	    if (empty($dt_state_save))
	    {
	        echo json_encode(array());
	    } 
	    else
	    {
	        //$state_array = json_decode($dt_state_save['state_json'], TRUE);
	        	        
	        //$state_array['start'] = intval($state_array['start']);
	        //$state_array['length'] = intval($state_array['length']);
	        
	        //var_dump($state_array); exit();
	        
	        echo $dt_state_save['state_json'];
	        //echo json_encode($state_array);
	    }
	    
	    exit();
	}
	
	/**
	 * Save the state for the DataTables plugin.
	 * 
	 * @author Christophe
	 */
	public function products_table_state_save()
	{
	    $this->load->model('users_m');
	    
	    //var_dump(json_encode($_POST)); exit();
	    
	    $state_json = json_encode($_POST, JSON_NUMERIC_CHECK);
	    
	    $dt_state_save = $this->users_m->get_datatables_state_save($this->user_id, $this->store_id, 'products');
	    
	    if (empty($dt_state_save))
	    {
	        $insert_data = array(
	        		'user_id' => $this->user_id,
	        		'table_name' => 'products',
	        		'state_json' => $state_json,
	        		'created' => date('Y-m-d H:i:s', time()),
	        		'modified' => date('Y-m-d H:i:s', time())
	        );

	        $this->users_m->insert_datatables_state_save($insert_data);
	    }
	    else
	    {
	        $update_data = array(
	        		'state_json' => $state_json,
	        		'modified' => date('Y-m-d H:i:s', time())
	        );
	        
	        $this->users_m->update_datatables_state_save($dt_state_save['id'], $update_data);
	    }
	    
	    echo 'true'; 
	    exit();
	}

	public function competitor_analysis(){
		$this->_layout = NULL;
		$this->_view = $this->_controller . '/' . '_competitor_analysis';

		//gather competitor columns
		$this->data->comp_products = $this->Store->getBrandCompetitorProducts($this->store_id);
	}

	public function product_lookup(){
		$this->_layout = NULL;
		$this->_view = $this->_controller . '/' . '_product_lookup';

		//$this->data->no_upc_marketplaces = $this->Marketplace->get_marketplaces_by_upc_lookup(FALSE);
		$this->data->no_upc_marketplaces = $this->Marketplace->get_marketplaces_by_storeid_using_categories($this->store_id);
	}

	function catalog_add($store_id, $search=''){
		$this->data->merchantItemsCount = 0;
		$this->data->store_id = $store_id;
		$this->data->merchantItems = $this->Store->getMerchantItemsMerchant($store_id, $user_id, $search);
		$this->data->groups = $this->Product->getGroups($this->user_id);
		$this->data->count = 150;

		$this->data->store_columns = $this->Store->get_columns_by_store($store_id);
		$this->data->included_columns = $this->Store->get_excluded_excluded_columns_by_store($store_id);
		$this->data->all_columns = $this->Store->get_all_columns($store_id);

		$this->data->products_saved = $this->session->userdata("products_saved");

		$this->load->view("front/merchant/product_management", $this->data);
	}

	function products($store_id = null){
		$this->data->store_id = $this->store_id;
		$this->data->dataArray = array();

		$this->error = '';

		$brand = $this->Store->get_brand_by_store($this->store_id);
		if ($this->input->post('rdProduct') === 'byform'){
			$title = $this->input->post('title');
			$upc_code = $this->input->post('upc_code');
			$sku = $this->input->post('sku');
			$retail_price = $this->input->post('retail_price');
			$price_floor = $this->input->post('price_floor');
			$wholesale_price = $this->input->post('wholesale_price');

			$added_item = FALSE;
			$errors = array();

			for ($i = 0, $n = count($sku); $i < $n; $i++){
				if ( ! $this->Product->product_exists_by_upc($upc_code[$i])) { // product not in system, add it
					$iT = time();
					$data = array(
						'store_id' => $this->store_id,
						'title' => $title[$i],
						'upc_code' => $upc_code[$i],
						'brand' => $brand,
						'sku' => $sku[$i],
						'wholesale_price' => $wholesale_price[$i],
						'retail_price' => $retail_price[$i],
						'price_floor' => $price_floor[$i],
						'created_at' => date('Y-m-d H:i:s', $iT)
					);
					$newProdId = $this->Product->add_product($data);
					$added_item = ($added_item or $newProdId);

					$this->db->insert($this->_table_products_pricing, array('product_id' => $newProdId,
																														'pricing_type' => 'retail_price',
																														'pricing_value' => $retail_price[$i],
																														'pricing_start' => date('Y-m-d H:i:s', $iT)));
					$this->db->insert($this->_table_products_pricing, array('product_id' => $newProdId,
																														'pricing_type' => 'wholesale_price',
																														'pricing_value' => $wholesale_price[$i],
																														'pricing_start' => date('Y-m-d H:i:s', $iT)));
					$this->db->insert($this->_table_products_pricing, array('product_id' => $newProdId,
																														'pricing_type' => 'price_floor',
																														'pricing_value' => $price_floor[$i],
																														'pricing_start' => date('Y-m-d H:i:s', $iT)));

				}else{ // product in system, generate notice
					$errors[] = $upc_code[$i].' is already in our database.';
				}
			}
			if ($added_item)
				$this->session->set_flashdata("success", 'Product(s) successfully added.');
			if (count($errors))
				$this->session->set_flashdata('error', implode("\n<br>", $errors));

			redirect(base_url().'catalog/catalog_add/'.$this->store_id);
		}elseif($this->input->post('rdProduct') === 'bycsv'){
			array_push($this->javascript_files, 'views/import.php');
			$headerArray = array();

			if ($_FILES['file']['tmp_name']){

				$ext = explode('.', $_FILES['file']['name']);
				$ext = $ext[count($ext) - 1];
				if ($ext === 'csv'){

					ini_set("auto_detect_line_endings", "1");
					$theFileSize = $_FILES['file']['size'];
					if ($theFileSize < (6 * 1024 * 1024)){
						$path = $this->config->item('csv_upload_path');
						move_uploaded_file($_FILES['file']['tmp_name'], $path.$_FILES['file']['name']);
						$extArray = explode('.', $_FILES['file']['tmp_name']);
						$ext = $extArray[count($extArray) - 1];
						if (strtolower($ext) != 'csv'){
							$this->error = false;
						}
						$handle = fopen($path.$_FILES['file']['name'], "r");
						$check = 0;
						$dataArray = array();
						$this->data->filename = $_FILES['file']['name'];

						$this->data->hasHeader = 0;
						$this->data->headerColumns = array();

						$hasHeader = $this->input->post("hasHeader");
						$this->data->hasHeader = $hasHeader;

						while (($data = fgetcsv($handle, 9999999, ",")) !== FALSE){
							$dataArray[] = $data;
							if(count($data) > count($this->data->headerColumns)){
								$this->data->headerColumns = $data;
							}
						}
						fclose($handle);

						if($hasHeader){
							if(isset($dataArray[0])){
								if(count($dataArray[0]) < count($this->data->headerColumns)){
									$missing_indexes = count($this->data->headerColumns) - count($dataArray[0]);
									$this->data->headerColumns = array_merge($dataArray[0], array_fill(count($dataArray[0]), $missing_indexes, ''));
								}else	{
									$this->data->headerColumns = $dataArray[0];
								}
							}

							$var_to_match = '';
							for ($i = 0, $n = count($this->data->headerColumns); $i < $n; $i++){
								if ($n - 1 == $i)
									$var_to_match .= $i.'=>%~'.preg_replace('/[=>%~&]/', '', $this->data->headerColumns[$i]);
								else
									$var_to_match .= $i.'=>%~'.preg_replace('/[=>%~&]/', '', $this->data->headerColumns[$i]).'&';
							}
							$headerArray = generateHeaderArray($var_to_match);
							if ($headerArray != '')
								$headerArray = generateHeaderPostArray($headerArray);
							else
								$headerArray = generateHeaderPostArray($var_to_match);
						}
						$this->data->dataArray = $dataArray;
						$this->data->headerArray = $headerArray;
						$this->load->view('front/merchant/import', $this->data);
					}else{
						$this->session->set_flashdata('error', 'Please upload file size must be less than 6MB.');
						redirect(base_url().'catalog/products/');
					}
				}else{
					$this->session->set_flashdata('error', 'Please provide your catalog in csv format.');
					redirect(base_url().'catalog/products/');
				}
			}else{
//echo "last else";exit;
				$this->error = false;
			}

			if ($this->error){
echo 'error';exit;
				redirect(base_url().'dashboard');
			}
		}else{
			array_push($this->javascript_files, 'views/import.php');
			array_push($this->javascript_files, 'views/add_products.php');
			$this->load->view('front/merchant/add_products', $this->data);
		}
	}
	
    /**
     * Handle bulk actions.
     * 
     * @author Christophe
     */
    public function bulk_action()
    {
        $this->load->library('Vision_products');
        
        //var_dump($_POST); exit();
        
        $product_ids = $_POST['select_product_id'];
        
        if ($this->role_id == 2)
        {
            if (isset($product_ids) || !empty($product_ids))
            {
                $bulk_action = intval($_POST['products-bulk-action-select']);
                
                switch($bulk_action)
                {
                    case 2:
                        // Track Product(s)
                        $this->vision_products->track_products($product_ids, $this->store_id);
                        
                        $this->session->set_flashdata('success_msg', 'Products are now being tracked.');
                        break;
                    case 3:
                        // Un-Track Product(s)
                        $this->vision_products->untrack_products($product_ids, $this->store_id);
                        
                        $this->session->set_flashdata('success_msg', 'Products are no longer being tracked.');
                        break;
                    case 4:
                        // Archive Product(s)
                        $this->vision_products->archive_products($product_ids, $this->store_id);
                        
                        $this->session->set_flashdata('success_msg', 'Products have been archived.');
                        break;
                    case 5:    
                        // Un-Archive Product(s)
                        $this->vision_products->unarchive_products($product_ids, $this->store_id);
                        
                        $this->session->set_flashdata('success_msg', 'Products are no longer archived.');
                        break;
                    default:
                        $this->session->set_flashdata('error_msg', 'Error: Please select a bulk action.');
                }
            }
            else
            {
                $this->session->set_flashdata('error_msg', 'Error: No products were selected.');
            }
        }
        else
        {
            $this->session->set_flashdata('error_msg', 'Error: Your account does not have access to this action.');
        }
        
        redirect('/catalog');
        exit();
    }
    
    /**
     * Determine if user wants to see archived products or not.
     * 
     * @author Christophe
     */
    public function set_show_archive_cookie()
    {
        $this->load->helper('cookie');
        
        //var_dump($this->input->cookie('show_archived_products', TRUE)); exit();
        
        $show_archived_products = $this->input->cookie('show_archived_products', TRUE);
        
        if ($show_archived_products == FALSE)
        {
            $cookie = array(
            		'name'   => 'show_archived_products',
            		'value'  => 'true',
            		'expire' => time() + (10 * 365 * 24 * 60 * 60),
            		'secure' => TRUE
            );
            
            set_cookie($cookie);
        }
        else
        {
            delete_cookie('show_archived_products');
        }
        
        redirect('/catalog');
        exit();
    }
    
    /**
     * Temporary way to bring back the old Lookup functionality that we had.
     * 
     * @author unknown, Christophe
     */
    public function lookup()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        
        $search = '';
        
        $this->data->merchantItemsCount = 0;
        
        $this->data->merchantItems = $this->Store->getMerchantItemsMerchant($this->store_id, $this->user_id, $search);
        $this->data->merchantAllItems = $this->Store->getMerchantItemsAllMerchant($this->store_id, $this->user_id, $search);
        $this->data->total_item_count = count($this->data->merchantAllItems);
        $this->data->groups = $this->Product->getGroups($this->store_id);
        $this->data->count = 150;
        
        $this->data->store_columns = $this->Store->get_columns_by_store($this->store_id);
        $this->data->included_columns = $this->Store->get_excluded_excluded_columns_by_store($this->store_id);
        $this->data->all_columns = $this->Store->get_all_columns();
        
        $this->data->catalog_ex_columns = $this->data->cat_inc_columns = array();
        $this->data->catalog_columns = $this->data->store_columns;
        $this->data->column_order = array();
        
        for($i=0, $n=sizeof($this->data->catalog_columns); $i<$n; $i++)
        {
        	array_push($this->data->cat_inc_columns, $this->data->catalog_columns[$i]);
        	array_push($this->data->column_order, $this->data->catalog_columns[$i]->db_name);
        }
        
        for($i=0, $n=sizeof($this->data->all_columns); $i<$n; $i++)
        {
        	if ( ! in_array($this->data->all_columns[$i], $this->data->cat_inc_columns))
        	{
        		array_push($this->data->catalog_ex_columns, $this->data->all_columns[$i]);
        	}
        }
        
        $this->data->products_saved = $this->session->userdata("products_saved");
        $this->data->STORE_CATEGORIES = $this->Store->get_categories_by_storeId($this->store_id);
        
        $this->_pricing();        
        
        $this->javascript('views/catalog.js.php');
    }
	
    /**
     * Form where admin can edit the details for a single product.
     * 
     * @todo start using UUIDs with products
     * @author Christophe
     * @param int $product_id
     */
    function edit_product($product_id)
    {
        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        $this->load->model('products_m');
        $this->load->model('products_pricing_m');
        
        $product_id = intval($product_id);
        
        $product = $this->products_m->get_product_by_id($product_id);
                
        // check to see if user has access to edit this product
        if ($this->role_id != 2 || $this->store_id != intval($product['store_id']))
        {            
            $this->session->set_flashdata('error_msg', 'Error: Your account does not have access to this item.');
            
            redirect('/catalog');
            exit();
        }
    
        $this->form_validation->set_rules('title', 'Product Title', 'trim|required|xss_clean');
        $this->form_validation->set_rules('upc_code', 'UPC Code', 'trim|xss_clean');
        $this->form_validation->set_rules('sku', 'SKU', 'trim|xss_clean');
        $this->form_validation->set_rules('is_tracked', 'Tracked', 'trim|xss_clean');
        $this->form_validation->set_rules('is_archived', 'Archived', 'trim|xss_clean');
        $this->form_validation->set_rules('retail_price', 'Retail Price', 'trim|xss_clean');
        $this->form_validation->set_rules('wholesale_price', 'Wholesale Price', 'trim|xss_clean');
        $this->form_validation->set_rules('price_floor', 'MAP', 'trim|xss_clean');
        
        if ($this->form_validation->run() == FALSE)
        {
            // validation failed, or first load
        }
        else
        {
            //var_dump($_POST); exit();
            
            $is_tracked = isset($_POST['is_tracked_checkbox']) ? 1 : 0;
            $is_archived = isset($_POST['is_archived_checkbox']) ? 1 : 0;
            
            $retail_price = str_replace('$', '', $this->input->post('retail_price', TRUE));
            $wholesale_price = str_replace('$', '', $this->input->post('wholesale_price', TRUE));
            $price_floor = str_replace('$', '', $this->input->post('price_floor', TRUE));
            	
            $update_data = array(
            		'title' => $this->input->post('title', TRUE),
                'upc_code' => $this->input->post('upc_code', TRUE),  
                'sku' => $this->input->post('sku', TRUE),
                'is_tracked' => $is_tracked,
                'is_archived' => $is_archived,
                'retail_price' => $retail_price,
                'wholesale_price' => $wholesale_price,
                'price_floor' => $price_floor
            );
            	
            $this->products_m->update_product($product_id, $update_data);
            
            $iT = time();
            
            if (isset($retail_price) && $retail_price != '')
            {
                $update_data = array(
                    'pricing_value' => $retail_price, 
                    'pricing_start' => date('Y-m-d H:i:s', $iT)
                );
                
                $this->products_pricing_m->update_pricing_by_product($product_id, 'retail_price', $update_data);
            }
            
            if (isset($wholesale_price) && $wholesale_price != '')
            {
                $update_data = array(
                    'pricing_value' => $wholesale_price,
                    'pricing_start' => date('Y-m-d H:i:s', $iT)
                );
                
                $this->products_pricing_m->update_pricing_by_product($product_id, 'wholesale_price', $update_data);
            }
            
            if (isset($price_floor) && $price_floor != '')
            {
                $update_data = array(
                    'pricing_value' => $price_floor,
                    'pricing_start' => date('Y-m-d H:i:s', $iT)
                );
                
                $this->products_pricing_m->update_pricing_by_product($product_id, 'price_floor', $update_data);
            }
            	
            $this->session->set_flashdata('success_msg', 'Product details have been successfully saved.');
            
            redirect('/catalog');
            exit();
        }
        
        $view_data['product'] = $product;
        
        $this->load->view('catalog/edit_product', $view_data);
    }
    
    /**
     * AJAX handler to set a product to be tracked.
     *
     * @author Christophe
     * @param int $product_id
     */
    public function archive_product($product_id)
    {
        $this->load->model('products_m');
        
        $product_id = intval($product_id);
        
        $product = $this->products_m->get_product_by_id($product_id);
        
        if (empty($product))
        {
            $this->session->set_flashdata('error_msg', 'Error: Product not found.');
        }
        else
        {
            // check to see if user can edit
            if (
            		intval($this->store_id) == intval($product['store_id']) &&
            		intval($this->role_id) == 2
            )
            {
                $update_data = array(
                		'is_archived' => 1
                );
                 
                $this->products_m->update_product($product_id, $update_data);
                
                $this->session->set_flashdata('success_msg', 'Product have been archived.');
            }
            else
            {
                $this->session->set_flashdata('error_msg', 'Error: Could not archive product.');
            }
        }
        
        redirect('/catalog');
        exit();
    }  

    /**
     * Toggle ON/OFF if a product is tracked or not.
     * 
     * @author Christophe
     * @param int $product_id
     * @return string
     */
    public function toggle_track_product($product_id)
    {
        $this->load->model('products_m');
        
        $product_id = intval($product_id);
        
        $product = $this->products_m->get_product_by_id($product_id);
        
        if (empty($product))
        {
            echo 'false';
        }
        else
        {
            // check to see if user can edit
            if (
            		intval($this->store_id) == intval($product['store_id']) &&
            		intval($this->role_id) == 2
            )
            {
                // check to see if product is tracked or untracked
                if (intval($product['is_tracked']) == 1)
                {
                    // set to untracked
                    $update_data = array(
                    		'is_tracked' => 0
                    );
                     
                    $this->products_m->update_product($product_id, $update_data);
                }
                else
                {
                    // set to tracked
                    $update_data = array(
                    		'is_tracked' => 1
                    );
                     
                    $this->products_m->update_product($product_id, $update_data);                    
                }
                
                echo 'true';
            }
            else
            {
                echo 'false';
            }
        }
        
        exit();        
    }
    
    /**
     * AJAX handler to set a product to be tracked.
     * 
     * @author Christophe
     * @param int $product_id
     */
    public function track_product($product_id)
    {
        $this->load->model('products_m');
        
        $product_id = intval($product_id);
        
        $product = $this->products_m->get_product_by_id($product_id);
        
        if (empty($product))
        {
            echo 'false';
        }
        else
        {
            // check to see if user can edit
            if (
                intval($this->store_id) == intval($product['store_id']) &&
                intval($this->role_id) == 2            
            )
            {
                $update_data = array(
                		'is_tracked' => 1
                );
                 
                $this->products_m->update_product($product_id, $update_data);   

                echo 'true';
            }
            else
            {
                echo 'false';
            }
        }
        
        exit();
    }
    
    /**
     * AJAX handler to set a product to be untracked.
     * 
     * @author Christophe
     * @param int $product_id
     */
    function untrack_product($product_id)
    {
        $this->load->model('products_m');
        
        $product_id = intval($product_id);
        
        $product = $this->products_m->get_product_by_id($product_id);
        
        if (empty($product))
        {
            echo 'false';
        }
        else
        {
            // check to see if user can edit
            if (
            		intval($this->store_id) == intval($product['store_id']) &&
            		intval($this->role_id) == 2
            )
            {
                $update_data = array(
                		'is_tracked' => 0
                );
                 
                $this->products_m->update_product($product_id, $update_data);
                
                echo 'true';
            }
            else
            {
                echo 'false';
            }
        }
        
        exit();
    }    

	function create_group(){
		$this->_response_type('json');

		$this->data->result = 'failed';
		$this->data->div_class = 'error';

		// guard against making groups that span multiple brands
		if ($this->store_id === 'all'){
			$this->data->html = 'Please select a brand before creating a group.';

			return;
		}

		// check that the group isn't empty
		$ids = $this->input->post('group_ids');
		if(empty($ids)){
			$this->data->html = "No item selected. Please select item(s) first to create group.";

			return;
		}

		// validate the group name
		$groupName = $this->input->post('group_name');
		if(empty($groupName)){
			$this->data->html = "Invalid group name.";

			return;
		}

		// verify the group doesn't exist
		if ($this->Product->isGroupExist($groupName, $this->store_id)){
			$this->data->html = "Group name already exists.";

			return;
		}

		// everything is good, we can make the group
		$ids = explode(',', $ids);
		$groupId = $this->Product->addGroup($groupName, $this->store_id);
		$this->Product->addGroupItems($groupId, $ids);
		$this->data->html = "New group successfully added";
		$this->data->result = "success";
		$this->data->div_class = 'success';
	}//end create_group

	function edit_group($group_id){
		$this->_response_type('json');

		$this->data->result = "failed";
		$this->data->div_class = "error";

		// Confirm that the merchant owns this group
		$user_id = $this->session->userdata('user_id');
		if ($this->Product->getGroupMerchant($group_id) != $user_id){
			$this->data->html = 'Group not found.';

			return;
		}

		// check that the edited group isn't empty
		if (empty($_POST['ids'])){
			$this->data->html = "No Item Selected";

			return;
		}

		// everything is good, we can update the group
		// Remove any ids that are already in the group
		$ids = array_flip(explode(',', $_POST['ids']));
		$current_group = $this->Product->getProductsByGroupId($group_id);
		for ($i = 0, $n = count($current_group); $i < $n; $i++){
			$pid = $current_group[$i]['product_id'];
			if (isset($ids[$pid]))
				unset($ids[$pid]);
		}
		$ids = array_keys($ids);

		// Add the products to the group
		$this->Product->addGroupItems($group_id, $ids);
		$this->data->html = "Products have been added to the group successfully";
		$this->data->result = "success";
		$this->data->div_class = 'success';
	}

	function delete_group($group_id){
		$this->_response_type('json');

		$this->Product->deleteGroup($group_id);
		$this->data->html = "Group has been deleted successfully";
		$this->data->result = "success";
		$this->data->div_class = 'success';
	}

	function getGroupsHTML(){
		$this->_response_type('json');

		$this->data->html = $this->Product->getGroupsHTML($this->store_id);
	}

	function product_tracking(){
		$this->_response_type('json');

		$ids = $this->input->post('ids');
		$action = $this->input->post('action');

		if ( ! is_array($ids))
			$ids = array($ids);

		if($action === 'tracking' || $action === 'skip'){
			$this->db->where_in('id', $ids)->update($this->_table_products, array('is_tracked' => '1'));
			$this->data->html = "Product(s) successfully marked as Tracked.";
		}elseif($action === 'untracking' || $action === 'unskip'){
			$this->db->where_in('id', $ids)->update($this->_table_products, array('is_tracked' => '0'));
			$this->data->html = "Product(s) successfully marked as Un-Tracked.";
		}
		/*	
		foreach ($ids as $key => $id) {
			$product = $this->Product->getProductsById($this->store_id, $id);
			if ( ! empty($product[0])) {
				if($action === 'tracking' || $action === 'skip'){
					$this->Product->markTracked($this->store_id, array($id));
					$this->data->html = "Product(s) successfully marked as Tracked.";
				}elseif($action === 'untracking' || $action === 'unskip'){
					$this->Product->markUnTracked($this->store_id, array($id));
					$this->data->html = "Product(s) successfully marked as Un-Tracked.";
				}
			}
		}*/

		$this->data->result = "success";
		$this->data->div_class = 'success';
	}//product_tracking

	function csvExport(){
		$buffer = $_POST['csvBuffer'];
		$fileName = $_POST['fileName'];
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".$fileName.".xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		try
		{
			echo $buffer;
		} catch (Exception $e){

		}
		exit();
	}

	function pdfExport(){
		$buffer = $_POST['csvBuffer'];
		$fileName = $_POST['fileName'];
		try
		{
			$this->load->helper('pdf');
			tcpdf_write($html, $fileName, tcpdf_options('catalog'));
		} catch (Exception $e){

		}
		exit();
	}

	function getEmail(){
		$this->db->where('id', $this->user_id);
		$resultEmail = $this->db->get($this->_table_users);
		if ($resultEmail->num_rows() > 0){
			$dataEmail = $resultEmail->result();
			return $dataEmail[0]->email;
		}else{
			return false;
		}
	}

	function get_all_catalog_items()
	{
		$this->_response_type('json');
		
		$request_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		$parse_url_array = parse_url($request_url);
		
		parse_str($parse_url_array['query']);
        
		$mer_s_id = $store_id;
		$examp = $q; //query number
		$archived = (isset($is_archived) && !empty($is_archived)) ? $is_archived : 0;
		if(!$archived) $archived = 0;

		if (isset($gID) && $gID != ''){
			$group_id = trim($gID);
			$this->db->where('group_id', $group_id);
			$groupData = $this->db->get($this->_table_group_products)->result();
			$ids = array();
			foreach ($groupData as $groupD){
				$ids[] = $groupD->product_id;
			}

			$IDS = "'".implode("','", $ids)."'";
			$this->db->where('id IN ('.$IDS.')');
		}
		
		//var_dump($keyword); exit();

		if (isset($keyword)){
			$kywd = $keyword;
			$kywd = trim($kywd);
			if (strlen($kywd)>0){
				//$kywd = str_replace(array('-',' ',',','_'), ' ', $kywd);
				$keyword = mysql_real_escape_string($kywd);
				$this->db->select("*, MATCH(`title`,`sku`,`search`) AGAINST('{$kywd}') as score", false);
				$this->db->where("MATCH(`title`,`sku`,`search`) AGAINST('{$kywd}')");
				$this->db->order_by("score", "desc");
				$this->db->limit('10');
			}
		}

		if($archived == 0) $this->db->where('is_archived', $archived);

		$result = $this->db
			->where_in('store_id', getStoreIdList($mer_s_id))
			->where('deleted_at', NULL)
            ->or_where('deleted_at', '0000-00-00 00:00:00')
			->get($this->_table_products)
			->result('array');
//echo $this->db->last_query(); exit;
        $data = array();
		for ($i = 0, $n = count($result); $i < $n; $i++){
			$row = array();
			
			/*if($result[$i]['is_processed'] == 1){
				$img = "<img src=\"".base_url()."images/icons/dot-red.png\" style=\"margin-top: 2px;\" alt=\"Skip\"/>";
			}else*/{
				if ($result[$i]['is_tracked'] == '1'){
					$img = "<img src=\"".base_url()."images/icons/checkmark.png\" style=\"cursor:pointer;margin-top: 2px;\" alt=\"Tracked\" title=\"Click for Not Track\" />";
				}else{
					$img = "<img src=\"".base_url()."images/icons/dot-yellow.png\" style=\"cursor:pointer;margin-top: 2px\" alt=\"Not Tracked\" title=\"Click for Track\" />";
				}
			}

			$row['check'] = 0;
			$row['upc_code'] = $result[$i]['upc_code'];
			$row['title'] = html_entity_decode($result[$i]['title']);
			$row['retail_price'] = $result[$i]['retail_price'];
			$row['price_floor'] = $result[$i]['price_floor'] > 0 ? $result[$i]['price_floor'] : '';
			$row['wholesale_price'] = $result[$i]['wholesale_price'];
            if (strlen($result[$i]['sku']) > 0) {
                $row['sku'] = $result[$i]['sku'];
            } else {
                $row['sku'] = '';
            }
			$row['is_tracked'] = ''.$img;
			$row['is_archived'] = $result[$i]['is_archived'];
			$row['id'] = $result[$i]['id'];
			$row['store_id'] = $result[$i]['store_id'];

			// Get Pricing
			foreach (array('retail_price','price_floor','wholesale_price') as $pricing_type) {
				$this->db->where('product_id', $result[$i]['id'])
				         ->where('pricing_type', $pricing_type)
				         ->order_by('pricing_start', 'desc')
				         ->limit(1);
				$pricing_result = $this->db->get('products_pricing');
				if ($pricing_result->num_rows() == 1) {
					$row_array = $pricing_result->row_array();
					$row[$pricing_type] = $row_array['pricing_value'];
				}
			}
			$data[$i] = $row;
		}
		$this->data = array('data' => $data);
	}//get_all_catalog_items

	function get_all_competitor_items()
	{
		$this->_response_type('json');

		$request_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		$parse_url_array = parse_url($request_url);
		
		parse_str($parse_url_array['query']);
		
		$keyword = false;
		
		if (isset($keyword)){
			if (trim($keyword)){
				$keyword = $keyword;
			}
		}

		// Store the competitor's product info in an array
		$competitorProducts = $this->Store->getBrandCompetitorProducts($this->store_id, $keyword);

		$data = array();
		for ($i = 0, $n = count($competitorProducts); $i < $n; $i++){
			$row = array();
			$row['check'] = 0;
			$row['upc_code'] = $competitorProducts[$i]['upc_code'];
			$row['title'] = html_entity_decode(trim($competitorProducts[$i]['title']));
			$row['owner'] = $this->Store->get_store_name($competitorProducts[$i]['store_id']);
			$row['id'] = $competitorProducts[$i]['id'];
			$row['associated_product'] = $competitorProducts[$i]['competing_product_title'];
			$row['associated_product_id'] = $competitorProducts[$i]['competing_product_id'];
			$data[$i] = $row;
		}

		$this->data = array('data' => $data);
	}//get_all_competitor_items

	function get_all_promotional_pricing($type){
		$this->_response_type('json');
		
		$request_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		$parse_url_array = parse_url($request_url);
		
		parse_str($parse_url_array['query']);

		$keyword = NULL;
		if (isset($keyword)){
			if (trim($keyword)){
				$keyword = $keyword;
			}
		}

		$promotional_pricing = $this->Store->getBrandPromotionalPricing($this->store_id, $type, $keyword);

		$data = array();
		for ($i = 0, $n = count($promotional_pricing); $i < $n; $i++){
			$pp = $promotional_pricing[$i];
			$row = array();
			$row['check'] = 0;
			$row['title'] = html_entity_decode(trim($pp['title']));
			$row['id'] = $pp['pricing_id'];
			$row['product_id'] = $pp['product_id'];
			$row[$type] = $pp['pricing_value'];
			$row['start_date'] = $pp['pricing_start'];
			$row['end_date'] = $pp['pricing_end'];
			$data[] = $row;
		}

		$this->data = array('data' => $data);
	}

	function get_all_lookup_items()
	{
		$this->_response_type('json');
		
		$request_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		$parse_url_array = parse_url($request_url);
		
		parse_str($parse_url_array['query']);
		
		if (empty($marketplace_id)){
			$this->data = array(
							'data' => array(
								'title'=>'Select a retailer'
							));
			return;
		}

		$kywd = null;
		if (isset($keyword)) {
			$kywd = $keyword;
			$kywd = trim($kywd);
			if (strlen($kywd)>0){
				$kywd = str_replace(array('-',' ', ',', '_'), ' ', $kywd);
			}
		}
		
		$query = "SELECT * FROM {$this->_table_products} p \n";
		if($kywd) {
			$query .= ", MATCH(title,sku,search) AGAINST('{$kywd}') as score\n";
		}
		$query .= "LEFT JOIN {$this->_table_products_lookup} pl ON p.id=pl.product_id AND pl.marketplace_id={$marketplace_id}\n";
		$query .= "WHERE p.store_id IN (" . getStoreIdList($this->store_id, TRUE) . ") \n";
		$query .= "AND p.deleted_at IS NULL \n";

		if (empty($is_archived))
			$query .= "AND is_archived = 0 \n";

		if($kywd) {
			$query .= "AND MATCH(title,sku,search) AGAINST('{$kywd}')\n";
			$query .= "ORDER BY score desc";
		}
		else{
			$query .= "ORDER BY title";
		}
		
		$result = $this->db
			->query($query)
			->result_array();
		//echo $this->db->last_query(); exit;
		
		$data = array();
		for ($i = 0, $n = count($result); $i < $n; $i++){
			$row = array();
			$row['check'] = 0;
			$row['upc_code'] = $result[$i]['upc_code'];
			$row['sku'] = $result[$i]['sku'];
			$row['title'] = html_entity_decode($result[$i]['title']);
			$row['is_archived'] = $result[$i]['is_archived'];
			$row['id'] = $result[$i]['id'];
			$row['url'] = !empty($result[$i]['url']) ? $result[$i]['url'] : '';

			$data[$i] = $row;
		}

		$this->data = array('data' => $data);
	}

	function Strip($value){
		if (get_magic_quotes_gpc() != 0){
			if (is_array($value))
				if (array_is_associative($value)){
					foreach ($value as $k => $v)
						$tmp_val[$k] = stripslashes($v);
					$value = $tmp_val;
				}
			else
				for ($j = 0; $j < sizeof($value); $j++)
				$value[$j] = stripslashes($value[$j]);
			else
				$value = stripslashes($value);
		}
		return $value;
	}

	function array_is_associative($array){
		if (is_array($array) && !empty($array)){
			for ($iterator = count($array) - 1; $iterator; $iterator--){
				if (!array_key_exists($iterator, $array)){
					return true;
				}
			}
			return!array_key_exists(0, $array);
		}
		return false;
	}

	function items_rules(){
		$this->_response_type('json');

		$item_id = array();
		$ceiling_price = array();
		$floor_price = array();
		$normal_increment_up_amount = array();
		$amazon_increment_up_amount = array();
		$normal_increment_up_percentage = array();
		$amazon_increment_up_percentage = array();
		$normal_increment_down_percentage = array();
		$amazon_increment_down_percentage = array();
		$normal_increment_down_amount = array();
		$amazon_increment_down_amount = array();
		$special_increment_down_amount = array();
		$item_id = $this->input->post("item_id");
		$floor_price = $this->input->post("floor_price");
		$ceiling_price = $this->input->post("ceiling_price");
		$normal_increment_up_amount = $this->input->post("normal_increment_up_amount");
		$amazon_increment_up_amount = $this->input->post("amazon_increment_up_amount");
		$normal_increment_up_percentage = $this->input->post("normal_increment_up_percentage");
		$amazon_increment_up_percentage = $this->input->post("amazon_increment_up_percentage");
		$normal_increment_down_amount = $this->input->post("normal_increment_down_amount");
		$amazon_increment_down_amount = $this->input->post("amazon_increment_down_amount");
		$special_increment_down_amount = $this->input->post("special_increment_down_amount");
		$normal_increment_down_percentage = $this->input->post("normal_increment_down_percentage");
		$amazon_increment_down_percentage = $this->input->post("amazon_increment_down_percentage");
		for ($i = 0; $i < count($item_id); $i++){
			$save_info = array();
			$save_info['floor_price'] = $floor_price[$i];
			$save_info['ceiling_price'] = $ceiling_price[$i];
			$save_info['normal_increment_up_percentage'] = $normal_increment_up_percentage[$i];
			$save_info['amazon_increment_up_percentage'] = $amazon_increment_up_percentage[$i];
			$save_info['normal_increment_down_percentage'] = $normal_increment_down_percentage[$i];
			$save_info['amazon_increment_down_percentage'] = $amazon_increment_down_percentage[$i];
			$save_info['normal_increment_up_amount'] = $normal_increment_up_amount[$i];
			$save_info['amazon_increment_up_amount'] = $amazon_increment_up_amount[$i];
			$save_info['normal_increment_down_amount'] = $normal_increment_down_amount[$i];
			$save_info['amazon_increment_down_amount'] = $amazon_increment_down_amount[$i];
			$save_info['special_increment_down_amount'] = $special_increment_down_amount[$i];
			$save_info['id'] = $item_id[$i];
			$save_info['is_excluded'] = 0;
			$this->Product->update_item($save_info);
		}
		$this->data->html = "Product settings successfully updated.";
		$this->data->div_class = 'success';
	}

	function edit_prod(){
		$this->_response_type('json');

		if ($this->Product->update($_POST['id'], $_POST)){
			$this->data->html = "Item successfully updated.";
			$this->data->div_class = "success";
		}else{
			$this->data->html = "Unable to update item.";
			$this->data->div_class = "error";
		}
	}

	function manageColumns(){
		$this->_response_type('json');

		$mer_s_id = $this->session->userdata('store_id');
		$seller_ids = $this->input->post('columns');
		$type = $this->input->post('type');
		if ($type == 'add_column'){
			$this->db->select_max('sort');
			$this->db->where('user_store_id', $mer_s_id);
			$max = $this->db->get($this->_table_brand_columns)->result_array();

			$insertArray = array('column_id' => $seller_ids, 'user_store_id' => $mer_s_id, 'sort' => (int)$max[0]['sort']+1);
			$this->db->insert($this->_table_brand_columns, $insertArray);
		}else{
			$this->db->where('user_store_id', $mer_s_id);
			$this->db->where('column_id', $seller_ids);
			$this->db->delete($this->_table_brand_columns);
		}
		$this->data->success = 'success';
	}//end manageColumns

	function update_column_sort(){
		$this->_response_type('json');

		$this->data = array('status' => false);

		$columns = $this->input->post('cols');
		if ( ! empty($columns)) {
			$this->Store->update_columns_order($columns);
			$this->data = array('status' => true);
		}
	}

	/**
	 * @deprecated Use Prod_management::update_catalog_cell()
	 */
	function saveCellEdit(){
		$field = $this->input->post('field');
		$val = $this->input->post('newVal');
		$id = $this->input->post('id');
		$oldVal  = str_replace("$", '', $this->input->post('oldVal'));
		$error = 0;
		$msg = '';
		$is_price = false;
		if ($field == 'title' || $field == 'sku' || $field == 'upc_code'){
			if ($val == ''){

				$error = 1;
				$msg = "Incorrect value given for FieldName: ".$this->makeFieldNameText($field)." must be of String type without special characters.";
			}else{
				if ($field == 'upc_code'){
					$val = preg_replace('/[^0-9]/', '', $val);
				}elseif($field == 'sku'){
						$val = preg_replace('/[^0-9 a-zA-Z]/', '', $val);
					}
			}
		}else{
			if ($val == ''){
				$error = 1;
			}else{
				$val = str_replace("$", '', $val);
				if (!is_numeric($val)){
					$error = 1;
					$msg = "Incorrect value given for FieldName: ".$this->makeFieldNameText($field)." must be of Numeric type.";
				}else{
					$val = number_format($val, 2);
				}
			}
			$is_price = true;
		}

		$allowFields = array('retail_price','price_floor','wholesale_price');
		if (!$error){
			$this->db->where('id', $id);
			$dataArra[$field] = $val;
			$this->db->update($this->_table_products, $dataArra);
			if($val != $oldVal && in_array($field,$allowFields)){
				$start_date = date('Y-m-d H:i:s');

				$qStr = "Insert into
                    ".$this->_table_products_pricing."
                    set
                        product_id = '$id',
                        pricing_type = '$field',
                        pricing_value='$val',
                        pricing_start = '$start_date'
                    ";
				$this->db->query($qStr);
			}
			echo json_encode(array('Status' => 1, 'Message' => '','value'=>$val));
		}else{
			echo json_encode(array('Status' => 0, 'Message' => $msg));
		}

		exit;
	}

	function makeFieldNameText($str){
		$finalText = strtoupper($str);
		$finalText = str_replace('_PRICE','',$finalText);
		$finalText = str_replace('IS_','',$finalText);
		$finalText = str_replace('PRICE_','',$finalText);
		$finalText = str_replace('FLOOR','MAP',$finalText);
		if($finalText != 'MAP'){
			$finalText = ucfirst(strtolower($finalText));
		}

		return $finalText;
	}

	function getMaxID(){

		$SQL = "SELECT * FROM ".$this->_table_products." WHERE store_id=".$this->store_id;
		$result = $this->db->query($SQL)->result('array');
		$newRow = array('title' => '', 'upc_code' => '', 'store_id' => $this->store_id, 'sku' => '', 'is_tracked' => 1);

		$insertData = $this->db->insert($this->_table_products, $newRow);
		$ID = $this->db->insert_id();
		$img = "<img src='".base_url()."images/green_circle.png' style='cursor:pointer;margin-top: 2px;' alt='Tracked' onclick=\"tracked(this,'".(count($result) + 1)."')\" />";

		$row['check'] = 0;
		$row['upc_code'] = '';
		$row['title'] = '';
		$row['retail_price'] = '';
		$row['price_floor'] = '';
		$row['wholesale_price'] = '';
		$row['sku'] = '';
		$row['is_tracked'] = $img;

		echo json_encode(array('success' => 1, 'ID' => $ID, 'data' => $row));
		exit;
	}

	function saveNewProduct(){
		$ret = array(
			'success' => 1,
			'new_record' => 0
		);
		$response = array();

		$data = array();
		$data['store_id'] = $this->store_id;
		$data['title'] = $this->input->post('product_title');
		$data['upc_code'] = $this->input->post('product_upc');
                $data['sku'] = $this->input->post('product_sku');
                $data['retail_price'] = $this->input->post('product_retail');
                $data['price_floor'] = $this->input->post('product_map');
                $data['wholesale_price'] = $this->input->post('product_wholesale');
		$data['brand'] = $this->Store->get_brand_by_store($this->store_id);
                $data['is_tracked'] = 1;
                $data['status'] = 1;
		$data = array_map('trim', $data);
                
		// get product if already exists
		$product = $this->db
		->where('store_id', $this->store_id)
		->where('upc_code', $data['upc_code'])
                ->where('sku', $data['sku'])        
		->get($this->_table_products)
		->row();

		// update
		if ($product && property_exists($product, 'id')) {
			$this->db
			->where('id', $product->id)
			->update($this->_table_products, $data);
			$product_id = $product->id;
                        $iT = time();
                        if(isset($data['retail_price'])){
                            $this->db->where('product_id',$product_id);
                            $this->db->where('pricing_type','retail_price');
                            $this->db->update($this->_table_products_pricing, array('pricing_value' => $data['retail_price'],'pricing_start' => date('Y-m-d H:i:s', $iT)));
                        }

                        if(isset($data['wholesale_price'])){
                            $this->db->where('product_id',$product_id);
                            $this->db->where('pricing_type','wholesale_price');
                            $this->db->update($this->_table_products_pricing, array('pricing_value' => $data['wholesale_price'],'pricing_start' => date('Y-m-d H:i:s', $iT)));
                        }

                        if(isset($data['price_floor'])){
                            $this->db->where('product_id',$product_id);
                            $this->db->where('pricing_type','price_floor');
                            $this->db->update($this->_table_products_pricing, array('pricing_value' => $data['price_floor'],'pricing_start' => date('Y-m-d H:i:s', $iT)));
                        }
                        
		} else {
			$data['created_at'] = date('Y-m-d H:i:s');
			$ret['new_record'] = $this->db->insert($this->_table_products, $data) ? 1 : 0;
			$product_id = $this->db->insert_id();
                        $iT = time();
                        if(isset($data['retail_price'])){
                            $this->db->insert($this->_table_products_pricing, array('product_id' => $product_id,'pricing_type' => 'retail_price','pricing_value' => $data['retail_price'],'pricing_start' => date('Y-m-d H:i:s', $iT)));
                        }

                        if(isset($data['wholesale_price'])){
                            $this->db->insert($this->_table_products_pricing, array('product_id' => $product_id,'pricing_type' => 'wholesale_price','pricing_value' => $data['wholesale_price'],'pricing_start' => date('Y-m-d H:i:s', $iT)));
                        }

                        if(isset($data['price_floor'])){
                            $this->db->insert($this->_table_products_pricing, array('product_id' => $product_id,'pricing_type' => 'price_floor','pricing_value' => $data['price_floor'],'pricing_start' => date('Y-m-d H:i:s', $iT)));
                        }
		}

		// get count
		$products_count = $this->db
		->select('count(*) as cnt')
		->where('store_id', $this->store_id)
		->get($this->_table_products)
		->row();

		// response
		if (isset($product_id)) {
			$ret['id'] = $product_id;

			$response['check'] = 0;
			$response['upc_code'] = $data['upc_code'];
			$response['title'] = $data['title'];
			$response['retail_price'] = number_format($data['retail_price'],2);
			$response['price_floor'] = number_format($data['price_floor'],2);
			$response['wholesale_price'] = number_format($data['wholesale_price'],2);
			$response['sku'] = $data['sku'];
			$response['is_tracked'] = "<img src='".base_url()."images/icons/checkmark.png' style='cursor:pointer;margin-top: 2px;' alt='Tracked' />"; //onclick=\"tracked(this,'".$products_count->cnt."')\"
			$response['is_archived'] = 0;
		}

		$ret['data'] = $response;

		exit(json_encode($ret));
	}//end saveNewProduct

	function product_archiving(){
		$this->_response_type('json');

		$ids = $this->input->post('ids');
		$action = $this->input->post('action');

		if((int)$action === 1){
			$this->Product->markArchived($this->store_id, $ids);
			$this->data->html = "Product(s) successfully Archived.";
		}else{
			$this->data->html = "Product(s) successfully Un-Archived.";
			$this->Product->markUnArchived($this->store_id, $ids);
		}

		$this->data->result = "success";
		$this->data->div_class = 'success';
	}//end product_archiving

	function delete_products(){
		$this->_response_type('json');

		$ids = $this->input->post('ids');

		if($ids){
			$this->Product->markDeleted($this->store_id, $ids);
			$this->data->html = 'Product(s) successfully Deleted.';

			// save deleted products for reversal
			$row_id = array();
			foreach ($ids as $id){
				$product = $this->Product->getProductsById($this->store_id, $id);
				if ( ! empty($product[0]['store_id'])) {
					$row = array(
						'store_id' => $product[0]['store_id'],
						'products_id' => $id
					);
					$insert_id = $this->ProductsDeleted->insert($row);
					if ($insert_id)
						$row_id[$insert_id] = $product[0];
				}
			}

			// get team members
			$user_email = $this->session->userdata("user_email");
			$owner_email = $this->session->userdata('owner_email');
			$members = $this->User->get_team_members($store_id, 2);

			$members[] = array('email' => $user_email);

			$htmlBody = $textBody = '';

			$hd = 'The following products were deleted by '.$this->session->userdata("user_name");
			$htmlBody .= $hd.'<br/><br/><ul>';
			$textBody .= $hd."\r\n\r\n";

			// get products data
			//$products = $this->Product->getProductsById($this->store_id, $ids);
			foreach($row_id as $insert_id => $product){
				$unDelete = base_url().'catalog/undelete_products/'.base64_encode($insert_id);
				$htmlBody .= '<li>'.$product['title'].' (<a href="' . $unDelete . '">undelete</a>)</li>';
				$textBody .= '• '.$product['title'].' Follow this link to un-delete this product: ' . $unDelete . "\r\n";
			}

			foreach($members as $member){
				send_email($member['email'], 'support@juststicky.com', 'TrackStreet Product Removal', $htmlBody, $textBody);
			}

			$this->data->result = "success";
			$this->data->div_class = 'success';
		}
	}//end delete_products

	function delete_group_products(){
		$this->_response_type('json');

		$ids = explode(',', $_POST['ids']);
		$group_id = $_POST['groupID'];

		$this->data->result = "failure";
		if($ids){
			$this->data->html = "Product(s) successfully removed from group.";
			$this->Product->removeFromGroup($group_id, $ids);

			// get team members
			$members = $this->User->get_team_members($this->store_id, 2);
			$members[] = array('email' => $this->session->userdata("user_email"));

			$this->data->result = "success";
			$this->data->div_class = 'success';
		}
	}//end remove_group_products

	function undelete_products($id){
		$id = base64_decode($id);
		if(trim($id)){
			$row = $this->ProductsDeleted->get($id);
			if($row){
				$this->Product->update_many(explode(',', $row->products_id), array('deleted_at' => NULL));
				$this->ProductsDeleted->delete($id);
			}
		}

		$this->session->set_flashdata("success", 'Products(s) retained successfully.');
		redirect(base_url().'catalog/');
	}//end undelete_products

	function add_competition($productId){
		$this->_response_type('json');

		$ret = FALSE;
		$message = '';
		if( ! empty($productId)){
			$product = $this->Product->getProductsById(FALSE, $productId);

			// Make sure the product is not owned by the user
			if (isset($product[0])) {
				if ($product[0]['store_id'] == $this->store_id) $message = 'The product "'.$product[0]['upc_code'].' - '.$product[0]['title'].'" cannot be added to the competitor\'s list because you are the owner.';
			}else{
				$message = 'Product not found.';
			}

			// Check if this product is already tied to the brand
			if (isset($product[0]) AND empty($message)) {
				$upc = $product[0]['upc_code'];
				$brand_products = $this->Store->getBrandCompetitorUPCs($this->store_id);
				if (isset($brand_products[$upc])) $message = 'You have already added this product.';
			}

			// Add to the brand_products table
			if (empty($message)) {
				if ($this->Product->insertBrandProduct($this->store_id, $productId))
					$ret = TRUE;
				else
					$message = 'Product could not be added.';
			}
		}

		// get count
		$products_count = $this->db
		->select('count(*) as cnt')
		->where('store_id', $this->store_id)
		->get($this->_table_brand_product)
		->row();

		$brand = $this->Store->get_store_name($product[0]['store_id']);

		$data = array(
			'check' => 0,
			'upc_code' => $product[0]['upc_code'],
			'title' => $product[0]['title'],
			'owner' => $brand
		);

		$this->data = array(
			'success' => $ret,
			'message' => $message,
			'id' => $productId,
			'data' => $data
		);
	}//end add_competition

	function remove_competition(){
		$this->_response_type('json');

		$delete = false;
		$ids = (isset($_POST['ids'])) ? $_POST['ids'] : array();

		for($i=0, $n=sizeof($ids); $i<$n; $i++)
			$delete = $this->Product->removeBrandProduct($this->store_id, $ids[$i]);

		$message = ( ! $delete) ? "Product was NOT removed": "Product successfully removed";

		$this->data = array(
			'success' => $delete,
			'message' => $message,
			'id' => $ids,
			'data' => ''
		);
	}//end remove_competition

	function associate(){
		$this->_response_type('json');

		$compProdId = (isset($_POST['competitor_prod_id'])) ? $_POST['competitor_prod_id'] : NULL;
		$myProdId = (isset($_POST['product_id'])) ? $_POST['product_id'] : NULL;

        $success = false;
		$assoc = $this->Product->getProductsById(FALSE, $compProdId);
		$myprod = $this->Product->getProductsById($this->store_id, $myProdId);

        if (!isset($assoc[0])){
			$message = 'The competitor product cannot found.';
		}elseif($compProdId && $myProdId && $this->store_id > 0) {
			// Make sure the associated product is not owned by the user
			if($assoc[0]['store_id'] == $this->store_id){
				$message = 'The product "'.$assoc[0]['upc_code'].' - '.$assoc[0]['title'].'" cannot be associated with this product because you are the owner.';
			}
			// Make sure the product is owned by the user
			$product = $this->Product->getProductsById(FALSE, $compProdId);
			if($product[0]['store_id'] != $this->store_id){
				$message = 'The product "'.$assoc[0]['upc_code'].' - '.$assoc[0]['title'].'" cannot be associated with this product because you are not the owner of "'.$assoc[0]['upc_code'].' - '.$assoc[0]['title'].'".';
			}

			// Check if these products are already associated
			$compEx = $this->Store->getAssociatedProduct($this->store_id, $compProdId);
			if (isset($compEx[0])){
				$message = 'These products are already associated.';
			}elseif($this->Product->insertBrandProductAssociation($myProdId, $assoc[0]['id'])){
				$success = TRUE;
				$message = 'Product successfully associated.';
			}else{
				$message = 'Product could not be associated.';
			}
		}else{
			$message = 'Product could not be associated.';
		}

		$this->data = array(
			'success' => $success,
			'message' => $message,
			'id' => $compProdId,
			'data' => array(
				'uid' => $myProdId,
				'associated_product' => $myprod[0]['title']
			)
		);
	}//end associate

	function unassociate(){
		$this->_response_type('json');

		$assProds = $_POST['ids'];
		for($i=0, $n=sizeof($assProds); $i<$n; $i++)
			$this->Product->removeCompetitorAssociation($assProds[$i]['apid'], $assProds[$i]['cid']);

		$this->data = array(
			'success' => true,
			'message' => 'Product(s) successfully un-associated'
		);
	}//end unassociate

  function get_products_names(){
		$this->_response_type('json');
		$this->data = array();

		$queryString = $this->input->post('term');
		$isComp = $this->input->post('comp');
		$isComp = ($isComp == 'true') ? true : false;

    if (strlen($queryString) > 0) {
			//exclude any of our own brand UPC's
			if($isComp){
				$this->db->where('p.store_id !=', $this->store_id);
				$this->db->where('length(upc_code) >= 12');
			}else{
				$this->db->where('p.store_id =', $this->store_id);
			}

			$result = $this->db
	          //->select('p.id as Id, p.title')
	          ->like('p.title', $queryString)
	          ->where('p.is_tracked', 1)
	          ->group_by('p.upc_code')
	          ->get($this->_table_products . ' p');

		  $num_rows = $result->num_rows();
      if ($num_rows > 0) {
			  $result = $result->result_array();
			  for ($i = 0; $i < $num_rows; $i++) {
				  $result[$i]['title'] = html_entity_decode($result[$i]['title']);
			  }
		  	$this->data = $result;
      }
    }
  }//end get_products_names

	function get_product_upcs() {
		$this->_response_type('json');
		$this->data = array();

		$queryString = $this->input->post('term');
		$store_id = $this->input->post('store_id');
		$isComp = $this->input->post('comp') === 'true' ? TRUE : FALSE;

		if (strlen($queryString) > 0) {
			//exclude any of our own brand UPC's
			if($isComp){
				$this->db->where('p.store_id !=', (int)$this->store_id);
			}elseif (!empty($store_id)){
				//specific brand search
				$this->db->where('p.store_id', (int)$store_id);
			}else{
				$this->db->where('p.store_id =', (int)$this->store_id);
			}

			$result = $this->db
				->like('upc_code', $queryString)
				->where('length(upc_code) >= 12')
				->group_by('upc_code')
				->limit(10)
				->get('products p');

			$num_rows = $result->num_rows();
			if ($num_rows > 0) {
				$result = $result->result_array();
				for ($i = 0; $i < $num_rows; $i++) {
					$result[$i]['upc_code'] = html_entity_decode($result[$i]['upc_code']);
				}
				$this->data = $result;
			}
		}
	}//end get_product_upcs

	function savePriceDates(){
		$this->_response_type('json');

		$productId = $this->input->post('id');
		$type = $this->input->post('type');
		$value = $this->input->post('value');
		$start = $this->input->post('start');
		$end = $this->input->post('end');

		$message = '';
		if(!is_numeric($value)){
			$message = 'Value entered was not numeric';
			$ins = false;
		}else{
			//insert historical record
			$data = array(
				'product_id' => $productId,
				'pricing_type' => $type,
				'pricing_value' => $value,
				'pricing_start' => $start." 00:00:00",
				'pricing_end' => $end." 00:00:00"
			);
			$ins = $this->db->insert($this->_table_products_pricing, $data) ? 1 : 0;
		}

		$this->data = array('success' => $ins, 'message' => $message);
	}//savePriceDates

	function update_catalog_cell(){
		$this->_response_type('json');

		$column = strip_tags($this->input->post('column'));
		$upc = $column === 'upc_code' ? $this->input->post('old_value') : $this->input->post('upc');
        $sku = $column === 'sku' ? $this->input->post('old_value') : $this->input->post('sku');
        
        $upc = strip_tags($upc);
        $sku = strip_tags($sku);
        
		//
		// First check that this product belongs to the user
		//

		$product = getProductIdByUPC($upc, $this->store_id);
		if ( ! $product)
			exit;


		//
		// Now we can make the update
		//

		$value = $this->input->post('value');
		$old_value = $this->input->post('old_value');

		$success = FALSE;
		switch ($column){
			case 'upc_code':
                $new_upc = $value;
                $product_id = $this->Product->get_product_id_from_detail($this->store_id,$new_upc,$sku);
                if(!$product_id) {
                    $product_id = $this->Product->get_product_id_from_detail($this->store_id,$upc,$sku);
                    $where = array(
                            'id' => $product_id
                            //$column => $old_value // too restrictive, unnecessary
                    );
                    $update = array(
                            $column => $new_upc
                    );
                    $success = $this->db->update($this->_table_products, $update, $where);
                }
                else {
                    $success = false;
                }
				break;
			case 'is_tracked':
			case 'title':
                $product_id = $this->Product->get_product_id_from_detail($this->store_id,$upc,$sku);
				$where = array(
					'id' => $product_id
					//$column => $old_value
				);
				$update = array(
					$column => $value
				);
				$success = $this->db->update($this->_table_products, $update, $where);
			case 'sku':
                $new_sku = $value;
                $product_id = $this->Product->get_product_id_from_detail($this->store_id,$upc,$new_sku);
                if(!$product_id) {
                    $product_id = $this->Product->get_product_id_from_detail($this->store_id,$upc,$sku);
                    $where = array(
                            'id' => $product_id
                            //$column => $old_value
                    );
                    $update = array(
                            $column => $new_sku
                    );
                    $success = $this->db->update($this->_table_products, $update, $where);
                }
                else {
                    $success = false;
                }
				break;
			case 'wholesale_price':
                $product_id = $this->Product->get_product_id_from_detail($this->store_id,$upc,$sku);
				$where = array(
					'id' => $product_id
					//$column => $old_value
				);
				$update = array(
					$column => $value
				);
				$success = $this->db->update($this->_table_products, $update, $where);
                $this->db->where('product_id',$product_id);
                $this->db->where('pricing_type',$column);
                $success = $this->db->update($this->_table_products_pricing, array('pricing_value' => (float)$value,'pricing_start' => date('Y-m-d H:i:s')));
				break;
			case 'retail_price':
                $product_id = $this->Product->get_product_id_from_detail($this->store_id,$upc,$sku);
				$where = array(
					'id' => $product_id
					//$column => $old_value
				);
				$update = array(
					$column => $value
				);
				$success = $this->db->update($this->_table_products, $update, $where);
                $this->db->where('product_id',$product_id);
                $this->db->where('pricing_type',$column);
                $success = $this->db->update($this->_table_products_pricing, array('pricing_value' => (float)$value,'pricing_start' => date('Y-m-d H:i:s')));
			case 'price_floor':
				$product_id = $this->Product->get_product_id_from_detail($this->store_id,$upc,$sku);
				$where = array(
					'id' => $product_id
					//$column => $old_value
				);
				$update = array(
					$column => $value
				);
				$success = $this->db->update($this->_table_products, $update, $where);
				//$error = $this->db->last_query();
                $this->db->where('product_id',$product_id);
                $this->db->where('pricing_type',$column);
                $success = $this->db->update($this->_table_products_pricing, array('pricing_value' => (float)$value,'pricing_start' => date('Y-m-d H:i:s')));
				break;
		}

		if ($success) { // record the change in the history table
			$this->Product->insertHistory($product_id, $column, $old_value, $value);
            echo json_encode(array('error'=>0));
            //echo json_encode(array('error'=>$error));
		}
        else {
            echo json_encode(array('error'=>1));
        }
		exit;
	}

	function update_promotional_pricing(){
		$this->_response_type('json');

		$valid_types = array(
			'wholesale_price' => TRUE,
			'retail_price' => TRUE,
			'price_floor' => TRUE
		);

		$this->data->status = FALSE;

		$id = $this->input->post('id');
		$product_id = $this->input->post('product_id');
		$type = $this->input->post('pricing_type');

		//
		// Validate the pricing type
		//

		if ( ! isset($valid_types[$type]))
			exit;

		//
		// Check that this promotional pricing belongs to the user
		//

		if ($id AND ! $this->Product->isOwnerProductsPricing($id, $this->store_id))
			exit;

		//
		// Verify the dates
		//

		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');

		$start_ts = strtotime($start_date);
		$end_ts = strtotime($end_date);

		if ( ! $start_ts) {
			$this->data->column = 'start_date';
			$this->data->msg = 'Start date must be a valid date.';

			return;
		}

		if ( ! $end_ts) {
			$this->data->column = 'end_date';
			$this->data->msg = 'End date must be a valid date.';

			return;
		}

		$start = date('Y-m-d H:i:s', $start_ts);
		$end = date('Y-m-d H:i:s', $end_ts);


		//
		// Everything is valid, we can insert/update the products_pricing row
		//

		$column = $this->input->post('column');
		$value = $this->input->post('value');
		switch ($column){
			case 'start_date':
			case 'end_date':
			case 'wholesale_price':
			case 'retail_price':
			case 'price_floor':
				if ($start AND $end AND $end > $start){
					$this->data->status = TRUE;
					if ( ! $id){
						$insert = array(
							'product_id' => $product_id,
							'pricing_type' => $type,
							'pricing_value' => (float)$value,
							'pricing_start' => $start,
							'pricing_end' => $end
						);
						$this->db->insert($this->_table_products_pricing, $insert);
					}
					else{
						$update = array(
							'pricing_type' => $type,
							'pricing_value' => (float)$value,
							'pricing_start' => $start,
							'pricing_end' => $end
						);
						$this->db->where('pricing_id', $id);
						$this->db->update($this->_table_products_pricing, $update);
					}
				}
				break;
		}
	}

	public function delete_promotional_pricing() {
		$this->_response_type('json');

		$valid_types = array(
			'wholesale_price' => TRUE,
			'retail_price' => TRUE,
			'price_floor' => TRUE
		);

		$this->data->status = FALSE;

		$ids = $this->input->post('ids');
		$type = $this->input->post('pricing_type');

		//
		// Validate the pricing type
		//

		if ( ! isset($valid_types[$type]))
			exit;

		//
		// Check that this promotional pricing belongs to the user
		//

		for ($i = 0, $n = count($ids); $i < $n; $i++) {
			if ( ! $this->Product->isOwnerProductsPricing($ids[$i], $this->store_id))
				unset($ids[$i]);
		}

		if (empty($ids)) {
			$this->data->msg = 'Please select the promotional pricing periods to delete.';
			exit;
		}


		//
		// Everything is valid, we can remove the product pricing rows
		//

		$this->data->status = $this->db
			->where_in('pricing_id', $ids)
			->delete($this->_table_products_pricing);

		$this->data->msg = $this->data->status ? 'Promotional pricing entries deleted successfully.' : 'Promotional pricing entries could not be deleted.';
	}

    /**
     * Handle AJAX inline change to product lookup table.
     * 
     * Reviewed by Christophe on 10/30/2015.
     * 
     * @author unknown
     */
    public function update_product_lookup() 
    {
        $this->load->model('products_m');
        
        $this->_response_type('json');
        
        $upc = $this->input->post('upc');
        
        $this->data->status = FALSE;
        
        // First check that this product belongs to the user
        $product = getProductIdByUPC($upc, $this->store_id);
        
        if (!$product)
        {
            ajax_return($this->data);
        }
        
        // Now we can make the update
        $column = $this->input->post('column');
        $value = trim($this->input->post('value'));
        
        switch ($column)
        {
            case 'url':
                
                // if URL value is blank, check to see if we have an existing row and delete
                if ($value == '')
                {
                    $current_url = $this->input->post('old_value');
                    
                    $existing_lookup_row = $this->products_m->get_lookup_by_url($upc, $current_url);
                    
                    if (!empty($existing_lookup_row))
                    {
                        $this->products_m->delete_product_lookup($existing_lookup_row['id']);
                    }
                }
                else
                {
                    //$marketplace_id = 0;//$this->input->post('marketplace_id');
                    // find the marketplace by the domain name used in the URL entered with the lookup
                    $marketplace_id = $this->Marketplace->get_marketplace_id_by_url($value);
                    
                    if ($marketplace_id == FALSE)
                    {
                        log_message('error', 'update_product_lookup() - marketplace ID not found');
                        
                        $this->data->status = 'false';
                    }
                    else
                    {
                        //if (empty($value) OR filter_var($value, FILTER_VALIDATE_URL))
                        if (
                            $value != '' && !empty($value)         
                            //preg_match('%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu', $value)
                        )    
                        {
                            $product_id = $this->Product->get_product_id_from_upc($upc, $this->store_id);
                            
                            // insert new row or update existing row
                            $this->data->status = $this->Marketplace->set_product_lookup(
                            		$product_id,
                            		$marketplace_id,
                            		$value,
                            		$upc
                            );
                            
                            log_message('debug', 'set_product_lookup() query: ' . $this->db->last_query());
                        } 
                        else
                        {
                            $this->data->status = 'false';
                            
                            log_message('error', 'update_product_lookup() - invalid product page URL: ' . $value);
                        }                   
                    }
                }
                
                break;
        }
    }

    /**
     * This handles the export functionality just for a store's product catalog.
     * 
     * @author unknown
     * @param string $format
     */
    public function export_catalog($format)
    {
        $catalog_columns = $this->Store->get_columns_by_store($this->store_id);
        
        $column_order = $column_names = array();
        
        for ($i = 0, $n = count($catalog_columns); $i < $n; $i++)
        {
            $column_order[] = $catalog_columns[$i]->db_name;
            $column_names[] = $catalog_columns[$i]->display_name;
        }
        
        $csv_data = array(
            $column_names
        );
        
        // get all non-archived products
        $products = $this->Product->get_products_for_store($this->store_id);
        //$products = $this->Product->getByStore($this->store_id);
        
        for ($i = 0, $n = count($products); $i < $n; $i++)
        {
            $row = array();
            
            foreach ($column_order as $column)
            {
                switch ($column)
                {
                    case 'wholesale_price':
                        
                        $price = getPricePoint($products[$i]['upc_code'], $this->store_id, $column);
                        
                        $price = empty($price) ? '0.00' : number_format($price, 2, '.', '');
                        
                        $row[] = $price;
                        
                        break;
                    case 'retail_price':
                        
                        $price = getPricePoint($products[$i]['upc_code'], $this->store_id, $column);
                        
                        $price = empty($price) ? '0.00' : number_format($price, 2, '.', '');
                        
                        $row[] = $price;
                        
                        break;
                    case 'price_floor':
                        
                        $price = getPricePoint($products[$i]['upc_code'], $this->store_id, $column);
                    	
                        $price = empty($price) ? '0.00' : number_format($price, 2, '.', '');
                    	
                        $row[] = $price;
                    	
                        break;
                    default:
                        $row[] = isset($products[$i][$column]) ? $products[$i][$column] : '';
                }
            }
            
            $csv_data[] = $row;
        }
        
        $name = 'catalog';
        
        /*
        if ($this->store_id !== 'all')
        	$name = str_replace(' ', '_', $this->store_data['store_name']);
        */
        
        // get current store being viewed
        $store = $this->Store->get_store_by_id_array($this->store_id);
        
        $name = str_replace(' ', '_', $store['store_name']);
        
        $name .= '_' . date('Y-m-d');
        
        if ($format == 'excel') 
        {
        	createCSV($csv_data, $name);
        }   
        else 
        {
        	createPDF($csv_data, $name);
        }
        
        exit();
    }

	/**
	 * Get the current stores groups and its products
	 * and return as JSON
	 */
	public function get_product_groups() {
		$this->_response_type('json');
        
		// get group data
		$this->data->data = array();
		$groups = $this->Product->getGroups($this->store_id);
        
		if ( ! empty($groups)) {
			foreach ($groups as $group) {
				$group->label = '<span class="groupName">' . $group->name . ' [' . $group->count . '] ' . '</span>' . '<img class="deleteGroup imgIcon" src="' . frontImageUrl() . 'icons/16/69.png' . '" />';
				$group->products = $this->Product->getProductsByGroupId($group->id);
				$this->data->data[] = $group;
			}
		}
	}

	/**
	 * Get the products of a group and return as JSON
	 *
	 * @param int $group_id
	 */
	public function get_group_products($group_id) {
		$this->_response_type('json');

		// check that the user is the owner of the group
		$groupStore = $this->Product->getGroupStore($group_id);

		$this->data->data = $groupStore == $this->store_id ? $this->Product->getProductsByGroupId($group_id, TRUE) : array();

		if ( ! empty($this->data->data)) {
			for ($i = 0, $n = count($this->data->data); $i < $n; $i++) {
				$product =& $this->data->data[$i];
				$product['label'] = '<span class="productName">' . $product['title'] . '</span>' . '<img class="deleteGroupProduct imgIcon" src="' . frontImageUrl() . 'icons/16/69.png' . '" />';
			}
		}
	}
}
/* End of file catalog.php */
/* Location: ./system/application/controllers/prod_managment.php */