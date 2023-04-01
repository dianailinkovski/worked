<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Vision_merchants 
{
    public function Vision_merchants() 
    {
        $this->CI =& get_instance();
    }
    
    /**
     * Set merchants to turnon.
     * 
     * @author Mindy
     * @param array $merchant_ids
     * @param int $store_id
     */
    public function turnon_merchants($merchant_ids, $store_id)
    {
        $this->CI->load->model('merchants_m');
        
        foreach ($merchant_ids as $merchant_id)
        {
            $merchant_id = intval($merchant_id);
            
            $merchant = $this->CI->merchants_m->get_merchant_by_id($merchant_id);
            
            if (!empty($merchant))
            {
                // check to see if user store matches merchant
                if (
                		intval($store_id) == intval($merchant['store_id'])
                )
                {
                    $update_data = array(
                    		'is_tracked' => 1
                    );
                    
                    $this->CI->merchants_m->update_merchant($merchant_id, $update_data);
                }
            }
        }        
    }

    /**
     * Set merchants to untracked.
     *
     * @author Mindy
     * @param array $merchant_ids
     * @param int $store_id
     */
    public function turnoff_merchants($merchant_ids, $store_id)
    {
        $this->CI->load->model('merchants_m');

        foreach ($merchant_ids as $merchant_id)
        {
            $merchant_id = intval($merchant_id);

            $merchant = $this->CI->merchants_m->get_merchant_by_id($merchant_id);

            if (!empty($merchant))
            {
                // check to see if user store matches merchant
                if (
                    intval($store_id) == intval($merchant['store_id'])
                )
                {
                    $update_data = array(
                        'is_tracked' => 0
                    );

                    $this->CI->merchants_m->update_merchant($merchant_id, $update_data);
                }
            }
        }
    }

    
}