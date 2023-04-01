<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// keep a temporary list of products not found
class Rejected_m extends MY_Model
{
    public $tableName;
	public $cutoff_date;
	public $cutoff_date_eager;
    
    public function __construct(){
        $this->tableName = "rejected"; //TODO: get this from config
		
		// randomize cache lifespan to prevent resource spikes
		$reject_cache_cutoff_days = (time()%2==0) ? 5 : 6; //TODO: get this from config
        $this->cutoff_date = time()- ($reject_cache_cutoff_days * 24 * 60 * 60);
		
        $this->cutoff_date_eager = time()- (8 * 60 * 60); // 8 hrs
    }
    
    public function save($upc, $api, $mpId, $notice=''){
		$data = array(
            'date_rejected' => time(),
            'marketplace'	=> $api,
			'mpId'          => $mpId,
            'upc'	 		=> $upc,
			'notice'		=> $notice
        );
		$this->db->insert($this->tableName, $data);
		// update rejected r join marketplaces m on r.marketplace = m.name set r.mpId = m.id;
		
		$sql = "update products_lookup set fails = (fails + 1), last_failed=UNIX_TIMESTAMP() where marketplace_id = {$mpId} and upc = '{$upc}'";
		$this->db->query($sql);
    }
    
    public function get_upcs_by_store_id($api, $store_id)
    {
        $data = $this->db->select('r.upc')
                ->join('products p', 'p.upc_code=r.upc')
                ->where('p.store_id', (int)$store_id)
                ->where('r.marketplace', $api)
                ->get($this->tableName.' r')
                ->result_array();
				
		//echo $this->db->last_query(); echo "\n\n";
        $arr_rejected_upcs = array();
        foreach($data as $d){
            $arr_rejected_upcs[] = $d['upc'];
        }
        return $arr_rejected_upcs;
    }
    
    // remove expired records
    public function prune(){
        // 1. delete "product not found" (after a long period)
        $this->db->delete($this->tableName, 'date_rejected < '.$this->cutoff_date);
		
		// 2. delete unrecognized (after a short period)
		$sql = "
			DELETE FROM `rejected`
			WHERE `date_rejected` < {$this->cutoff_date_eager}
				AND `notice` NOT LIKE  '%[product.error]%'
				AND `notice` NOT LIKE  '%Product not found%'
		";
        $this->db->query($sql);
        //echo $this->db->last_query(); echo "\n\n";exit;
    }
	
	public function get_all($is_retailer=1){
		$upc_lookup      = $is_retailer==1 ? 0 : 1;
		$and_is_retailer = $is_retailer==1 ? "AND m.is_retailer=1" : "";
        $sql = "
            SELECT r.upc, r.marketplace, s.store_name, p.title, pl.url, r.notice
            FROM rejected r 
                INNER JOIN marketplaces m ON(r.marketplace = m.name)
                INNER JOIN products_lookup pl ON(r.upc = pl.upc AND pl.marketplace_id=r.mpId)
                INNER JOIN products p ON(pl.product_id = p.id)
                INNER JOIN store s ON(p.store_id = s.id)
            WHERE m.upc_lookup=0
                AND m.is_active=1
                and m.is_retailer=1
            ORDER BY marketplace
        ";
				//AND r.date_rejected > UNIX_TIMESTAMP()-(24*60*60)
        $data = $this->db->query($sql)->result_array();
		return $data;
	}
	
	public function summary(){
		$sql = "
			SELECT
				r.marketplace,
				count(*) as failed, 
				(select count(*) from products_lookup pl2 where pl2.marketplace_id=r.mpId) as mapped,
				ceil(abs(1-(count(*) / (select count(*) from products_lookup pl2 where pl2.marketplace_id=r.mpId))*100)) as percent
            FROM rejected r
                INNER JOIN marketplaces m ON(r.marketplace = m.name)
                INNER JOIN products_lookup pl ON(r.upc = pl.upc AND pl.marketplace_id=r.mpId)
            WHERE m.upc_lookup=0
                AND m.is_active=1
                AND m.is_retailer=1
            group by r.marketplace
			order by r.marketplace  
		";
        $data = $this->db->query($sql)->result_array();
		return $data;
    }
    
	public function get_worker_queue(){
        $sql = "
            SELECT r.upc, r.marketplace, r.mpId, s.store_name, p.retail_price, p.title, pl.url, r.notice, m.brands_url
            FROM rejected r 
                INNER JOIN marketplaces m ON(r.marketplace = m.name)
                INNER JOIN products_lookup pl ON(r.upc = pl.upc AND pl.marketplace_id=r.mpId)
                INNER JOIN products p ON(pl.product_id = p.id)
                INNER JOIN store s ON(p.store_id = s.id)
            WHERE m.upc_lookup=0
                AND m.is_active=1
                and m.is_retailer=1
				and r.notice like 'Product not found%'
            ORDER BY marketplace
        ";
        $data = $this->db->query($sql)->result_array();
		return $data;
	}
	

}

?>