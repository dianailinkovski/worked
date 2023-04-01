<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// keep a list of products found on each brands page
class Products_candidates_m extends MY_Model
{
    public $tableName;
    
    //Fields: id, marketplace_id, store_id, title, url, search_url, price, image, timestamp
    
    public function __construct(){
        $this->tableName = "products_candidates"; //TODO: get this from config
    }
    
    public function save($data){
		$this->db->insert($this->tableName, $data);
    }

    public function get_all_products()
    {
		//SELECT pc.marketplace_id, pc.title, pc.url, pc.search_url, s.store_name
        $sql = "
            SELECT pc.*, s.store_name
            FROM  {$this->tableName} pc
            JOIN  store s ON (pc.store_id = s.id)
        ";
        $sql .= "ORDER BY RAND()";//test
        return $this->db->query($sql)->result_array();
    }

}
?>
