<?php
/**
 * @property Report_m $report
 */
class Schedule extends MY_Controller {

	function Schedule() {
		parent::__construct();

		$this->load->library('form_validation');
		// validation class has been deprecated and replaced by form validation as of CI 1.7.
		// We should use it instead.
		$this->load->library('validation');

		$this->load->model('crawl_data_m', 'crawl_data');
		$this->load->model('Users_m', 'User');
		$this->load->model('products_m', 'products');
		$this->load->model('report_m', "report");
		$this->load->model('store_m');

		$this->javascript('views/schedule.js.php');
	}
	
    /**
     * Delete a saved report from the saved and scheduled reports tables.
     * 
     * @author Christophe
     * @param int $report_id
     */
    public function delete_report($report_id)
    {
        $this->load->model('report_m');
        
        $saved_report = $this->report_m->get_save_report_by_id($report_id, TRUE);
        
        $schedule_report = $this->report_m->get_schedule($report_id);
        
        //var_dump($report); exit();
        //var_dump($schedule_report); exit();
        
        // check to see if user has access to edit this product
        if ($this->role_id != 2 || $this->store_id != intval($saved_report['store_id']))
        {
            $this->session->set_flashdata('error_msg', 'Error: Your account does not have access to this item.');
            
            redirect('/schedule');
            exit();
        }
        
        $this->report_m->delete_report($report_id);
        
        if (!empty($schedule_report))
        {
            $this->report_m->delete_schedule_report($report_id);
        }
        
        $this->session->set_flashdata('success_msg', 'Report has been successfully deleted.');
        
        redirect('/schedule');
        exit();
    }

    /**
     * Top page for schedule reports area.
     * 
     * @author unknown, Christophe
     */
    function index() 
    {    
        $reports = array();
        
        $search = '';
        
        $recurrings = array(
        		0 => "",
        		1 => "Every Day",
        		7 => "Every Week",
        		31 => "Every Month",
        		365 => "Every Year"
        );
        
        $rows = $this->report->get_report_schedule_list($search);
        
        for ($i = 0; $i < count($rows); $i++)
        {
            $wInfo = json_decode($rows[$i]->report_where, true);
            	
            $dName = '';
            
            if (isset($wInfo['groupId']))
            {
            	$group = $this->products->getGroupByID($wInfo['groupId'][0]);
            
            	$dName = $group['name'];
            }
            else if(isset($wInfo['productIds']))
            {
                for ($x = 0, $y = sizeof($wInfo['productIds']); $x < $y; $x++)
                {
                		$dName .= getProductsTitle($wInfo['productIds'][$x]).', ';
                }
            
                $dName = trim($dName, ', ');
            }
            
            $row = array();
            $row['check'] = 0;
            $row['id'] = $rows[$i]->id;
            $row['report_id'] = $rows[$i]->id;
            $row['report_name'] = $rows[$i]->report_name;
            $row['display_type'] = $this->report->set_report_type($rows[$i]->controller);
            $row['report_products_val'] = $dName;
            $row['report_where'] = $rows[$i]->report_where;
            $row['controller'] = $rows[$i]->controller;
            $row['controller_function'] = $rows[$i]->controller_function;
            $row['report_datetime'] = $rows[$i]->report_datetime;
            //$row['report_datetime'] = $rows[$i]->datetime;
            $row['report_recursive_frequency'] = $rows[$i]->report_recursive_frequency;
            $row['report_recursive_string'] = $recurrings[(int)$rows[$i]->report_recursive_frequency];
            $row['email_addresses'] = $rows[$i]->email_addresses;
            				
            $reports[$i] = $row;
        }	  

        $view_data['reports'] = $reports;
	    
        $this->load->view('schedule/index', $view_data);
    }
    
    public function edit_report($report_id)
    {
        $report_id = intval($report_id);
    }

	function save_report(){
		$this->_response_type('json');

		//information from the form
		$report_id = $this->input->post('report_id');

		$frequency = (int)$this->input->post('report_recursive_frequency');
		//$report_is_recursive = (int)$this->input->post('report_is_recursive')>0 ? true : false;
        //$report_is_recursive = ($frequency>0 && $report_is_recursive) ? true : false;
        $report_is_recursive = ($frequency>0) ? true : false;

		$stamp = date("Y-m-d H:i:s");

		//information for the db
		$rData['report_name'] = $this->input->post('report_name');
		$rData['controller'] = $this->input->post('controller');
		$rData['controller_function'] = $this->input->post_default('controller_function', 'index');
		$rData['user_id'] = $this->user_id;
		$rData['store_id'] = $this->store_id;
		$rData['datetime'] = $stamp;

		//TODO - the report_where needs some help
		//it's not all that consistent from form to form
		$rData['report_where'] = array();
		$where = $this->input->post('report_where');
		if(!empty($where)){
			json_decode(html_entity_decode($where), true);
			$rData['report_where'] = json_decode(html_entity_decode($where), true);
		}

		$productType = $this->input->post('report_products');
		//report_products_vals represents products or groups
		$products = $this->input->post_default('report_products_vals');
		if($products){
			$pKey = ($productType == 'group_report') ? 'group_id' : 'product_ids';
			$rData['report_where'][$pKey] = array($products);
		}
		if($rData['controller'] == "group_report") $rData['report_where']['group_id'] = $this->input->post('group_id');
		$rData['report_where'] = json_encode($rData['report_where']);

		if ($report_is_recursive) {
			$scheData['report_recursive_frequency'] = $this->input->post('report_recursive_frequency');
			$scheData['datetime'] = $stamp;
			$scheData['email_addresses'] = is_array($this->input->post('email_addresses')) ? join(',', array_flip(array_flip($this->input->post('email_addresses')))) : $this->input->post('email_addresses');

			$scheData['report_datetime'] = date('Y-m-d H:i:00', strtotime(str_replace('-', '/', $this->input->post('report_datetime')).' '.$this->input->post('hh').':'.$this->input->post('mm').':00'.$this->input->post('ampm')));
			/* Added for handling time if passed time is selected */
			$actualTime = time();
			while (strtotime($scheData['report_datetime']) < $actualTime) {
				$scheData['report_datetime'] = date('Y-m-d H:i:00', strtotime($scheData['report_datetime'] . ' +5 minutes'));
			}
		}

		if($report_id){
			//update exising report
			$this->report->update_report($rData, $report_id);
			$this->data->report_id = $report_id;
			if($report_is_recursive){
				$scheData['saved_reports_id'] = $this->data->report_id;
				$this->report->update_schedule_report($scheData, $report_id);
			}else{
				$this->report->delete_schedule_report($report_id);
			}
			$this->data->message = 'Report successfully updated.';
		}else{
			//insert new report
			$this->data->report_id = $report_id = $this->report->add_report($rData);
			if($report_is_recursive){
				$scheData['saved_reports_id'] = $this->data->report_id;
				$this->report->add_schedule_report($scheData);
			}
			$this->data->message = 'Report successfully added.';
		}

		$this->data->status = 'success';
	}

	public function schedule_reports_list() 
	{
		$this->_response_type('json');

		$data = array();
		
		$search = isset($_REQUEST['keyword']) ? $this->input->xss_clean($_REQUEST['keyword']): '';
      
    $recurrings = array(
        0 => "",
        1 => "Every Day",
        7 => "Every Week",
        31 => "Every Month",
        365 => "Every Year"
    );
    
		$rows = $this->report->get_report_schedule_list($search);
		
    for ($i = 0; $i < count($rows); $i++) 
    {
			$wInfo = json_decode($rows[$i]->report_where, true);
			
			$dName = '';

			if (isset($wInfo['groupId']))
			{
				$group = $this->products->getGroupByID($wInfo['groupId'][0]);
				
				$dName = $group['name'];
			}
			else if(isset($wInfo['productIds']))
			{
				for ($x = 0, $y = sizeof($wInfo['productIds']); $x < $y; $x++)
				{
					$dName .= getProductsTitle($wInfo['productIds'][$x]).', ';
				}
				
				$dName = trim($dName, ', ');
			}

			$row = array();
			$row['check'] = 0;
			$row['id'] = $rows[$i]->id;
			$row['report_id'] = $rows[$i]->id;
			$row['report_name'] = $rows[$i]->report_name;
			$row['display_type'] = $this->report->set_report_type($rows[$i]->controller);
			$row['report_products_val'] = $dName;
			$row['report_where'] = $rows[$i]->report_where;
			$row['controller'] = $rows[$i]->controller;
			$row['controller_function'] = $rows[$i]->controller_function;
			$row['report_datetime'] = $rows[$i]->report_datetime;
      //$row['report_datetime'] = $rows[$i]->datetime;
			$row['report_recursive_frequency'] = $rows[$i]->report_recursive_frequency;
      $row['report_recursive_string'] = $recurrings[(int)$rows[$i]->report_recursive_frequency];
			$row['email_addresses'] = $rows[$i]->email_addresses;
			
			$data[$i] = $row;
		}

		$this->data = array('data' => $data);
	}

	function delete_reports() {
		$this->_response_type('json');

		$ids = $this->input->post('ids');

		for ($i = 0; $i < count($ids); $i++) {
			$this->report->delete_report($ids[$i]);
		}

		$this->data = array('1');
	}

	function get_report() {
		$this->_response_type('json');

		$product_name = '';
		$group_name = '';

		$id = $this->input->post('id');
		$result = $this->db
		->where('id', (int)$id)
		->get($this->_table_saved_reports_schedule)
		->row();

		if(count($result) > 0){
			if ($result->controller == 'specific_product_report') {
				$product_data = $this->db
				->where('id', (int)$result->saved_reports_id)
				->get('products')
				->row();
				if ($product_data !== false) {
					$product_name = $product_data->title;
				}
			} elseif ($result->controller == 'group_report') {
				//TODO - group_id
				//we are going to have to adjust this to receive data from the
				$group_data = $this->db
				->where('id', (int)$result->group_id)
				->get('groups')
				->row();
				if ($group_data !== false) {
					$group_name = $group_data->name;
				}
			}
		}

		$schedule_timestamp = strtotime($result->report_datetime);

		$response['data'] = array(
			'id'                               => $result->id,
			'controller'                    => $result->controller,
			'saved_reports_id'         => $result->saved_reports_id,
			'group_name'                       => $group_name,
			'product_id'                       => $result->saved_reports_id,
			'product_name'                     => $product_name,
			'report_datetime'                  => date('m-d-Y', $schedule_timestamp),
			'hh'                               => date('h', $schedule_timestamp),
			'mm'                               => date('i', $schedule_timestamp),
			'ampm'                             => date('a', $schedule_timestamp),
			'report_recursive_frequency'       => $result->report_recursive_frequency,
			'email_addresses' => $result->email_addresses,
		);

		$this->data = $response;
	}

	function get_groups_names($queryString) {
		$this->_response_type('json');

		if (strlen($queryString) > 0) {
			$result = $this->db->select('id, name as title')
			->like('name', $queryString)
			->where('store_id', $this->store_id)
			->limit(10)
			->get('groups');

			if ($result->num_rows() > 0) {
				ajax_return($result->result_array());
			}
		}

		$this->data = array();
	}

	function get_products_names($queryString, $is_competition = FALSE) {
		$this->_response_type('json');
		$this->data = array();

		if (strlen($queryString) > 0) {
			$store_id_list = getStoreIdList($this->store_id);
			if ($is_competition) {
				$this->db->join($this->_table_brand_product.' bp', 'bp.product_id=p.id')
					->where_in('bp.store_id', $store_id_list);
			}else{
				$this->db->where_in('p.store_id', $store_id_list);
			}

			$result = $this->db
				->select('p.id, p.title')
				->like('p.title', $queryString)
				->where('p.is_tracked', 1)
				->group_by('p.upc_code')
				->limit(10)
				->get($this->_table_products.' p');

			$num_rows = $result->num_rows();
			if ($num_rows > 0) {
				$result = $result->result_array();
				for ($i = 0; $i < $num_rows; $i++) {
					$result[$i]['title'] = html_entity_decode($result[$i]['title']);
				}
				$this->data = $result;
			}
		}
	}

	function get_team_emails() {
		//TODO - this doesn't perform a partial search...
		ajax_return(array());

		if ( ! $this->input->is_ajax_request()) redirect(site_url('schedule/'));

		$data = array();
		$query = $this->input->get('name');

		if(empty($query)) ajax_return($data);

		$team = $this->User->get_team_members($this->store_id);

		foreach ($team as $member) {
			$data[] = array('value' => $member['email'],
											'label' => trim($member['name']).' ('.$member['email'].')',
											'name' => $member['name'],
											'email' => $member['email'],
											'id' => $member['id']);
		}
		ajax_return($data);
	}

	/**
	 * Add or edit a row in violator_notifications table via AJAX
	 *
	 * @param int $merchant_name_id
	 */
	function violator_notification($merchant_name_id) {
		$this->_response_type('json');

		$this->data->status = FALSE;
		$this->data->html = '';

		$cmn = $this->crawl_data->crowlMerchantByID($merchant_name_id);
		$type = 'known_seller';
		if ( ! empty($cmn->marketplace))
			if ( ! $this->Marketplace->is_retailer($cmn->marketplace))
				$type = 'unknown_seller';

		$data = array(
			'id' => NULL,
			'store_id' => $this->store_id,
			'crowl_merchant_name_id' => (int)$merchant_name_id,
			'notification_type' => $type
		);
		$fields = getTableFields($this->_table_violator_notifications, array_keys($data));
		unset($data['id']);
		$data = array_merge($data, $this->input->post_default($fields, NULL, TRUE));
		$data['active'] = empty($data['active']) ? 0 : 1;

		$edit_row = $this->store_m->get_violator_notification_by_seller($merchant_name_id, $data['store_id']);
		$crowl_merchant = $this->crawl_data->crowlMerchantByID($merchant_name_id);

		// Validate the request data
		$errors = array();
		if ( ! $crowl_merchant)
			$errors[] = 'An error occurred and the seller was not found.';
		if ( ! valid_email($data['email_to']))
			$errors[] = 'Please provide a valid email address to notify.';
		if ( ! valid_email($data['email_from']))
			$errors[] = 'Please provide a valid reply email address.';

		if ( ! empty($errors)) {
			$this->data->html = '<p>' . implode('</p><p>', $errors) . '</p>' ;
			ajax_return($this->data);
		}

		// Everything is valid, let's add the notification
		if ( ! empty($edit_row) && ! $edit_row['default']) {
			if ($this->store_m->update_violator_notification($edit_row['id'], $data)) {
				$this->data->status = TRUE;
				$this->data->html = 'The violation notification has been updated.';
			}
			else {
				$this->data->html = 'The notification could not be updated.';
			}
		}
		else {
			if ($this->store_m->create_violator_notification($data)) {
				$this->data->status = TRUE;
				$this->data->html = 'The violation notification has been created.';
			}
			else {
				$this->data->html = 'The notification could not be created.';
			}
		}
	}

	/**
	 * Get the current violator_notification row for a seller
	 *
	 * @param int $merchant_name_id
	 */
	function get_violator_notification($merchant_name_id){
		$this->_response_type('json');

		$this->data = $this->store_m->get_violator_notification_by_seller($merchant_name_id, $this->store_id);
	}
}
