<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Products_pricing_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Find a row in the products_pricing table.
     * 
     * @author Christophe
     * @param int $product_id
     * @param string $pricing_type
     * @return array
     */
    public function get_existing_pricing_row($product_id, $pricing_type)
    {
        $product_id = intval($product_id);
        
        $this->db->select('*');
        $this->db->from('products_pricing');
        $this->db->where('product_id', $product_id);
        $this->db->where('pricing_type', $pricing_type);
         
        $query = $this->db->get();
         
        return $query->row_array();        
    }

    /**
     * 
     * @author unknown
     * @param unknown_type $pid
     * @param unknown_type $pricing_type
     * @param unknown_type $pricing_value
     */
    public function set_products_pricing($pid, $pricing_type, $pricing_value)
    {
        $data = array(
            'product_id' => $pid,
            'pricing_type' => $pricing_type,
            'pricing_value' => $pricing_value,
            'pricing_start' => '2014-11-11 11:11:11',
        );
        
        $prid = $this->insert($data);
        
        if (!$prid)
        {
            var_dump($prid); exit;
        }
    }
    
    /**
     * Insert a new row into the products_pricing table.
     * 
     * @author unknown, Christophe
     * @param array
     * @return int
     */
    public function insert($insert_data)
    {
        if (!isset($insert_data['pricing_type']) || !isset($insert_data['product_id']))
        {
            return FALSE;
        }
        
        // first check to make sure we don't already have a pricing row for the product
        $existing_pricing_row = $this->get_existing_pricing_row($insert_data['product_id'], $insert_data['pricing_type']);
        
        if (empty($existing_pricing_row))
        {
            // insert new row
            return $this->db->insert('products_pricing', $insert_data);
        }
        else
        {
            $update_data = $existing_pricing_row;
            
            unset($update_data['id']);
            
            return $this->update_pricing($existing_pricing_row['pricing_id'], $update_data);
        }
    }
    
    /**
     * Deprecated update function, that still may be in use with code base.
     * 
     * @author unknown
     */
    public function update($id, $input)
    {
        return parent::update($id, $input);
    }
    
    /**
     * Update existing row in products_pricing table.
     * 
     * @author Christophe
     * @param int $row_id
     * @param array $update_data
     */
    public function update_pricing($row_id, $update_data)
    {
        $row_id = intval($row_id);
        
        $this->db->where('pricing_id', $row_id);
        $this->db->update('products_pricing', $update_data);
                
        return $row_id;
    }
    
    /**
     * Update existing row in products_pricing table.
     *
     * @author Christophe
     * @param int $product_id
     * @param string $pricing_type
     * @param array $update_data
     */
    public function update_pricing_by_product($product_id, $pricing_type, $update_data)
    {
        $product_id = intval($product_id);
        
        // first check to make sure we don't already have a pricing row for the product
        $existing_pricing_row = $this->get_existing_pricing_row($product_id, $pricing_type);
        
        //var_dump($existing_pricing_row); exit();
        
        if (empty($existing_pricing_row))
        {
            if (!isset($update_data['product_id']))
            {
                $update_data['product_id'] = $product_id;
            }
            
            if (!isset($update_data['pricing_type']))
            {
                $update_data['pricing_type'] = $pricing_type;
            }
            
            return $this->insert($update_data);
        }
        else 
        {
            $this->update_pricing($existing_pricing_row['pricing_id'], $update_data);
            
            return $existing_pricing_row['pricing_id'];
        }
    }    
}
