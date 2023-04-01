<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Merchants_m extends MY_Model 
{
    /**
     * Delete a row from the merchant_contacts table.
     * 
     * @author Christophe
     * @param int $contact_id
     */
    public function delete_merchant_contact($contact_id)
    {
        $this->db->where('id', $contact_id);
        $this->db->delete('merchant_contacts');
    }
    
    /**
     * Delete a row from the merchant_do_not_sell_list table.
     * 
     * @author Christophe
     * @param int $store_id
     * @param int $merchant_id
     */
    public function delete_dns_merchant($store_id, $merchant_id)
    {
        $this->db->where('store_id', $store_id);
        $this->db->where('merchant_id', $merchant_id);
        $this->db->delete('merchant_do_not_sell_list');        
    }
    
    /**
     * Delete rows from the merchant_do_not_sell_notify table.
     * 
     * @author Christophe
     * @param int $store_id
     */
    public function delete_dns_notify_emails($store_id)
    {
        $this->db->where('store_id', $store_id);
        $this->db->delete('merchant_do_not_sell_notify');        
    }
    
    /**
     * Clear all DNS time period settings.
     * 
     * @author Christophe
     * @param int $store_id
     */
    public function delete_dns_time_period_settings($store_id)
    {
        $this->db->where('store_id', $store_id);
        $this->db->like('name', 'remain_on_list_days_offense');
        $this->db->delete('merchant_do_not_sell_settings');        
    }
    
    /**
     * Fetch all rows from crowl_merchant_name_new table.
     * 
     * @author Christophe
     * @return array
     */
    public function get_all_merchants()
    {
        $this->db->select('*');
        $this->db->from('crowl_merchant_name_new');
        $this->db->order_by('id', 'desc');
         
        $query = $this->db->get();
         
        return $query->result_array();        
    }
    
    /**
     * Get all rows from violator_notifications table.
     * 
     * @author Christophe
     * @return array
     */
    public function get_all_violator_notification_contacts()
    {
        $this->db->select('*');
        $this->db->from('violator_notifications');
         
        $query = $this->db->get();
         
        return $query->result_array();        
    }
    
    /**
     * Get a contact type label by type ID.
     * 
     * @author Christophe
     * @param int $type_id
     * @return string
     */
    public function get_contact_label_from_type($type_id)
    {
        $type_id = intval($type_id);
        
        switch ($type_id)
        {
            case 1:
                $type_label = 'Primary Contact';
                break;
            case 2:
                $type_label = 'Account Rep';
                break;
            case 3:
                $type_label = 'CC Address';
                break;    
            default:
                $type_label = 'Unknow Contact Type';               
        }
        
        return $type_label;
    }
    
    /**
     * Determine how many times a merchant has been added to the DNS list.
     * 
     * @author Christophe
     * @param int $store_id
     * @param int $merchant_id
     * @return int
     */
    public function get_dns_added_count($store_id, $merchant_id)
    {
        $store_id = intval($store_id);
        $merchant_id = intval($merchant_id);
        
        $this->db->select('COUNT(*) as added_count');
        $this->db->from('merchant_do_not_sell_periods');
        $this->db->where('merchant_id', $merchant_id);
        $this->db->where('store_id', $store_id);
        
        $query = $this->db->get();
        
        $row = $query->row_array();
        
        $count = (empty($row)) ? 0 : intval($row['added_count']);
        
        return $count;        
    }
    
    /**
     * Get DNS merchants for store.
     *
     * @author Christophe
     * @param int $store_id
     * @param int $merchant_id
     * @return array
     */
    public function get_dns_merchant($store_id, $merchant_id)
    {
        $store_id = intval($store_id);
        $merchant_id = intval($merchant_id);
        
        $this->db->select('*');
        $this->db->from('merchant_do_not_sell_list');
        $this->db->where('store_id', $store_id);
        $this->db->where('merchant_id', $merchant_id);
        
        $query = $this->db->get();
        
        return $query->row_array();
    }    
    
    /**
     * Get DNS merchants for store.
     * 
     * @author Christophe
     * @param int $store_id
     * @return array
     */
    public function get_dns_merchants($store_id)
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('merchant_do_not_sell_list');
        $this->db->where('store_id', $store_id);
        
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
    /**
     * Get DNS notify email address.
     *
     * @author Christophe
     * @param int $store_id
     * @return array
     */
    public function get_dns_notify_emails($store_id)
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('merchant_do_not_sell_notify');
        $this->db->where('store_id', $store_id);
        
        $query = $this->db->get();
        
        $rows = $query->result_array();
        
        if (empty($rows))
        {
            return $rows;
        }
        else 
        {
            $emails = array();
            
            foreach ($rows as $row)
            {
                $emails[] = $row['email'];
            }
            
            return $emails;
        }
    } 

    /**
     * Get DNS notify email address for people that aren't team members.
     *
     * @author Christophe
     * @param int $store_id
     * @return array
     */
    public function get_dns_notify_external_emails($store_id)
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('merchant_do_not_sell_notify');
        $this->db->where('store_id', $store_id);
        $this->db->where('user_id', 0);
        
        $query = $this->db->get();
        
        $rows = $query->result_array();
        
        return $rows;
    }    
    
    /**
     * Get number of defined DNS offense periods for store.
     * 
     * @author Christophe
     * @param int $store_id
     * @return int
     */
    public function get_dns_offense_time_setting_count($store_id)
    {
        $this->db->select('*');
        $this->db->from('merchant_do_not_sell_settings');
        $this->db->where('store_id', $store_id);
        $this->db->like('name', 'remain_on_list_days_offense');
         
        $query = $this->db->get();
         
        $rows = $query->result_array();

        if (empty($rows))
        {
            return 1;
        }
        else
        {
            return count($rows);
        }
    }
    
    /**
     * Get DNS list periods for a store merchant.
     * 
     * @author Christophe
     * @param int $store_id
     * @param int $merchant_id
     * @return array
     */
    public function get_dns_periods_for_merchant($store_id, $merchant_id)
    {
        $store_id = intval($store_id);
        $merchant_id = intval($merchant_id);
        
        $this->db->select('*');
        $this->db->from('merchant_do_not_sell_periods');
        $this->db->where('store_id', $store_id);
        $this->db->where('merchant_id', $merchant_id);
        
        $query = $this->db->get();
        
        return $query->result_array();        
    }
    
    /**
     * Get a single row from merchant_do_not_sell_settings table.
     *
     * @author Christophe
     * @param int $store_id
     * @param string $setting_name
     * @return array
     */
    public function get_dns_setting($store_id, $setting_name)
    {
        $this->db->select('*');
        $this->db->from('merchant_do_not_sell_settings');
        $this->db->where('store_id', $store_id);
        $this->db->where('name', $setting_name);
         
        $query = $this->db->get();
         
        return $query->row_array();
    } 

    /**
     * Get a single row from merchant_do_not_sell_settings table.
     *
     * @author Christophe
     * @param int $store_id
     * @param string $setting_name
     * @return array
     */
    public function get_dns_setting_value($store_id, $setting_name, $default_value = NULL)
    {
        $this->db->select('*');
        $this->db->from('merchant_do_not_sell_settings');
        $this->db->where('store_id', $store_id);
        $this->db->where('name', $setting_name);

        $query = $this->db->get();

        $row = $query->row_array();

        if (!empty($row))
        {
            return $row['value'];
        }
        else
        {
            return $default_value;
        }
    }    
    
    /**
     * Get record for when merchant was first crawled.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @return array
     */
    public function get_first_crawl_record($merchant_id)
    {
        $merchant_id = intval($merchant_id);
        
        $query_str = "
            SELECT *
            FROM products_trends_new
            WHERE mid = {$merchant_id}
            ORDER BY dt ASC
            LIMIT 1
        ";
        
        return $this->db->query($query_str)->row_array();        
    }
    
    /**
     * Find first occurence of product(s) being tracked for merchants.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @return array
     */
    public function get_first_merchant_store_record($merchant_id, $store_id)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        
        $query_str = "
            SELECT *
            FROM products_per_merchant_per_day
            WHERE merchant_id = {$merchant_id}
            AND store_id = {$store_id}
            AND product_count >= 1
            ORDER BY select_date ASC
            LIMIT 1                      
        ";
        
        return $this->db->query($query_str)->row_array();
    }
    
    /**
     * Get the last day the merchant agg cron ran over. Agg cron is going back in time
     * 1 day each run.
     *
     * @author Christophe
     * @return string
     */
    public function get_last_merchant_cron_run()
    {
        $query_str = "
            SELECT *
            FROM merchant_agg_cron_log
            ORDER BY run_date ASC
            LIMIT 1
        ";
        
        $row = $this->db->query($query_str)->row_array();
        
        if (empty($row))
        {
            return FALSE;
        }
        else
        {
            return $row['run_date'];
        }       
    } 
    
    /**
     * Get last notice row that was sent to merchant for store.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @return array
     */
    public function get_last_notice_sent($merchant_id, $store_id)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('violator_notifications_history');
        $this->db->where('store_id', $store_id);
        $this->db->where('crowl_merchant_name_id', $merchant_id);
        $this->db->order_by('date', 'desc');
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        return $query->row_array();        
    }
    
    /**
     * Get last violation for date.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @return array
     */
    public function get_last_violation($merchant_id, $store_id)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        
        $query_str = "
            SELECT *
            FROM violations_per_merchant_per_day
            WHERE merchant_id = {$merchant_id}
            AND store_id = {$store_id}
            AND violation_count >= 1
            ORDER BY select_date DESC
            LIMIT 1                        
        ";
        
        return $this->db->query($query_str)->row_array();
    }
    
    /**
     * Get array for action IDs.
     * 
     * @author Christophe
     * @return array
     */
    public function get_history_action_array()
    {
        $history_array = array(
            1 => 'Added to Do Not Sell List',
            2 => 'Removed from Do Not Sell List',            
            3 => 'Violation Level Changed',
            4 => 'Merchant Details Changed',
            5 => 'Primary Contact Added',
            6 => 'Account Rep Contact Added',
            7 => 'CC Address Contact Added',
            8 => 'Do Not Sell List Removal Date Changed',
            9 => 'Permanently Added to Do Not Sell List',
            10 => 'Do Not Sell List Status Set to Temporary'                                                                                                                           
        );
        
        return $history_array;
    }
    
    /**
     * Get last import version number.
     * 
     * @author Christophe
     * @param int $store_id
     * @return int
     */
    public function get_last_merchant_data_version_num($store_id)
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('merchant_data_import_log');
        $this->db->where('store_id', $store_id);
        $this->db->order_by('created', 'desc');
        $this->db->limit(1);
         
        $query = $this->db->get();
         
        $row = $query->row_array();     

        if (empty($row))
        {
            return 0;
        }
        else
        {
            return intval($row['version_num']);
        }
    }

    /**
     * Get last import version number.
     *
     * @author Christophe
     * @param int $store_id
     * @return int
     */    
    public function get_last_merchant_contact_data_version_num($store_id)
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('merchant_contact_data_import_log');
        $this->db->where('store_id', $store_id);
        $this->db->order_by('created', 'desc');
        $this->db->limit(1);
         
        $query = $this->db->get();
         
        $row = $query->row_array();
        
        if (empty($row))
        {
            return 0;
        }
        else
        {
            return intval($row['version_num']);
        }        
    }
    
    /**
     * Get a row from the crowl_merchant_name_new table.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @return array
     */
    public function get_merchant_by_id($merchant_id)
    {
        $merchant_id = intval($merchant_id);
        
        $this->db->select('*');
        $this->db->from('crowl_merchant_name_new');
        $this->db->where('id', $merchant_id);
         
        $query = $this->db->get();
         
        return $query->row_array();        
    }
    
    /**
     * Get merchant by marketplace column.
     *  
     * @author Christophe
     * @param string $marketplace
     * @return array
     */
    public function get_merchant_by_marketplace($marketplace)
    {        
        $this->db->select('*');
        $this->db->from('crowl_merchant_name_new');
        $this->db->where('marketplace', $marketplace);
         
        $query = $this->db->get();
         
        return $query->row_array();        
    }
    
    /**
     * Get a single merchant by marketplace and seller ID.
     * 
     * @author Christophe
     * @param string $marketplace
     * @param int $seller_id
     * @return array
     */
    public function get_merchant_by_marketplace_seller_id($marketplace, $seller_id)
    {
        $seller_id = intval($seller_id);
        
        $this->db->select('*');
        $this->db->from('crowl_merchant_name_new');
        $this->db->where('marketplace', $marketplace);
        $this->db->where('seller_id', $seller_id);
         
        $query = $this->db->get();
         
        return $query->row_array();        
    }
    
    /**
     * Find a retailer merchant by original name (TLD name) without "www" and without ".com".
     * 
     * @author Christophe
     * @param string $original_name
     * @return array
     */
    public function get_merchant_by_original_name($original_name)
    {
        $this->db->select('*');
        $this->db->from('crowl_merchant_name_new');
        // retailers: marketplace = original_name
        $this->db->where('marketplace', $original_name); 
        $this->db->where('original_name', $original_name);
         
        $query = $this->db->get();
         
        return $query->row_array();        
    }
    
    /**
     * Get history of a merchant for a store.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @return array
     */
    public function get_merchant_history($merchant_id, $store_id)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('merchant_history_log');
        $this->db->where('merchant_id', $merchant_id);
        $this->db->where('store_id', $store_id);
        $this->db->order_by('created', 'desc');
         
        $query = $this->db->get();
         
        return $query->result_array();        
    }
    
    /**
     * Get the name for a merchant that we should display to users.
     * 
     * @author Christophe
     * @param array $merchant
     * @param boolean $for_profile
     * @return string
     */
    public function get_merchant_human_name($merchant, $for_profile = FALSE)
    {
        if (isset($merchant['seller_id']) && isset($merchant['marketplace']))
        {
            if ($merchant['seller_id'] != $merchant['marketplace'])
            {
                $merchant_name = $merchant['original_name'];
                
                if ($for_profile)
                {
                    $merchant_name .= ' (' . ucfirst($merchant['marketplace']) . ' seller)';
                }
            }
            else
            {
                $merchant_name = str_replace('http://', '', $merchant['merchant_url']);
                
                $merchant_name = str_replace('https://', '', $merchant_name);
                
                $merchant_name = str_replace('www.', '', $merchant_name);
            }
        }
        else
        {
            $merchant_name = 'Merchant Name N/A';
        }
        
        return $merchant_name;
    }
    
    /**
     * Find all merchants that we are currently crawling or have crawled for a specific
     * brand (aka store).
     * 
     * @author Christophe
     * @param int $store_id
     * @return array
     */
    public function get_all_merchants_for_store($store_id)
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('violator_notifications');
        $this->db->where('store_id', $store_id);
         
        $query = $this->db->get();
         
        return $query->result_array();        
    }
    
    /**
     * Find merchants at, or above, a violation level.
     * 
     * @param int $store_id
     * @param int $violation_level
     * @return array
     */
    public function get_merchant_ids_at_above_violation_level($store_id, $violation_level)
    {
        $store_id = intval($store_id);
        $violation_level = intval($violation_level);
        
        $this->db->select('crowl_merchant_name_id');
        $this->db->from('violator_notifications_history');
        $this->db->where('store_id', $store_id);
        $this->db->where('email_level >=', $violation_level);
        $this->db->group_by('crowl_merchant_name_id');
         
        $query = $this->db->get();
         
        $results = $query->result_array();   

        $merchant_ids = array();
        
        foreach ($results as $result)
        {
            $merchant_id = intval($result['crowl_merchant_name_id']);
            
            $merchant_ids[] = $merchant_id;
        }
        
        return $merchant_ids;
    }
    
    /**
     * Get all merchant records for a store.
     *
     * @author Christophe
     * @param int $store_id
     * @param string $start
     * @param string $end
     * @return array
     */
    public function get_merchants_by_store($store_id, $merchant_type = 'all', $start = FALSE, $end = FALSE)
    {
        $store_id = intval($store_id);

        $this->db->select('cmn.*');
        $this->db->from('crowl_merchant_name_new cmn');
        $this->db->join('violator_notifications vn', 'vn.crowl_merchant_name_id = cmn.id', 'inner');
        $this->db->join('crowl_product_list_new cpl', 'cpl.merchant_name_id = cmn.id', 'inner');
        $this->db->where('vn.store_id', $store_id);

        if ($start != FALSE && $end != FALSE)
        {
            $this->db->where('cpl.last_date >=', strtotime($start));

            $this->db->where('cpl.last_date <=', (strtotime($end) + (24 * 60 * 60)));
        }

        if ($merchant_type != 'all')
        {
            $this->db->join('marketplaces mp', 'mp.name = cmn.marketplace');

            if ($merchant_type == 'retailers')
            {
                $this->db->where('mp.is_retailer', 1);
            }
            else
            {
                $this->db->where('mp.is_retailer', 0);
            }
        }

        $this->db->group_by('cmn.id');
        $this->db->order_by('cmn.original_name', 'asc');

        $query = $this->db->get();

        return $query->result_array();
    }
    
    /**
     * Get single row from merchant_contacts table.
     * 
     * @author Christophe
     * @param int $id
     * @return array
     */
    public function get_merchant_contact_by_id($id)
    {
        $id = intval($id);
        
        $this->db->select('*');
        $this->db->from('merchant_contacts');
        $this->db->where('id', $id);
         
        $query = $this->db->get();
         
        return $query->row_array();        
    }
    
    /**
     * Get a merchant contact for a store by their email.
     *
     * @author Christophe
     * @param int $store_id
     * @param int $merchant_id
     * @param int $phone
     * @return array
     */
    public function get_merchant_contact_by_email($store_id, $merchant_id, $email)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('merchant_contacts');
        $this->db->where('store_id', $store_id);
        $this->db->where('merchant_id', $merchant_id);
        $this->db->where('email', $email);
         
        $query = $this->db->get();
         
        return $query->row_array();
    }    
    
    /**
     * Get a merchant contact for a store by their phone number.
     * 
     * @author Christophe
     * @param int $store_id
     * @param int $merchant_id
     * @param int $phone
     * @return array
     */
    public function get_merchant_contact_by_phone($store_id, $merchant_id, $phone)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('merchant_contacts');
        $this->db->where('store_id', $store_id);
        $this->db->where('merchant_id', $merchant_id);
        $this->db->where('phone', $phone);
         
        $query = $this->db->get();
         
        return $query->row_array();        
    }
    
    /**
     * Get a contact row by its UUID.
     * 
     * @author Christophe
     * @param string $contact_uuid
     * @return array
     */
    public function get_merchant_contact_by_uuid($contact_uuid)
    {        
        $this->db->select('*');
        $this->db->from('merchant_contacts');
        $this->db->where('uuid', $contact_uuid);
         
        $query = $this->db->get();
         
        return $query->row_array();        
    }
    
    /**
     * Get all contacts that a customer has entered in for a merchant.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @return array
     */
    public function get_merchant_contacts_by_store($merchant_id, $store_id)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('merchant_contacts');
        $this->db->where('merchant_id', $merchant_id);
        $this->db->where('store_id', $store_id);
         
        $query = $this->db->get();
         
        return $query->result_array();        
    }
    
    /**
     * Get merchant contacts for store by type.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @param int $type_id
     * @return array
     */
    public function get_merchant_contacts_by_type($merchant_id, $store_id, $type_id)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        $type_id = intval($type_id);
        
        $this->db->select('*');
        $this->db->from('merchant_contacts');
        $this->db->where('merchant_id', $merchant_id);
        $this->db->where('store_id', $store_id);
        $this->db->where('type_id', $type_id);
         
        $query = $this->db->get();
         
        return $query->result_array();    
    }

    /**
     * Get row from merchant_agg_cron_log.
     * 
     * @author Christophe
     * @param string $date_str
     */
    public function get_merchant_cron_log_row($date_str)
    {
        $this->db->select('*');
        $this->db->from('merchant_agg_cron_log');
        $this->db->where('run_date', $date_str);
         
        $query = $this->db->get();
         
        return $query->row_array();        
    }

    /**
     * Get a single row from merchant_settings table.
     *
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @param string $setting_name
     * @return array
     */
    public function get_merchant_setting($merchant_id, $store_id, $setting_name)
    {
        $this->db->select('*');
        $this->db->from('merchant_settings');
        $this->db->where('merchant_id', $merchant_id);
        $this->db->where('store_id', $store_id);
        $this->db->where('name', $setting_name);
         
        $query = $this->db->get();
         
        return $query->row_array();
    }    
    
    /**
     * Get a single row from merchant_settings table.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @param string $setting_name
     * @return array
     */
    public function get_merchant_setting_value($merchant_id, $store_id, $setting_name)
    {
        $this->db->select('*');
        $this->db->from('merchant_settings');
        $this->db->where('merchant_id', $merchant_id);
        $this->db->where('store_id', $store_id);
        $this->db->where('name', $setting_name);
         
        $query = $this->db->get();
         
        $row = $query->row_array();

        if (!empty($row))
        {
            return $row['value'];
        }
        else
        {
            return NULL;
        }
    }
    
    /**
     * Get most recent period a merchant was listed on DNS List.
     * 
     * @author Christophe
     * @param int $store_id
     * @param int $merchant_id
     * @return array
     */
    public function get_most_recent_dns_period($store_id, $merchant_id)
    {
        $store_id = intval($store_id);
        $merchant_id = intval($merchant_id);
        
        $this->db->select('*');
        $this->db->from('merchant_do_not_sell_periods');
        $this->db->where('store_id', $store_id);
        $this->db->where('merchant_id', $merchant_id);
        $this->db->order_by('end_date', 'desc');
        $this->db->limit(1);
        
        $query = $this->db->get();
         
        return $query->row_array();    
    }
    
    /**
     * Get all notifications for a store.
     * 
     * @author Christophe
     * @param int $store_id
     */
    public function get_notification_history_for_store($store_id)
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('violator_notifications_history');
        $this->db->where('store_id', $store_id);
        $this->db->order_by('id', 'desc');
         
        $query = $this->db->get();
         
        return $query->result_array();        
    }
    
    /**
     * Determine number of notification levels that a store has defined.
     * 
     * @author Christophe
     * @param int $store_id
     * @return int|boolean
     */
    public function get_notification_levels_num($store_id)
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('violator_notification_email_settings');
        $this->db->where('store_id', $store_id);
         
        $query = $this->db->get();
         
        $row = $query->row_array();
        
        if (!empty($row))
        {
            return intval($row['notification_levels']);
        }
        else
        {
            return FALSE;
        }        
    }
    
    /**
     * Get notified records for a specific violation notice email level.
     * 
     * @author Christophe
     * @param int $store_id
     * @param int $level_num
     */
    public function get_notification_merchants_by_level($store_id, $level_num)
    {
        $store_id = intval($store_id);
        $level_num = intval($level_num);
        
        $this->db->select('*');
        $this->db->from('violator_notifications_history');
        $this->db->where('store_id', $store_id);
        $this->db->where('email_level', $level_num);
         
        $query = $this->db->get();
         
        return $query->result_array();        
    }
    
    /**
     * Find merchants by their original_name column value.
     * 
     * @author Christophe
     * @param string $original_name
     * @param int $not_merchant_id
     * @return array
     */
    public function get_other_merchants_by_original_name($original_name, $not_merchant_id)
    {
        $this->db->select('*');
        $this->db->from('crowl_merchant_name_new');
        $this->db->where('original_name', $original_name);
        $this->db->where('id !=', $not_merchant_id);
        $this->db->order_by('original_name', 'asc');
         
        $query = $this->db->get();
         
        return $query->result_array();        
    }
    
    /**
     * Find other merchant records linked to this one.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @return array
     */
    public function get_other_merchants_by_parent_id($merchant_id)
    {
        $merchant_id = intval($merchant_id);
        
        $this->db->select('*');
        $this->db->from('crowl_merchant_name_new');
        $this->db->where('parent_merchant_id', $merchant_id);
        $this->db->order_by('original_name', 'asc');
         
        $query = $this->db->get();
         
        return $query->result_array();        
    }
    
    /**
     * Get sent notices for merchant.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     */
    public function get_notifications_sent_to_merchant($merchant_id, $store_id, $start = FALSE, $end = FALSE)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('violator_notifications_history');
        $this->db->where('store_id', $store_id);
        $this->db->where('crowl_merchant_name_id', $merchant_id);
        
        if ($start !== FALSE)
        {
            $this->db->where('date >=', $start);
        }
        
        if ($end !== FALSE)
        {
            $this->db->where('date <=', $end);
        }
        
        $this->db->order_by('date', 'desc');
        
        $query = $this->db->get();
        
        return $query->result_array();        
    }
    
    /**
     * Find other merchant records that this marketplace merchant has gone by.
     * 
     * 
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param string $seller_id
     * @param string $marketplace
     * @return array
     */
    public function get_other_marketplace_profiles($merchant_id, $seller_id, $marketplace)
    {
        /*
        
        Query from Chris F to find marketplace seller renames:
        
        select * from (
            SELECT count(crowl_merchant_name_new.seller_id) as cnt,
            `crowl_merchant_name_new`.`seller_id`,
            `crowl_merchant_name_new`.`created`,
            `crowl_merchant_name_new`.`id`,
            `crowl_merchant_name_new`.`logo_img_url`,
            `crowl_merchant_name_new`.`marketplace`,
            `crowl_merchant_name_new`.`merchant_name`,
            `crowl_merchant_name_new`.`merchant_url`,
            `crowl_merchant_name_new`.`original_name`
             FROM crowl_merchant_name_new
             WHERE marketplace = 'amazon'
            group by seller_id
        ) as derived
        where derived.cnt > 1
        order by derived.cnt desc;
        
        */
        
        $merchant_id = intval($merchant_id);
        
        $this->db->select('*');
        $this->db->from('crowl_merchant_name_new');
        $this->db->where('id !=', $merchant_id);
        $this->db->where('seller_id', $seller_id);
        $this->db->where('marketplace', $marketplace);
        $this->db->order_by('original_name', 'asc');
         
        $query = $this->db->get();
         
        return $query->result_array();        
    }
    
    /**
     * Get count of products being indexed for merchant.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @param string $start
     * @param string $end
     * @return int
     */
    public function get_product_count_for_merchant($merchant_id, $store_id, $start = FALSE, $end = FALSE)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);   

        $this->db->select('COUNT(*) as product_count');
        $this->db->from('crowl_product_list_new cpl');
        $this->db->join('products p', 'p.upc_code = cpl.upc');
        $this->db->where('cpl.merchant_name_id', $merchant_id);
        $this->db->where('p.store_id', $store_id);
        
        if ($start != FALSE && $end != FALSE)
        {
            $this->db->where('last_date >=', strtotime($start));
            $this->db->where('last_date <=', (strtotime($end) + (24 * 60 * 60)));  
        }

        $query = $this->db->get();
        
        $row = $query->row_array();
        
        $count = (empty($row)) ? 0 : $row['product_count'];

        return $count;
    }   
     
    /**
     * Get product counts per day for a date range - that are listed with a merchant.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    public function get_products_listed_for_date_range($merchant_id, $store_id, $start_date, $end_date)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('products_per_merchant_per_day');
        $this->db->where('merchant_id', $merchant_id);
        $this->db->where('store_id', $store_id);
        $this->db->where('select_date >=', $start_date);
        $this->db->where('select_date <=', $end_date);
        $this->db->order_by('select_date', 'asc');
         
        $query = $this->db->get();
         
        return $query->result_array();        
    }
    
    /**
     * Find a product record by the primary key ID.
     *
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @param string $current_date_str
     * @return array
     */
    public function get_products_per_merchant_per_day($merchant_id, $store_id, $current_date_str)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('products_per_merchant_per_day');
        $this->db->where('merchant_id', $merchant_id);
        $this->db->where('store_id', $store_id);
        $this->db->where('select_date', $current_date_str);
         
        $query = $this->db->get();
         
        return $query->row_array();
    }
    
    /**
     * Get product trend data for a merchant/store.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    public function get_product_trend_rows($merchant_id, $store_id, $start_date, $end_date)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id); 

        $start_time_int = strtotime($start_date);
        $end_time_int = strtotime($end_date);        
        
        $query_str = "
            SELECT ptn.*
            FROM products_trends_new ptn
            JOIN products p ON p.upc_code = ptn.upc
            WHERE ptn.mid = {$merchant_id}
            AND p.store_id = {$store_id}
            AND ptn.dt >= {$start_time_int}
            AND ptn.dt <= {$end_time_int}
            ORDER BY ptn.dt DESC
        ";
        
        return $this->db->query($query_str)->result_array();
    }
    
    /**
     * Get count of products in violation for a merchant for a store.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @return int
     */
    public function get_violation_count($merchant_id, $store_id)
    {
        $query_str = "
            SELECT COUNT(*) AS violation_count
            FROM crowl_product_list_new cpl
            INNER JOIN products p ON p.upc_code = cpl.upc
            WHERE p.store_id = {$store_id}
            AND cpl.merchant_name_id = {$merchant_id}
            AND cpl.violated = 1
        ";
        
        $row = $this->db->query($query_str)->row_array();
        
        $count = empty($row) ? 0 : intval($row['violation_count']);
        
        return $count;
    }

    /**
     * Find a product record by the primary key ID.
     *
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @param string $current_date_str
     * @return array
     */
    public function get_violations_per_merchant_per_day($merchant_id, $store_id, $current_date_str)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('violations_per_merchant_per_day');
        $this->db->where('merchant_id', $merchant_id);
        $this->db->where('store_id', $store_id);
        $this->db->where('select_date', $current_date_str);
         
        $query = $this->db->get();
         
        return $query->row_array();
    } 
    
    /**
     * Get product counts per day for a date range - that are listed with a merchant.
     *
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    public function get_violations_for_date_range($merchant_id, $store_id, $start_date, $end_date)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('violations_per_merchant_per_day');
        $this->db->where('merchant_id', $merchant_id);
        $this->db->where('store_id', $store_id);
        $this->db->where('select_date >=', $start_date);
        $this->db->where('select_date <=', $end_date);
        $this->db->order_by('select_date', 'asc');
         
        $query = $this->db->get();
         
        return $query->result_array();
    }  

    /**
     * Get records from products_trends_new table for merchant/store.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @param string $start_date
     * @param string $end_date
     */
    public function get_violation_records_for_date_range($merchant_id, $store_id, $start_date, $end_date)
    {
        $violation_query = "
            SELECT ptn.*
            FROM products_trends_new ptn
            INNER JOIN products p ON p.upc_code = ptn.upc
            WHERE ptn.mid = {$merchant_id}
            AND p.store_id = {$store_id}
            AND ptndt >= {$start_time_int}
            AND ptn.dt <= {$end_time_int}
            AND ptn.mpo < ptnap
            ORDER BY ptn.dt DESC
        ";
        
        //var_dump($violation_query); exit();
         
        $price_trends = $this->db->query($violation_query)->result_array();
        
        return $price_trends;
    }

    /**
     * Get notes that are only see by team members of a store.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @return array
     */
    public function get_staff_notes($merchant_id, $store_id)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        
        $query_str = "
            SELECT sn.*
            FROM crowl_merchant_staff_notes sn
            JOIN users_store us ON us.user_id = sn.user_id
            WHERE us.store_id = {$store_id}
            AND sn.merchant_name_id = {$merchant_id}    
            ORDER BY date DESC                                                
        ";
        
        $staff_notes = $this->db->query($query_str)->result_array();
        
        return $staff_notes;
    }

    /**
     * Insert a new row into the merchant_do_not_sell_history_log table.
     *
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_dns_merchant_history_log($insert_data)
    {
        $this->db->insert('merchant_do_not_sell_history_log', $insert_data);
        
        return $this->db->insert_id();
    }    
    
    /**
     * Insert a new row into the merchant_do_not_sell_list table.
     *
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_dns_merchant($insert_data)
    {
        $this->db->insert('merchant_do_not_sell_list', $insert_data);
        
        return $this->db->insert_id();
    }    
    
    /**
     * Insert a new row into the merchant_do_not_sell_notify table.
     *
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_dns_notify($insert_data)
    {
        $this->db->insert('merchant_do_not_sell_notify', $insert_data);
        
        return $this->db->insert_id();
    }    
    
    /**
     * Insert a new row into the merchant_do_not_sell_settings table.
     *
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_dns_setting($insert_data)
    {
        $this->db->insert('merchant_do_not_sell_settings', $insert_data);
        
        return $this->db->insert_id();
    }    
    
    /**
     * Insert a new row into the merchant_do_not_sell_periods table.
     *
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_dns_period($insert_data)
    {
        $this->db->insert('merchant_do_not_sell_periods', $insert_data);
        
        return $this->db->insert_id();
    }    
    
    /**
     * Insert a new row into crowl_merchant_name_new table.
     *
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_merchant($insert_data)
    {
        $this->db->insert('crowl_merchant_name_new', $insert_data);
        
        return $this->db->insert_id();
    }  

    /**
     * Insert a new row into the merchant_contacts table.
     *
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_merchant_contact($insert_data)
    {
        $this->db->insert('merchant_contacts', $insert_data);
        
        return $this->db->insert_id();
    }    

    /**
     * Insert a new row into merchant_cron_log table.
     * 
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_merchant_cron_log($insert_data)
    {
        $this->db->insert('merchant_agg_cron_log', $insert_data);
        
        return $this->db->insert_id();        
    }
    
    /**
     * Insert a new row into the merchant_data_import_log table.
     *
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_merchant_data_import($insert_data)
    {
        $this->db->insert('merchant_data_import_log', $insert_data);
        
        return $this->db->insert_id();
    }    

    /**
     * Insert a new row into the merchant_contact_data_import_log table.
     *
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_merchant_contact_data_import($insert_data)
    {
        $this->db->insert('merchant_contact_data_import_log', $insert_data);
        
        return $this->db->insert_id();
    }    
    
    /**
     * Insert a new row into merchant_history_log table.
     *
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_merchant_history_log($insert_data)
    {
        $this->db->insert('merchant_history_log', $insert_data);
        
        return $this->db->insert_id();
    }    
    
    /**
     * Insert a new row into the merchant_settings table.
     * 
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_merchant_setting($insert_data)
    {
        $this->db->insert('merchant_settings', $insert_data);
        
        return $this->db->insert_id();        
    }

    /**
     * Insert a new record into the products_per_merchant_per_day table.
     *
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_products_per_merchant_per_day($insert_data)
    {
        $this->db->insert('products_per_merchant_per_day', $insert_data);
        
        return $this->db->insert_id();
    }
    
    /**
     * Insert new row into crowl_merchant_staff_notes table.
     * 
     * @author Christophe
     * @param array $insert_data
     */
    public function insert_staff_note($insert_data)
    {
        $this->db->insert('crowl_merchant_staff_notes', $insert_data);
        
        return $this->db->insert_id();        
    }
    
    /**
     * Insert a new record into the violations_per_merchant_per_day table.
     *
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_violations_per_merchant_per_day($insert_data)
    {
        $this->db->insert('violations_per_merchant_per_day', $insert_data);
        
        return $this->db->insert_id();
    }
    
    /**
     * Insert a new record into the violator_notifications_history table.
     *
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_violator_notification_history($insert_data)
    {
        $this->db->insert('violator_notifications_history', $insert_data);
        
        return $this->db->insert_id();
    }    

    /**
     * Search for a merchant by name.
     * 
     * @author Christophe
     * @param string $search_str
     * @return array
     */
    public function merchant_search($search_str)
    {
        $this->db->select('*');
        $this->db->from('crowl_merchant_name_new');
        $this->db->like('original_name', $search_str);
        $this->db->like('merchant_name', $search_str);
        
        $query = $this->db->get();
         
        return $query->result_array();
    }
    
    /**
     * Update a single merchant_do_not_sell_list row.
     *
     * @author Christophe
     * @param int $row_id
     * @param array $update_data
     */
    public function update_dns_list_entry($row_id, $update_data)
    {
        $row_id = intval($row_id);
        
        $this->db->where('id', $row_id);
        $this->db->update('merchant_do_not_sell_list', $update_data);
    }    

    /**
     * Update a single merchant_do_not_sell_periods row.
     *
     * @author Christophe
     * @param int $row_id
     * @param array $update_data
     */
    public function update_dns_setting($row_id, $update_data)
    {
        $row_id = intval($row_id);
        
        $this->db->where('id', $row_id);
        $this->db->update('merchant_do_not_sell_settings', $update_data);
    }    
    
    /**
     * Update a single merchant_do_not_sell_periods row.
     *
     * @author Christophe
     * @param int $row_id
     * @param array $update_data
     */
    public function update_dns_period($row_id, $update_data)
    {
        $row_id = intval($row_id);
        
        $this->db->where('id', $row_id);
        $this->db->update('merchant_do_not_sell_periods', $update_data);
    }    

    /**
     * Update a single crowl_merchant_name_new row.
     *
     * @author Christophe
     * @param int $row_id
     * @param array $update_data
     */    
    public function update_merchant($row_id, $update_data)
    {
        $row_id = intval($row_id);
        
        $this->db->where('id', $row_id);
        $this->db->update('crowl_merchant_name_new', $update_data);        
    }

    /**
     * Update a single merchant_agg_cron_log row.
     *
     * @author Christophe
     * @param int $row_id
     * @param array $update_data
     */
    public function update_merchant_cron_log($row_id, $update_data)
    {
        $row_id = intval($row_id);
        
        $this->db->where('id', $row_id);
        $this->db->update('merchant_agg_cron_log', $update_data);
    }  

    /**
     * Update a single merchant_contacts row.
     *
     * @author Christophe
     * @param int $row_id
     * @param array $update_data
     */
    public function update_merchant_contact($row_id, $update_data)
    {
        $row_id = intval($row_id);
        
        $this->db->where('id', $row_id);
        $this->db->update('merchant_contacts', $update_data);
    }  

    /**
     * Update a single merchant_contacts row by UUID.
     *
     * @author Christophe
     * @param int $contact_uuid
     * @param array $update_data
     */
    public function update_merchant_contact_by_uuid($contact_uuid, $update_data)
    {
        $this->db->where('uuid', $contact_uuid);
        $this->db->update('merchant_contacts', $update_data);
    }    
    
    /**
     * Update a single merchant_settings row.
     *
     * @author Christophe
     * @param int $row_id
     * @param array $update_data
     */
    public function update_merchant_setting($row_id, $update_data)
    {
        $row_id = intval($row_id);
        
        $this->db->where('id', $row_id);
        $this->db->update('merchant_settings', $update_data);
    }    
    
    /**
     * Update a single products_per_merchant_per_day row.
     *
     * @author Christophe
     * @param int $row_id
     * @param array $update_data
     */
    public function update_products_per_merchant_per_day($row_id, $update_data)
    {
        $row_id = intval($row_id);
        
        $this->db->where('id', $row_id);
        $this->db->update('products_per_merchant_per_day', $update_data);
    }   

    /**
     * Update a single violations_per_merchant_per_day row.
     *
     * @author Christophe
     * @param int $row_id
     * @param array $update_data
     */
    public function update_violations_per_merchant_per_day($row_id, $update_data)
    {
        $row_id = intval($row_id);
        
        $this->db->where('id', $row_id);
        $this->db->update('violations_per_merchant_per_day', $update_data);
    }    
    
    /**
     * Update a single violator_notifications_history row.
     *
     * @author Christophe
     * @param int $row_id
     * @param array $update_data
     */
    public function update_violator_notifications_history($row_id, $update_data)
    {
        $row_id = intval($row_id);
        
        $this->db->where('id', $row_id);
        $this->db->update('violator_notifications_history', $update_data);
    }    
}

?>