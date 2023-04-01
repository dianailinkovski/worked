<?php

/**
 *
 *
 * @package   Cron
 *
 * @property   Users_m     $User
 * @property   Store_m      $store_m
 * @property   Products_m   $products_m
 * @property   Products_deleted_m   $products_deleted_m
 *
 *
 */
class Cron extends MY_Controller
{
	protected $_acl = array(
		'*' => 'cli'
	);
	
	public $cron_log_output = '';

  public function Cron()
  {
    parent::__construct();
    
    $this->load->model("users_m", "User");
    $this->load->model("products_m");
    $this->load->model("products_deleted_m");
    $this->load->model("store_m");

    $this->record_per_page = $this->config->item("record_per_page");
    
    $this->role_id = 0;
  }
  
  public function index($arg=''){
	echo "index! $arg\n";
	exit;
  }

  /**
   *
   * function products
   *
   *
   *
   */
  function products()
  {
    $offset = 0;
    $limit = 100;

    $one_week_before = date('Y-m-d', strtotime('-1 week'));
    $six_days_before = date('Y-m-d', strtotime('-0 days'));

    // delete all products which were marked as deleted and are older than 6 days
    $this->products_m->delete_by('deleted_at IS NOT NULL AND DATE(deleted_at) <= "'.$one_week_before.'"');
    $this->products_deleted_m->delete_by('DATE(created_at) <= "'.$one_week_before.'"');

    do
    {
      $rows = $this->products_deleted_m->limit($limit, $offset)->get_many_by('DATE(created_at) = "'.$six_days_before.'"');
      // debugging info
      //echo '<pre>'.$this->db->last_query()."\n";print_r($rows);echo '</pre>';
      // iterate over deleted products data
      foreach($rows as $row)
      {
        $products = $this->products_m->getProductsById(0, explode(',', $row->products_id));

        if($products)
        {
          // get store and merchant info
          $store_info = $this->store_m->get_by(array('store_enable' => '1', 'id' => $products[0]['store_id']));
          $merchant_info = $this->User->get($store_info->store_id);

          // get team members
          $members = $this->User->get_team_members($store_info->store_id, 2);
          $members[] = array('email' => $merchant_info->email);

          $hd = 'Following products will be deleted permanantely tomorrow.';
          $htmlBody = $hd.'<br/><br/><ul>';
          $textBody = $hd."\r\n\r\n";

          foreach($products as $product){
            $htmlBody .= '<li>'.$product['title'].'</li>';
            $textBody .= '• '.$product['title']."\r\n";
          }

          $unDelete = base_url().'catalog/undelete_products/'.base64_encode($row->id);
					$htmlBody .= '</ul><br/><br/>Follow this link to <a href="'.$unDelete.'">un-delete these products</a>.';
					$textBody .= "\r\n\r\nFollow this link to un-delete these products: ".$unDelete;

          foreach($members as $member){
            send_email($member['email'], 'support@juststicky.com', 'TrackStreet Product Removal', $htmlBody, $textBody);
          }
        }
      }

      $offset += $limit;
    }
    while($rows && count($rows) > 0);

    exit('complete');
  }
  
    /**
     * For reporting, determine number of violated products for a date/day for each store.
     * 
     * Ex:
     * 
     * 1-day
     * https://app.trackstreet.com/cron/count_violations_for_day/2015-06-07
     * 
     * more than 1 day (call in loop - see violations_date_range)
     * https://app.trackstreet.com/cron/count_violations_for_day/2015-06-07/true
     * 
     * @author Christophe
     * @param string $date_str
     * @param string $multiday
     */
    public function count_violations_for_day($date_str = '', $multiday = '')
    {        
        ob_start();
        
        $this->load->model('store_m');
        $this->load->model('products_m');
        
        if ($date_str == '')
        {
            $date_str = date('Y-m-d', strtotime('-1 days'));
        }
        
        // $date_str = '2015-08-12'
                    
        $violation_query = 
            'SELECT DISTINCT mid, pid, ap, mpo ' .
            "FROM {$this->_table_products_trends} ptn " .
            "WHERE ptn.dt >= " . strtotime($date_str . ' 00:00:00') . ' ' .
            "AND ptn.dt <= " . strtotime($date_str . ' 23:59:59');
        
        $violations = $this->db->query($violation_query)->result_array();
        
        //var_dump($violation_query); exit();
        
        $store_counts = array();
        $multi_check = array();
        
        foreach ($violations as $violation)
        {
            $merchant_id = intval($violation['mid']);
            $product_id = intval($violation['pid']);
            
            // get details on product
            $product = $this->products_m->get_product_by_id($product_id);
            
            if (!empty($product))
            {                
                $store_id = intval($product['store_id']);
                
                if (!isset($store_counts[$store_id]))
                {
                    $store_counts[$store_id] = 0;
                }
                
                // only record 1 violation per day per product per merchant
                if (isset($multi_check[$merchant_id][$product_id]))
                {
                    if ($multi_check[$merchant_id][$product_id] === TRUE)
                    {
                        // skip duplicates -- we scan more than once a day so there may be more than 1 record in products_trends_new table in 1 day
                    }
                    else
                    {
                        // if MAP price greater than merchant price
                        // @todo check to see if promotional pricing is set on product - use promo price instead of MAP price with comparison
                        if ($violation['ap'] > $violation['mpo'])
                        {
                            $store_counts[$store_id] = $store_counts[$store_id] + 1;
                        
                            $multi_check[$merchant_id][$product_id] = TRUE; // price violation found
                        }
                        else
                        {
                            $multi_check[$merchant_id][$product_id] = FALSE; // price was not in violation
                        }
                    }
                }
                else
                {
                    // if MAP price greater than merchant price
                    // @todo check to see if promotional pricing is set on product - use promo price instead of MAP price with comparison
                    if ($violation['ap'] > $violation['mpo'])
                    {
                        $store_counts[$store_id] = $store_counts[$store_id] + 1;
                        
                        $multi_check[$merchant_id][$product_id] = TRUE; // price violation found
                    }
                    else
                    {
                        $multi_check[$merchant_id][$product_id] = FALSE; // price was not in violation
                    }
                }
            }
        }
        
        echo 'product_trends_new records processed for ' . $date_str . ': ' . count($violations) . "\n";
        
        //var_dump($store_counts); exit();
        
        foreach ($store_counts as $store_id => $count)
        {
            // check to see if row already exists
            $day_violation_row = $this->products_m->get_day_violation_row($store_id, $date_str);
            
            if ($count !== 0)
            {
                echo 'Store: ' . $store_id . ' - Violations: ' . $count . "\n";
                
                ob_flush();
                flush();
            }
            
            if (empty($day_violation_row))
            {
                // insert new row
                $insert_data = array(
                    'store_id' => $store_id,
                    'violation_count' => $count,
                    'select_date' => $date_str,
                    'created' => date('Y-m-d H:i:s'),
                    'modified' => date('Y-m-d H:i:s'),                                                                                 
                );
                
                $this->products_m->insert_day_violations($insert_data);
            }
            else
            {
                $update_data = array(
                		'violation_count' => $count,
                		'modified' => date('Y-m-d H:i:s'),
                );                
                
                $this->products_m->update_day_violations($day_violation_row['id'], $update_data);
            }
        }
        
        echo 'Completed: count_violations_for_day ' . "\n"; 
        
        ob_flush();
        flush();
        
        if ($multiday == '')
        {
            ob_end_flush();
            exit();
        }
    }  
    
    /**
     * Based off of stores' DNS settings, determine if we need to perform actions on their
     * merchant data in terms of violation levels.
     *
     * @author Christophe
     */
    public function do_not_sell_check()
    {
        $this->load->library('Trackstreet_merchants');
        $this->load->library('Vision_users');
        $this->load->model('cron_m');
        $this->load->model('store_m');
        $this->load->model('merchants_m');
        $this->load->model('merchant_products_m');
        
        $email_send_count = 0;
        
        $cron_run_start = date('Y-m-d H:i:s');
        
        $today_date_str = date('Y-m-d');
        $yesterday_date_str = date('Y-m-d', strtotime('-1 days'));
        
        $start_time = strtotime($yesterday_date_str . ' 00:00:00');
        $end_time = strtotime($yesterday_date_str . ' 23:59:59');
        
        // go through all global system enabled stores
        $stores = $this->store_m->get_enabled_stores();
        
        foreach ($stores as $store)
        {
            $store_id = intval($store['id']);
            
            $store = $this->store_m->get_store_by_id_array($store_id);
            
            // determine if store wants the DNS list enabled
            $dns_list_enabled = $this->merchants_m->get_dns_setting_value($store_id, 'dns_list_enabled', 0);
            
            if ($dns_list_enabled == 0 || $dns_list_enabled == '0')
            {
                $this->cron_log_output .= 'DNS skipping store: ' . $store['store_name'] . "\n";
                
                // skip this store
                continue;
            }
            
            $this->cron_log_output .= 'DNS set to ON. Checking store: Store ID: ' . $store_id . ' - Name: ' . $store['store_name'] . "\n";
            
            // get setting to determine at above what level violators should land on DNS list
            $dns_min_violation_level = $this->merchants_m->get_dns_setting_value($store_id, 'notificaton_level_nums', 3);
            
            // find merchants at or above this level, and then check to see if they have had a violation today
            // with DNS settings page, user sets the min level that merchant has to be on before they get put on DNS list
            $merchant_ids = $this->merchants_m->get_merchant_ids_at_above_violation_level($store_id, $dns_min_violation_level);
            
            foreach ($merchant_ids as $merchant_id)
            {
                //$this->cron_log_output .= 'checking for violation - merchant ID: ' . $merchant_id . "\n";
                
                // check to see if merchant is already on DNS list
                $dns_merchant = $this->merchants_m->get_dns_merchant($store_id, $merchant_id);
                
                if (empty($dns_merchant))
                {
                    // check if merchant had a violation yesterday
                    $query_str = "
                        SELECT ptn.*
                        FROM products_trends_new ptn
                        INNER JOIN products p ON p.upc_code = ptn.upc
                        WHERE p.store_id = {$store_id}
                        AND ptn.mid = {$merchant_id}
                        AND ptn.mpo < ptn.ap
                        AND dt >= {$start_time}
                        AND dt <= {$end_time}
                        LIMIT 1
                    ";
                    
                    $violation = $this->db->query($query_str)->row_array();
                    
                    if (!empty($violation))
                    {
                        $this->cron_log_output .= 'New Violation Found - Adding merchant to DNS list: ' . $merchant_id . ' - store ID: ' . $store_id . "\n";
                        
                        // determine # of times merchant has been on DNS list
                        $num_of_times = $this->merchants_m->get_dns_added_count($store_id, $merchant_id);
                        
                        $is_permanent = $this->merchants_m->get_dns_setting_value($store_id, 'initial_permanent', 0);
                        
                        // add to DNS list
                        $insert_data = array(
                        		'store_id' => $store_id,
                        		'merchant_id' => $merchant_id,
                        		'num_of_times' => $num_of_times + 1,
                        		'is_permanent' => $is_permanent,
                        		'created' => date('Y-m-d H:i:s'),
                        		'modified' => date('Y-m-d H:i:s')
                        );
                        
                        $dns_list_id = $this->merchants_m->insert_dns_merchant($insert_data);
                        
                        // add DNS list period
                        
                        // determine end_date based on store DNS list settings
                        $end_date_str = $this->trackstreet_merchants->get_dns_end_date($store_id, $merchant_id);
                        
                        $insert_data = array(
                        		'store_id' => $store_id,
                        		'merchant_id' => $merchant_id,
                        		'period_num' => $num_of_times + 1,
                        		'start_date' => date('Y-m-d'),
                        		'end_date' => $end_date_str,
                        		'added_by' => 0,
                        		'removed_by' => 0,
                        		'created' => date('Y-m-d H:i:s'),
                        		'modified' => date('Y-m-d H:i:s')
                        );
                        
                        $this->merchants_m->insert_dns_period($insert_data);   

                        // insert history for merchant
                        $merchant_history_insert_data = array(
                        		'store_id' => $store_id,
                        		'merchant_id' => $merchant_id,
                        		'action_id' => 1, // see merchants_m->get_history_action_array()
                            'action_text' => 'TrackStreet added merchant to Do Not Sell list because violation was found that raised their level.',
                        		'created' => date('Y-m-d H:i:s'),
                        		'created_by' => 0,
                        		'modified' => date('Y-m-d H:i:s'),
                        		'modified_by' => 0
                        );
                        
                        $dns_merchant_history_insert_data = array(
                        		'store_id' => $store_id,
                        		'merchant_id' => $merchant_id,
                        		'action_id' => 1, // see merchants_m->get_history_action_array()
                            'action_text' => 'TrackStreet added merchant to Do Not Sell list because violation was found that raised their level.',
                        		'created' => date('Y-m-d H:i:s'),
                        		'created_by' => 0,
                        		'modified' => date('Y-m-d H:i:s'),
                        		'modified_by' => 0
                        );
                        
                        $this->merchants_m->insert_merchant_history_log($merchant_history_insert_data);
                        $this->merchants_m->insert_dns_merchant_history_log($dns_merchant_history_insert_data);
                        
                        $this->cron_log_output .= 'Added merchant to DNS list: ' . $merchant_id . ' - store ID: ' . $store_id . "\n";
                    }
                }
            }
        }
        
        $this->cron_log_output .= 'DNS List Check complete!' . "\n\n";     

        $insert_data = array(
            'function_name' => 'do_not_sell_check',
            'run_start_time' => $cron_run_start,
            'run_end_time' => date('Y-m-d H:i:s'),
            'output' => $this->cron_log_output,
            'created' => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s')                 
        );
        
        $this->cron_m->insert_cron_run_log($insert_data);
        
        echo 'do_not_sell_check() - complete!' . "\n\n";
        exit();
    } 

    /**
     * Cron to handle sending the DNS report email to users that are set up to receive it.
     * 
     * @author Christophe
     */
    public function do_not_sell_email_report()
    {
        $this->load->library('Trackstreet_merchants');
        $this->load->library('Trackstreet_email');
        $this->load->library('Vision_users');
        $this->load->model('cron_m');
        $this->load->model('store_m');
        $this->load->model('merchants_m');
        $this->load->model('merchant_products_m');
        
        $email_send_count = 0;
        
        $cron_run_start = date('Y-m-d H:i:s');
        
        $today_date_str = date('Y-m-d');
        $yesterday_date_str = date('Y-m-d', strtotime('-1 days'));
        
        $start_time = strtotime($yesterday_date_str . ' 00:00:00');
        $end_time = strtotime($yesterday_date_str . ' 23:59:59');
        
        // go through all global system enabled stores
        $stores = $this->store_m->get_enabled_stores();
        
        $this->cron_log_output .= 'Day of week: ' . serialize(date('w')) . "\n";
        $this->cron_log_output .= 'Hour: ' . serialize(date('G')) . "\n";
        
        foreach ($stores as $store)
        {
            $store_id = intval($store['id']);
            
            $store = $this->store_m->get_store_by_id_array($store_id);
            
            // determine if store wants the DNS list enabled
            $dns_list_enabled = $this->merchants_m->get_dns_setting_value($store_id, 'dns_list_enabled', 0);
            
            if ($dns_list_enabled == 0 || $dns_list_enabled == '0')
            {
                $this->cron_log_output .= 'Skipping store: ' . $store['store_name'] . "\n";
                
                // skip this store
                continue;
            }
            
            // frequency of report email - default: weekly on Sunday at midnight
            $email_freq = $this->merchants_m->get_dns_setting_value($store_id, 'dns_email_report_frequency', 'weekly');
            
            $send_report = FALSE;
            
            // determine if it is the correct time that they want to have the report sent
            if ($email_freq == 'daily')
            {
                // determine if it is the correct time that user wants to send
                $dns_email_report_time = intval($this->merchants_m->get_dns_setting_value($store_id, 'dns_email_report_time', 0));
                
                if (intval(date('G')) == $dns_email_report_time)
                {
                    $send_report = TRUE;
                }
            }
            else if ($email_freq == 'weekly')
            {
                // determine if it is the correct time and day of the week that user wants to send
                $dns_email_report_day = intval($this->merchants_m->get_dns_setting_value($store_id, 'dns_email_report_day', 0));  
                $dns_email_report_time = intval($this->merchants_m->get_dns_setting_value($store_id, 'dns_email_report_time', 0));   

                // check day/time to see if it matches send time setting put in for store
                if (intval(date('w')) == $dns_email_report_day && intval(date('G')) == $dns_email_report_time)
                {
                    $send_report = TRUE;
                }
            }
    
            // next check to see if we should send an email report for store to users
            if ($send_report)
            {
                $this->cron_log_output .= 'Sending report to store: ' . $store['store_name'] . "\n";
                
                // a merchant should only appear on 1 list - keep track here
                $already_on_a_list = array();
    
                // get current DNS list merchants
                $dns_merchants = $this->merchants_m->get_dns_merchants($store_id);
    
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
    
                    $dns_period = $this->merchants_m->get_most_recent_dns_period($store_id, $merchant_id);
    
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
    
                $view_data['dns_merchants'] = $dns_merchants;
    
        				$html_message = $this->load->view('enforcement/do_not_sell_email_report', $view_data, TRUE);
    
        				$text_message = strip_tags($html_message);
        				 
        				// get the subject from their setting value				
        				$default_subject = '[TrackStreet] Do Not Sell List Report - ' . date('m-d-Y');
        				$subject = $this->merchants_m->get_dns_setting_value($store_id, 'email_subject', $default_subject);
    
        				// get email addresses that are set to store for receiving report
        				$email_addresses = $this->merchants_m->get_dns_notify_emails($store_id);
        				
        				//$email_send_count += count($email_addresses);
        				
        				$email = implode(',', $email_addresses);
        				
        				//var_dump($email); exit();
        				
        				$this->cron_log_output .= 'Sending DNS report email to: ' . $email . "\n";
        				
        				// determine if we use their global SMTP or DNS-specific SMTP settings
        				$dns_use_global_smtp_settings = $this->merchants_m->get_dns_setting_value($store_id, 'dns_use_global_smtp_settings', 'yes');
    
        				if ($dns_use_global_smtp_settings == 'yes')
        				{
        				    $this->cron_log_output .= 'Using global SMTP settings for store: ' . $store['store_name'] . "\n";
        				    
        				    $smtp_settings = array(
		                    'email_to' => $email,
		                    'email_subject' => $subject,
		                    'email_body' => $html_message,
            				    'host' => $smtp_settings['smtp_host'],
            				    'port' => $smtp_settings['smtp_port'],
            				    'use_ssl' => $smtp_settings['smtp_ssl'],
            				    'use_tls' => $smtp_settings['smtp_tls'],
            				    'username' => $smtp_settings['smtp_username'],
            				    'password' => $smtp_settings['smtp_password']
        				    );
        				}
        				else
        				{
        				    $this->cron_log_output .= 'Using DNS SMTP settings for store: ' . $store['store_name'] . "\n";
        				    
        				    $use_ssl = $this->merchants_m->get_dns_setting_value($store_id, 'dns_smtp_use_ssl', '') == 'yes' ? TRUE : FALSE;
        				    $use_tls = $this->merchants_m->get_dns_setting_value($store_id, 'dns_smtp_use_tls', '') == 'yes' ? TRUE : FALSE;
        				    
        				    $smtp_settings = array(
		                    'email_to' => $email,
        				        'email_from' => $this->merchants_m->get_dns_setting_value($store_id, 'dns_email_from', ''),            
		                    'email_subject' => $subject,
		                    'email_body' => $html_message,
        				    		'smtp_host' => $this->merchants_m->get_dns_setting_value($store_id, 'dns_smtp_host', ''),
        				    		'smtp_port' => $this->merchants_m->get_dns_setting_value($store_id, 'dns_smtp_port', ''),
        				    		'smtp_ssl' => $use_ssl,
        				    		'smtp_tls' => $use_tls,
        				    		'smtp_username' => $this->merchants_m->get_dns_setting_value($store_id, 'dns_smtp_username', ''),
        				    		'smtp_password' => $this->merchants_m->get_dns_setting_value($store_id, 'dns_smtp_password', '')
        				    );
        				}
        				
        				// attempt to send email using SMTP settings (either global or DNS SMTP)
        				if ($this->trackstreet_email->send_smtp_email($store_id, $smtp_settings) == TRUE)
        				{
        				    $email_send_count += count($email_addresses);
        				}
            }
        }
        
        $this->cron_log_output .= 'Emails sent: ' . $email_send_count . "\n";
        
        $insert_data = array(
        		'function_name' => 'do_not_sell_email_report',
        		'run_start_time' => $cron_run_start,
        		'run_end_time' => date('Y-m-d H:i:s'),
        		'output' => $this->cron_log_output,
        		'created' => date('Y-m-d H:i:s'),
        		'modified' => date('Y-m-d H:i:s')
        );
        
        $this->cron_m->insert_cron_run_log($insert_data);
        
        echo 'do_not_sell_email_report() - complete!' . "\n";
        
        exit();
    }
    
    /**
     * Aggregate merchant related stats for 1 day.
     * 
     * @author Christophe
     */
    public function merchant_data($current_date_str = FALSE)
    {
        //ini_set('memory_limit', '1024M');
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);
        
        $this->load->library('Trackstreet_merchants');
        $this->load->model('crowl_merchant_name_m');
        $this->load->model('merchants_m');  

        if ($current_date_str == FALSE)
        {
            // if param not set, then go over previous day
            $current_date_str = date('Y-m-d', strtotime('-1 days'));
        }

        // Following code is used for going back over a date range - need to delete rows in merchant_agg_cron_log
        // and then run via cron. Use following cron to go back through dates:
        // */5 * * * * curl https://app.trackstreet.com/cron/merchant_data
        /*
        if ($current_date_str == FALSE)
        {
            // check to see when this last run and do previous day
            $previous_run_date_str = $this->merchants_m->get_last_merchant_cron_run();
            
            if ($previous_run_date_str != FALSE)
            {
                echo 'Last process date: ' . $previous_run_date_str . "\n";
                
                $current_date_str = date('Y-m-d', strtotime($previous_run_date_str . ' -1 day'));
            }
            else
            {
                $current_date_str = date('Y-m-d', strtotime('-1 days'));
            } 
        }
        */
        
        echo 'Now Processing: ' . $current_date_str . "\n";
        
        $trend_data_query =
        'SELECT DISTINCT mid, pid, ap, mpo ' .
        "FROM {$this->_table_products_trends} " .
        "WHERE dt >= " . strtotime($current_date_str . ' 00:00:00') . ' ' .
        "AND dt <= " . strtotime($current_date_str . ' 23:59:59');
        
        //var_dump($trend_data_query); exit();
        
        $trend_rows = $this->db->query($trend_data_query)->result_array();
        
        //var_dump(count($trend_rows)); exit();
        
        echo 'Cron Start' . "\n";
        
        echo 'Memory: ' . memory_get_usage() . "\n";
        
        // start fresh and clear existing count records
        $this->trackstreet_merchants->clear_merchant_count_records($current_date_str);
        
        $multi_check = array();
        
        for ($i = 0; $i < count($trend_rows); $i++)
        {
            $merchant_id = intval($trend_rows[$i]['mid']);
            $product_id = intval($trend_rows[$i]['pid']);

            // get details on product
            $product = $this->products_m->get_product_by_id($product_id);

            if (!empty($product))
            {
                $store_id = intval($product['store_id']);

                // check to make sure we don't count more than 1 crawl's data per day
                if (isset($multi_check[$merchant_id][$product_id]))
                {
                    if ($multi_check[$merchant_id][$product_id] === TRUE)
                    {
                        // skip duplicates
                        // we scan more than once a day so there may be more than 1 record in products_trends_new table in 1 day
                    }
                    else
                    {
                        // if MAP price greater than merchant price
                        // @todo check to see if promotional pricing is set on product - use promo price instead of MAP price with comparison
                        if ($trend_rows[$i]['ap'] > $trend_rows[$i]['mpo'])
                        {
                            // update violations_per_merchant_per_day row
                            $this->trackstreet_merchants->record_merchant_store_violation($merchant_id, $store_id, $current_date_str);
                            
                            echo 'Memory: ' . memory_get_usage() . "\n";

                            $multi_check[$merchant_id][$product_id] = TRUE; // price violation found
                        }
                        else
                        {
                            $multi_check[$merchant_id][$product_id] = FALSE; // price was not in violation
                        }                   
                    }
                }
                else
                {
                    // if MAP price greater than merchant price
                    // @todo check to see if promotional pricing is set on product - use promo price instead of MAP price with comparison
                    if ($trend_rows[$i]['ap'] > $trend_rows[$i]['mpo'])
                    {
                        $this->trackstreet_merchants->record_merchant_store_violation($merchant_id, $store_id, $current_date_str);

                        $multi_check[$merchant_id][$product_id] = TRUE; // price violation found
                    }
                    else
                    {
                        $multi_check[$merchant_id][$product_id] = FALSE; // price was not in violation
                    }
                    
                    // insert or update product count for merchant/store
                    $this->trackstreet_merchants->record_merchant_store_product_count($merchant_id, $store_id, $current_date_str);
                    
                    //echo 'Memory: ' . memory_get_usage() . "\n";
                }
            }        
        }
        
        echo 'Memory: ' . memory_get_usage() . "\n";
        
        $trend_rows_processed = count($trend_rows);
        
        $this->trackstreet_merchants->record_merchant_cron_log($current_date_str, $trend_rows_processed);
        
        echo 'merchant_data cron done! - product trend rows: ' . $trend_rows_processed;
        
        exit();
    }
    
    /**
     * Go through a number of dates to compute # of violations for that date.
     * 
     * @author Christophe
     * @param string $start_date_str
     * @param string $end_date_str
     */
    public function violations_date_range($start_date_str, $end_date_str)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 3600);
        
        $process = TRUE;
        $current_date_str = $start_date_str;
        
        while ($process == TRUE)
        {
            echo 'Processing date: ' . $current_date_str . "\n";
            
            $this->count_violations_for_day($current_date_str, 'true');
            
            if ($current_date_str == $end_date_str)
            {
                $process = FALSE;
            }
            else
            {
                $current_date_str = date('Y-m-d', strtotime($current_date_str . ' +1 day'));
            }
            
            echo 'Memory: ' . memory_get_usage() . "\n";
        }
        
        exit();
    }
}
