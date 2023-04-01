<?php

class Enforcement extends MY_Controller 
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('form_validation');
        $this->load->library('encrypt');
        
        $this->load->model('crawl_data_m', 'Crawl_data');
        $this->load->model("store_m", 'Store');
        $this->load->model("report_m", 'Report');
        $this->load->model('account_m', "Account");
        $this->load->model('Users_m', 'User');
        $this->load->model('marketplace_m', 'Marketplace');
        $this->load->model('violator_m', 'Violator');
        
        $this->data->my = 'enforcement';
        $this->data->widget = 'mv-report';
        $this->data->displayBookmark = true;
        $this->data->smtp = array(
            'host' => '',
            'port' => '',
            'use_ssl' => '',
            'username' => '',
            'password' => ''
        );
        
        // legacy functionality? - Christophe
        if ( 
            $this->store_id == 'all' && 
            ($this->uri->segment(2) != 'select_product' && $this->uri->segment(2) != 'get_products_list') 
        ) 
        {
            redirect("enforcement/select_product");
            exit;
        }
    }
    
    /**
     * Page where customers can keep track of bad merchants who have gotten on this
     * list as a result of violations meeting criteria defined for store.
     * 
     * @author Christophe
     */
    public function do_not_sell()
    {
        $this->load->model('merchants_m');
        $this->load->model('merchant_products_m');
        
        // a merchant should only appear on 1 list - keep track here
        $already_on_a_list = array();
        
        // get current DNS list merchants
        $dns_merchants = $this->merchants_m->get_dns_merchants($this->store_id);
        
        for ($i = 0; $i < count($dns_merchants); $i++)
        {
            $merchant_id = intval($dns_merchants[$i]['merchant_id']);
            
            $merchant = $this->merchants_m->get_merchant_by_id($merchant_id);
            
            $already_on_a_list[] = $merchant_id;
            
            $merchant['profile_name'] = $this->merchants_m->get_merchant_human_name($merchant);
            
            if ($merchant['original_name'] != $merchant['marketplace'])
            {
                $merchant['marketplace_url'] = $this->merchant_products_m->get_marketplace_seller_url($merchant, $merchant['marketplace']);
            }
            
            $dns_period = $this->merchants_m->get_most_recent_dns_period($this->store_id, $merchant_id);
            
            $merchant['start_date'] = date('m/d/Y', strtotime($dns_period['start_date']));
            
            if (intval($dns_merchants[$i]['is_permanent']) == 1)
            {
                $merchant['removal_date'] = 'Permanent';
            }
            else
            {
                $merchant['removal_date'] = date('m/d/Y', strtotime($dns_period['end_date']));                
            }
                    
            $dns_merchants[$i] = array_merge($dns_merchants[$i], $merchant);
        }
        
        // get all merchant records in the violator_notifications_history table for this store
        $notif_merchants = $this->merchants_m->get_notification_history_for_store($this->store_id, $i);
           
        $level_merchant_ids = array();
        
        foreach ($notif_merchants as $notif_merchant)
        {
            $merchant_id = intval($notif_merchant['crowl_merchant_name_id']);
            
            if (!in_array($merchant_id, $already_on_a_list))
            {
                $level = intval($notif_merchant['email_level']);
                
                if ($level >= 1)
                {
                    if (isset($level_merchant_ids[$level]))
                    {
                        $level_merchant_ids[$level][] = $merchant_id;
                    }
                    else
                    {
                        $level_merchant_ids[$level] = array();
                        $level_merchant_ids[$level][] = $merchant_id;
                    }
                    
                    $already_on_a_list[] = $merchant_id;;
                }
            }
        }
        
        //var_dump($level_merchant_ids); exit();
        
        // determine # of violation levels that this store has
        $amount_of_levels = $this->merchants_m->get_notification_levels_num($this->store_id);
        
        // go through non-DNS levels highest to lowest and fill in merchant details
        $level_merchants = array();
        
        for ($i = $amount_of_levels; $i >= 1; $i--)
        {
            $merchants = array();
            
            $merchant_ids = $level_merchant_ids[$i];
            
            foreach ($merchant_ids as $merchant_id)
            {
                $merchant = $this->merchants_m->get_merchant_by_id($merchant_id);
                
                $last_violation = $this->merchants_m->get_last_violation($merchant_id, $this->store_id);
                $last_violation_date = empty($last_violation) ? 'N/A' : date('m/d/Y', strtotime($last_violation['select_date']));
                
                $merchant['last_violation_date'] = $last_violation_date;
                
                $merchant['profile_name'] = $this->merchants_m->get_merchant_human_name($merchant);
                
                if ($merchant['original_name'] != $merchant['marketplace'])
                {
                    $merchant['marketplace_url'] = $this->merchant_products_m->get_marketplace_seller_url($merchant, $merchant['marketplace']);
                }
                
                $merchants[] = $merchant;
            }
            
            // add merchants to a specific level
            $level_merchants[$i] = $merchants;            
        }
        
        //var_dump($level_merchants); exit();
        
        $this->data->store_id = $this->store_id;
        $this->data->dns_merchants = $dns_merchants;
        $this->data->level_merchants = $level_merchants;
        
        // report export variables
        $this->data->graphDataType = 'chart';
        $this->data->report_name = 'Do Not Sell List Report';
        $this->data->icon = 'ico-report';
        $this->data->widget = 'mv-report';
        $this->data->displayBookmark = true;      
        $this->data->report_type = 'dns_list';
        $this->data->report_where = array(
            'report_type' => $this->data->report_type,
        		'is_retailer' => false,
        		'report_function' => '',
        		'marketplace' => '',
        		'merchant_id' => '',
        		'time_frame' => '24'
        );
    }
    
    /**
     * Form where user can edit a DNS list entry.
     * 
     * @author Christophe
     * @param int $store_id
     * @param int $merchant_id
     */
    public function do_not_sell_edit($merchant_id)
    {
        $this->load->helper(array('form'));
        $this->load->library('form_validation');  
        $this->load->library('Trackstreet_merchants');      
        $this->load->model('merchants_m');
        
        $merchant_id = intval($merchant_id);
        
        $this->_layout = 'modal';
        
        $merchant = $this->merchants_m->get_merchant_by_id($merchant_id);
        
        $merchant['profile_name'] = $this->merchants_m->get_merchant_human_name($merchant);
        
        $dns_merchant = $this->merchants_m->get_dns_merchant($this->store_id, $merchant_id);
        
        // check to see if user has access to edit this product
        if ($this->role_id != 2)
        {
            $this->session->set_flashdata('error_msg', 'Error: Your account does not have access to this item.');
            
            redirect('/');
            exit();
        }
        
        $this->form_validation->set_rules('is_permanent', 'Temporary or Permanent', 'trim|required|xss_clean');
        $this->form_validation->set_rules('end_date', 'Removal Date', 'trim|xss_clean');       
        
        if ($this->form_validation->run() == FALSE)
        {
            // validation failed, or first load
        }
        else
        {
            //var_dump($_POST); exit();

            $dns_period = $this->merchants_m->get_most_recent_dns_period($this->store_id, $merchant_id);
            
            $end_date = $this->input->post('end_date', TRUE);
            $is_permanent = intval($this->input->post('is_permanent', TRUE));
            
            if ($end_date == '0000-00-00' || $end_date == '' || $is_permanent == 1)
            {
                // end date for permanent DNS listing
                $dns_end_date = '0000-00-00';
            }
            else
            {
                //$dns_end_date = $this->trackstreet_merchants->get_dns_end_date($this->store_id, $merchant_id);
                $dns_end_date = $end_date;
            }

            if (empty($dns_period))
            {
                $num_of_times = $this->merchants_m->get_dns_added_count($this->store_id, $merchant_id);
                
                // insert new DNS period       
                $insert_data = array(
                		'store_id' => $this->store_id,
                		'merchant_id' => $merchant_id,
                		'period_num' => $num_of_times + 1,
                		'start_date' => date('Y-m-d'),
                		'end_date' => $dns_end_date,
                		'added_by' => $this->user_id,
                		'removed_by' => 0,
                		'created' => date('Y-m-d H:i:s'),
                		'modified' => date('Y-m-d H:i:s')
                );
                
                $this->merchants_m->insert_dns_period($insert_data);
            }
            else
            {
                // update existing latest DNS period
                $update_data = array(
                    'end_date' => $dns_end_date                
                );
                
                $this->merchants_m->update_dns_period($dns_period['id'], $update_data);
            }
            
            // update DNS list merchant entry
            $update_data = array(
                'is_permanent' => $is_permanent,
                'modified' => date('Y-m-d H:i:s')                           
            );
            
            $this->merchants_m->update_dns_list_entry($dns_merchant['id'], $update_data);
            
            $this->session->set_flashdata('success_msg', 'Do Not Sell entry has been successfully saved.');
            
            redirect('/enforcement/do_not_sell_edit/' . $merchant_id);
            exit();
        }
        
        // determine when they are expected to be removed
        $dns_period = $this->merchants_m->get_most_recent_dns_period($this->store_id, $merchant_id);
        
        $removal_date = $dns_period['end_date'] == '0000-00-00' ? '' : date('Y-m-d', strtotime($dns_period['end_date']));
        
        // status drop down setting for DNS merchant
        $status_options = array(
            0 => 'Temporary',
            1 => 'Permanent'                            
        );
        
        $saved_status = intval($dns_merchant['is_permanent']);
        
        $status_dropdown = form_dropdown('is_permanent', $status_options, $saved_status, 'id="status-dropdown"');
        
        // determine notification level merchant is on, and if they are on a level
        $dns_merchant = $this->merchants_m->get_dns_merchant($this->store_id, $merchant_id);

        $notif_level_amount = $this->merchants_m->get_notification_levels_num($this->store_id);

        if (!empty($dns_merchant))
        {
            // $current_level = 'dns';
            // for some reason CodeIgniter didn't like mixed key use for dropdown options array
            // DNS list level = (# of Defined Notifications Levels) + 1
            $current_level = $notif_level_amount + 1;
        }
        else
        {
            // check to see if they are on a numeric violation level
            $last_notice = $this->merchants_m->get_last_notice_sent($merchant_id, $this->store_id);

            //var_dump($last_notice); exit();

            if (empty($last_notice))
            {
                $current_level = 0;
            }
            else
            {
                $current_level = intval($last_notice['email_level']);
            }
        }

        $violation_level_options = array(
                        '0' => 'Not Listed'
        );

        for ($i = 1; $i <= $notif_level_amount; $i++)
        {
            $violation_level_options[] = $i;
        }

        $violation_level_options[$i] = 'Do Not Sell';

        $violation_level_dropdown = form_dropdown('violation_level', $violation_level_options, $current_level);
        
        $this->data->store_id = $this->store_id;
        $this->data->current_level = $current_level;
        $this->data->violation_level_dropdown = $violation_level_dropdown;
        $this->data->merchant_id = $merchant_id;
        $this->data->merchant = $merchant;
        $this->data->removal_date = $removal_date;
        $this->data->status_dropdown = $status_dropdown;
        $this->data->saved_status = $saved_status;
    }
    
    /**
     * Ability to edit the DNS email report template.
     * 
     * @author Christophe
     */
    public function do_not_sell_email_template()
    {
        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        $this->load->library('Trackstreet_merchants');
        $this->load->model('merchants_m');
        
        $this->_layout = 'modal';
        
        // check to see if user has access to edit this product
        if ($this->role_id != 2)
        {
            $this->session->set_flashdata('error_msg', 'Error: Your account does not have access to this item.');
            
            redirect('/');
            exit();
        }     

        $this->form_validation->set_rules('email_body', 'Email Template Body', 'trim|xss_clean');
        
        if ($this->form_validation->run() == FALSE)
        {
            // validation failed, or first load
        }
        else
        {
            //var_dump($_POST); exit();
            
            $email_body_form_value = $this->input->post('email_body');
            
            $this->trackstreet_merchants->save_dns_setting($this->store_id, $this->user_id, 'email_body', $email_body_form_value);
            
            $this->session->set_flashdata('success_msg', 'Do Not Sell List email template has been successfully saved.');
            
            redirect('/enforcement/do_not_sell_email_template');
            exit();
        }
        
        $view_data = array();
        
        $default_html = $this->load->view('enforcement/do_not_sell_email_report_template', $view_data, TRUE);
        
        $email_body_default = $this->merchants_m->get_dns_setting_value($this->store_id, 'email_body', $default_html);
        
        $email_body = set_value('dns_email_report_frequency', $email_body_default);
        
        $email_body = html_entity_decode($email_body);
        
        $this->javascript('tinymce/tiny_mce.js');
        
        $this->data->store_id = $this->store_id;
        $this->data->email_body = $email_body;
    }
    
    /**
     * Handle the removal of merchant from DNS list.
     * 
     * @author Christophe
     * @param int $merchant_id
     */
    public function do_not_sell_remove($merchant_id)
    {
        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        $this->load->library('Trackstreet_merchants');
        $this->load->model('merchants_m');
        
        $merchant_id = intval($merchant_id);
        
        $this->_layout = 'modal';
        
        $merchant = $this->merchants_m->get_merchant_by_id($merchant_id);
        
        $merchant['profile_name'] = $this->merchants_m->get_merchant_human_name($merchant);
        
        $dns_merchant = $this->merchants_m->get_dns_merchant($this->store_id, $merchant_id);
        
        // check to see if user has access to edit this product
        if ($this->role_id != 2)
        {
            $this->session->set_flashdata('error_msg', 'Error: Your account does not have access to this item.');
            
            redirect('/');
            exit();
        }
        
        $this->form_validation->set_rules('change_note', 'Note About Removal', 'trim|xss_clean');
        
        if ($this->form_validation->run() == FALSE)
        {
        	// validation failed, or first load
        }
        else
        {            
            // update DNS period and set end date
            $dns_period = $this->merchants_m->get_most_recent_dns_period($this->store_id, $merchant_id);
            
            $update_data = array(
                'end_date' => date('Y-m-d'),
                'removed_by' => $this->user_id,             
                'modified' => date('Y-m-d H:i:s')                
            );
            
            $this->merchants_m->update_dns_period($dns_period['id'], $update_data);
            
            // delete merchant from DNS list
            $this->merchants_m->delete_dns_merchant($this->store_id, $merchant_id);
            
            // record history about item
            $merchant_history_insert_data = array(
            		'store_id' => $this->store_id,
            		'merchant_id' => $merchant_id,
            		'action_id' => 2, // see merchants_m->get_history_action_array()
            		'created' => date('Y-m-d H:i:s'),
            		'created_by' => $this->user_id,
            		'modified' => date('Y-m-d H:i:s'),
            		'modified_by' => $this->user_id
            );
            
            $dns_merchant_history_insert_data = array(
            		'store_id' => $this->store_id,
            		'merchant_id' => $merchant_id,
            		'action_id' => 2, // see merchants_m->get_history_action_array()
            		'created' => date('Y-m-d H:i:s'),
            		'created_by' => $this->user_id,
            		'modified' => date('Y-m-d H:i:s'),
            		'modified_by' => $this->user_id
            );
            
            $history_note = $this->input->post('change_note', TRUE);
            
            if ($history_note != FALSE)
            {
                $merchant_history_insert_data['action_text'] = $history_note;
                $dns_merchant_history_insert_data['action_text'] = $history_note;
            }
            
            $this->merchants_m->insert_merchant_history_log($merchant_history_insert_data);
            $this->merchants_m->insert_dns_merchant_history_log($dns_merchant_history_insert_data);            
            
            $this->session->set_flashdata('success_msg', $merchant['profile_name'] . ' has been successfully removed from the Do Not Sell list.');
            
            $redirect_to = $this->input->post('redirect_to', TRUE);
            
            if ($redirect_to == FALSE)
            {
                redirect('/enforcement/do_not_sell');
                exit();
            }
            else
            {
                redirect($redirect_to);
                exit();                
            }
        }
        
        $this->data->merchant_id = $merchant_id;
        $this->data->merchant = $merchant;
    }


    
    /**
     * Page where a user can edit a violation list entry for a merchant.
     * 
     * @author Christophe
     * @param int $merchant_id
     */
    public function edit_list_entry($merchant_id)
    {
        $this->load->helper(array('form'));
        $this->load->library('form_validation');  
        $this->load->library('Trackstreet_merchants');      
        $this->load->model('merchants_m');
        
        $merchant_id = intval($merchant_id);
        
        $this->_layout = 'modal';
        
        $merchant = $this->merchants_m->get_merchant_by_id($merchant_id);
        
        $merchant['profile_name'] = $this->merchants_m->get_merchant_human_name($merchant);
        
        $dns_merchant = $this->merchants_m->get_dns_merchant($this->store_id, $merchant_id);
        
        // check to see if user has access to edit this product
        if ($this->role_id != 2)
        {
            $this->session->set_flashdata('error_msg', 'Error: Your account does not have access to this item.');
            
            redirect('/');
            exit();
        }
        
        // determine notification level merchant is on, and if they are on a level
        $dns_merchant = $this->merchants_m->get_dns_merchant($this->store_id, $merchant_id);

        $notif_level_amount = $this->merchants_m->get_notification_levels_num($this->store_id);

        if (!empty($dns_merchant))
        {
            // $current_level = 'dns';
            // for some reason CodeIgniter didn't like mixed key use for dropdown options array
            // DNS list level = (# of Defined Notifications Levels) + 1
            $current_level = $notif_level_amount + 1;
        }
        else
        {
            // check to see if they are on a numeric violation level
            $last_notice = $this->merchants_m->get_last_notice_sent($merchant_id, $this->store_id);

            if (empty($last_notice))
            {
                $current_level = 0;
            }
            else
            {
                $current_level = intval($last_notice['email_level']);
            }
        }

        $violation_level_options = array(
                        '0' => 'Not Listed'
        );

        for ($i = 1; $i <= $notif_level_amount; $i++)
        {
            $violation_level_options[] = $i;
        }

        $violation_level_options[$i] = 'Do Not Sell';

        $violation_level_dropdown = form_dropdown('violation_level', $violation_level_options, $current_level);
        
        $this->data->store_id = $this->store_id;
        $this->data->current_level = $current_level;
        $this->data->violation_level_dropdown = $violation_level_dropdown;
        $this->data->merchant_id = $merchant_id;
        $this->data->merchant = $merchant;
    }
	
	/**
	 * 
	 * View all products
	 */
	public function select_product( $store_id = 0, $subpage = "" ) {
		if ( $this->store_id != 'all' ) {
			$store_id = $this->store_id; 
		}
		if ( $store_id > 0 ) {
			$this->_switch_brand($store_id);
			
			if ( strlen($subpage) > 0 )  {
				redirect("enforcement/".$subpage);	
			} else {
				redirect("enforcement");
			}
			
			exit;
		}
		$this->javascript('views/enforcement/select_product.js.php');
	}
	
	public function get_products_list() {
		$this->_response_type('json');
		
		// get the data
		if ( ! empty($_REQUEST['keyword'])) {
			$keyword = strtolower( trim($_REQUEST['keyword']) );
			if ( strlen($keyword) > 0 ) {
				$this->db->where("(LOWER(s.store_name) LIKE '%".$keyword."%' OR LOWER(s.store_name) LIKE '%".$keyword."%')", NULL, FALSE);
			}
		}
		//$this->db->where("s.user_id", $this->user_id);
		$this->db->where("s.id in (select store_id from ".$this->_table_users_store." where user_id='".$this->user_id."')", NULL, FALSE);
		$this->db->order_by("s.store_name");
		$data = $this->db->get($this->_table_store . " s");

		// prep the data
		$ret = array();
		foreach ( $data->result() as $row ) {
			$action = '<a href="'.site_url("enforcement/select_product/".$row->id).'">Edit Store</a>';
			$action.= '&nbsp;|&nbsp;'; 
			$action.= '<a href="'.site_url("enforcement/select_product/".$row->id."/settings").'">Settings</a>';
			$action.= '&nbsp;|&nbsp;'; 
			$action.= '<a href="'.site_url("enforcement/select_product/".$row->id."/email_settings").'">Email Settings</a>';
			$action.= '&nbsp;|&nbsp;'; 
			$action.= '<a href="'.site_url("enforcement/select_product/".$row->id."/merchant").'">Merchant Info</a>';
			$action.= '&nbsp;|&nbsp;'; 
			$action.= '<a href="'.site_url("enforcement/select_product/".$row->id."/templates").'">Email Templates</a>';
			$action.= '&nbsp;|&nbsp;'; 
			$action.= '<a href="'.site_url("enforcement/select_product/".$row->id."/amazone_violator").'">Amazon Violator</a>';

			$logo = $row->brand_logo;
			if ( $logo != '' ) {
				$logo = '<img src="http://' . $this->config->item('s3_bucket_name') . '/stickyvision/brand_logos/' . $logo. '" width=120/>';
			}
			$ret[] = array(
				"store_id"	=> $row->id,
				"store_name"=> $row->store_name,
				"brand_logo"=> $logo,
				"action"	=> $action
			);
		}

		$this->data = $ret;
	}
	
	/*Start of Duncan's Funtions */
	public function email_settings(){
		$store_id = $this->store_id;
		
		$this->javascript('views/enforcement/email_settings.js.php');
	}
	
	public function get_email_settings() {
		$this->_response_type('json');
		
		$templates = array();
		
		$store_id = $this->store_id;
		$email_from = $this->session->userdata('user_email');
		$email_setting = $this->Violator->get_map_enforcement_settings($store_id,$email_from);
		if($email_setting){
			//settings exist
			$this->Violator->implement_map_enforcement_notification_levels($email_setting->id);
			$email_templates = $this->Violator->get_enforcement_email_templates($email_setting->id);
			if($email_templates) {
				foreach($email_templates->result() as $e_template){
					$templates[] = array(
						"id"				=> $e_template->id,
						"level"				=> $e_template->notification_level,
						"name"				=> convert_number_to_name($e_template->notification_level).' Warning E-mail',
						"subject"			=> $e_template->subject,
						"notify_after_days"	=> $e_template->notify_after_days,
						"no_of_days_to_repeat"=>$e_template->no_of_days_to_repeat,
						"template"			=> '<a href="'.site_url("enforcement/template/".$e_template->id).'">Edit</a>'
					);
				}
			}
		}
		else{
			//settings do not exists
		}
		
		$this->data = $templates;
	}
	
	public function update_email_setting() {
		$store_id = $this->store_id;
		
		$column = $this->input->post('column');
		$value = $this->input->post('value');
		
		if ( $column == 'no_of_days_to_repeat' || $column == 'notify_after_days' ) {
			$value = (int)$value;
			if ( $value < 1 || $value > 10 ) {
				$value = 1;
			} 
		}
		
		$data = array(
			$column => $value
		); 
		
		if ( $this->db->where("id", $this->input->post('id'))->update($this->_table_violator_notification_email_templates, $data) ) {
			if ( $column == 'no_of_days_to_repeat' ) {
				die( json_encode(array("no_of_days_to_repeat"=>$value)) );
			} elseif ( $column == 'notify_after_days' ) {
				die( json_encode(array("notify_after_days"=>$value)) );
			} else {
				die( json_encode(array("status"=>'success')) );
			}
		} else {
			die( json_encode(array("status"=>'failed')) );
		}
	}
	
    /**
     * Show email templates for enforecment emails that are sent to marketplaces.
     * 
     * @author unknown, Christophe
     */
    public function templates()
    {
        $this->load->model('users_m');
        
        $store_id = $this->store_id;
        
        $user = $this->users_m->get_user_by_id($this->user_id);
        
        $email_from = $user['email'];
        
        $email_setting = $this->Violator->get_notification_email_setting_by_store($store_id);
        
        if ($email_setting)
        {
            $this->Violator->implement_map_enforcement_notification_levels($email_setting['id']);
            
            $email_templates = $this->Violator->get_enforcement_email_templates($email_setting['id']);
            
            if ($email_templates)
            {
                $this->data->email_templates = $email_templates;
            }
        }
        else
        {
            //settings do not exists
        }
    }

    /**
     * View a single email template for an enforcement email.
     * 
     * @author unknown, Christophe
     * @param int $id
     */
    public function template($id = 0)
    {   
        $this->load->library('form_validation');
        
        $id = $this->input->post('template_id') ? $this->input->post('template_id') : $id;
        
        $id = intval($id); // template ID
        
        $template = $this->Violator->get_email_template_by_id($id);
        
        $email_setting = $this->Violator->get_notification_email_setting_by_id($template->email_settings_id);
        
        // check to see if user is admin and same store as template
        if ($this->role_id != 2 || intval($email_setting['store_id']) != $this->store_id)
        {
            $this->session->set_flashdata('error_msg', 'Your account does not have access to this item.');
            
            redirect('/enforcement/templates');
            exit();
        }
        
        $this->form_validation->set_rules('template_id', 'Template', 'trim|required');
        $this->form_validation->set_rules('known_seller_html_body', 'Known Seller Template', 'required');
        $this->form_validation->set_rules('unknown_seller_html_body', 'Unkown Seller Template', 'required');
        
        if ($this->input->post('submit'))
        {
            //var_dump($_POST); exit();
            
            if ($this->form_validation->run())
            {                
                $update_data = array(
                		'known_seller_html_body' => $this->input->post('known_seller_html_body'),
                		'unknown_seller_html_body' => $this->input->post('unknown_seller_html_body')
                );
                
                //var_dump($id); exit();
                
                $this->Violator->update_email_template($id, $update_data);   

                $this->session->set_flashdata('success_msg', 'Enforecement templates have been successfully saved.');
                
                redirect('/enforcement/template/' . $id);
                exit();
            }
            else
            {
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                
                $this->data->message = 'All fields are required - please correct form errors';
                
                $this->data->message = validation_errors();
            }
        }
        
        $template = $this->Violator->get_email_template_by_id($id);
        
        if ($template)
        {
            foreach ($template as $key => $val)
            {
                $key = $key=='id'?'template_id':$key;
                
                $this->data->$key = $val;
            }
        }
        else
        {
            redirect('/enforcement/templates');
        }
        
        $this->javascript('tinymce/tiny_mce.js');
        $this->javascript('views/enforcement/emails.js.php');
    }
    
    /**
     * Edit the settings for an enforcement template.
     * 
     * @author Christophe
     * @param int $template_id
     */
    public function template_settings($template_id = 0)
    {
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('Vision_users');
        
        $template_id = intval($template_id); // template ID
        
        $template = $this->Violator->get_email_template_by_id($template_id);
        
        $email_setting = $this->Violator->get_notification_email_setting_by_id($template->email_settings_id);
        
        // check to see if user is admin and same store as template
        if ($this->role_id != 2 || intval($email_setting['store_id']) != $this->store_id)
        {
            $this->session->set_flashdata('error_msg', 'Your account does not have access to this item.');
            
            redirect('/enforcement/templates');
            exit();
        }
        
        $this->form_validation->set_rules('subject', 'Email Subject', 'xss_clean|required|trim');
        $this->form_validation->set_rules('notify_after_days', 'Notify After How Many Days', 'xss_clean|required|trim');
        $this->form_validation->set_rules('no_of_days_to_repeat', 'How Many Days to Repeat', 'xss_clean|required|trim');
        
        if ($this->form_validation->run() === FALSE)
        {
            // validation failed, or first load
        }
        else
        {
            $subject = $this->input->post('subject', TRUE);
            $notify_after_days = $this->input->post('notify_after_days', TRUE);
            $no_of_days_to_repeat = $this->input->post('no_of_days_to_repeat', TRUE);
            
            $update_data = array(
            		'subject' => $subject,
            		'notify_after_days' => $notify_after_days,
                'no_of_days_to_repeat' => $no_of_days_to_repeat            
            );
            
            $this->Violator->update_email_template($template_id, $update_data); 
            
            $this->session->set_flashdata('success_msg', 'Email template settings have been successfully updated.');
            
            redirect('/enforcement/templates');
            exit();
        }
        
        $view_data['template'] = $template;
        
        $this->load->view('enforcement/template_settings', $view_data);        
    }
	
	public function edit($id=0){
		$id = $this->input->post('template_id')?$this->input->post('template_id'):$id;
		$template = $this->Violator->get_email_template_by_id($id);

		if($this->input->post('submit')){
			$rules = array(
        			'template_id' => array('human' => 'ID', 'rules' => 'trim|required'),
                    'subject' => array('human' => 'Subject', 'rules' => 'trim|required'),
                    'notify_after_days' => array('human' => 'Notify after', 'rules' => 'trim|required|integer'),
                    'no_of_days_to_repeat' => array('human' => 'Repeat for', 'rules' => 'trim|required|integer')
			);
			foreach($rules as $field => $vals){
				$this->form_validation->set_rules($field, $vals['human'], $vals['rules']);
			}
			if ($this->form_validation->run()){
				$this->data->message = 'E-mail Settings successfully saved';
				$data = array(
						'subject' => $this->input->post('subject'),
						'notify_after_days' => $this->input->post('notify_after_days'),
						'no_of_days_to_repeat' => $this->input->post('no_of_days_to_repeat')
				);


				$this->db->where('id', $id);
				$this->db->update($this->_table_violator_notification_email_templates, $data);

			}else{
				$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
				$this->data->message = 'All fields are required - please correct form errors';
				$this->data->message = validation_errors();
			}
		}
		if($template){
			foreach($template as $key => $val){
				$key = $key=='id'?'template_id':$key;
				$this->data->$key = $val;
			}
		}
		else{
			redirect('enforcement');
		}
	}

	public function index(){
    redirect('/enforcement/settings');
    exit();
	}

	/**
	 * 
	 * @author unknown, Christophe
	 */
	public function amazone_violator()
	{
		$this->load->model("amazon_settings_m", "AmazonSetting");
		$this->load->helper("password_helper");
		
		//$amazon_proxy_setting = $this->db->get("amazon_violator_proxy")->result_array();
		//$this->data->proxy_address	= "";
		//$this->data->proxy_port		= "";
		//$this->data->proxy_user		= "";
		//$this->data->proxy_password	= "";
		
		$this->data->email_settings = array();
		
		if ($this->input->post("store_button"))
		{
			$this->AmazonSetting->deleteAmazonEmailSettingByStoreID($this->store_id);
			
			//var_dump($_POST); //exit();
			
			for ($i = 1; $i <= 10; $i++)
			{
				$str = $this->input->post("ID".$i."_email");
				
				if (!empty($str))
				{
					$setting_info['store_id']	= $this->store_id;
					$setting_info['email'] = $this->input->post("ID".$i."_email");
					$setting_info['password'] = my_encrypt($this->input->post("ID".$i."_password"));
					$setting_info['marketplace'] = $this->input->post("ID".$i."_marketplace");
					//$setting_info['message'] = $this->input->post("message");
					
					$this->db->insert($this->_table_amazon_violator_email_settings, $setting_info);
				}
			}
			
			$this->data->email_message = 'Amazon violator email settings successfully updated.';
			
			//$this->db->query("DELETE FROM amazon_violator_proxy");
			//if ($this->input->post('proxy_address')) { // save proxy settings
			//	$this->data->proxy_address	= $this->input->post('proxy_address');
			//	$this->data->proxy_port		= $this->input->post('proxy_port');
			//	$this->data->proxy_user		= $this->input->post('proxy_user');
			//	$this->data->proxy_password	= $this->input->post('proxy_password');
			//	
			//	$this->db->insert("amazon_violator_proxy", array(
			//		"proxy_address"	=> $this->data->proxy_address,
			//		"proxy_port"	=> $this->data->proxy_port,
			//		"proxy_user"	=> $this->data->proxy_user,
			//		"proxy_password"=> $this->data->proxy_password
			//	));
			//}
				
			//if ( count($amazon_proxy_setting) > 0 ) {
			//	$this->data->proxy_address	= $amazon_proxy_setting[0]['proxy_address'];
			//	$this->data->proxy_port		= $amazon_proxy_setting[0]['proxy_port'];
			//	$this->data->proxy_user		= $amazon_proxy_setting[0]['proxy_user'];
			//	$this->data->proxy_password	= $amazon_proxy_setting[0]['proxy_password'];
			//}
		}
		
		$this->data->email_settings = $this->AmazonSetting->getAmazonEmailSettingByStoreID($this->store_id);
		
		$this->javascript('views/settings.js.php');
		$this->javascript('views/enforcement/amazon_violator.js.php');
	}

	function upload_logo(){

		if($_FILES['brand_logo']['error'] == 0){
			//upload and update the file
			$iT = time();
			$logo_name = $this->store_id.'_logo_'.$iT.'.'.$this->getFileExtension($_FILES['brand_logo']['name']);
			$thumb_name = $this->store_id.'_logo_'.$iT.'_thumb.'.$this->getFileExtension($_FILES['brand_logo']['name']);

			$config['upload_path'] = $this->config->item('uploaded_files').'brand_logo_images/';
                        if(!is_dir($this->config->item('uploaded_files').'brand_logo_images/')) {
                            mkdir($this->config->item('uploaded_files').'brand_logo_images/', 0777);
                            chmod($this->config->item('uploaded_files').'brand_logo_images/', 0777);
                        }
			$config['allowed_types'] = 'gif|jpg|png';
			$config['overwrite'] = true;
			$config['remove_spaces'] = true;
			$config['file_name'] = $logo_name;
			$this->load->library('upload', $config);

			if(!$this->upload->do_upload('brand_logo')){
				$this->file_upload_error = $this->upload->display_errors('', '');
				return false;
			}else{
				//Image Resizing
				$config['image_library'] = 'gd2';
				$config['source_image'] = $this->config->item('uploaded_files').'brand_logo_images/'.$logo_name;
				$config['maintain_ratio'] = TRUE;
				$config['create_thumb'] = TRUE;
				$config['width'] = 200;
				$config['height'] = 64;

				$this->load->library('image_lib', $config);
				$this->image_lib->resize();
				if(!$this->image_lib->resize()){
					$upload_error = $this->image_lib->display_errors();
					$this->image_lib->clear();
					$this->file_upload_error = $upload_error;
					@unlink($config['source_image'].$logo_name);
					return false;
				}else{
					$has_error = false;
					$this->load->library('S3');
					$s3 = new S3($this->config->item('s3_access_key'), $this->config->item('s3_secret_key'));
					$s3Folder = 'stickyvision/brand_logos/';
					if(file_exists($config['upload_path'].$logo_name)) {
						if($put = $s3->putObjectFile($config['upload_path'].$logo_name, $this->config->item('s3_bucket_name'), $s3Folder.$logo_name, S3::ACL_PUBLIC_READ)) {
						}else{
							$has_error = true;
							$this->file_upload_error = 'Could not upload logo to server.';
						}
						@unlink($config['upload_path'].$logo_name);
					}
					if(file_exists($config['upload_path'].$thumb_name)) {
						if($s3->putObjectFile($config['upload_path'].$thumb_name, $this->config->item('s3_bucket_name'), $s3Folder.$thumb_name, S3::ACL_PUBLIC_READ)) {
						}else{
							$has_error = true;
							$this->file_upload_error = 'Could not upload thumbnail to server.';
						}
						@unlink($config['upload_path'].$thumb_name);
					}
					unset($s3);

					$this->uploaded_file = $logo_name;

					return $has_error ? false : true;
				}
			}
		}
	}

	private function getFileExtension($filename){
		$array = explode('.', $filename);
		return end($array);
	}
	
    /**
     * Violator settings (shows all merchants, contact info, which email template they get, who else 
     * may get copied on the emails (ie. rep or broker)
     * 
     * @author Christophe
     */
    public function violator_settings()
    {
       
    }

    /**
     * Enforecement settings and Do Not Sell list settings.
     * 
     * See:
     * 
     * https://sendgrid.com/docs/Classroom/Basics/Email_Infrastructure/recommended_smtp_settings.html
     * 
     * @author Christophe, unknown
     */
    public function settings()
    {
        $this->load->library('form_validation');
        $this->load->library('Trackstreet_marketplaces');
        $this->load->library('Trackstreet_merchants');
        $this->load->model('merchants_m');
        $this->load->model('users_m');
        $this->load->helper('form');
        
        // check to see if user has access to edit this product
        if ($this->role_id != 2)
        {
            $this->session->set_flashdata('error_msg', 'Error: Your account does not have access to this item.');
            
            redirect('/');
            exit();
        }        
        
        $email_from = $this->session->userdata('user_email');
        
        $this->data->id = $this->store_id;
        
        $map_settings = $this->Violator->get_map_enforcement_settings($this->store_id, $email_from);
        
        if ($map_settings)
        {
            foreach($map_settings as $key=>$val)
            {
                $key = $key == 'id' ? 'setting_id' : $key;
                
                $this->data->$key = $val;
            }
        }
        else
        {
            $default_settings = array(
                'notification_levels' => 1,
                'reset_after_reaching' => 0,
                'company' => '',
                'phone' => '',
                'name_from' => '',
                'smtp_host' => '',
                'smtp_port' => '25',
                'smtp_ssl' => '',
                'smtp_username' => '',
                'smtp_password' => '',
                'name_to' => '',
                'email_to' => '',
                'email_from' => ''
            );
            
            foreach($default_settings as $key=>$val)
            {
                $this->data->$key = $val;
            }
        }
                
        // determine saved marketplace notification settings
        $marketplaces = $this->trackstreet_marketplaces->get_active_marketplaces_for_store($this->store_id);
        
        $this->data->marketplaces = $marketplaces;
        
        $this->data->marketplace_notification_dropdowns = array();
        
        $notification_options = array('on' => 'On', 'off' => 'Off');
        
        $this->data->notification_options = $notification_options;
        
        foreach ($marketplaces as $marketplace)
        {
            $marketplace_notification_setting_value = $this->trackstreet_marketplaces->get_marketplace_setting_value($marketplace, $this->store_id, 'send_notifications', 'on');
            
            //$marketplace_notification_value = set_value('marketplace_notifications_' . $marketplace, $marketplace_notification_setting_value);
            $marketplace_notification_value = $marketplace_notification_setting_value;
            
            //$this->data->marketplace_notification_dropdowns[$marketplace] = form_dropdown('marketplace_notifications_' . $marketplace, $notification_options, $marketplace_notification_value);
            $this->data->marketplace_notification_dropdowns[$marketplace] = $marketplace_notification_setting_value;
        }
        
        if (isset($_POST['general_settings_submit']))
        {
            $this->form_validation->set_rules('id', 'ID', 'trim|required');
            $this->form_validation->set_rules('email_from', 'Email From', 'trim|required|email');
            $this->form_validation->set_rules('name_from', 'Name From', 'trim|required');
            $this->form_validation->set_rules('notification_levels', 'Notification Levels', 'trim|required');
            $this->form_validation->set_rules('no_notifications_price_in_cart', 'Under MAP Cart Prices Marked as Violations', '');
            $this->form_validation->set_rules('company', 'Company', 'trim|required');
            $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
            $this->form_validation->set_rules('smtp_host', 'SMTP Host', 'trim|required');
            $this->form_validation->set_rules('smtp_port', 'SMTP Port', 'trim|required');
            $this->form_validation->set_rules('smtp_ssl', 'SMTP SSL', 'trim');
            $this->form_validation->set_rules('smtp_tls', 'SMTP TLS', 'trim');
            $this->form_validation->set_rules('smtp_username', 'SMTP Username', 'trim|required');
            $this->form_validation->set_rules('smtp_password', 'SMTP Password', 'trim|required');
            $this->form_validation->set_rules('reset_after_reaching', 'Reset After Reaching a Certain #', 'trim');
            
            foreach ($marketplaces as $marketplace)
            {
                $this->form_validation->set_rules('marketplace_notifications_' . $marketplace, ucfirst($marketplace) . ' Seller Violation Notices - On/Off', 'trim|required');
            }
        }    
        
        $num_of_periods_before_perm = $this->merchants_m->get_dns_setting_value($this->store_id, 'num_of_times_before_perm', 3);
        
        if (isset($_POST['dns_settings_submit']))
        {
            $this->form_validation->set_rules('dns_list_enabled', 'DNS List Enabled?', 'trim|xss_clean');
            $this->form_validation->set_rules('initial_permanent', 'Initial DNS List Entry Permanent or Temporary', 'trim|xss_clean');
            $this->form_validation->set_rules('notificaton_level_nums', 'Put on DNS List after they rise above level #', 'trim|xss_clean');
            $this->form_validation->set_rules('num_of_times_before_perm', 'Number of Times on DNS List Before Permanent', 'trim|xss_clean');
            $this->form_validation->set_rules('dns_email_report_frequency', 'Email Frequency', 'trim|xss_clean');
            $this->form_validation->set_rules('dns_email_report_day', 'Email Report Day', 'trim|xss_clean');
            $this->form_validation->set_rules('dns_email_report_time', 'Email Report Time', 'trim|xss_clean');
            $this->form_validation->set_rules('email_subject', 'Email Subject', 'trim|xss_clean');
            $this->form_validation->set_rules('dns_use_global_smtp_settings', 'Use Global SMTP Settings', 'trim|xss_clean');
            $this->form_validation->set_rules('dns_name_from', 'DNS Email Report - Name From', 'trim|xss_clean');
            $this->form_validation->set_rules('dns_email_from', 'DNS Email Report - Email From', 'trim|xss_clean');
            $this->form_validation->set_rules('dns_smtp_host', 'DNS SMTP Host', 'trim|xss_clean');
            $this->form_validation->set_rules('dns_smtp_port', 'DNS SMTP Port', 'trim|xss_clean');
            $this->form_validation->set_rules('dns_smtp_use_ssl', 'DNS SMTP Use SSL', 'trim|xss_clean');
            $this->form_validation->set_rules('dns_smtp_use_tls', 'DNS SMTP Use TLS', 'trim|xss_clean');
            $this->form_validation->set_rules('dns_smtp_username', 'DNS SMTP Username', 'trim|xss_clean');
            $this->form_validation->set_rules('dns_smtp_password', 'DNS SMTP Password', 'trim|xss_clean');
            
            for ($i = 1; $i <= $num_of_periods_before_perm; $i++)
            {
                $this->form_validation->set_rules('remain_on_list_days_offense_' . $i, 'Merchants Remain on List for # of Days - Offense #' . $i, 'trim|xss_clean');
            }
        }
        
        if ($this->form_validation->run() == FALSE)
        {
            // validation failed, or first load
        }
        else
        {
            //var_dump($_POST); exit();
            
            if (isset($_POST['general_settings_submit']))
            {
                $this->data->message = 'Enforcement settings have been successfully saved. ';
            }
            else if (isset($_POST['dns_settings_submit']))
            {
                $this->data->message = 'Do Not Sell List settings have been successfully saved.';
            }
            else
            {
                $this->data->message = 'Settings have been successfully saved.';
            }

            if (isset($_POST['general_settings_submit']))
            {
                $no_notifications_price_in_cart_form_value = $this->input->post('no_notifications_price_in_cart') == FALSE ? 'off' : 'on';
                
                $this->trackstreet_marketplaces->save_marketplace_setting('all', $this->store_id, $this->user_id, 'no_notifications_price_in_cart', $no_notifications_price_in_cart_form_value);
                
                $smtpPassword = $this->input->post('smtp_password');
    
                $data = array(
                    'store_id' => $this->store_id,
                    'email_from' => $this->input->post('email_from'),
                    'name_from' => $this->input->post('name_from'),
                    'company' => $this->input->post('company'),
                    'phone' => $this->input->post('phone'),
                    'notification_levels' => $this->input->post('notification_levels'),
                    'smtp_host' => $this->input->post('smtp_host'),
                    'smtp_port' => $this->input->post('smtp_port'),
                    'smtp_ssl' => $this->input->post('smtp_ssl'),
                    'smtp_tls' => $this->input->post('smtp_tls'),
                    'smtp_username' => $this->input->post('smtp_username'),
                    'smtp_password' => $smtpPassword,
                    'email_to' => '',//$this->input->post('email_to'),
                    'name_to' => '',//$this->input->post('name_to')
                    'reset_after_reaching' => $this->input->post('reset_after_reaching'),
                    'notifications_on_off' => $this->input->post('notifications_on_off'),
                    'notification_frequency' => $this->input->post('notification_frequency')
                );
    
                // store individual marketplace violation notification settings
                foreach ($marketplaces as $marketplace)
                {
                    $marketplace_notification_form_value = $this->input->post('marketplace_notifications_' . $marketplace, TRUE);
    
                    $this->trackstreet_marketplaces->save_marketplace_setting($marketplace, $this->store_id, $this->user_id, 'send_notifications', $marketplace_notification_form_value);
                }
    
                if ($this->input->post('setting_id'))
                {
                    $id = $this->input->post('setting_id');
    
                    $this->db->where('id', $id);
                    $this->db->update($this->_table_violator_notification_email_settings, $data);
                }
                else
                {
                    $this->db->insert($this->_table_violator_notification_email_settings, $data);
    
                    $id = $this->db->insert_id();
                }
    
                $this->Violator->implement_map_enforcement_notification_levels($id);
    
                $map_settings = $this->Violator->get_map_enforcement_settings_by_id($id);
    
                if ($map_settings)
                {
                    foreach ($map_settings as $key=>$val)
                    {
                        $key = $key == 'id' ? 'setting_id' : $key;
    
                        $this->data->$key = $val;
                    }
                }
                
                // send test email and print out delivery status message
                if (!empty($data['smtp_username']))
                {
                    // check to see if SMTP settings were changed and notify TrackStreet developers if SMTP test fails
                    $this->send_test_email($data);
                }
            }
            
            if (isset($_POST['dns_settings_submit'])) 
            {            
                $dns_use_global_smtp_settings = $this->input->post('dns_use_global_smtp_settings', TRUE) == FALSE ? 'no' : 'yes';
                $dns_smtp_use_ssl = $this->input->post('dns_smtp_use_ssl', TRUE) == FALSE ? 'no' : 'yes';
                $dns_smtp_use_tls = $this->input->post('dns_smtp_use_tls', TRUE) == FALSE ? 'no' : 'yes';
                
                $settings = array(
                    'dns_list_enabled' => $this->input->post('dns_list_enabled', TRUE),
                    'initial_permanent' => $this->input->post('initial_permanent', TRUE),
                    'notificaton_level_nums' => $this->input->post('notificaton_level_nums', TRUE),
                    'num_of_times_before_perm' => $this->input->post('num_of_times_before_perm', TRUE),
                    'dns_email_report_frequency' => $this->input->post('dns_email_report_frequency', TRUE),
                    'dns_email_report_day' => $this->input->post('dns_email_report_day', TRUE),
                    'dns_email_report_time' => $this->input->post('dns_email_report_time', TRUE),
                    'email_subject' => $this->input->post('email_subject', TRUE),
                    'dns_use_global_smtp_settings' => $dns_use_global_smtp_settings,
                    'dns_name_from' => $this->input->post('dns_name_from', TRUE),
                    'dns_email_from' => $this->input->post('dns_email_from', TRUE),
                    'dns_smtp_host' => $this->input->post('dns_smtp_host', TRUE),
                    'dns_smtp_port' => $this->input->post('dns_smtp_port', TRUE),
                    'dns_smtp_use_ssl' => $dns_smtp_use_ssl,
                    'dns_smtp_use_tls' => $dns_smtp_use_tls,
                    'dns_smtp_username' => $this->input->post('dns_smtp_username', TRUE),
                    'dns_smtp_password' => $this->input->post('dns_smtp_password', TRUE),
                );
    
                // clear DNS entry period time settings, to then save from scratch
                $this->merchants_m->delete_dns_time_period_settings($this->store_id);
    
                $i = 1;
    
                while ($this->input->post('remain_on_list_days_offense_' . $i, TRUE) != FALSE)
                {
                    log_message('debug', 'remain_on_list_days_offense_' . $i . ' found');
    
                    $settings['remain_on_list_days_offense_' . $i] = $this->input->post('remain_on_list_days_offense_' . $i, TRUE);
    
                    $i++;
                }
    
                // save DNS settings for store
                foreach ($settings as $name => $value)
                {
                    $this->trackstreet_merchants->save_dns_setting($this->store_id, $this->user_id, $name, $value);
                }
    
                // handle saving of email address to send report to
                $dns_user_ids = $this->input->post('dns_user_ids', TRUE);
                 
                $this->merchants_m->delete_dns_notify_emails($this->store_id);
    
                if ($dns_user_ids == FALSE || empty($dns_user_ids))
                {
                    // do nothing
                }
                else
                {
                    foreach ($dns_user_ids as $dns_user_id)
                    {
                        $user = $this->users_m->get_user_by_id($dns_user_id);
    
                        // save email address to merchant_do_not_sell_notify table
                        $insert_data = array(
                            'store_id' => $this->store_id,
                            'user_id' => $dns_user_id,
                            'email' => $user['email'],
                            'first_name' => $user['first_name'],
                            'last_name' => $user['last_name'],
                            'created' => date('Y-m-d H:i:s'),
                            'modified' => date('Y-m-d H:i:s')
                        );
    
                        $this->merchants_m->insert_dns_notify($insert_data);
                    }
                }
    
                $external_email_addresses = $this->input->post('external_email_addresses', TRUE);
                $external_email_first_names = $this->input->post('external_email_first_names', TRUE);
                $external_email_last_names = $this->input->post('external_email_last_names', TRUE);
    
                if ($external_email_addresses == FALSE || empty($external_email_addresses))
                {
                    // do nothing
                }
                else
                {
                    for ($j = 0; $j < count($external_email_addresses); $j++)
                    {
                        // save email address to merchant_do_not_sell_notify table
                        $insert_data = array(
                            'store_id' => $this->store_id,
                            'user_id' => 0,
                            'email' => trim($external_email_addresses[$j]),
                            'first_name' => trim($external_email_first_names[$j]),
                            'last_name' => trim($external_email_last_names[$j]),
                            'created' => date('Y-m-d H:i:s'),
                            'modified' => date('Y-m-d H:i:s')
                        );
    
                        $this->merchants_m->insert_dns_notify($insert_data);
                    }
                }
            }
            
            // end form POST processing
        }
        
        $no_notifications_price_in_cart = $this->trackstreet_marketplaces->get_marketplace_setting_value('all', $this->store_id, 'no_notifications_price_in_cart', 'off');
        
        // DNS list enabled or not
        $dns_list_enabled_value = intval($this->merchants_m->get_dns_setting_value($this->store_id, 'dns_list_enabled', 0));

        $dns_list_enabled_value_str = $dns_list_enabled_value === 1 ? 'On' : 'Off'; 
        
        $dns_list_enabled_options = array(
            0 => 'Off',
            1 => 'On'
        );

        $dns_list_enabled_dropdown = form_dropdown('dns_list_enabled', $dns_list_enabled_options, set_value('dns_list_enabled', $dns_list_enabled_value), 'id="dns-list-enabled-dropdown"');

        // when put on DNS list, permanent or temporary
        $initial_permanent_setting_value = intval($this->merchants_m->get_dns_setting_value($this->store_id, 'initial_permanent', 0));

        $initial_permanent_options = array(
            0 => 'temporarily',
            1 => 'permanently'
        );

        $initial_permanent_dropdown = form_dropdown('initial_permanent', $initial_permanent_options, set_value('initial_permanent', $initial_permanent_setting_value), 'id="initial-permanent-dropdown"');

        // determine minimum violation level before merchant lands on DNS list
        $notif_level_amount = $this->merchants_m->get_notification_levels_num($this->store_id);

        $notificaton_level_nums_setting_value = $this->merchants_m->get_dns_setting_value($this->store_id, 'notificaton_level_nums', 3);

        $notificaton_level_nums_options = array();

        for ($i = 1; $i <= $notif_level_amount; $i++)
        {
            $notificaton_level_nums_options[] = $i;
        }

        $notificaton_level_nums_dropdown = form_dropdown('notificaton_level_nums', $notificaton_level_nums_options, set_value('notificaton_level_nums', $notificaton_level_nums_setting_value));

        $email_subject_default = '[TrackStreet] Do Not Sell List Report: {date}';

        $email_subject = $this->merchants_m->get_dns_setting_value($this->store_id, 'email_subject', $email_subject_default);

        $dns_settings = array(
            'num_of_times_before_perm' => $num_of_periods_before_perm,
        );

        // determine # of offense period day duration settings they have
        for ($i = 1; $i <= $num_of_periods_before_perm; $i++)
        {
            $dns_settings['remain_on_list_days_offense_' . $i] = $this->merchants_m->get_dns_setting_value($this->store_id, 'remain_on_list_days_offense_' . $i, 30);
        }

        // get list of users that can be selected for email report
        $users = $this->users_m->get_team_members($this->store_id);

        $user_emails = array();

        foreach ($users as $user)
        {
            $user_emails[] = $user['email'];
        }

        $dns_email_report_frequency_setting_value = $this->merchants_m->get_dns_setting_value($this->store_id, 'dns_email_report_frequency', 'weekly');

        $dns_email_report_frequency_options = array('weekly' => 'weekly', 'daily' => 'daily');

        $dns_email_report_frequency_dropdown_value = set_value('dns_email_report_frequency', $dns_email_report_frequency_setting_value);

        $dns_email_report_frequency_dropdown = form_dropdown('dns_email_report_frequency', $dns_email_report_frequency_options, $dns_email_report_frequency_dropdown_value, 'id="dns-email-report-frequency-dropdown"');

        $dns_email_report_day_options = array(
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday'
        );

        $dns_email_report_day_setting_value = $this->merchants_m->get_dns_setting_value($this->store_id, 'dns_email_report_day', 1);

        $dns_email_report_day_dropdown = form_dropdown('dns_email_report_day', $dns_email_report_day_options, set_value('dns_email_report_day', $dns_email_report_day_setting_value));

        $dns_email_report_time_setting_value = $this->merchants_m->get_dns_setting_value($this->store_id, 'dns_email_report_time', 8);

        $dns_email_report_time_options = get_hour_dropdown_array();

        $dns_email_report_time_dropdown = form_dropdown('dns_email_report_time', $dns_email_report_time_options, set_value('dns_email_report_time', $dns_email_report_time_setting_value));

        // get selected email addresses that are set to receive DNS email report
        $dns_notify_emails = $this->merchants_m->get_dns_notify_emails($this->store_id);
        $dns_notify_external_emails = $this->merchants_m->get_dns_notify_external_emails($this->store_id);
        
        $dns_use_global_smtp_settings = $this->merchants_m->get_dns_setting_value($this->store_id, 'dns_use_global_smtp_settings', 'yes');
        $dns_name_from = $this->merchants_m->get_dns_setting_value($this->store_id, 'dns_name_from', '');
        $dns_email_from = $this->merchants_m->get_dns_setting_value($this->store_id, 'dns_email_from', '');
        $dns_smtp_host = $this->merchants_m->get_dns_setting_value($this->store_id, 'dns_smtp_host', '');
        $dns_smtp_port = $this->merchants_m->get_dns_setting_value($this->store_id, 'dns_smtp_port', '');
        $dns_smtp_use_ssl = $this->merchants_m->get_dns_setting_value($this->store_id, 'dns_smtp_use_ssl', 'no');
        $dns_smtp_use_tls = $this->merchants_m->get_dns_setting_value($this->store_id, 'dns_smtp_use_tls', 'no');
        $dns_smtp_username = $this->merchants_m->get_dns_setting_value($this->store_id, 'dns_smtp_username', '');
        $dns_smtp_password = $this->merchants_m->get_dns_setting_value($this->store_id, 'dns_smtp_password', '');

        // set up view variables
        $this->data->users = $users;
        $this->data->no_notifications_price_in_cart = $no_notifications_price_in_cart;
        $this->data->dns_email_report_frequency_dropdown = $dns_email_report_frequency_dropdown;
        $this->data->dns_email_report_time_dropdown = $dns_email_report_time_dropdown;
        $this->data->dns_email_report_day_dropdown = $dns_email_report_day_dropdown;
        $this->data->dns_email_report_frequency_setting_value = $dns_email_report_frequency_setting_value;
        $this->data->user_emails = $user_emails;
        $this->data->email_subject = $email_subject;
        $this->data->dns_notify_emails = $dns_notify_emails;
        $this->data->dns_notify_external_emails = $dns_notify_external_emails;
        $this->data->dns_settings = $dns_settings;
        $this->data->num_of_periods_before_perm = $num_of_periods_before_perm;
        $this->data->initial_permanent_setting_value = $initial_permanent_setting_value;
        $this->data->dns_list_enabled_dropdown = $dns_list_enabled_dropdown;
        $this->data->dns_list_enabled_value_str = $dns_list_enabled_value_str;
        $this->data->initial_permanent_dropdown = $initial_permanent_dropdown;
        $this->data->notificaton_level_nums_dropdown = $notificaton_level_nums_dropdown;
        $this->data->dns_use_global_smtp_settings = $dns_use_global_smtp_settings;
        $this->data->dns_name_from = $dns_name_from;
        $this->data->dns_email_from = $dns_email_from;
        $this->data->dns_smtp_host = $dns_smtp_host;
        $this->data->dns_smtp_port = $dns_smtp_port;
        $this->data->dns_smtp_use_ssl = $dns_smtp_use_ssl;
        $this->data->dns_smtp_use_tls = $dns_smtp_use_tls;
        $this->data->dns_smtp_username = $dns_smtp_username;
        $this->data->dns_smtp_password = $dns_smtp_password;
    }
	
	/*End of Duncan's Funtions */
	
    /**
     * Test a store's SMTP settings.
     * 
     * @todo finish this function
     * @author Christophe
     */
    public function test_smtp_settings()
    {
        $this->load->model('violator_m');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
             
        $this->form_validation->set_rules('email', 'Email Address', 'xss_clean');
        
        //$email_settings = $this->violator_m->get_violator_notification_email_setting_by_store($store_id);
        
        if ($this->form_validation->run() === FALSE)
        {
            // validation failed, or first load
        }
        else
        {
            
        }        
    }    
    
    /**
     * Used to test SMTP setting entered in by user.
     * 
     * @author unknown
     * @todo finish this function (this is just copied and pasted from elsewhere, so need variables changed, and return status)
     * @todo move to library function
     * @param array $smtp_settings
     */
    public function send_test_email($smtp_settings)
    {
        $this->load->library('Vision_users');
        
        $smtpInfo = array(
            'host' => $smtp_settings['smtp_host'],
            'port' => $smtp_settings['smtp_port'],
            'use_ssl' => $smtp_settings['smtp_ssl'],
            'use_tls' => $smtp_settings['smtp_tls'],
            'username' => $smtp_settings['smtp_username'],
            'password' => $smtp_settings['smtp_password']
        );
        
        $this->load->library('SMTP_auth', $smtpInfo, 'smtp');
        
        $to = $from = $smtp_settings['email_from'];
        
        $support_team_error_msg = '';
        
        if (empty($this->data->error))
        {
            $this->data->error = '';
        }
        
        try 
        {
            send_smtp(
                $this->smtp,
                $to,
                "SMTP Test E-mail from TrackStreet",
                "<div>This is a test email that was successfully sent to you as a result of the SMTP settings you entered with TrackStreet.</div>",
                "This is a test email that was successfully sent to you as a result of the SMTP settings you entered with TrackStreet.",
                $from,
                FALSE
            );
        }
        catch (phpmailerException $e) 
        {
            $this->data->error .= "SMTP settings entered are not valid. Please review. Also, support team was notified and will be in touch to help.";
            
            $support_team_error_msg .= $e->getMessage();
            
            //return;
        }
        catch (Exception $e) 
        {
            $this->data->error .= "SMTP settings entered are not valid. Please review. Also, support team was notified and will be in touch to help.";
            
            $support_team_error_msg .= $e->getMessage();
            
            //return;
        }
        
        if ($support_team_error_msg == '')
        {
            $this->data->message .= "SMTP settings look good and test email was successfully sent to you!";
        }
        else
        {
            // send notification to TrackStreet staff about store being added
            $email = $this->config->item('environment') == 'production' ? 'christophe@trackstreet.com, chris@trackstreet.com' : 'christophe@trackstreet.com';
             
            $subject = '[TrackStreet] Error with SMTP Settings for Store: ' . $from;
             
            $html_message = "<p>Check on SMTP for: {$from}</p>";
            $html_message .= "<p>User who added store: {$this->user['first_name']} {$this->user['last_name']} (ID: {$this->user_id})</p>";
            $html_message .= "<p>Error:</p>"; 
            $html_message .= "<p>{$support_team_error_msg}</p>";
            
            $text_message = strip_tags($html_message);
             
            $this->vision_users->sendSESEmail($email, $subject, $html_message, $text_message);
        }
            
        return;
    }
	
	public function merchant()
	{
		$this->data->brandname = getBrandName($this->session->userdata("store_id"));

		// violator notification data
		$this->data->show_notify_resource = 'true';
		$this->data->smtp = $this->Store->get_store_smtp_by_store($this->store_id);
		$this->data->default_email_from = $this->Store->get_smtp_email($this->data->smtp);

		// get store information
		if ( $this->store_id > 0 ) {
			$selected_store = $this->Store->get_store_track($this->store_id);
			$this->data->note_enable = $selected_store->note_enable; // Show merchant discussions on MAP Enforcement Page
		}
		
		$this->javascript('views/enforcement/merchant.js.php');
    $this->javascript('views/enforcement/history.js.php');
		$this->javascript('dynamic.js.php');
		$this->javascript('jqwidgets/jqxdata.export.js');
		$this->javascript('jqwidgets/jqxgrid.export.js');
		
		// date picker
		$this->data->optArray = 1;
		$this->data->time_frame = 24;
		$this->data->date_from = '';
		$this->data->date_to = '';
		$this->data->display = '1';
		$this->data->is_first = '';

	}

	public function history($merchant_name_id){
		$this->data->merchant_name_id = $merchant_name_id;
		$this->javascript('views/enforcement/history.js.php');
	}

	public function get_history($merchant_name_id){
		$this->_response_type('json');

		$history = $this->Store->get_violator_notifications_history_by_merchant_name_id($merchant_name_id, $this->store_id);
		
		$history_array = array();
		foreach($history as $item) {
			$item['email_level'] = convert_number_to_name($item['email_level']);
			$history_array[] = $item;
		}

		$this->data = array('data' => $history_array);
	}

	public function staff_notes($merchant_name_id){
		$this->data->merchant_name_id = $merchant_name_id;
		$this->javascript('views/enforcement/staff_notes.js.php');
	}
    
	/**
	 *
	 * Action for add staff note
	 */
	public function save_staff_note(){
        $this->_response_type('json');
        
        $this->data->status = FALSE;
        if ( $this->permission_id != 0 && $this->permission_id != 2 ) {
            $this->data->html = 'You have not permission to write';
        } else { // owner, admin
            $request = $this->input->post(array(
                'merchant_name_id',
                'column',
                'value',
                'note_id'
            ));
            
            $request['value'] = $request['value']?$request['value']: NULL;

            if ( empty($request['merchant_name_id']) ) {
                $this->data->html = 'An error occurred. The merchant could not be found.';
                ajax_return($this->data);
            }

            $this->load->model("merchant_staff_notes_m", "Merchant_Staff_Notes");
            $data = array(
                "merchant_name_id"  => $request['merchant_name_id'],
                "user_id"           => $this->user_id,
                "entry"             => $request['value'],
                "date"              => date("Y-m-d H:i:s"),
            );
            if ( $request['note_id'] ) {
                $this->data->status = $this->Merchant_Staff_Notes->updateStaffNote($request['note_id'], $data);
            } else {
                $this->data->status = $this->Merchant_Staff_Notes->insertStaffNote($data);
            }

            $this->data->html = $this->data->status ? 'Staff note writed successfully.' : 'Staff note not be write.';
        }
	}
    
	/**
	 *
	 * Action for delete staff note
	 */
	public function delete_staff_note(){
        $this->_response_type('json');
        
        $ids = $this->input->post("ids");
        
        $this->data->status = FALSE;
        if ( $this->permission_id != 0 && $this->permission_id != 2 ) {
            $this->data->html = 'You have not permission to write';
        } else { // owner, admin
            $this->load->model("merchant_staff_notes_m", "Merchant_Staff_Notes");
            $this->data->status = $this->Merchant_Staff_Notes->deleteStaffNotes($ids);

            $this->data->html = $this->data->status ? 'Staff notes deleted successfully.' : 'Staff notes not be delete.';
        }
	}
    
	/**
	 * Action for get preview of merchant staff notes.
	 * 
	 * @author unknown, Christophe
	 */
	public function staff_notes_preview($merchant_name_id) 
	{
	    $this->load->model("merchant_staff_notes_m", "Merchant_Staff_Notes");
	    
      $this->data->notes_count = $this->Merchant_Staff_Notes->getStaffNotesCountByMerchantID($merchant_name_id, $this->store_id);
      $this->data->staff_notes = $this->Merchant_Staff_Notes->getStaffNotesByMerchantID($merchant_name_id, $this->store_id);
      
      die( $this->load->view($this->_controller . '/staff_notes_preview', $this->data, TRUE) );
	}

	/**
	 * Action for get merchant staff notes.
	 * 
	 * @author unknown
	 */
	public function get_staff_notes($merchant_name_id)
	{
		$this->_response_type('json');

    $this->load->model("merchant_staff_notes_m", "Merchant_Staff_Notes");
    $this->load->model('users_m');
		
    $staff_notes = $this->Merchant_Staff_Notes->getStaffNotesByMerchantID($merchant_name_id, $this->store_id);
		
		$note_array = array();
		
		foreach($staff_notes as $item) 
		{
		  /*
		  $user = $this->users_m->get_user_by_id($item['user_id']);  
		    
		  $item['user_first_name'] = empty($user) ? 'N/A' : $user['first_name'];
		  $item['user_last_name'] = empty($user) ? 'N/A' : $user['last_name'];
		  */
		    
			$note_array[] = $item;
		}

		$this->data = array('data' => $note_array);
	}

	/**
	 * Action for get merchant staff notes
	 */
	public function get_merchant_staff_notes($merchant_name_id) 
	{
	    $this->load->model('users_m');

	    $page   = $this->input->post("page");
		  $keyword  = $this->input->post("keyword");
        if ( $this->permission_id == 1) { // repoter
            $page_rows = 8;
        } elseif ( $this->permission_id == 0 || $this->permission_id == 2 ) { // store owner, admin
            $page_rows = 6;
        } else {
            die("You have not permission for staff note");
        }
		

        $this->load->model("merchant_notes_m", "Merchant_Notes");
		$merchant = $this->Merchant_Notes->getMerchantNameByMerchantNameID($merchant_name_id);
		if ( $merchant === FALSE ) echo "Invalied merchant: ".$merchant_name_id;

		$this->load->model("merchant_staff_notes_m", "Merchant_Staff_Notes");
		
		$notes_count = $this->Merchant_Staff_Notes->getStaffNotesCountByMerchantID($merchant_name_id, $this->store_id, $keyword);
		
		$data = array("merchant"=>$merchant, "notes_count"=>$notes_count, "page"=>$page*1, "page_rows"=>$page_rows, "permission_id"=>$this->permission_id);
		
		if ( $notes_count > 0 ) 
		{
		  $notes = $this->Merchant_Staff_Notes->getStaffNotesByMerchantID($merchant_name_id, $this->store_id, $keyword, $page, $page_rows);  
		    
		  for ($i = 0; $i < count($notes); $i++) 
		  {
		      $user = $this->users_m->get_user_by_id($notes[$i]['user_id']);
		      
		      $notes[$i]['user_first_name'] = empty($user) ? 'N/A' : $user['first_name'];
		      $notes[$i]['user_last_name'] = empty($user) ? 'N/A' : $user['last_name'];
		  } 
		    
			$data['notes'] = $notes;
		}

		$return_html = $this->load->view("enforcement/merchant_staff_notes", $data, TRUE);

		die($return_html);
	}

	/**
	 * Provides row data to Merchant Info table.
	 * 
	 * @author unknown
	 */
	public function get_notifications()
	{
		$this->_response_type('json');

		// get store information
		$selected_store = $this->Store->get_store_track($this->store_id);

		$where = array(
			'vn.store_id' => $this->store_id,
		);

		$this->db
		->select('cmn.*, vn.store_id, vn.email_to, vn.name_to, vn.phone, vn.title, vn.active')
		->join($this->_table_crowl_product_list . ' cpl', 'cmn.id=cpl.merchant_name_id')
		//->join($this->_table_products . ' p', 'cpl.upc=p.upc_code')
		->join($this->_table_violator_notifications . ' vn', 'cmn.id=vn.crowl_merchant_name_id')
		->where($where);

		//var_dump($_REQUEST['keyword']); exit();
		$request_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		$parse_url_array = parse_url($request_url);
		
		parse_str($parse_url_array['query']);
		
		// get the data
		if (!empty($keyword)) 
		{
			/*$like = array(
				'cmn.original_name' => $_REQUEST['keyword'],
				'cmn.merchant_name' => $_REQUEST['keyword'],
				'cmn.seller_id'		=> $_REQUEST['keyword'],
				'cmn.marketplace'	=> $_REQUEST['keyword']
				);
				$this->db->or_like($like);*/
				
			//$keyword = strtolower( trim($_REQUEST['keyword']) );
		  $keyword = strtolower(trim($keyword));
			$keyword = str_replace('undefined', '', $keyword);
			$keyword = str_replace(' ', '%', $keyword);
			
			// $keyword param is now packed with other filters
			list($keyword, $merchOrRet, $date_from, $date_to) = explode('|', $keyword);
			
			if ( strlen($keyword) > 0 ) 
			{
				$this->db->where("( LOWER(cmn.original_name) LIKE '%".$keyword."%'
								 OR LOWER(cmn.merchant_name) LIKE '%".$keyword."%'
								 OR LOWER(cmn.seller_id) LIKE '%".$keyword."%'
								 OR LOWER(cmn.marketplace) LIKE '%".$keyword."%')", NULL, FALSE);
			}
			
			// value can be 'all', 'merchants', or 'retailers'.
			// if empty or 'all' then skip this
			if( strlen($merchOrRet) > 3 )
			{ 
				$this->db->join($this->_table_marketplaces . ' mp', 'mp.name = cmn.marketplace');
				$wh = "mp.is_retailer=".(($merchOrRet=='retailers')?'1':'0');
				$this->db->where($wh,  NULL, FALSE);
			}
			
			if( strlen($date_from) > 0 )
			{ 
				$this->db->where('cpl.last_date >='.strtotime($date_from),  NULL, FALSE);
			}
			
			if( strlen($date_to) > 0 )
			{ 
				$this->db->where('cpl.last_date <='.(strtotime($date_to)+(24*60*60)),  NULL, FALSE);  // add 24 hours to get the whole day
			}
		}
		
		if(empty($date_from))
		{
			$this->db->where('cpl.last_date >='.strtotime('yesterday'),  NULL, FALSE);
		}
		
		$this->db->group_by('cmn.id')->order_by('cmn.original_name')->order_by('cmn.marketplace');
		$data = $this->db->get($this->_table_crowl_merchant_name . ' cmn');
		
		// prep the data
		$ret = array();

		$email_levels = array(
			1	=> "First",
			2	=> "Second",
			3	=> "Third",
			4	=> "Fourth",
			5	=> "Fith",
			6	=> "Sixth",
			7	=> "Seventh",
			8	=> "Eighth",
			9	=> "Ninth",
			10	=> "Tenth"
		);
		
		// load merchant_notes model
		$this->load->model("merchant_notes_m", "Merchant_Notes");
		
		for ($i = 0, $n = $data->num_rows(); $i < $n; $i++)
		{
			$row = $data->row($i);
			
			$last_history = $this->Violator->getLastViolationNotificationHistory($this->store_id, $row->id);
			
			$last_contacted = '';
			$violation_level = 'None';
			$repeat_violator = "N";
			$history_url = site_url('enforcement/history/' . $row->id);
      $staff_note_history_url = site_url('enforcement/staff_notes/' . $row->id);
			$reset_url = "";
			
			if ( $last_history !== FALSE ) 
			{
				$violation_level = " ".$email_levels[$last_history['email_level']];
				
				$repeat_violator = ($last_history['email_repeat']>1 ? $last_history['email_repeat']:"N");
				
				$last_contacted = date('m/d/Y', strtotime($last_history['date']));
			}

			$violations = $this->Violator->getSellerViolations($this->store_id, $row->id, 0, strtotime('now'));
			$last_violation = '';
			$violated_count = '';
			
			if ( ! empty($violations[0]->last_date)) 
			{
				$last_violation = date('m/d/Y', $violations[0]->last_date);
				$crawl = $this->Crawl_data->get_crawl_by_time($violations[0]->last_date, $row->marketplace);
                
        if ( ! empty($crawl)) 
        {
					$start = strtotime($crawl['start_datetime']);
					$end = strtotime($crawl['end_datetime']);
					$violations = $this->Violator->getSellerViolations($this->store_id, $row->id, $start, $end);
					$violated_count = $violations ? count($violations) : 0;
                    
          if ( $violated_count > 0 ) 
          {
              $reset_url = '<span style="cursor:pointer;">Reset</span>';
          }
				}
			}

			$type = $this->Marketplace->is_retailer($row->marketplace) ? 'Known' : 'Unknown';
			//$type = $row->name_to != '' ? 'Known Seller' : 'Unknown Seller';

			$merchant_name = trim($row->original_name);
			
			if ($this->Marketplace->is_retailer($row->marketplace))
			{
				$merchant_name = ucfirst($merchant_name) . ".com";
			}
			
			$site = '';
			
			if($row->marketplace == 'amazon')
			{
				$site = '<a href="http://www.amazon.com/gp/aag/main/?seller='.$row->seller_id.'" target="_blank" class="ui-tooltip" title="http://www.amazon.com/gp/aag/main/?seller='.$row->seller_id.'">amazon.com</a>';
			}
			else
			{
				$site = '<a href="http://www.'.$row->marketplace.'.com" target="_blank" class="ui-tooltip" title="http://www.'.$row->marketplace.'.com">'.$row->marketplace.'.com</a>';
			}
				
			$merchant_notes_count = 0;
			
			if ( $selected_store->note_enable == '1' ) 
			{ 
			    // If note_enable is true, get note list
				$merchant_notes_count = $this->Merchant_Notes->getEntriesCountByMerchantNameID($row->id);
			}
				
      $this->load->model("merchant_staff_notes_m", "Merchant_Staff_Notes");
      
      $staff_note = $this->Merchant_Staff_Notes->getLastStaffNoteByMerchantID($row->id, $this->store_id);
            
			$tmp = array(
				'id' => $row->id,
				'merchant' => $merchant_name,
				'seller_id' => $row->seller_id,
				'notes' => ($selected_store->note_enable == 1 ? $this->get_info_image($row->id, $merchant_notes_count) : ""),
				'note_count' => $merchant_notes_count,
				'active' => $this->get_active_image($row->active == '1'),
				'website_address' => $site,
				'name_to' => $row->name_to,
				'email_to' => $row->email_to,
				'type' => $type,
				'last_violation' => $last_violation,
				'violated_count' => $violated_count,
				'violation_level' => $violation_level,
				'repeat_violator' => $repeat_violator,
				'last_contacted' => $last_contacted,
				//'history_url' => '<a href="' . $history_url . '">View</a>',
        'history_url' => '<a>View</a>',
        'reset_url' => $reset_url,
        'staff_note' => empty($staff_note) ? "" : '<a link="'.site_url("enforcement/staff_notes_preview/".$row->id).'" class="ajaxtooltip">'.$staff_note['entry'] .'</a>',
        //'staff_note_history_url' => '<a href="' . $staff_note_history_url . '">View</a>',
				'time_frame' => '',
				'date_from' => '',
				'date_to' => '',
				'display' => '1',
				'is_first' => '',
        'marketplace' => $row->marketplace
			);
			
			$ret[] = $tmp;
		}
		
		$this->data = $ret;
	}

	/**
	 * Called via AJAX when contact data is updated with table on 
	 * https://app.trackstreet.com/enforcement/merchant
	 * 
	 * @author unknown
	 */
	public function update_contact(){
		$this->_response_type('json');

		$this->data->status = FALSE;

		$request = $this->input->post(array(
			'merchant_name_id',
			'column',
			'value'
		));
		$request['value'] = $request['value']?$request['value']: NULL;

		if (empty($request['merchant_name_id']))
		$this->data->html = 'An error occurred. The merchant could not be found.';

        $field = "";
        
		$data = array(
			'crowl_merchant_name_id' => $request['merchant_name_id'],
			'store_id' => $this->store_id
		);
		switch ($request['column']){
			case 'name_to':
				$data['name_to'] = $request['value'];
                
                $field = "Contact Name";
				break;
			case 'email_to':
				if ( ! empty($request['value']) ) {
					$arrEml = explode(',', $request['value']);
					foreach($arrEml as $eml){
						$eml = trim($eml);
						if(! valid_email($eml)){
							$this->data->html .= "The email {$eml} is not a valid email address. ";
						}
					}
				}
					
				$data['email_to'] = $request['value'];
				if (empty($data['email_to']))
					$data['active'] = 0;
                
                $field = "Contact Email";
				break;
		}

		if ( ! empty($this->data->html)) // there was a validation error
		ajax_return($this->data);

		$vn = $this->Store->get_violator_notification_by_seller($data['crowl_merchant_name_id'], $this->store_id);
		$seller = $this->Crawl_data->crowlMerchantByID($data['crowl_merchant_name_id']);
		$seller_name = ! empty($seller->original_name) ? trim($seller->original_name) : 'The violator';
		if ( ! empty($vn['crowl_merchant_name_id'])) {
			if ($request['column'] === 'active') {
				$data['active'] = $vn['active'] == '1' ? 0 : 1;
				$this->data->active = $this->get_active_image($data['active']);
			}
			$this->data->status = $this->Store->update_violator_notification($vn['id'], $data);
		}
		else {
			if ($request['column'] === 'active') {
				$data['active'] = 1;
				$this->data->active = $this->get_active_image($data['active']);
			}

			// create the record with default data from global notification settings
			$data['email_from'] = '';
			$data['name_from'] = '';
			$data['phone'] = '';
			$default_data = $this->Store->get_violator_notification_settings_by_store($this->store_id);
			if ( ! empty($default_data)) {
				$data['email_from'] = $default_data['email_from'];
				$data['name_from'] = $default_data['company'];
				$data['phone'] = $default_data['phone'];
				//$data['days_to_warning1'] = $default_data['days_to_warning1'];
				//$data['days_to_warning2'] = $default_data['days_to_warning2'];
				//$data['warning1_repetitions'] = $default_data['warning1_repetitions'];
				//$data['warning2_repetitions'] = $default_data['warning2_repetitions'];
			}
			$this->data->status = $this->Store->create_violator_notification($data);
		}
		if ($request['column'] === 'email_to' AND empty($data['email_to']) AND $this->data->status) {
			$this->data->active = $this->get_active_image(FALSE);
		}
		$this->data->html = $this->data->status ? $seller_name . ' '.$field.' info was updated successfully.' : $seller_name . ' '.$field.' info could not be updated.';
	}

	/**
	 *
	 * Action for get preview of merchant notes
	 */
	public function notes_preview($merchant_name_id) {
		$this->load->model("merchant_notes_m", "Merchant_Notes");
		$this->data->merchant_notes_count = $this->Merchant_Notes->getEntriesCountByMerchantNameID($merchant_name_id);

		if ( $this->data->merchant_notes_count == 0 ) {
			die("New write comment");
		}

        $this->data->merchant_notes = $this->Merchant_Notes->searchEntries($merchant_name_id, 0, "", 2);
        die( $this->load->view($this->_controller . '/notes_preview', $this->data, TRUE) );
	}

	/**
	 * Action for get merchant notes
	 */
	public function get_merchant_notes($merchant_name_id) {
		$page   = $this->input->post("page");
		$keyword  = $this->input->post("keyword");
		$page_rows = 5;

		$this->load->model("merchant_notes_m", "Merchant_Notes");

		$merchant = $this->Merchant_Notes->getMerchantNameByMerchantNameID($merchant_name_id);
		if ( $merchant === FALSE ) echo "Invalied merchant: ".$merchant_name_id;

		$notes_count = $this->Merchant_Notes->getEntriesCountByMerchantNameID($merchant_name_id, $keyword);
		$data = array("merchant"=>$merchant, "notes_count"=>$notes_count, "page"=>$page*1, "page_rows"=>$page_rows);
		if ( $notes_count > 0 ) {
			$data['notes'] = $this->Merchant_Notes->searchEntries($merchant_name_id, $page, $keyword, $page_rows);
		}

		$return_html = $this->load->view("enforcement/merchant_notes", $data, TRUE);

		die($return_html);
	}

	/**
	 * Save note of merchant
	 */
	public function save_note_of_merchant() {
		$merchant_name_id = $this->uri->segment(3);

		$store_info = $this->Store->get_store_info($this->store_id);

		$note = array(
			"reporter_name"	=> $this->session->userdata('user_name'), //$this->input->post("reporter_name"),
			"company"		=> $store_info['store_name'], //$this->input->post("company_name"),
			"type_of_entry"	=> $this->input->post("type_of_entry"),
			"entry"			=> $this->input->post("entry_note"),
			"user_id"		=> $this->user_id
		);

		$this->load->model("merchant_notes_m", "Merchant_Notes");
		if ( $this->Merchant_Notes->saveNote($merchant_name_id, $note) ) {
			die("success");
		} else {
			die("false");
		}

		exit;
	}

	protected function get_active_image($active)
	{
		if ($active){
			$img = img(array(
				'src' => frontImageUrl() . 'icons/checkmark.png',
				'class' => 'catalogCheck',
				'alt' => 'Active'
				));
		} else {
			$img = img(array(
				'src' => frontImageUrl() . 'icons/dot-red.png',
				'class' => 'catalogDot',
				'alt' => 'Inactive'
				));
		}

		return $img;
	}

	protected function get_info_image($merchant_name_id, $count=0)
	{
		/*if ( $count == 0 ) {
			return img(array(
			'src' => frontImageUrl() . 'icons/16/2.png',
			'class' => 'catalogCheck',
			'alt' => "New write comment",
			'title' => "New write comment"
			));
			}*/

		$img = img(array(
				'src' => frontImageUrl() . 'icons/16/2.png',
				'class' => 'catalogCheck'
        ));

        return '<a link="'.site_url("enforcement/notes_preview/".$merchant_name_id).'" class="ajaxtooltip">'.$img.'</a>';
	}
    
    public function reset_violation_count( $merchant_name_id ) {
        
        $violations = $this->Violator->getSellerViolations($this->store_id, $merchant_name_id, 0, strtotime('now'));
        if ( !empty($violations[0]->last_date) && !empty($violations[0]->marketplace) ) {
            $last_violation = date('m/d/Y', $violations[0]->last_date);
            $crawl = $this->Crawl_data->get_crawl_by_time($violations[0]->last_date, $violations[0]->marketplace);
            if ( !empty($crawl) ) {
                $start = strtotime($crawl['start_datetime']);
                $end = strtotime($crawl['end_datetime']);
                $violations = $this->Violator->getSellerViolations($this->store_id, $merchant_name_id, $start, $end);
                
                for ( $i = 0; $i < count($violations); $i ++ ) {
                    $this->db->update($this->_table_crowl_product_list, array("violated"=>0), array("id"=>$violations[$i]->cplid));
                }
            }
            
            die('success');
        }
        
        die('error');
    }
}

/* End of file enforcement.php */
/* Location: ./system/application/controllers/enforcement.php */