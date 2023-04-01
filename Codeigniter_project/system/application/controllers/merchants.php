<?php

class Merchants extends MY_Controller
{
    private $countProduct, $countViolation;
    
    public function Merchants()
    {
        parent::__construct();
    }
    
    /**
     * Allow users the ability to add a new linked merchant record.
     * 
     * @author Christophe
     * @param int $merchant_id
     */
    public function add_merchant_link($merchant_id)
    {
        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        $this->load->library('Vision_users');
        $this->load->model('merchants_m');
        
        $this->_layout = 'modal';
        
        $merchant_id = intval($merchant_id);
        
        $merchant = $this->merchants_m->get_merchant_by_id($merchant_id);
        
        // check to see if user has access to edit this product
        if ($this->role_id != 2)
        {
            $this->session->set_flashdata('error_msg', 'Error: Your account does not have access to this item.');
            
            redirect('/');
            exit();
        }
        
        $this->form_validation->set_rules('merchant_name', 'Merchant Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('seller_id', 'Marketplace Seller ID', 'trim|xss_clean');
        $this->form_validation->set_rules('marketplace', 'Marketplace', 'trim|xss_clean');
        $this->form_validation->set_rules('merchant_url', 'Merchant URL', 'trim|xss_clean');
        
        if ($this->form_validation->run() == FALSE)
        {
        	// validation failed, or first load
        }
        else
        {
        	//var_dump($_POST); exit();  

          // check to see if user is linking to an existing merchant  
          if (isset($_POST['existing_merchant_id'])) 
          {
              $existing_merchant_id = intval($_POST['existing_merchant_id']);
              
              $updated_merchant = $this->merchants_m->get_merchant_by_id($existing_merchant_id);
              
              $update_data = array(
                  'modified_at' => date('Y-m-d H:i:s'),            
                  'modified_by' => $this->user_id,            
                  'parent_merchant_id' => $merchant_id                
              );              
              
              $this->merchants_m->update_merchant($existing_merchant_id, $update_data);
              
              $email = $this->config->item('environment') == 'production' ? 'andrew@trackstreet.com,christophe@trackstreet.com' : 'christophe@trackstreet.com,christophe@juststicky.com';
               
              $subject = '[TrackStreet] Merchant Record Updated by User';
               
              $html_message = '<p>Following merchant record was modified: ' . $updated_merchant['original_name'] . ' (ID: ' . $updated_merchant['id'] .')</p>';
              $html_message .= '<p>Modified by ' . $this->user['first_name'] . ' ' . $this->user['last_name'];
              $html_message .= ' (Company: ' . $this->user['company_name'] . ' - Email: ' . $this->user['email'] . ')</p>';
              $html_message .= '<p>Now linked to this merchant: ' . $merchant['original_name'] . ' (ID: ' . $merchant['id'] .')</p>';
               
              $text_message = strip_tags($html_message);
               
              $this->vision_users->sendSESEmail($email, $subject, $html_message, $text_message);
              
              $this->session->set_flashdata('success_msg', 'Merchant profiles were successfully linked together.');
              
              redirect('/merchants/add_merchant_link/' . $merchant_id);
              exit();
          } 
            
        	// user wants to add a new merchant 
          $marketplace = $this->input->post('marketplace', TRUE); 
          $seller_id = $this->input->post('seller_id', TRUE);  
          
          // check to see if we find other merchants with same marketplace and seller ID
          $existing_merchant = $this->merchants_m->get_merchant_by_marketplace_seller_id($marketplace, $seller_id);
          
          if (!empty($existing_merchant))
          {
              $error_msg = 'A marketplace merchant with this seller ID already exists. ';
              $error_msg .= '<a href="/merchants/profile/' . $existing_merchant['id'] . '">View existing merchant here.</a>';
              
              $this->session->set_flashdata('error_msg', $error_msg);
              
              redirect('/merchants/add_merchant_link/' . $merchant_id);
              exit();
          }
          
          // check to see if we find other merchants with same domain
          if ($marketplace == '')
          {
              // not a marketplace seller: this route is for retailers only such as http://www.bobsguns.com
              $merchant_url = $this->input->post('merchant_url', TRUE);
              
              // if user didn't use http:// or https:// in the URL they entered
              if (strstr($merchant_url, 'http://') == FALSE && strstr($merchant_url, 'https://') == FALSE) 
              {
                  // add http:// to URL string entered by user
                  $merchant_url = 'http://' . $merchant_url;
              }
              
              // use TLD part
              $url_parts = explode('.', $merchant_url);
              
              // did user use "www." with URL entered
              if (strstr($merchant_url, 'www.') == FALSE)
              {
                  $marketplace = $url_parts[0];
                  $seller_id = $url_parts[0];
              }
              else
              {
                  $marketplace = $url_parts[1];
                  $seller_id = $url_parts[1]; 
              }
              
              // check to see if we have an existing merchant with similar URL
              $existing_merchant = $this->merchants_m->get_merchant_by_marketplace($marketplace);
              
              if (!empty($existing_merchant))
              {
                  $error_msg = 'A marketplace merchant with this seller ID already exists. ';
                  $error_msg .= '<a href="/merchants/profile/' . $existing_merchant['id'] . '">View existing merchant here.</a>';
                
                  $this->session->set_flashdata('error_msg', $error_msg);
                
                  redirect('/merchants/add_merchant_link/' . $merchant_id);
                  exit();
              }
          }
          else
          {
              // this is for marketplaces 
              // @todo do we have any marketplaces in our system that aren't a .com?? - Christophe
              $merchant_url = 'http://www.' . $marketplace . '.com';
          }
          
        	$insert_data = array(
        			'merchant_name' => $this->input->post('merchant_name', TRUE),
        			'original_name' => $marketplace,
        	    'marketplace' => $marketplace,
        	    'parent_merchant_id' => $merchant_id,
        	    'merchant_url' => $merchant_url,
        	    'user_id' => $this->user_id,
        	    'seller_id' => $seller_id                                               
        	);
        	 
        	$new_merchant_id = $this->merchants_m->insert_merchant($insert_data);
        	
        	$email = $this->config->item('environment') == 'production' ? 'andrew@juststicky.com' : 'christophe@trackstreet.com';
        	
        	$subject = '[TrackStreet] New Merchant Submitted - Needs Config';
        	
        	$html_message = '<p>A new merchant has been submitted and needs to be set up to be crawled.</p>';
        	$html_message .= '<p>A new merchant was added by ' . $this->user['first_name'] . ' ' . $this->user['last_name'];
        	$html_message .= ' (Company: ' . $this->user['company_name'] . ' - Email: ' . $this->user['email'] . ')</p>';
        	$html_message .= '<p>New Merchant ID: ' . $new_merchant_id . '</p>';
        	$html_message .= '<p>New merchant profile page: <a href="https://app.trackstreet.com/merchants/profile/' . $new_merchant_id .'">https://app.trackstreet.com/merchants/profile/' . $new_merchant_id .'</a></p>';
        	                
        	$text_message = strip_tags($html_message);
        	
        	$this->vision_users->sendSESEmail($email, $subject, $html_message, $text_message);
        	 
        	$this->session->set_flashdata('success_msg', 'New merchant association record has been successfully added.');
        
        	//redirect('/merchants/profile/' . $merchant_id);
        	//exit();
        	redirect('/merchants/add_merchant_link/' . $merchant_id);
        	exit();
        }
        
        $options = array(
        		''  => 'Not Marketplace Seller (Direct Website)',
        		'amazon'    => 'Amazon',
        		'ebay'   => 'eBay',
        		'walmart' => 'Walmart',
            'gunbroker' => 'Gunbroker',     
            'rakuten' => 'Rakuten'                   
        );
        
        $this->data->marketplace_dropdown = form_dropdown('marketplace', $options, '', 'id="marketplace-dropdown"');
        
        $this->data->merchant_id = $merchant_id;
        $this->data->merchant = $merchant;
    }
    
    public function build_links()
    {
        $query_str = "
            SELECT *
            FROM crowl_merchant_name_new
            WHERE marketplace = seller_id
            AND marketplace != 'amazon'
            AND marketplace != 'ebay'
            AND marketplace != 'walmart'
            AND marketplace != 'unknown'
            AND marketplace != 'gunbroker'
        ";
    }
    
    /**
     * Change the violation level that a merchant is set to.
     * 
     * @author Christophe
     * @param int $merchant_id
     */
    public function change_level($merchant_id)
    {
        $this->load->model('merchants_m');
        $this->load->library('Trackstreet_merchants');
        
        $merchant_id = intval($merchant_id);
        
        $last_notice = $this->merchants_m->get_last_notice_sent($merchant_id, $this->store_id);
        
        $prev_violation_level = intval($last_notice['email_level']);;
        
        // handle change of notification level, if they are changing it
        $violation_level = $this->input->post('violation_level', TRUE);
        
        log_message('debug', 'change_level() - Saving merchant to level: ' . $violation_level);
        
        $this->trackstreet_merchants->change_violation_level($this->store_id, $merchant_id, $violation_level, $this->user_id);
        
        $merchant_history_insert_data = array(
            'store_id' => $this->store_id,
            'merchant_id' => $merchant_id,
            'action_id' => 3, // see merchants_m->get_history_action_array()
            'created' => date('Y-m-d H:i:s'),
            'created_by' => $this->user_id,
            'modified' => date('Y-m-d H:i:s'),
            'modified_by' => $this->user_id                 
        );
        
        $dns_merchant_history_insert_data = array(
        		'store_id' => $this->store_id,
        		'merchant_id' => $merchant_id,
        		'action_id' => 3, // see merchants_m->get_history_action_array()
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
        else
        {
            $merchant_history_insert_data['action_text'] = '';
            $dns_merchant_history_insert_data['action_text'] = '';
        }
        
        $merchant_history_insert_data['action_text'] .= ' - Violation Level changed to Level ' . $violation_level . ' from Level ' . $prev_violation_level; 
        $dns_merchant_history_insert_data['action_text'] .= ' - Violation Level changed to Level ' . $violation_level . ' from Level ' . $prev_violation_level; 
        
        $this->merchants_m->insert_merchant_history_log($merchant_history_insert_data);
        $this->merchants_m->insert_dns_merchant_history_log($dns_merchant_history_insert_data);
        
        // send user back to merchant profile page
        $this->session->set_flashdata('success_msg', 'Violation Level has been successfully changed for merchant.');
        
        $redirect_to = $this->input->post('redirect_to', TRUE);
        
        if ($redirect_to == FALSE)
        {
            redirect('/merchants/profile/' . $merchant_id);
        }
        else
        {
            redirect($redirect_to);
        }
        
        exit();
    }
    
    /**
     * Modal window form where user can change the violation level for a merchant.
     * 
     * @author Christophe
     * @param int $merchant_id
     */
    public function change_level_modal($merchant_id)
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
     * Perform action of removing a merchant contact from the database.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param string $contact_uuid
     */
    public function delete_contact($merchant_id, $contact_uuid)
    {
        $this->load->model('merchants_m');
        
        $contact = $this->merchants_m->get_merchant_contact_by_uuid($contact_uuid);
        
        $merchant_id = intval($merchant_id);
        
        if (empty($contact))
        {
            $this->session->set_flashdata('error_msg', 'Error: Contact not found.');
            
            redirect('/merchants/profile/' . $merchant_id);
            exit();            
        }
        
        // check to see if user can edit contact
        if ($this->role_id != 2 || $this->store_id != intval($contact['store_id']))
        {
            $this->session->set_flashdata('error_msg', 'Error: Your account does not have access to this item.');
            
            redirect('/merchants/profile/' . $merchant_id);
            exit();
        } 

        $this->merchants_m->delete_merchant_contact($contact['id']);
        
        $this->session->set_flashdata('success_msg', 'Merchant contact has been successfully removed.');
        
        redirect('/merchants/profile/' . $merchant_id);
        exit();
    }
    
    /**
     * Form that allows users a way to edit the details for a merchant.
     * 
     * @author Christophe
     */
    public function edit($merchant_id)
    {
        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        $this->load->library('Trackstreet_data');
        $this->load->model('merchants_m');

        $this->_layout = 'modal';

        $merchant_id = intval($merchant_id);
        
        $this->form_validation->set_rules('contact_email', 'Email', 'trim|xss_clean');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|xss_clean');
        $this->form_validation->set_rules('fax', 'Fax', 'trim|xss_clean');
        $this->form_validation->set_rules('address_1', 'Address Line 1', 'trim|xss_clean');
        $this->form_validation->set_rules('address_2', 'Address Line 2', 'trim|xss_clean');        
        $this->form_validation->set_rules('city', 'city', 'trim|xss_clean');
        $this->form_validation->set_rules('state', 'State/Provence', 'trim|xss_clean');
        $this->form_validation->set_rules('zip', 'ZIP/Postal Code', 'trim|xss_clean');
        
        $merchant = $this->merchants_m->get_merchant_by_id($merchant_id);
        
        if (empty($merchant))
        {
            $this->session->set_flashdata('error_msg', 'A record for this merchant could not be found or is no longer available.');
            
            redirect('/');
            exit();
        }
                
        if ($this->form_validation->run() == FALSE)
        {
            // validation failed, or first load
        }
        else
        {
            //var_dump($_POST); exit();
            
            $merchant = $this->merchants_m->get_merchant_by_id($merchant_id);
            
            // updated merchant data
            $update_data = array(
                'contact_email' => $this->input->post('contact_email', TRUE),                            
                'phone' => $this->input->post('phone', TRUE),   
                'fax' => $this->input->post('fax', TRUE),
            		'address_1' => $this->input->post('address_1', TRUE),
            		'address_2' => $this->input->post('address_2', TRUE),
                'city' => $this->input->post('city', TRUE),
                'state' => $this->input->post('state', TRUE),
                'zip' => $this->input->post('zip', TRUE),                                                                                    
            		'modified_at' => date('Y-m-d H:i:s'),
            		'modified_by' => $this->user_id
            );
             
            $this->merchants_m->update_merchant($merchant_id, $update_data);
            
            $this->trackstreet_data->record_data_changes($this->user_id, $merchant_id, 'crowl_merchant_name_new', $update_data, $merchant);
             
            $this->session->set_flashdata('success_msg', 'Merchant has been successfully updated.');
            
            redirect('/merchants/edit/' . $merchant_id);
            exit();
        } 
        
        $merchant = $this->merchants_m->get_merchant_by_id($merchant_id);
        
        if ($merchant['seller_id'] != $merchant['marketplace'])
        {
            $merchant_name = $merchant['original_name'] . ' (' . ucfirst($merchant['marketplace']) . ' seller)';
        }
        else
        {
            $merchant_name = $merchant['original_name'] . '.com';
        }

        $this->data->merchant_name = $merchant_name;
        $this->data->merchant_id = $merchant_id;
        $this->data->merchant = $merchant;
    }
    
    /**
     * Page where customers can get details on the export process to get
     * their merchant and contact data.
     * 
     * @author Christophe
     */
    public function export()
    {
        // see view file
    }
    
    /**
     * Form where user can add or edit a contact.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $type_id
     * @param int $existing_contact_id
     */
    public function edit_contact($merchant_id, $type_id, $existing_contact_uuid = FALSE)
    {
        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        $this->load->model('merchants_m');
        $this->load->model('crowl_merchant_name_m');
        
        $this->_layout = 'modal';
        
        $merchant_id = intval($merchant_id);
        $type_id = intval($type_id);
        
        $action = $existing_contact_uuid == FALSE ? 'create' : 'edit';
        
        $merchant = $this->crowl_merchant_name_m->get_merchant_by_id($merchant_id);
        
        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', 'Email Address', 'trim|xss_clean');
        $this->form_validation->set_rules('phone', 'Phone #', 'trim|xss_clean');
        
        // check to see if we are creating a new contact or editing an existing contact
        if ($existing_contact_uuid == FALSE)
        {
            // create a new contact record
            $contact = array(
                'first_name' => '',
                'last_name' => '',
                'email' => '',
                'phone' => ''                                                    
            );
            
            $form_action = '/merchants/edit_contact/' . $merchant_id . '/' . $type_id;
        }
        else
        {            
            $contact = $this->merchants_m->get_merchant_contact_by_uuid($existing_contact_uuid);
            
            $contact_id = intval($contact['id']);
            
            // check to see if user can edit contact
            if ($this->role_id != 2 || $this->store_id != intval($contact['store_id']))
            {
                $this->session->set_flashdata('error_msg', 'Error: Your account does not have access to this item.');
                
                redirect('/merchants/profile/' . $merchant_id);
                exit();
            }
            
            $form_action = '/merchants/edit_contact/' . $merchant_id . '/' . $type_id . '/' . $existing_contact_uuid;
        }
        
        if ($this->form_validation->run() == FALSE)
        {
            // validation failed, or first load
        }
        else
        {
            //var_dump($_POST); exit();
            
            if ($existing_contact_uuid == FALSE)
            {
                // insert new contact
                $insert_data = array(
                    'uuid' => uuid(),
                    'merchant_id' => $merchant_id,
                    'store_id' => $this->store_id,      
                    'type_id' => $type_id,                                          
                		'first_name' => $this->input->post('first_name', TRUE),
                		'last_name' => $this->input->post('last_name', TRUE),
                		'email' => $this->input->post('email', TRUE),
                		'phone' => $this->input->post('phone', TRUE),
                    'created' => date('Y-m-d H:i:s'),
                    'created_by' => $this->user_id,
                    'modified' => date('Y-m-d H:i:s'),
                    'modified_by' => $this->user_id             
                );
                 
                $this->merchants_m->insert_merchant_contact($insert_data);
                 
                $this->session->set_flashdata('success_msg', 'Merchant contact has been successfully added.');    

                redirect('/merchants/edit_contact/' . $merchant_id . '/' . $type_id);
                exit();
            }
            else
            {
                // update existing contact
                $update_data = array(
                		'first_name' => $this->input->post('first_name', TRUE),
                		'last_name' => $this->input->post('last_name', TRUE),
                		'email' => $this->input->post('email', TRUE),
                    'phone' => $this->input->post('phone', TRUE),
                    'modified' => date('Y-m-d H:i:s'),
                    'modified_by' => $this->user_id                                
                );
                 
                $this->merchants_m->update_merchant_contact($contact_id, $update_data);
                 
                $this->session->set_flashdata('success_msg', 'Merchant contact has been successfully updated.');

                redirect('/merchants/edit_contact/' . $merchant_id . '/' . $type_id . '/' . $existing_contact_uuid);
                exit();
            }           
        }        
        
        switch ($type_id)
        {
            case 1:
                $contact_type = 'Primary Contact';
                break;
            case 2:
                $contact_type = 'Account Rep Contact';
                break;
            case 3:
                $contact_type = 'Email CC Address Contact';
                break;
        }
        
        $this->data->action = $action;
        $this->data->form_action = $form_action;
        $this->data->contact_type = $contact_type;
        $this->data->merchant = $merchant;
        $this->data->contact = $contact;
        $this->data->merchant_id = $merchant_id;
    }
    
    /**
     * Export a data file where customers can maintain details on each of their merchants.
     * 
     * @author Christophe
     * @param string $type
     */
    public function export_merchants_file($type = 'csv')
    {
        $this->load->model('merchants_m');
        $this->load->model('merchant_products_m');
        $this->load->model('violator_m');
        
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=merchants.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');
        
        // output the column headings
        $column_headings = array(
            'Merchant ID', 
            'Merchant Name', 
            'Merchant Marketplace', 
            'Email',
            'Phone',
            'Fax',
            'Address 1',
            'Address 2',
            'City',
            'State',
            'Zip',                                                            
            'Website',
            'Send Violation Notifications',
            'Notification Template to Use',
            'Send to Primary Contact(s)',
            'Send to Account Rep(s)',
            'Send to CC Address(es)'
        );
        
        fputcsv($output, $column_headings);
        
        // get existing merchant contact data for store
        // query violator_notifications table for merchant data
        $merchants = $this->merchants_m->get_all_merchants_for_store($this->store_id);
        
        //var_dump($merchants); exit();
        
        $csv_data_rows = array();
        
        foreach ($merchants as $merchant)
        {
            $merchant_id = intval($merchant['crowl_merchant_name_id']);
            
            $merchant_data = $this->merchants_m->get_merchant_by_id($merchant_id);
            
            $merchant = array_merge($merchant, $merchant_data);
            
            $merchant['original_name'] = isset($merchant['original_name']) ? $merchant['original_name'] : '';
            $merchant['marketplace'] = isset($merchant['marketplace']) ? $merchant['marketplace'] : '';
            $merchant['seller_id'] = isset($merchant['seller_id']) ? $merchant['seller_id'] : '';
            $merchant['contact_email'] = isset($merchant['contact_email']) ? $merchant['contact_email'] : '';
            $merchant['fax'] = isset($merchant['fax']) ? $merchant['fax'] : '';
            $merchant['merchant_url'] = isset($merchant['merchant_url']) ? $merchant['merchant_url'] : '';
            $merchant['address_1'] = isset($merchant['address_1']) ? $merchant['address_1'] : '';
            $merchant['address_2'] = isset($merchant['address_2']) ? $merchant['address_2'] : '';
            $merchant['city'] = isset($merchant['city']) ? $merchant['city'] : '';
            $merchant['state'] = isset($merchant['state']) ? $merchant['state'] : '';
            $merchant['zip'] = isset($merchant['zip']) ? $merchant['zip'] : '';
            
            if ($merchant['seller_id'] != $merchant['marketplace'])
            {
                $merchant_name = $merchant['original_name'] . ' (' . ucfirst($merchant['marketplace']) . ' seller)';
                
                $merchant_marketplace = ucfirst($merchant['marketplace']);
            }
            else
            {
                $merchant_name = str_replace('http://', '', $merchant['merchant_url']);
                
                $merchant_name = str_replace('https://', '', $merchant_name);
                
                $merchant_marketplace = '';
            }
            
            $merchant_name = preg_replace("/[\r\n]+/", "", $merchant_name);
            
            // check setting to see if we send violation notifications
            $notific_setting = $this->violator_m->get_violator_notification_by_store_merchant($this->store_id, $merchant_id);
            
            if (empty($notific_setting))
            {
                $notific_setting_onoff_value = 'y';
            }
            else
            {
                $notific_setting_onoff_value = intval($notific_setting['active']) == 1 ? 'y' : 'n';
            }
            
            // determine URL for merchant
            if ($merchant['original_name'] != $merchant['marketplace'])
            {
                $merchant['marketplace_url'] = $this->merchant_products_m->get_marketplace_seller_url($merchant, $merchant['marketplace']);
            }
            
            if ($merchant['original_name'] == $merchant['marketplace'])
            {
                $website_url = $merchant['merchant_url'];
            }
            else
            {
                $website_url = $merchant['marketplace_url'];
            }
            
            // known/unknown template
            $notification_template_type_setting = $this->merchants_m->get_merchant_setting_value($merchant_id, $this->store_id, 'notification_template_type');
            
            if ($notification_template_type_setting === NULL)
            {
                // check to see if marketplace seller or retailer
                $notification_template_type_value = $merchant['marketplace'] == $merchant['seller_id'] ? 'known_seller_template' : 'unknown_seller_template';
            }
            else
            {
                $notification_template_type_value = $notification_template_type_setting;
            }
            
            $notification_template_type_options = array(
            		//'known_seller_template' => 'Known Seller Message Template',
            		//'unknown_seller_template' => 'Unknown Seller Message Template'
             		'known_seller_template' => 1,
            		'unknown_seller_template' => 2                           
            );
            
            // enforcement protocol and contact informtion
            $send_to_primary_contact_setting = $this->merchants_m->get_merchant_setting_value($merchant_id, $this->store_id, 'send_to_primary_contact');
            $primary_contact_checkbox_value = $send_to_primary_contact_setting == 'true' ? 'y' : 'n';
            
            $send_to_account_rep_setting = $this->merchants_m->get_merchant_setting_value($merchant_id, $this->store_id, 'send_to_account_rep');
            $account_rep_checkbox_value = $send_to_account_rep_setting == 'true' ? 'y' : 'n';
            
            $send_to_cc_address_setting = $this->merchants_m->get_merchant_setting_value($merchant_id, $this->store_id, 'send_to_cc_address');
            $cc_address_checkbox_value = $send_to_cc_address_setting == 'true' ? 'y' : 'n';
            
            /*

            $column_headings = array(
                'Merchant ID', 
                'Merchant Name', 
                'Merchant Marketplace', 
                'Email',
                'Phone',
                'Fax',
                'Address 1',
                'Address 2',
                'City',
                'State',
                'Zip',                                                            
                'Website',
                'Send Violation Notifications',
                'Notification Template to Use',
                'Send to Primary Contact(s)',
                'Send to Account Rep(s)',
                'Send to CC Address(es)'
            );

            */            
            
            $csv_data_rows[] = array(
            		$merchant_id,
            		$merchant_name,
            		$merchant_marketplace,
                $merchant['contact_email'],
                $merchant['phone'],
                $merchant['fax'],
                $merchant['address_1'],
                $merchant['address_2'],
                $merchant['city'],
                $merchant['state'],
                $merchant['zip'],
                $website_url,
                $notific_setting_onoff_value,
                $notification_template_type_options[$notification_template_type_value],
                $primary_contact_checkbox_value,
                $account_rep_checkbox_value,
                $cc_address_checkbox_value              
            );
        }
        
        //var_dump($csv_data_rows); exit();
        
        foreach ($csv_data_rows as $row)
        {
            fputcsv($output, $row);
        }
        
        exit();        
    }
    
    /**
     * Generate a CSV file that customers can edit and use to put in their contacts with
     * so that we can later import it.
     * 
     * See:
     * 
     * http://stackoverflow.com/questions/217424/create-a-csv-file-for-a-user-in-php
     * http://code.stephenmorley.org/php/creating-downloadable-csv-files/
     * 
     * @author Christophe
     */
    public function export_contacts_file($type = 'csv')
    {
        $this->load->model('merchants_m');
        
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=merchant_contacts.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');
        
        // output the column headings
        $column_headings = array(
            'Contact ID', 
            'Merchant ID', 
            'Merchant Name', 
            'Merchant Marketplace', 
            'First Name', 
            'Last Name', 
            'Email', 
            'Phone Number', 
            'Contact Type'
        );
        
        fputcsv($output, $column_headings);
        
        // get existing merchant contact data for store
        // query violator_notifications table for merchant data
        $merchants = $this->merchants_m->get_all_merchants_for_store($this->store_id);
        
        //var_dump($merchants); exit();
        
        $csv_data_rows = array();
        
        foreach ($merchants as $merchant)
        {
            $merchant_id = intval($merchant['crowl_merchant_name_id']);
            
            $merchant_data = $this->merchants_m->get_merchant_by_id($merchant_id);
            
            $merchant = array_merge($merchant, $merchant_data);
            
            $merchant['original_name'] = isset($merchant['original_name']) ? $merchant['original_name'] : '';
            $merchant['marketplace'] = isset($merchant['marketplace']) ? $merchant['marketplace'] : '';
            $merchant['seller_id'] = isset($merchant['seller_id']) ? $merchant['seller_id'] : '';
            $merchant['contact_email'] = isset($merchant['contact_email']) ? $merchant['contact_email'] : '';
            $merchant['fax'] = isset($merchant['fax']) ? $merchant['fax'] : '';
            $merchant['merchant_url'] = isset($merchant['merchant_url']) ? $merchant['merchant_url'] : '';
            $merchant['address_1'] = isset($merchant['address_1']) ? $merchant['address_1'] : '';
            $merchant['address_2'] = isset($merchant['address_2']) ? $merchant['address_2'] : '';
            $merchant['city'] = isset($merchant['city']) ? $merchant['city'] : '';
            $merchant['state'] = isset($merchant['state']) ? $merchant['state'] : '';
            $merchant['zip'] = isset($merchant['zip']) ? $merchant['zip'] : '';            
            
            // check to see if merchant is marketplace seller or unique website
            if ($merchant['seller_id'] != $merchant['marketplace'])
            {
                $merchant_name = $merchant['original_name'] . ' (' . ucfirst($merchant['marketplace']) . ' seller)';
                
                $merchant_marketplace = ucfirst($merchant['marketplace']);
            }
            else
            {
                $merchant_name = str_replace('http://', '', $merchant['merchant_url']);
                
                $merchant_name = str_replace('https://', '', $merchant_name);
                
                $merchant_marketplace = '';
            }
            
            $merchant_name = preg_replace("/[\r\n]+/", "", $merchant_name);
            
            // check to see if they have already entered any contacts for merchant
            $contacts = $this->merchants_m->get_merchant_contacts_by_store($merchant_id, $this->store_id);
            
            if (empty($contacts))
            {
                // just put a starter row if they want to start putting in a contact
                $csv_data_rows[] = array(
                    '',
                    $merchant_id,
                    $merchant_name,
                    $merchant_marketplace,
                    //'Enter First Name',
                    //'Enter Last Name',
                    //'Enter Email',
                    //'Enter Phone Number',
                    '',
                    '',
                    '',
                    '',
                    1                                    
                );
            }
            else
            {
                // add 1 row for each contact
                foreach ($contacts as $contact)
                {
                    //$contact_label = $this->merchants_m->get_contact_label_from_type($contact['type_id']);
                    $contact_label = $contact['type_id'];
                    
                    $csv_data_rows[] = array(
                        $contact['uuid'],
                        $merchant_id,
                        $merchant_name,
                        $merchant_marketplace,
                        $contact['first_name'],
                        $contact['last_name'],
                        $contact['email'],
                        $contact['phone'],
                        $contact_label
                    );
                }
            }
        }
        
        //var_dump($csv_data_rows); exit();
        
        foreach ($csv_data_rows as $row)
        {
            fputcsv($output, $row);
        }
        
        exit();
    }
    
    /**
     * Page where users can see ways they can import merchant-related data.
     * 
     * @author Christophe
     */
    public function import()
    {
        
    }
    
    /**
     * Form where users can import their merchant data CSV file.
     * 
     * @author Christophe
     */
    public function import_merchant_data()
    {
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('Trackstreet_merchants');
        
        $this->form_validation->set_rules('csv_file', 'Merchant Data CSV File', '');        
        
        if ($this->form_validation->run() === FALSE)
        {
            // validation failed, or first load
        }
        else
        {
            //var_dump($_FILES); exit();
            
            if ($_FILES['csv_file']['tmp_name'])
            {
                $config['upload_path'] = $this->config->item('uploaded_files') . 'brand_csv_uploads/' . $this->store_id;

                //$brand_file = $this->config->item('uploaded_files') . 'brand_csv_uploads';

                $config['allowed_types'] = 'csv';
                $config['overwrite'] = TRUE;
                $config['remove_spaces'] = TRUE;
                $config['file_name'] = $_FILES['csv_file']['name'];
                
                //if(!is_dir($brand_file)) mkdir($brand_file, 0777);
                if (!is_dir($config['upload_path']))
                {
                    mkdir($config['upload_path'], 0777, TRUE);
                    
                    chmod($config['upload_path'], 0777);
                }

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('csv_file'))
                {
                    // go through their file and import the data
                    $file_name = $this->upload->file_name;
                    
                    $file_path = $config['upload_path'] . '/' . $file_name;
                    
                    $this->trackstreet_merchants->import_merchant_data($file_path, $this->store_id, $this->user_id);
                    
                    $this->session->set_flashdata('success_msg', 'Merchant data has been successfully imported.');
                    
                    redirect('/merchants/import_merchant_data');
                    exit();
                }
                else
                {
                    $this->session->set_flashdata('error_msg', 'Error: ' . $this->upload->display_errors());
                    
                    redirect('/merchants/import_merchant_data');
                    exit();
                }
            }
            else
            {
                $this->session->set_flashdata('error_msg', 'Error: Could not upload merchant data file.');
                
                redirect('/merchants/import_merchant_data');
                exit();
            }
        }
    }
    
    /**
     * Form where users can import their merchant contact data CSV file.
     *
     * @author Christophe
     */    
    public function import_merchant_contact_data()
    {
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('Trackstreet_merchants');

        $this->form_validation->set_rules('csv_file', 'Merchant Contact Data CSV File', '');

        if ($this->form_validation->run() === FALSE)
        {
            // validation failed, or first load
        }
        else
        {
            //var_dump($_FILES); exit();

            if ($_FILES['csv_file']['tmp_name'])
            {
                $config['upload_path'] = $this->config->item('uploaded_files') . 'brand_csv_uploads/' . $this->store_id;

                //$brand_file = $this->config->item('uploaded_files') . 'brand_csv_uploads';

                $config['allowed_types'] = 'csv';
                $config['overwrite'] = TRUE;
                $config['remove_spaces'] = TRUE;
                $config['file_name'] = $_FILES['csv_file']['name'];

                //if(!is_dir($brand_file)) mkdir($brand_file, 0777);
                if (!is_dir($config['upload_path']))
                {
                    mkdir($config['upload_path'], 0777, TRUE);

                    chmod($config['upload_path'], 0777);
                }

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('csv_file'))
                {
                    // go through their file and import the data
                    $file_name = $this->upload->file_name;

                    $file_path = $config['upload_path'] . '/' . $file_name;

                    $this->trackstreet_merchants->import_merchant_contact_data($file_path, $this->store_id, $this->user_id);

                    $this->session->set_flashdata('success_msg', 'Merchant contact data has been successfully imported.');

                    redirect('/merchants/import_merchant_contact_data');
                    exit();
                }
                else
                {
                    $this->session->set_flashdata('error_msg', 'Error: ' . $this->upload->display_errors());

                    redirect('/merchants/import_merchant_contact_data');
                    exit();
                }
            }
            else
            {
                $this->session->set_flashdata('error_msg', 'Error: Could not upload merchant contact data file.');

                redirect('/merchants/import_merchant_contact_data');
                exit();
            }
        }
    }
    
    /**
     * Import contact data for merchants (from Andrew's CSV file), and add
     * data to crowl_merchant_name_new table.
     * 
     * @author Christophe
     */
    public function import_trackstreet_contact_data()
    {
        $this->load->model('merchants_m');
        
        //var_dump(FCPATH); exit();
        
        //$csv_file = FCPATH . 'migrations/import/merchant_contact_information_10132015.csv';
        $csv_file = FCPATH . 'migrations/import/merchant_contact_information_10212015.csv';
        
        $inserted_merchants = array();
        $updated_merchants = array();
        
        $handle = fopen($csv_file, "r");
        
        $i = 0;
        
        if (empty($handle) === false) 
        {
            // go through each row and see if we can find existing merchant to add data to
            // if merchant not found, create new merchant record and set status to 2 (pending review)
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE)
            {
                /*
                 * // need to adjust these columns to the values of the CSV file
                 * 0 = ID
                 * 1 = Merchant URL
                 * 2 = Address 1 / Street
                 * 3 = City
                 * 4 = State/Province
                 * 5 = Zip Code
                 * 6 = Phone
                 * 7 = Fax
                 * 8 = Email
                 * 9 = Remarks -- don't add
                 */        
                if ($i > 0) // skip header row
                {
                    //var_dump($data); exit();
                    
                    $merchant_url = strtolower($data[1]);
                    
                    $original_name = str_replace('http://', '', $merchant_url);
                    $original_name = str_replace('https://', '', $original_name);
                    $original_name = str_replace('www.', '', $original_name);                    
                    $original_name = str_replace('.com', '', $original_name);
                    $original_name = str_replace('.net', '', $original_name);
                    $original_name = str_replace('.us', '', $original_name);
                    $original_name = str_replace('.biz', '', $original_name);
                    
                    $merchant_id = intval($data[0]);
                    
                    $merchant = $this->merchants_m->get_merchant_by_id($merchant_id);
                    //$merchant = $this->merchants_m->get_merchant_by_original_name($original_name);
                    
                    $address_1 = $data[2] == 'N/A' || $data[2] == '' ? '' : trim($data[2]);
                    $city = $data[3] == 'N/A' || $data[3] == '' ? '' : trim($data[3]);
                    $state = $data[4] == 'N/A' || $data[4] == '' ? '' : trim($data[4]);
                    $zip = $data[5] == 'N/A' || $data[5] == '' ? '' : trim($data[5]);
                    $phone = $data[6] == 'N/A' || $data[6] == '' ? '' : trim($data[6]);
                    $fax = $data[7] == 'N/A' || $data[7] == '' ? '' : trim($data[7]);                    
                    $contact_email = $data[8] == 'N/A' || $data[8] == '' ? '' : str_replace('mailto:', '', trim($data[8]));
                    
                    if (!empty($merchant))
                    {
                        // update with contact data from CSV
                        $update_data = array(
                            'address_1' => $address_1,
                            'city' => $city,
                            'state' => $state,
                            'zip' => $zip,  
                            'phone' => $phone,
                            'fax' => $fax,                                        
                            'contact_email' => $contact_email,      
                            'modified_by' => 0,
                            'modified_at' => date('Y-m-d H:i:s'),                                            
                        );
                        
                        $this->merchants_m->update_merchant($merchant['id'], $update_data);
                        
                        $updated_merchants[] = $data;
                    }
                    else
                    {
                        // insert new merchant record with contact data from CSV
                        $insert_data = array(
                            'merchant_name' => $original_name,
                            'original_name' => $original_name,
                            'marketplace' => $original_name,
                            'seller_id' => $original_name,                        
                            'merchant_url' => $merchant_url,
                            'phone' => $phone,
                            'fax' => $fax,
                            'address_1' => $address_1,
                            'city' => $city,
                            'state' => $state,
                            'zip' => $zip,
                            'contact_email' => $contact_email,
                            'created' => date('Y-m-d H:i:s'),
                            'created_at' => date('Y-m-d H:i:s'),
                            'user_id' => 0,
                            'modified_by' => 0,
                            'modified_at' => date('Y-m-d H:i:s'),                                                            
                            'status' => 2                                                                                            
                        );
                        
                        $this->merchants_m->insert_merchant($insert_data);
                        
                        $inserted_merchants[] = $data;
                    }
                }
                
                $i++;
            }
            
            fclose($handle);
        }
        
        //var_dump($updated_merchants); exit();
        $this->data->updated_merchants = $updated_merchants;
        $this->data->inserted_merchants = $inserted_merchants;
    }
    
    /**
     * Full list of all merchants that are selling products for this store.
     * 
     * @author Christophe
     */
    public function index()
    {
        //ini_set('memory_limit', '512M');
        
        $this->load->model('merchants_m');
        $this->load->model('merchant_products_m');
        
        $this->page_title = 'Merchants - TrackStreet';
        
        // check search criteria        
        if (!empty($_POST))
        {
            //var_dump($_POST); exit();
            
            $merchant_type_post = $this->input->post('merchant_type', TRUE);
            
            $merchant_type = $merchant_type_post == FALSE || empty($merchant_type_post) ? 'all' : $merchant_type_post;
            
            if (isset($_POST['time_frame']))
            {
                $time_frame = intval($_POST['time_frame']);
                
                switch ($time_frame)
                {
                    case 24:
                        $start_date = date('Y-m-d', strtotime('-1 days'));
                        break;
                    case 7:
                        $start_date = date('Y-m-d', strtotime('-7 days'));
                        break;
                    case 30:
                        $start_date = date('Y-m-d', strtotime('-30 days'));
                        break;
                }
                
                $end_date = date('Y-m-d');
                
                $this->data->crawl_start = $start_date . ' 00:00:00';
                $this->data->crawl_end = $end_date . ' 23:59:59';
                
                
            }
            else
            {
                $this->data->crawl_start = $_POST['date_from'] . ' 00:00:00';
                $this->data->crawl_end = $_POST['date_to'] . ' 23:59:59';
                
                $time_frame = 0;
            }

            $start_date = $_POST['date_from'];
            $end_date = $_POST['date_to'];
        }
        else
        {
            $merchant_type = 'all';
            
            $start_date = date('Y-m-d', strtotime('-1 days'));
            $end_date = date('Y-m-d');
            
            $this->data->crawl_start = $start_date . ' 00:00:00';
            $this->data->crawl_end = $end_date . ' 23:59:59';
            
            $time_frame = 24;
        }
        
        $this->data->date_from = date('Y-m-d', strtotime($this->data->crawl_start));
        $this->data->date_to = date('Y-m-d', strtotime($this->data->crawl_end));
        
        $merchants = $this->merchants_m->get_merchants_by_store($this->store_id, $merchant_type, $this->data->crawl_start, $this->data->crawl_end);        
        
        for ($i = 0; $i < count($merchants); $i++)
        {
            if ($merchants[$i]['original_name'] != $merchants[$i]['marketplace'])
            {
                $merchants[$i]['marketplace_url'] = $this->merchant_products_m->get_marketplace_seller_url($merchants[$i], $merchants[$i]['marketplace']);
            }
            
            // get number of products currently being sold
            $merchants[$i]['product_count'] = $this->merchants_m->get_product_count_for_merchant($merchants[$i]['id'], $this->store_id);
            
            // get date for when this merchant started get recorded for this store
            $first_tracking_row = $this->merchants_m->get_first_merchant_store_record($merchants[$i]['id'], $this->store_id);
            
            $merchants[$i]['tracking_started'] = empty($first_tracking_row) ? FALSE : $first_tracking_row['select_date'];
            
            $merchants[$i]['human_name'] = $this->merchants_m->get_merchant_human_name($merchants[$i]);
        }
        
        // used for report exporting
        $this->data->report_name = 'Merchants';
        $this->data->file_name = str_replace(' ', '_', $this->data->report_name);
        $this->data->icon = 'ico-report';
        $this->data->widget = 'mv-report';
        
        $this->data->report_info = array('report_name' => $this->data->report_name);
        $this->data->report_where = array(
        		'report_function' => 'index',
        		'fromDate' => $start_date,
        		'toDate' => $end_date,
        		'report_type' => 'merchants'
        );        
        
        $this->data->merchants = $merchants;
        $this->data->merchant_type = $merchant_type;
        $this->data->time_frame = $time_frame;
    }
    
    /**
     * Page that displays all of our merchants.
     * 
     * @author Christophe
     */
    public function get_all()
    {
        $this->load->model('merchants_m');
        
        $merchants = $this->merchants_m->get_all_merchants();
        
        $this->data->merchants = $merchants;
    }
    
	/**
     * Handle bulk actions.
     * 
     * @author Mindy
     */
    public function bulk_action()
    {
        $this->load->library('Vision_merchants');
        
        //var_dump($_POST); exit();
        
        $merchant_ids = $_POST['select_merchant_id'];
        
            if (isset($merchant_ids) || !empty($merchant_ids))
            {
                $bulk_action = intval($_POST['merchants-bulk-action-select']);
                
                switch($bulk_action)
                {
                    case 0:
                        // Turn On Violation Notifications
                        $this->vision_merchants->turnon_merchants($merchant_ids, $this->store_id);
                        
                        $this->session->set_flashdata('success_msg', 'Merchants are now being tracked.');
                        break;
                    case 1:
                        // Turn Off Violation Notifications
                        $this->vision_merchants->turnoff_merchants($merchant_ids, $this->store_id);
                        
                        $this->session->set_flashdata('success_msg', 'Merchants are no longer being tracked.');
                        break;
                    default:
                        $this->session->set_flashdata('error_msg', 'Error: Please select a bulk action.');
                }
            }
            else
            {
                $this->session->set_flashdata('error_msg', 'Error: No Merchants were selected.');
            }
        
        redirect('/merchants');
        exit();
    }
    
    /**
     * Page where a user can request removal of a merchant association link.
     * 
     * @author Christophe
     * @param int $parent_merchant_id
     * @param int $child_merchant_id
     */
    public function link_removal_request($parent_merchant_id, $child_merchant_id)
    {
        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        $this->load->library('Vision_users');
        $this->load->model('merchants_m');
        
        $this->_layout = 'modal';        
        
        $parent_merchant_id = intval($parent_merchant_id);
        $child_merchant_id = intval($child_merchant_id);
        
        $parent_merchant = $this->merchants_m->get_merchant_by_id($parent_merchant_id);
        $child_merchant = $this->merchants_m->get_merchant_by_id($child_merchant_id);
        
        if ($parent_merchant['seller_id'] != $parent_merchant['marketplace'])
        {
            $parent_merchant['merchant_name'] = $parent_merchant['original_name'] . ' (' . ucfirst($parent_merchant['marketplace']) . ' seller)';
        }
        else
        {
            $parent_merchant['merchant_name'] = $parent_merchant['original_name'] . '.com';
        }
        
        if ($child_merchant['seller_id'] != $child_merchant['marketplace'])
        {
            $child_merchant['merchant_name'] = $child_merchant['original_name'] . ' (' . ucfirst($child_merchant['marketplace']) . ' seller)';
        }
        else
        {
            $child_merchant['merchant_name'] = $child_merchant['original_name'] . '.com';
        }        
        
        $this->form_validation->set_rules('reason', 'Reason Field', 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == FALSE)
        {
            // validation failed, or first load
        }
        else
        {
            $reason = $this->input->post('reason', TRUE);
            
            $email = $this->config->item('environment') == 'production' ? 'andrew@juststicky.com' : 'christophe@trackstreet.com,csautot@gmail.com';
             
            $subject = '[TrackStreet] Request for Merchant Association Link Removal';            

            $html_message = "
                <p>
                    Requested by {$this->user['first_name']} {$this->user['last_name']}
                    (Company: {$this->user['company_name']} - Email: {$this->user['email']})
                </p>
                <p>
                    Parent merchant: {$parent_merchant['merchant_name']} (ID: {$parent_merchant_id})
                </p>
                <p>
                    Child merchant: {$child_merchant['merchant_name']} (ID: {$child_merchant_id})
                </p>
                <p>
                    Requested on: https://app.trackstreet.com/merchants/profile/{$parent_merchant_id}
                </p>
                <p>
                    <b>Reason:</b>
                </p>    
                <p>
                    {$reason}
                </p>
            ";
             
            $text_message = strip_tags($html_message);
             
            $this->vision_users->sendSESEmail($email, $subject, $html_message, $text_message);
            
            $this->session->set_flashdata('success_msg', 'Your request has been successfully submitted and our support team will get back to your soon.');
            
            redirect('/merchants/link_removal_request/' . $parent_merchant_id . '/' . $child_merchant_id);
            exit();
        }            
        
        $this->data->parent_merchant_id = $parent_merchant_id;
        $this->data->child_merchant_id = $child_merchant_id;
        $this->data->parent_merchant = $parent_merchant;
        $this->data->child_merchant = $child_merchant;        
    }
    
    /**
     * This handles AJAX call to search for merchants in the database.
     * 
     * @author Christophe
     */
    public function merchant_search_auto_complete()
    {
        $this->load->model('merchants_m');
        $this->load->model('merchant_products_m');
        
        $this->_layout = 'ajax_html';

        $search_str = $this->input->post('search_str', TRUE);
        $current_merchant_id = intval($this->input->post('current_merchant_id', TRUE));
        
        // this is the merchant that we are adding links to
        $merchant = $this->merchants_m->get_merchant_by_id($current_merchant_id);
        
        // see if we already auto-linked to these merchants
        $already_linked_merchants = $this->merchants_m->get_other_merchants_by_original_name($merchant['original_name'], $current_merchant_id);
        
        $already_linked_merchant_ids = array();
        
        foreach ($already_linked_merchants as $linked_merchant)
        {
            $already_linked_merchant_ids[] = intval($linked_merchant['id']);
        }
        
        $final_merchant_array = array();
        
        // find merchants they may want to link to by using search string typed in by user
        $merchants = $this->merchants_m->merchant_search($search_str);
        
        // go through and add note about marketplace seller or add .com to name
        for ($i = 0; $i < count($merchants); $i++)
        {
            if (
                intval($merchants[$i]['parent_merchant_id']) == $current_merchant_id ||
                intval($merchants[$i]['id']) == $current_merchant_id ||
                in_array(intval($merchants[$i]['id']), $already_linked_merchant_ids)                        
            )
            {
                continue;
            }
            else
            {
                $merchants[$i]['merchant_real_name'] = $merchants[$i]['original_name'] == $merchants[$i]['marketplace'] ? $merchants[$i]['merchant_name'] . '.com' : $merchants[$i]['merchant_name'];
                $merchants[$i]['product_count'] = $this->merchants_m->get_product_count_for_merchant($merchants[$i]['id'], $this->store_id);
                
                if ($merchants[$i]['original_name'] != $merchants[$i]['marketplace'])
                {
                    $merchants[$i]['marketplace_url'] = $this->merchant_products_m->get_marketplace_seller_url($merchants[$i], $merchants[$i]['marketplace']);
                }
                
                $final_merchant_array[] = $merchants[$i];
            }
        }
        
        $this->data->merchants = $final_merchant_array;
    }
    
    /**
     * Run this to get data from violator_notifications table and insert/update rows in merchant_contacts table.
     * This is used to move contact data from the violator_notifications table.
     * 
     * @author Christophe
     */
    /*
    public function migrate_existing_contact_data()
    {
        $this->load->library('Trackstreet_merchants');
        $this->load->model('merchants_m');
        
        // get old contact data that we will migrate to new tables
        $merchant_contacts = $this->merchants_m->get_all_violator_notification_contacts();
        
        $migrated_count = 0;
        $created_count = 0;
        $updated_count = 0;
        
        foreach ($merchant_contacts as $merchant_contact)
        {
            if ($merchant_contact['email_to'] == '' && $merchant_contact['name_to'] == '')
            {
                // skip
            }
            else
            {
                $full_name = trim($merchant_contact['name_to']);
                
                $name = explode(' ', $full_name);
                
                if (isset($name[1]))
                {
                    $last_name = $name[1];
                }   
                else
                {
                    $last_name = '';
                } 
                
                $merchant_data = array(
                    'first_name' => $name[0],                
                    'last_name' => $last_name,
                    'email' => trim($merchant_contact['email_to'])                       
                );
                
                // update or insert merchant contact
                $return_data = $this->trackstreet_merchants->save_old_merchant_contact($merchant_contact['crowl_merchant_name_id'], $merchant_contact['store_id'], $merchant_data);
                
                if ($return_data['action'] == 'created')
                {
                    $created_count++;
                }
                else
                {
                    $updated_count++;
                }
                
                echo $return_data['contact_email'] . "\n";
                
                // save setting that we need to email Primary Contact for merchant
                $this->trackstreet_merchants->save_merchant_setting($merchant_contact['crowl_merchant_name_id'], $merchant_contact['store_id'], 0, 'send_to_primary_contact', 'true');
                
                $migrated_count++;
            }
        }
        
        echo 'Records Moved: ' . $migrated_count . "\n";
        echo 'Records Created: ' . $created_count . "\n";
        echo 'Records Updated: ' . $updated_count . "\n";
        
        exit();
    }
    */
    
    /**
     * Profile details on a single merchant.
     *
     * @author Christophe
     * @param int $merchant_id
     */
    public function profile($merchant_id)
    {
        $this->load->helper('form');
        $this->load->library('trackstreet_merchants');
        $this->load->model('crowl_merchant_name_m');
        $this->load->model('merchant_products_m');
        $this->load->model('violator_m');
        $this->load->model('products_trends_m');
        $this->load->model('merchants_m');
        $this->load->model('merchant_products_m');
        $this->load->model('users_m');
        
        $merchant_id = intval($merchant_id);
        
        if ($this->config->item('environment') == 'local')
        {
            $this->data->google_maps_key = 'AIzaSyAN0om9mFmy1QN6Wf54tXAowK4eT0ZUPrU';
        }
        else
        {
            // needed to do turn on static and embed APIs: http://stackoverflow.com/questions/19408066/the-google-maps-api-server-rejected-your-request
            // see https://console.developers.google.com/project/onyx-badge-109921/apiui/credential/key/0
            $this->data->google_maps_key = 'AIzaSyDMXKZ3IFikQUcYR6EjQ_tQEF5AxjzNgI4'; // trackstreet.com key
            //$this->data->google_maps_key = 'AIzaSyAN0om9mFmy1QN6Wf54tXAowK4eT0ZUPrU';
        }
        
        $merchant = $this->merchants_m->get_merchant_by_id($merchant_id);
        
        if (empty($merchant))
        {
            $this->session->set_flashdata('error_msg', 'Error: Merchant not found.');
            
            redirect('/merchants');
            exit();
        }
        
        if ($merchant['address_1'] != '' && $merchant['city'] != '' && $merchant['state'] != '')
        {
            // 123+State+Street,+Santa+Barbara,+CA,
            $merchant['google_map_query'] = 
                str_replace(' ', '+', $merchant['address_1']) . ',+' . 
                str_replace(' ', '+', $merchant['city']) . ',+' .
                str_replace(' ', '+', $merchant['state']);
            
            if ($merchant['zip'])
            {
                $merchant['google_map_query'] .= ',+' . $merchant['zip'];
            }
        }
        else
        {
            $merchant['google_map_query'] = '';
        }
        
        //var_dump($merchant); exit();
        
        // find products connected to merchant
        $products = $this->merchant_products_m->get_products_by_merchant($merchant_id, $this->store_id);
        
        $violator_notification_data = $this->violator_m->get_violator_notification_data($merchant_id, $this->store_id);
        
        $first_tracking = $this->merchants_m->get_first_merchant_store_record($merchant_id, $this->store_id);
        $first_tracking_date = empty($first_tracking) ? 'N/A' : date('m/d/Y', strtotime($first_tracking['select_date']));
        
        $last_violation = $this->merchants_m->get_last_violation($merchant_id, $this->store_id);
        $last_violation_date = empty($last_violation) ? 'None' : date('m/d/Y', strtotime($last_violation['select_date']));
        
        // get product index counts for store merchant for past 30 days
        if ($this->config->item('environment') == 'local')
        {
            $start_date = '2015-06-24';
            $end_date = '2015-07-17';
        }
        else
        {
            $start_date = date('Y-m-d', strtotime('-30 days'));
            $end_date = date('Y-m-d', strtotime('-1 day'));
        }
        
        $product_data_rows = $this->merchants_m->get_products_listed_for_date_range($merchant_id, $this->store_id, $start_date, $end_date);
        
        //var_dump($product_data_rows); exit();
        
        // find internal staff notes
        $staff_notes = $this->merchants_m->get_staff_notes($merchant_id, $this->store_id);
        
        //echo $this->db->last_query(); exit();
        
        for ($i = 0; $i < count($staff_notes); $i++)
        {
            $staff_notes[$i]['user'] = $this->users_m->get_user_by_id($staff_notes[$i]['user_id']);
        }
        
        // get other merchant records that are similar
        $other_merchants = $this->merchants_m->get_other_merchants_by_original_name($merchant['original_name'], $merchant_id);
        
        $child_merchants = $this->merchants_m->get_other_merchants_by_parent_id($merchant_id);
         
        $other_merchants = array_merge($other_merchants, $child_merchants);
        
        // for some reason we have a merchant record in our DB with ID = 0
        if (intval($merchant['parent_merchant_id']) > 0)
        {
            $parent_merchant = $this->merchants_m->get_merchant_by_id($merchant['parent_merchant_id']);
        }
        else
        {
            $parent_merchant = array();
        }
        
        if (!empty($parent_merchant))
        {
            $other_merchants[] = $parent_merchant;
        }   
       
        // Check to see if this merchant has sold by other names via marketplace.
        // This would only list marketplace profiles that are in the same marketplace but where merchant changed their name.
        $other_marketplace_merchants = $this->merchants_m->get_other_marketplace_profiles($merchant_id, $merchant['seller_id'], $merchant['marketplace']);
        
        // determine URL for merchant
        if ($merchant['original_name'] != $merchant['marketplace'])
        {
            $merchant['marketplace_url'] = $this->merchant_products_m->get_marketplace_seller_url($merchant, $merchant['marketplace']);
        }
        
        // determine setting for on/off with notifications for merchant
        $notific_setting = $this->violator_m->get_violator_notification_by_store_merchant($this->store_id, $merchant_id);
        
        if (empty($notific_setting))
        {
            $notific_setting_onoff_value = 'on';
        }
        else
        {
            $notific_setting_onoff_value = intval($notific_setting['active']) == 1 ? 'on' : 'off';
        }
        
        $notification_onoff_options = array('on' => 'On', 'off' => 'Off');
        
        $this->data->notifications_onoff_dropdown = form_dropdown('notifications_onoff', $notification_onoff_options, $notific_setting_onoff_value);
        
        // enforcement protocol and contact informtion
        $send_to_primary_contact_setting = $this->merchants_m->get_merchant_setting_value($merchant_id, $this->store_id, 'send_to_primary_contact');
        $primary_contact_checkbox_value = $send_to_primary_contact_setting == 'true' ? TRUE : FALSE;
        
        $send_to_account_rep_setting = $this->merchants_m->get_merchant_setting_value($merchant_id, $this->store_id, 'send_to_account_rep');
        $account_rep_checkbox_value = $send_to_account_rep_setting == 'true' ? TRUE : FALSE;
        
        $send_to_cc_address_setting = $this->merchants_m->get_merchant_setting_value($merchant_id, $this->store_id, 'send_to_cc_address');
        $cc_address_checkbox_value = $send_to_cc_address_setting == 'true' ? TRUE : FALSE;    	
        
        // known/unknown template
        $notification_template_type_setting = $this->merchants_m->get_merchant_setting_value($merchant_id, $this->store_id, 'notification_template_type');
        
        if ($notification_template_type_setting === NULL)
        {
            // check to see if marketplace seller or retailer
            $notification_template_type_value = $merchant['marketplace'] == $merchant['seller_id'] ? 'known_seller_template' : 'unknown_seller_template';
        }
        else
        {
            $notification_template_type_value = $notification_template_type_setting;
        }
        
        $notification_template_type_options = array(
            'known_seller_template' => 'Known Seller Message Template',
            'unknown_seller_template' => 'Unknown Seller Message Template'                            
        );
        
        $this->data->notification_template_dropdown = form_dropdown('notification_template_type', $notification_template_type_options, $notification_template_type_value);
        
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
        
        // get contacts that are connected to this merchant
        $primary_contacts = $this->merchants_m->get_merchant_contacts_by_type($merchant_id, $this->store_id, 1);
        $account_rep_contacts = $this->merchants_m->get_merchant_contacts_by_type($merchant_id, $this->store_id, 2);
        $cc_address_contacts = $this->merchants_m->get_merchant_contacts_by_type($merchant_id, $this->store_id, 3);
        
        $merchant_profile_name = $this->merchants_m->get_merchant_human_name($merchant, TRUE);
        
        $this->page_title = 'TrackStreet Profile - ' . $merchant_profile_name;
        
        // view variables
        $this->data->other_marketplace_merchants = $other_marketplace_merchants;
        $this->data->merchant_marketplace = $merchant['marketplace'];
        $this->data->merchant_profile_name = $merchant_profile_name;
        $this->data->primary_contacts = $primary_contacts;
        $this->data->account_rep_contacts = $account_rep_contacts;
        $this->data->cc_address_contacts = $cc_address_contacts;
        $this->data->primary_contact_checkbox_value = $primary_contact_checkbox_value;
        $this->data->account_rep_checkbox_value = $account_rep_checkbox_value;
        $this->data->cc_address_checkbox_value = $cc_address_checkbox_value;
        $this->data->staff_notes = $staff_notes;
        $this->data->other_merchants = $other_merchants;
        $this->data->merchant_id = $merchant_id;
        $this->data->product_data_rows = $product_data_rows;
        $this->data->first_tracking_date = $first_tracking_date;
        $this->data->last_violation_date = $last_violation_date;
        $this->data->violator_notification_data = $violator_notification_data;
        $this->data->current_level = $current_level;
        $this->data->violation_level_dropdown = $violation_level_dropdown;
        $this->data->merchant = $merchant;
        $this->data->products = $products;
    }
    
    /**
     * Products listed details on a single merchant.
     *
     * @author Christophe
     * @param int $merchant_id
     */
    public function profile_products($merchant_id)
    {
        $this->load->model('crowl_merchant_name_m');
        $this->load->model('merchant_products_m');
        $this->load->model('violator_m');
        $this->load->model('products_trends_m');
        $this->load->model('merchants_m');
        $this->load->model('users_m');

        $merchant_id = intval($merchant_id);

        if ($this->config->item('environment') == 'local')
        {
            $start_date = '2015-08-01';
            $end_date = '2015-08-03';
        }
        else
        {
            $start_date = date('Y-m-d', strtotime('-30 days'));
            $end_date = date('Y-m-d', strtotime('-1 day'));
        }
        
        // check search criteria
        if (!empty($_POST))
        {
            //var_dump($_POST); exit();
            
            $this->data->crawl_start = $_POST['date_from'] . ' 00:00:00';
            $this->data->crawl_end = $_POST['date_to'] . ' 23:59:59';

            $start_date = $_POST['date_from'];
            $end_date = $_POST['date_to'];
        }
        else
        {
            $start_date = date('Y-m-d', strtotime('-30 days'));
            $end_date = date('Y-m-d');

            $this->data->crawl_start = $start_date . ' 00:00:00';
            $this->data->crawl_end = $end_date . ' 23:59:59';
        }

        $merchant = $this->crowl_merchant_name_m->get_merchant_by_id($merchant_id);

        // find products connected to merchant
        $products = $this->merchant_products_m->get_products_by_merchant($merchant_id, $this->store_id);
        
        //var_dump($this->db->last_query());

        $violator_notification_data = $this->violator_m->get_violator_notification_data($merchant_id, $this->store_id);
        
        // get product trend rows for date range for merchant for store
        $product_trend_rows = $this->merchants_m->get_product_trend_rows($merchant_id, $this->store_id, $this->data->crawl_start, $this->data->crawl_end);
        
        //var_dump($this->db->last_query());
        
        $product_price_data = array();
        
        foreach ($product_trend_rows as $ptn_row)
        {
            $upc = $ptn_row['upc'];
            $price = (float)$ptn_row['mpo'];
            
            if (isset($product_price_data[$upc]))
            {
                // check to see if this price is a new low or high
                if ($product_price_data[$upc]['low'] > $price)
                {
                    $product_price_data[$upc]['low'] = $price;
                }
                
                if ($product_price_data[$upc]['high'] < $price)
                {
                    $product_price_data[$upc]['high'] = $price;
                }                
            }
            else
            {
                // add new entry
                $product_price_data[$upc] = array('low' => $price, 'high' => $price); 
            }
        }

        // get product index counts for store merchant for past 30 days
        $product_data_rows = $this->merchants_m->get_products_listed_for_date_range($merchant_id, $this->store_id, $start_date, $end_date);

        // determine name to show for merchant
        $merchant_profile_name = $this->merchants_m->get_merchant_human_name($merchant, TRUE);
         
        $this->page_title = 'TrackStreet Profile - ' . $merchant_profile_name;
        
        $this->data->product_price_data = $product_price_data;
        $this->data->merchant_profile_name = $merchant_profile_name;
        $this->data->date_from = date('Y-m-d', strtotime($this->data->crawl_start));
        $this->data->date_to = date('Y-m-d', strtotime($this->data->crawl_end));
        $this->data->merchant_id = $merchant_id;
        $this->data->product_data_rows = $product_data_rows;
        $this->data->violator_notification_data = $violator_notification_data;
        $this->data->merchant = $merchant;
        $this->data->products = $products;
    }

    /**
     * Profile details on a single merchant.
     *
     * @author Christophe
     * @param int $merchant_id
     */
    public function profile_violations($merchant_id)
    {
        $this->load->model('crowl_merchant_name_m');
        $this->load->model('merchant_products_m');
        $this->load->model('violator_m');
        $this->load->model('products_trends_m');
        $this->load->model('merchants_m');
        $this->load->model('products_m');
        $this->load->model("violator_m", "Violator");

        $merchant_id = intval($merchant_id);

        $merchant = $this->crowl_merchant_name_m->get_merchant_by_id($merchant_id);

        // find products connected to merchant
        $products = $this->merchant_products_m->get_products_by_merchant($merchant_id, $this->store_id);

        // ---------- violation records

        $this->data->report_name = $merchant['original_name'] . ' Violations';
        $this->data->file_name = str_replace(' ', '_', $this->data->report_name);
        $this->data->my = 'pricingviolator';
        $this->data->icon = 'ico-report';
        $this->data->widget = 'mv-report';
        $this->data->displayBookmark = true;

        $this->data->report_info = array('report_name' => $this->data->report_name);
        $this->data->report_where = array(
            'report_function' => 'report_marketplace',
            'marketplace' => $merchant,
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

            $this->data->crawl_start = $this->input->post('date_from', TRUE) . ' 00:00:00';
            $this->data->crawl_end = $this->input->post('date_to', TRUE) . ' 23:59:59';

            $this->data->date_from = date('Y-m-d', strtotime($this->data->crawl_start));
            $this->data->date_to = date('Y-m-d', strtotime($this->data->crawl_end));
            
            $start_date = $this->input->post('date_from', TRUE);
            $end_date = $this->input->post('date_to', TRUE);
        }
        else
        {
            // get product index counts for store merchant for past 30 days
            if ($this->config->item('environment') == 'local')
            {
            	$start_date = '2015-06-24';
            	$end_date = '2015-07-17';
            }
            else
            {
            	$start_date = date('Y-m-d', strtotime('-30 days'));
            	$end_date = date('Y-m-d', strtotime('-1 day'));
            }            
        }
 
        $viol_data_rows = $this->merchants_m->get_violations_for_date_range($merchant_id, $this->store_id, $start_date, $end_date);

        $start_time_int = strtotime($this->data->crawl_start);
        $end_time_int = strtotime($this->data->crawl_end);

        $violation_query = "
            SELECT *
            FROM products_trends_new
            WHERE mid = {$merchant_id}
            AND dt >= {$start_time_int}
            AND dt <= {$end_time_int}
            AND mpo < ap
            ORDER BY dt DESC
        ";

        $price_trends = $this->db->query($violation_query)->result_array();

        $final_violations_array = array();

        foreach ($price_trends as $price_trend)
        {
            //safety hack to not show incorrect violations
            if ((float)$price_trend['mpo'] >= (float)$price_trend['ap'])
            {
    				    continue;
            }

            $product_id = intval($price_trend['pid']);

            $merchant_name = empty($merchant) || $merchant == FALSE ? 'N/A' : $merchant['original_name'];
            	
            $product = $this->products_m->get_product_by_id($price_trend['pid']);
            	
            // Christophe: database queries to product_trends_new are slow, so faster to put processing
            // here with PHP, and just go through all rows
            if (intval($product['store_id']) != intval($this->store_id))
            {
                continue;
            }

            $violation_array = array(
                'productId' => (int)$price_trend['pid'],
                'upc_code'  => (string)$price_trend['upc'],
                'retail'    => (float) $price_trend['rp'],
                'wholesale' => (float)$price_trend['wp'],
                'price' 		=> (float)$price_trend['mpo'],
                'map' 			=> (float)$price_trend['ap'],
                'title' 		=> (string)$product['title'], //$price_trend->t
                'marketplace' 	=> (string)$price_trend['ar'],
                'url' 			=> (string)$price_trend['l'],
                'timestamp'		=> (int)$price_trend['dt'],
                'hash_key'		=> (string)$price_trend['um'],
                'merchant_id' 	=> (string)$price_trend['mid'],
                'original_name' => $merchant_name,
                'date' 			=> (string)date('m/d/Y G:i:s', (int)$price_trend['dt']),
                'shot' 			=> (string)$price_trend['ss']
            );

            $final_violations_array[] = $violation_array;
        }
        
        // violation status
        $last_notice = $this->merchants_m->get_last_notice_sent($merchant_id, $this->store_id);
        $last_notice_date = empty($last_notice) ? 'N/A' : date('m/d/Y', strtotime($last_notice['date']));
        
        // check to see if they are on the DNS list
        $dns_merchant = $this->merchants_m->get_dns_merchant($this->store_id, $merchant_id);
        
        if (empty($dns_merchant))
        {
            $violation_status = empty($last_notice) ? 'Not Listed' : '<span style="padding: 2px 4px; background-color: #faebcc">Level ' . $last_notice['email_level'] . '</span>';
        }
        else
        {
            $violation_status = '<span style="padding: 2px 4px; background-color: #FBE6F2; color:#b30000; font-weight:bold;">DO NOT SELL</span>';
        }
        
        // last violation
    	  $last_violation = $this->merchants_m->get_last_violation($merchant_id, $this->store_id);
        $last_violation_date = empty($last_violation) ? 'N/A' : date('m/d/Y', strtotime($last_violation['select_date']));

        // sent notices
        $notices = $this->merchants_m->get_notifications_sent_to_merchant($merchant_id, $this->store_id);
        
        $final_notices_array = array();
        
        for ($i = 0; $i < count($notices); $i++)
        {
            // don't show internal notices to user
            if ($notices[$i]['email_to'] != 'support@trackstreet.com')
            {
                $final_notices_array[] = $notices[$i];
            }
        }
        
        // changes made to merchant
        $history_changes = $this->merchants_m->get_merchant_history($merchant_id, $this->store_id);
        
        $history_change_titles = $this->merchants_m->get_history_action_array();
        
        for ($i = 0; $i < count($history_changes); $i++)
        {
            $action_id = intval($history_changes[$i]['action_id']);
            
            $user = $this->users_m->get_user_by_id($history_changes[$i]['created_by']);
            
            $history_changes[$i]['user'] = $user;
            
            $history_changes[$i]['action_title'] = $history_change_titles[$action_id];
        }
        
        // current violation count
        $current_violation_count = $this->merchants_m->get_violation_count($merchant_id, $this->store_id);

        // determine name to show for merchant
        $merchant_profile_name = $this->merchants_m->get_merchant_human_name($merchant, TRUE);
         
        $this->page_title = 'TrackStreet Profile - ' . $merchant_profile_name;
        
        // view data
        $this->data->merchant_profile_name = $merchant_profile_name;        
        $this->data->notices = $final_notices_array;
        $this->data->history_changes = $history_changes;
        $this->data->current_violation_count = $current_violation_count;
        $this->data->last_violation_date = $last_violation_date;
        $this->data->last_notice_date = $last_notice_date;
        $this->data->violation_status = $violation_status;
        $this->data->violations = $final_violations_array;
        $this->data->merchant_id = $merchant_id;
        $this->data->store_id = $this->store_id;
        $this->data->viol_data_rows = $viol_data_rows;
        $this->data->merchant = $merchant;
        $this->data->products = $products;
    }
    
    /**
     * Save setting for which contacts should be notified for a merchant with a store.
     * 
     * @author Christophe
     * @param int $merchant_id
     */
    public function send_to_settings($merchant_id)
    {
        $this->load->library('Trackstreet_merchants');
        $this->load->model('violator_m');
        
        $merchant_id = intval($merchant_id);
        
        //var_dump($_POST); exit();
        
        // check to see if user is admin
        if ($this->role_id != 2)
        {
            $this->session->set_flashdata('error_msg', 'Error: Your account does not have the permissions to perform this action.');
            
            redirect('/merchants/profile/' . $merchant_id);
            exit();
        }
        
        // handle on/off setting of sending violation notifications
        $notifications_active_setting = $_POST['notifications_onoff'] == 'off' ? 0 : 1;
        
        $notification_row = $this->violator_m->get_violator_notification_by_store_merchant($this->store_id, $merchant_id);
        
        if (empty($notification_row))
        {
            // create a new row in the violator_notifications table
            $insert_data = array(
                'store_id' => $this->store_id,
                'crowl_merchant_name_id' => $merchant_id,
                'email_to' => '',
                'email_from' => '',
                'name_to' => '',
                'name_from' => '',
                'phone' => '',
                'notification_type' => 'unknown_seller',
                'title' => '',
                'message' => '',
                'days_to_warning1' => 1,
                'days_to_warning2' => 1,
                'warning1_repetitions' => 1,
                'warning2_repetitions' => 1,
                'active' => $notifications_active_setting                                                                                                                                                                                        
            );
            
            $this->violator_m->insert_violator_notification($insert_data);
        }
        else
        {
            // update existing row in violator_notifications table
            $update_data = array(
                'active' => $notifications_active_setting               
            );
            
            $row_id = intval($notification_row['id']);
            
            $this->violator_m->update_violator_notification($row_id, $update_data);
        }
        
        // handle notification template type setting
        if (isset($_POST['notification_template_type']))
        {
            $notification_template_type_value = $this->input->post('notification_template_type', TRUE);
            
            $this->trackstreet_merchants->save_merchant_setting($merchant_id, $this->store_id, $this->user_id, 'notification_template_type', $notification_template_type_value);
        }
        
        if (isset($_POST['primary_contact']))
        {
            // save setting that we need to email Primary Contact for merchant
            $this->trackstreet_merchants->save_merchant_setting($merchant_id, $this->store_id, $this->user_id, 'send_to_primary_contact', 'true');            
        }
        else
        {
            $this->trackstreet_merchants->save_merchant_setting($merchant_id, $this->store_id, $this->user_id, 'send_to_primary_contact', 'false');
        }
        
        if (isset($_POST['account_rep']))
        {
            // save setting that we need to email Account Rep for merchant
            $this->trackstreet_merchants->save_merchant_setting($merchant_id, $this->store_id, $this->user_id, 'send_to_account_rep', 'true');
        }
        else 
        {
            $this->trackstreet_merchants->save_merchant_setting($merchant_id, $this->store_id, $this->user_id, 'send_to_account_rep', 'false');
        }  

        if (isset($_POST['cc_address']))
        {
            // save setting that we need to email CC Address for merchant
            $this->trackstreet_merchants->save_merchant_setting($merchant_id, $this->store_id, $this->user_id, 'send_to_cc_address', 'true');        
        } 
        else
        {
            $this->trackstreet_merchants->save_merchant_setting($merchant_id, $this->store_id, $this->user_id, 'send_to_cc_address', 'false');
        }
        
        // handle change of notification level, if they are changing it
        $violation_level = $this->input->post('violation_level', TRUE);
        
        log_message('debug', 'Saving merchant to level: ' . $violation_level);
        
        $this->trackstreet_merchants->change_violation_level($this->store_id, $merchant_id, $violation_level, $this->user_id);

        // send user back to merchant profile page
        $this->session->set_flashdata('success_msg', 'Enforcement protocol settings have been successfully updated.');
        
        redirect('/merchants/profile/' . $merchant_id);
        exit();
    }
    
    /**
     * Page where merchant details and notes can be edited.
     * 
     * @author Christophe
     * @param int $merchant_id
     */
    public function save_note($merchant_id)
    {
        $this->load->model('merchants_m');
        
        $merchant_id = intval($merchant_id);

        $note = $this->input->post('note', TRUE);

        if ($note != FALSE && $note != '')
        {    
            $insert_data = array(
                'merchant_name_id' => $merchant_id,
                'user_id' => $this->user_id,
                'date' => date('Y-m-d H:i:s', time()),
                'entry' => $note                                                    
            );
            
            $this->merchants_m->insert_staff_note($insert_data);
            
            $this->session->set_flashdata('success_msg', 'Internal staff note has been successfully added.');
            
            redirect('/merchants/profile/' . $merchant_id);
            exit();
        }  
        else
        {
            $this->session->set_flashdata('error_msg', 'Error: Could not add internal note.');
            
            redirect('/merchants/profile/' . $merchant_id);
            exit();
        }      
    }
    
    /**
     * Get the datetime for when we first started tracking a merchant.
     * 
     * @author Christophe
     */
    /*
    public function update_merchant_created_at()
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 3600);
        
        $this->load->model('merchants_m');
        
        $merchants = $this->merchants_m->get_all_merchants();
        
        for ($i = 0; $i < count($merchants); $i++)
        {
            if ($merchants[$i]['created_at'] == '0000-00-00 00:00:00')
            {            
                // find first crawl record
                $crawl_record = $this->merchants_m->get_first_crawl_record($merchants[$i]['id']);
                
                if (empty($crawl_record))
                {
                    // skip
                }
                else 
                {
                    // update merchant
                    // date('Y-m-d H:i:s')
                    $update_data = array('created_at' => date('Y-m-d H:i:s', $crawl_record['dt']));
                    
                    $this->merchants_m->update_merchant($merchants[$i]['id'], $update_data);
                }
            }
        }
        
        echo 'Update completed!'; exit();
    }
    */
}

?>