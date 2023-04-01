<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Trackstreet_merchants
{
    public $linked_merchant_ids = array();
    public $linked_merchant_list = array();
    public $import_merchant_email_html = '';
    
    public function Trackstreet_merchants() 
    {
        $this->CI =& get_instance();
    }
    
    /**
     * Go through an array of merchant records and check to see if we should add them to 
     * the list of merchants that are linked to merchant user is currently viewing.
     * 
     * @param array $merchants
     */
    public function add_merchants_to_linked_list($merchants)
    {
        foreach ($merchants as $merchant)
        {
            $merchant_id = intval($merchant['id']);
            
            if (!$this->already_added_to_linked_merchant_list($merchant_id))
            {
                $this->linked_merchant_list[] = $merchant;
            }
        }
    }
    
    /**
     * Check to see if merchant ID is already in linked list.
     * 
     * @param int $new_merchant_id
     * @return boolean
     */
    public function already_added_to_linked_merchant_list($new_merchant_id)
    {
        $new_merchant_id = intval($new_merchant_id);
        
        if (in_array($new_merchant_id, $this->linked_merchant_ids))
        {
            return TRUE;
        }
        else
        {
            $this->linked_merchant_ids[] = $new_merchant_id;
            
            return FALSE;
        }
    }
    
    /**
     * Handle the changing of violation notification level (list level) that a merchant is on. Also,
     * handle taking on/off the merchant from the DNS list.
     * 
     * @author Christophe
     * @param int $store_id
     * @param int $merchant_id
     * @param mixed $violation_level
     */
    public function change_violation_level($store_id, $merchant_id, $violation_level, $user_id)
    {
        $this->CI->load->model('merchants_m');
        
        log_message('debug', 'change_violation_level() function call for merchant: ' . $merchant_id . ' - violation_level: ' . $violation_level);
        
        $store_id = intval($store_id);
        $merchant_id = intval($merchant_id);
        $violation_level = intval($violation_level);
        
        $notif_level_amount = $this->CI->merchants_m->get_notification_levels_num($store_id);
        
        $dns_level = $notif_level_amount + 1;
        
        if ($violation_level == $dns_level)
        {
            // if merchant is not on Do Not Sell list, add them
            $dns_merchant = $this->CI->merchants_m->get_dns_merchant($store_id, $merchant_id);
            
            if (empty($dns_merchant))
            {
                $num_of_times = $this->CI->merchants_m->get_dns_added_count($store_id, $merchant_id);
                
                $is_permanent = $this->CI->merchants_m->get_dns_setting_value($store_id, 'initial_permanent', 0);
                
                // add to DNS list
                $insert_data = array(
                    'store_id' => $store_id,
                    'merchant_id' => $merchant_id,
                    'num_of_times' => $num_of_times + 1,
                    'is_permanent' => $is_permanent,
                    'created' => date('Y-m-d H:i:s'),
                    'modified' => date('Y-m-d H:i:s')                                                                            
                );
                
                $dns_list_id = $this->CI->merchants_m->insert_dns_merchant($insert_data);
                
                log_message('debug', 'Added merchant to DNS list: ' . $dns_list_id . ' - merchant ID: ' . $merchant_id);
                
                // add DNS list period
                
                // determine end_date based on store DNS list settings
                $dns_end_date = $this->get_dns_end_date($store_id, $merchant_id);
                
                $insert_data = array(
                		'store_id' => $store_id,
                		'merchant_id' => $merchant_id,
                		'period_num' => $num_of_times + 1,
                		'start_date' => date('Y-m-d'),
                    'end_date' => $dns_end_date,            
                    'added_by' => $user_id,     
                    'removed_by' => 0,                   
                		'created' => date('Y-m-d H:i:s'),
                		'modified' => date('Y-m-d H:i:s')
                );                
                
                $this->CI->merchants_m->insert_dns_period($insert_data);
            }
            else
            {
                // merchant is already on DNS list -- do nothing
                log_message('debug', 'Merchant already on DNS list: ' . $dns_merchant['id']);
            }
        }
        else
        {
            // check to see if merchant is on DNS list and remove them if they are (they should not remain on DNS list
            // if they are downgraded to a numerical level)
            $dns_merchant = $this->CI->merchants_m->get_dns_merchant($store_id, $merchant_id);
            
            if (!empty($dns_merchant))
            {
                // remove merchant from DNS list
                $this->CI->merchants_m->delete_dns_merchant($store_id, $merchant_id);
                
                log_message('debug', 'Removed merchant from DNS list: ' . $merchant_id);
                
                $dns_period = $this->CI->merchants_m->get_most_recent_dns_period($store_id, $merchant_id);
                
                if (!empty($dns_period))
                {
                    $row_id = intval($dns_period['id']);
                    
                    $update_data = array(
                        'end_date' => date('Y-m-d'),
                        'removed_by' => $user_id,
                        'modified' => date('Y-m-d H:i:s')
                    );
                    
                    $this->CI->merchants_m->update_dns_period($row_id, $update_data);          
                }
            }
            
            $last_notice = $this->CI->merchants_m->get_last_notice_sent($merchant_id, $store_id);
            
            if (empty($last_notice))
            {
                log_message('debug', 'Inserting violation level row into violator_notifications_history merchant: ' . $merchant_id . ' to level: ' . $violation_level);
                
                // create new violation notice as it is needed to keep track of level
                $insert_data = array(
                    'store_id' => $store_id,
                    'crowl_merchant_name_id' => $merchant_id,
                    'email_to' => 'support@trackstreet.com',
                    'email_from' => 'support@trackstreet.com',
                    'name_to' => 'support@trackstreet.com',
                    'name_from' => 'support@trackstreet.com',
                    'phone' => '',
                    'email_level' => $violation_level,
                    'email_repeat' => 1,
                    'is_exit' => 0,
                    'title' => 'TrackStreet Internal Note: Violation Level Changed for Merchant',
                    'full_message' => 'Notification was not sent to merchant. This is simply a note that their violation level was changed by a team member or by the TrackStreet system',
                    'regarding' => '',
                    'date' => date('Y-m-d H:i:s')                                        
                );
                
                $this->CI->merchants_m->insert_violator_notification_history($insert_data);
            }
            else
            {
                $row_id = intval($last_notice['id']);
                
                log_message('debug', 'Changing violation level for violator_notifications_history row: ' . $row_id . ' to level: ' . $violation_level . ' - merchant ID: ' . $merchant_id);
                
                $update_data = array(
                    'email_level' => $violation_level                
                );
                
                $this->CI->merchants_m->update_violator_notifications_history($row_id, $update_data);
                
                log_message('debug', 'Merchant updated to level: ' . $violation_level);
            }
        }
    }
    
    /**
     * Clear entries in products_per_merchant_per_day table and violations_per_merchant_per_day.
     * 
     * @author Christophe
     * @param int $store_id
     * @param string $current_date_str
     */
    public function clear_merchant_count_records($current_date_str)
    {
        $this->CI->load->model('merchants_m');
        
        $this->CI->db->where('select_date', $current_date_str);
        $this->CI->db->delete('products_per_merchant_per_day');
        
        $this->CI->db->flush_cache();
        
        $this->CI->db->where('select_date', $current_date_str);
        $this->CI->db->delete('violations_per_merchant_per_day');
    }
    
    /**
     * Determine the end date for the current period that this store merchant will be on the DNS list for.
     * 
     * @author Christophe
     * @param int $store_id
     * @param int $merchant_id
     * @return string
     */
    public function get_dns_end_date($store_id, $merchant_id)
    {
        $this->CI->load->model('merchants_m');
        
        $store_id = intval($store_id);
        $merchant_id = intval($merchant_id);
        
        // determine if permanent
        $is_permanent = $this->CI->merchants_m->get_dns_setting_value($store_id, 'initial_permanent', 0);
        
        if ($is_permanent == 1)
        {
            return '0000-00-00';
        }
        else
        {
            // determine how many times merchant has been put on DNS list
            $dns_list_times = intval($this->CI->merchants_m->get_dns_periods_for_merchant($store_id, $merchant_id));
            
            log_message('debug', 'merchant ' . $merchant_id . ' has been on DNS list ' . $dns_list_times . ' times');
            
            // if they are over limit setting for # of times on DNS, then they are permanent
            $num_of_times_before_perm = intval($this->CI->merchants_m->get_dns_setting_value($store_id, 'num_of_times_before_perm', 3));
            
            if ($dns_list_times > $num_of_times_before_perm)
            {
                // they are now permanently on list because they have been on the list too many times
                return '0000-00-00';
            }
            else
            {
                // get number of days that they will be on list for - defined on DNS settings page
                $remain_on_list_days_offense = $this->CI->merchants_m->get_dns_setting_value($store_id, 'remain_on_list_days_offense_' . $dns_list_times, 30);
                
                $end_date = strtotime("+{$remain_on_list_days_offense} day");
                
                $end_date_str = date('Y-m-d', $end_date);
                
                return $end_date_str; 
            }
        }
    }
    
    /**
     * Handle the importing of the merchant data CSV file and recording the data into the database.
     * 
     * @author Christophe
     * @param string $file_path
     * @param int $store_id
     * @param int $user_id
     */
    public function import_merchant_data($file_path, $store_id, $user_id)
    {
        $this->CI->load->library('Trackstreet_data');
        $this->CI->load->library('Vision_users');
        $this->CI->load->model('merchants_m');
        $this->CI->load->model('store_m');
        $this->CI->load->model('users_m');
        
        $store_id = intval($store_id);
        $user_id = intval($user_id);
        
        $handle = fopen($file_path, "r");
        
        while (($data = fgetcsv($handle, 9999999, ',')) !== FALSE)
        {
            $data_rows[] = $data;
        }
        
        fclose($handle);
        
        //var_dump($data_rows); exit();
        
        $store = $this->CI->store_m->get_store_by_id_array($store_id);
        
        $user = $this->CI->users_m->get_user_by_id($user_id);
        
        $this->import_merchant_email_html .= 'Merchant data has been imported by ' . $user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['email'] . ') ' . '.<br /><br />' . "\n";
        
        $last_version_num = $this->CI->merchants_m->get_last_merchant_data_version_num($store_id);
        
        $new_version_num = $last_version_num + 1;
        
        log_message('debug', 'import_merchant_data() - store: ' . $store_id . ' - new version: ' . $new_version_num);
        
        // import each row of CSV file -- skip first row (header)
        for ($i = 1; $i < count($data_rows); $i++)
        {
            // get the merchant ID from the CSV file row
            $merchant_id = isset($data_rows[$i][0]) ? $data_rows[$i][0] : FALSE;
            
            $merchant_id = $merchant_id == '' || $merchant_id == NULL || $merchant_id == FALSE ? FALSE : intval($merchant_id);
            
            if ($merchant_id == FALSE)
            {
                // insert new merchant record
                $insert_data = array(
                    'contact_email' => $data_rows[$i][3],
                    'phone' => $data_rows[$i][4],
                    'fax' => $data_rows[$i][5],
                    'address_1' => $data_rows[$i][6],
                    'address_2' => $data_rows[$i][7],
                    'city' => $data_rows[$i][8],
                    'state' => $data_rows[$i][9],
                    'zip' => $data_rows[$i][10], 
                    'merchant_url' => $data_rows[$i][11],
                );
                
                $merchant_id = $this->CI->merchants_m->insert_merchant($insert_data);
                
                log_message('debug', 'import_merchant_data() - new merchant added: ' . $merchant_id);
                
                $this->import_merchant_email_html .= 'New merchant added by customer. Merchant ID: ' . $merchant_id . '<br /><br />' . "\n"; 
            }
            
            $insert_data = array(
                'store_id' => $store_id,
                'user_id' => $user_id,
                'version_num' => $new_version_num,            
                'merchant_id' => $data_rows[$i][0],
                'merchant_name' => $data_rows[$i][1],
                'merchant_marketplace' => $data_rows[$i][2],
                'email' => $data_rows[$i][3],
                'phone' => $data_rows[$i][4],
                'fax' => $data_rows[$i][5],
                'address_1' => $data_rows[$i][6],
                'address_2' => $data_rows[$i][7],
                'city' => $data_rows[$i][8],
                'state' => $data_rows[$i][9],
                'zip' => $data_rows[$i][10],
                'website' => $data_rows[$i][11],
                'send_violation_notifications' => $data_rows[$i][12],
                'notification_template' => $data_rows[$i][13],
                'send_to_primary' => $data_rows[$i][14],
                'send_to_account_reps' => $data_rows[$i][15],
                'send_to_cc_addresses' => $data_rows[$i][16],
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')                            
            );
            
            $this->CI->merchants_m->insert_merchant_data_import($insert_data);
            
            $incoming_values = array(
                'contact_email' => $data_rows[$i][3],
                'phone' => $data_rows[$i][4],
                'fax' => $data_rows[$i][5],
                'address_1' => $data_rows[$i][6],
                'address_2' => $data_rows[$i][7],
                'city' => $data_rows[$i][8],
                'state' => $data_rows[$i][9],
                'zip' => $data_rows[$i][10],
                'merchant_url' => $data_rows[$i][11],
            );
            
            $merchant = $this->CI->merchants_m->get_merchant_by_id($merchant_id);
            
            $prev_values = $merchant;
            
            // record changes that were made, if any, to our TrackStreet merchant record
            $this->CI->trackstreet_data->record_data_changes($user_id, $merchant_id, 'crowl_merchant_name_new', $incoming_values, $prev_values);
            
            // see if we should update any values with the TrackStreet merchant record
            $this->update_merchant_check($merchant_id, $incoming_values);
            
            // update merchant violation enforcement settings
            $setting_value = intval($data_rows[$i][13]) == 1 ? 'known_seller_template' : 'unknown_seller_template';
            $this->save_merchant_setting($merchant_id, $store_id, $user_id, 'notification_template_type', $setting_value);
            
            $setting_value = $data_rows[$i][14] == 'y' ? 'true' : 'false';
            $this->save_merchant_setting($merchant_id, $store_id, $user_id, 'send_to_primary_contact', $setting_value);
            
            $setting_value = $data_rows[$i][15] == 'y' ? 'true' : 'false';
            $this->save_merchant_setting($merchant_id, $store_id, $user_id, 'send_to_account_rep', $setting_value);
            
            $setting_value = $data_rows[$i][16] == 'y' ? 'true' : 'false';
            $this->save_merchant_setting($merchant_id, $store_id, $user_id, 'send_to_cc_address', $setting_value);
        }
        
        $email = $this->CI->config->item('environment') == 'production' ? 'andrew@trackstreet.com,christophe@trackstreet.com' : 'christophe@trackstreet.com,christophe@juststicky.com';
        $subject = '[TrackStreet] Merchant Data Imported - Store: ' . $store['store_name'] . ' - ' . date('Y-m-d H:i');
        $text_message = strip_tags($this->import_merchant_email_html);
         
        $this->CI->vision_users->sendSESEmail($email, $subject, $this->import_merchant_email_html, $text_message);
    }
    
    /**
     * Handle the importing of the merchant contact data CSV file and recording the data into the database.
     *
     * @author Christophe
     * @param string $file_path
     * @param int $store_id
     * @param int $user_id
     */
    public function import_merchant_contact_data($file_path, $store_id, $user_id)
    {
        $this->CI->load->library('Trackstreet_data');
        $this->CI->load->library('Vision_users');
        $this->CI->load->model('merchants_m');
        $this->CI->load->model('store_m');
        $this->CI->load->model('users_m');

        $handle = fopen($file_path, "r");
        
        $store_id = intval($store_id);
        $user_id = intval($user_id);

        while (($data = fgetcsv($handle, 9999999, ',')) !== FALSE)
        {
            $data_rows[] = $data;
        }

        fclose($handle);

        //var_dump($data_rows); exit();
        
        $store = $this->CI->store_m->get_store_by_id_array($store_id);
        
        $user = $this->CI->users_m->get_user_by_id($user_id);

        $last_version_num = $this->CI->merchants_m->get_last_merchant_contact_data_version_num($store_id);

        $new_version_num = $last_version_num + 1;

        log_message('debug', 'import_merchant_contact_data() - store: ' . $store_id . ' - new version: ' . $new_version_num);
        
        $this->import_merchant_email_html .= 
            'import_merchant_contact_data() - store: ' . $store['store_name'] . 
            ' - new version: ' . $new_version_num . '<br/><br/>' . "\n";

        // import each row of CSV file -- skip first row (header)
        for ($i = 1; $i < count($data_rows); $i++)
        {
            $contact_uuid = $data_rows[$i][0];
            $merchant_id = $data_rows[$i][1];
            
            $first_name = $data_rows[$i][4];
            $last_name = $data_rows[$i][5];
            $email = $data_rows[$i][6];
            $phone = $data_rows[$i][7];   
            $contact_type = $data_rows[$i][8];         

            if (
                $contact_uuid == '' 
                && ($first_name != '' || $last_name != '' || $email !=  '' || $phone != '')
            )
            {
                // check to see if we can find a contact that already exists by phone or email
                if ($phone != '')
                {
                    $existing_contact_by_phone = $this->CI->merchants_m->get_merchant_contact_by_phone($store_id, $merchant_id, $phone);
                }
                else
                {
                    $existing_contact_by_phone = array();
                }
                
                if ($email != '')
                {
                    $existing_contact_by_email = $this->CI->merchants_m->get_merchant_contact_by_email($store_id, $merchant_id, $email);
                }
                else
                {
                    $existing_contact_by_email = array();
                }
                
                if (!empty($existing_contact_by_phone) || !empty($existing_contact_by_email))
                {
                    $found_contact_msg = 'Found existing contact by phone: ' . $phone . ' or email: ' . $email;
                    
                    log_message('debug', $found_contact_msg);
                    
                    $this->import_merchant_email_html .= $found_contact_msg . '<br/><br/>' . "\n";
                    
                    $contact_uuid = !empty($existing_contact_by_phone) ? $existing_contact_by_phone['uuid'] : $existing_contact_by_email['uuid'];
                    
                    // update existing merchant contact
                    $update_data = array(
                    		'first_name' => $first_name,
                    		'last_name' => $last_name,
                    		'email' => $email,
                    		'phone' => $phone,
                    		'modified' => date('Y-m-d H:i:s'),
                    		'modified_by' => $user_id
                    );
                    
                    $this->CI->merchants_m->update_merchant_contact_by_uuid($contact_uuid, $update_data);
                }
                else
                {
                    // insert new merchant contact record
                    $insert_data = array(
                    		'uuid' => uuid(),
                    		'merchant_id' => $merchant_id,
                    		'store_id' => $store_id,
                    		'first_name' => $first_name,
                    		'last_name' => $last_name,
                    		'email' => $email,
                    		'phone' => $phone,
                    		'type_id' => $contact_type,
                    		'created' => date('Y-m-d H:i:s'),
                    		'created_by' => $user_id,
                    		'modified' => date('Y-m-d H:i:s'),
                    		'modified_by' => $user_id
                    );
                    
                    $contact_id = $this->CI->merchants_m->insert_merchant_contact($insert_data);    

                    $contact_inserted_msg = 
                    'import_merchant_contact_data() - store: ' . $store['store_name'] . 
                    ' - merchant ID: ' . $merchant_id .
                    ' - new contact added: ' . $contact_id;
                    
                    log_message('debug', $contact_inserted_msg);
                    
                    $this->import_merchant_email_html .= $contact_inserted_msg . '<br/><br/>' . "\n";
                }    
            }
            else if ($contact_uuid != '')
            {
                // update existing merchant contact
                $update_data = array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'phone' => $phone,
                    'modified' => date('Y-m-d H:i:s'),
                    'modified_by' => $user_id
                );
                
                $this->CI->merchants_m->update_merchant_contact_by_uuid($contact_uuid, $update_data);
            }

            // this is for logging all versions of imported data
            $insert_data = array(
                'store_id' => $store_id,
                'user_id' => $user_id,
                'version_num' => $new_version_num,
                'merchant_id' => $merchant_id,
                'contact_id' => $contact_uuid,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone_number' => $phone,
                'contact_type' => $contact_type,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            );

            $this->CI->merchants_m->insert_merchant_contact_data_import($insert_data);
        }
        
        $email = $this->CI->config->item('environment') == 'production' ? 'andrew@trackstreet.com,christophe@trackstreet.com' : 'christophe@trackstreet.com,christophe@juststicky.com';
        $subject = '[TrackStreet] Merchant Contact Data Imported - Store: ' . $store['store_name'] . ' - ' . date('Y-m-d H:i');
        $text_message = strip_tags($this->import_merchant_email_html);
         
        $this->CI->vision_users->sendSESEmail($email, $subject, $this->import_merchant_email_html, $text_message);
    }

    /**
     * Increment product indexed count for the day for the merchant.
     *
     * @author Christophe
     * @param string $current_date_str
     */
    public function record_merchant_cron_log($run_date, $trend_rows_processed)
    {
        $this->CI->load->model('merchants_m');

        $existing_row = $this->CI->merchants_m->get_merchant_cron_log_row($run_date);

        if (!empty($existing_row))
        {
            $update_data = array(
                'trend_rows_processed' => $trend_rows_processed,
                'modified' => date('Y-m-d H:i:s')
            );

            $this->CI->merchants_m->update_merchant_cron_log($existing_row['id'], $update_data);
        }
        else
        {
            $insert_data = array(
                'run_date' => $run_date,
                'trend_rows_processed' => $trend_rows_processed,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            );

            $this->CI->merchants_m->insert_merchant_cron_log($insert_data);
        }
    }
    
    /**
     * Increment product indexed count for the day for the merchant.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @param string $current_date_str
     */
    public function record_merchant_store_product_count($merchant_id, $store_id, $current_date_str)
    {
        $this->CI->load->model('merchants_m');
        
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        
        $existing_row = $this->CI->merchants_m->get_products_per_merchant_per_day($merchant_id, $store_id, $current_date_str);
        
        if (!empty($existing_row))
        {
            $new_count = intval($existing_row['product_count']) + 1;
            
            $update_data = array(
                'product_count' => $new_count,
                'modified' => date('Y-m-d H:i:s')                            
            );
            
            $this->CI->merchants_m->update_products_per_merchant_per_day($existing_row['id'], $update_data);
        }
        else
        {
            $insert_data = array(
                'store_id' => $store_id,
                'merchant_id' => $merchant_id,
                'product_count' => 1,  
                'select_date' => $current_date_str,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')                                                                          
            );
            
            $this->CI->merchants_m->insert_products_per_merchant_per_day($insert_data);
        }
    }
    
    /**
     * Increment product violation count for the day for the merchant.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @param string $current_date_str
     */
    public function record_merchant_store_violation($merchant_id, $store_id, $current_date_str)
    {
        $this->CI->load->model('merchants_m');

        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);

        $existing_row = $this->CI->merchants_m->get_violations_per_merchant_per_day($merchant_id, $store_id, $current_date_str);

        if (!empty($existing_row))
        {
            $new_count = intval($existing_row['violation_count']) + 1;

            $update_data = array(
                'violation_count' => $new_count,
                'modified' => date('Y-m-d H:i:s')
            );

            $this->CI->merchants_m->update_violations_per_merchant_per_day($existing_row['id'], $update_data);
        }
        else
        {
            $insert_data = array(
                'store_id' => $store_id,
                'merchant_id' => $merchant_id,
                'violation_count' => 1,
                'select_date' => $current_date_str,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            );

            $this->CI->merchants_m->insert_violations_per_merchant_per_day($insert_data);
        }
    }
    
    /**
     * Update existing or insert a new merchant_contacts row.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @param array $merchant_data
     * @param int $type_id
     * @return array
     */
    public function save_old_merchant_contact($merchant_id, $store_id, $merchant_data, $type_id = 1)
    {
        $this->CI->load->model('merchants_m');
        
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        $user_id = 0;
        
        $existing_row = $this->CI->merchants_m->get_merchant_contact_by_email($merchant_id, $store_id, $merchant_data['email']);
        
        if (!empty($existing_row))
        {
            $update_data = array(
            		'first_name' => $merchant_data['first_name'],
                'last_name' => $merchant_data['last_name'],
                'email' => $merchant_data['email'],                        
            		'modified' => date('Y-m-d H:i:s'),
            		'modified_by' => $user_id
            );
            
            $this->CI->merchants_m->update_merchant_contact($existing_row['id'], $update_data);
            
            $return_data = array(
            		'action' => 'updated',
            		'contact_email' => $merchant_data['email']
            );            
        }
        else
        {
            $insert_data = array(
                'uuid' => uuid(),            
                'merchant_id' => $merchant_id,
                'store_id' => $store_id,                        
            		'first_name' => $merchant_data['first_name'],
                'last_name' => $merchant_data['last_name'],
                'email' => $merchant_data['email'], 
                'type_id' => $type_id,
            		'created' => date('Y-m-d H:i:s'),
            		'created_by' => $user_id,
            		'modified' => date('Y-m-d H:i:s'),
            		'modified_by' => $user_id
            );
            
            $this->CI->merchants_m->insert_merchant_contact($insert_data);
            
            $return_data = array(
                'action' => 'created',
                'contact_email' => $merchant_data['email']                             
            );
        } 

        return $return_data;
    }
    
    /**
     * Save a setting for the DNS list for a store.
     * 
     * @author Christophe
     * @param int $store_id
     * @param int $user_id
     * @param string $setting_name
     * @param string $setting_value
     */
    public function save_dns_setting($store_id, $user_id, $setting_name, $setting_value)
    {
        $this->CI->load->model('merchants_m');
        
        $store_id = intval($store_id);
        $user_id = intval($user_id);
        
        $existing_row = $this->CI->merchants_m->get_dns_setting($store_id, $setting_name);
        
        if (!empty($existing_row))
        {
            $update_data = array(
            		'value' => $setting_value,
            		'modified' => date('Y-m-d H:i:s'),
            		'modified_by' => $user_id
            );
            
            $this->CI->merchants_m->update_dns_setting($existing_row['id'], $update_data);
        }
        else
        {
            $insert_data = array(
            		'store_id' => $store_id,
            		'name' => $setting_name,
            		'value' => $setting_value,
            		'created' => date('Y-m-d H:i:s'),
            		'created_by' => $user_id,
            		'modified' => date('Y-m-d H:i:s'),
            		'modified_by' => $user_id
            );
            
            $this->CI->merchants_m->insert_dns_setting($insert_data);
        }        
    }
    
    /**
     * Update existing or insert a new merchant setting row.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @param string $setting_name
     * @param string $setting_value
     */
    public function save_merchant_setting($merchant_id, $store_id, $user_id, $setting_name, $setting_value)
    {
        $this->CI->load->model('merchants_m');
        
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        $user_id = intval($user_id);
        
        $existing_row = $this->CI->merchants_m->get_merchant_setting($merchant_id, $store_id, $setting_name);
        
        if (!empty($existing_row))
        {         
            $update_data = array(
            		'value' => $setting_value,
                'modified' => date('Y-m-d H:i:s'),
                'modified_by' => $user_id                            
            );
            
            $this->CI->merchants_m->update_merchant_setting($existing_row['id'], $update_data);
        }
        else
        {
            $insert_data = array(
            		'store_id' => $store_id,
            		'merchant_id' => $merchant_id,
            		'name' => $setting_name,
            		'value' => $setting_value,
            		'created' => date('Y-m-d H:i:s'),
                'created_by' => $user_id,            
            		'modified' => date('Y-m-d H:i:s'),
                'modified_by' => $user_id            
            );
            
            $this->CI->merchants_m->insert_merchant_setting($insert_data);
        }        
    }   

    /**
     * Given incoming data, see if any of the values are different than that we have for an existing merchant
     * and update values in database record if they are different.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param array $incoming_values
     */
    public function update_merchant_check($merchant_id, $incoming_values)
    {
        $this->CI->load->model('merchants_m');
        
        $merchant_id = intval($merchant_id);
        
        $merchant_data = $this->CI->merchants_m->get_merchant_by_id($merchant_id);
        
        $update_data = array();
        
        if (empty($merchant_data))
        {
            log_message('debug', 'update_merchant_check() - merchant not found: ' . $merchant_id);
            
            $this->import_merchant_email_html .= 'update_merchant_check() - merchant not found: ' . $merchant_id . '<br /><br />' . "\n";
        }
        else 
        {   
            foreach ($incoming_values as $key => $incoming_value)
            {
                if ($merchant_data[$key] != $incoming_value)
                {
                    $update_data[$key] = $incoming_values[$key];
                    
                    $update_merchant_msg = 
                        'update_merchant_check() - merchant: ' . $merchant_id . 
                        ' - updating ' . $key . ': ' . $incoming_values[$key] . 
                        ' - former value: ' . $merchant_data[$key];
                    
                    log_message('debug', $update_merchant_msg);
                    
                    $this->import_merchant_email_html .= $update_merchant_msg . ' <br /><br />' . "\n";
                }
                else
                {
                    $update_merchant_msg = 
                        'update_merchant_check() - merchant: ' . $merchant_id . 
                        ' - not updated - ' . $key . ': ' . $incoming_value;
                    
                    //log_message('debug', $update_merchant_msg);   
                }
            }
            
            if (!empty($update_data))
            {
                $this->CI->merchants_m->update_merchant($merchant_id, $update_data);
            }
        }
    }
}