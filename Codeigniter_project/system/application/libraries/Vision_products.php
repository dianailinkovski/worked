<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Vision_products 
{
    public function Vision_products() 
    {
        $this->CI =& get_instance();
    }
    
    /**
     * Archive products.
     * 
     * @author Christophe
     * @param array $product_ids
     * @param int $store_id
     */
    public function archive_products($product_ids, $store_id)
    {
        $this->CI->load->model('products_m');
        
        foreach ($product_ids as $product_id)
        {
            $product_id = intval($product_id);
            
            $product = $this->CI->products_m->get_product_by_id($product_id);
            
            if (!empty($product))
            {
                // check to see if user store matches product
                if (
                		intval($store_id) == intval($product['store_id'])
                )
                {
                    $update_data = array(
                        'is_archived' => 1,
                        'is_tracked' => 0 // also untrack products that get archived
                    );
                    
                    $this->CI->products_m->update_product($product_id, $update_data);
                }
            }
        }
    }
    
    /**
     * Set products to tracked.
     * 
     * @author Christophe
     * @param array $product_ids
     * @param int $store_id
     */
    public function track_products($product_ids, $store_id)
    {
        $this->CI->load->model('products_m');
        
        foreach ($product_ids as $product_id)
        {
            $product_id = intval($product_id);
            
            $product = $this->CI->products_m->get_product_by_id($product_id);
            
            if (!empty($product))
            {
                // check to see if user store matches product
                if (
                		intval($store_id) == intval($product['store_id'])
                )
                {
                    $update_data = array(
                    		'is_tracked' => 1
                    );
                    
                    $this->CI->products_m->update_product($product_id, $update_data);
                }
            }
        }        
    }

    /**
     * Set products to untracked.
     *
     * @author Christophe
     * @param array $product_ids
     * @param int $store_id
     */
    public function untrack_products($product_ids, $store_id)
    {
        $this->CI->load->model('products_m');

        foreach ($product_ids as $product_id)
        {
            $product_id = intval($product_id);

            $product = $this->CI->products_m->get_product_by_id($product_id);

            if (!empty($product))
            {
                // check to see if user store matches product
                if (
                    intval($store_id) == intval($product['store_id'])
                )
                {
                    $update_data = array(
                        'is_tracked' => 0
                    );

                    $this->CI->products_m->update_product($product_id, $update_data);
                }
            }
        }
    }

    /**
     * Unarchive products. Bring them back from the dead.
     *
     * @author Christophe
     * @param array $product_ids
     * @param int $store_id
     */    
    public function unarchive_products($product_ids, $store_id)
    {
        $this->CI->load->model('products_m');

        foreach ($product_ids as $product_id)
        {
            $product_id = intval($product_id);

            $product = $this->CI->products_m->get_product_by_id($product_id);

            if (!empty($product))
            {
                // check to see if user store matches product
                if (
                    intval($store_id) == intval($product['store_id'])
                )
                {
                    $update_data = array(
                        'is_archived' => 0,
                        'is_tracked' => 1            
                    );

                    $this->CI->products_m->update_product($product_id, $update_data);
                }
            }
        }
    }
}