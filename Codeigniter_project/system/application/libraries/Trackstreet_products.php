<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Trackstreet_products
{    
    public function Trackstreet_products() 
    {
        $this->CI =& get_instance();
    }
    
    /**
     * Check to see if UPC codes are unique for a group of products.
     * 
     * @author Christophe
     * @param array $products
     * @return boolean|array
     */
    public function bad_upc_check($products, $header_array)
    {
        //var_dump($products); exit();
        
        $product_upcs = array();
        $bad_product_upcs = array();
        
        foreach ($products as $product)
        {
            $product = array_combine($header_array, $product);
            $product = array_map('trim', $product);
            
            //var_dump($product); exit();
            
            if (in_array($product['upc_code'], $product_upcs))
            {
                if (!in_array($product['upc_code'], $bad_product_upcs))
                {
                    $bad_product_upcs[] = $product['upc_code'];
                }
            }
            
            $product_upcs[] = $product['upc_code'];
        }
        
        if (empty($bad_product_upcs))
        {
            // we did not detect any multiples in terms of UPC usage (a good thing!)
            return FALSE;
        }
        else
        {
            return $bad_product_upcs;
        }
    }
}

?>