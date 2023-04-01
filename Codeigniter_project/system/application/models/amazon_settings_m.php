<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Amazon_settings_m extends MY_Model
{
	public $cache;
	
	function getAmazonEmailSettingByStoreID ( $store_id, $randomOne = false ){
		if($randomOne){
			$this->db
				->order_by('RAND()')
				->limit(1);
		}
		else{
			$this->db->mysql_cache();
		}
		$setting = $this->db
					->where("store_id", $store_id)
					->get($this->_table_amazon_violator_email_settings)
					->result('array');
					
		if ( count($setting) >= 1 ){
			return $setting;
		}
		else{
			$dummy[0] = array('id'=>'', 'email'=>'', 'password'=>'', 'message'=>'', 'login_failed'=>0, 'marketplace'=>'');
			return $dummy;
		}
	}
	
	function getAll(){
		$return = array();
		$res = $this->db
					->get($this->_table_amazon_violator_email_settings)
					->result('array');
					
		foreach($res as $row)
			$return[$row['store_id']][] = $row;
			
		return $return;
	}
	
	function deleteAmazonEmailSettingByStoreID ( $store_id ) {
		return $this->db->where("store_id", $store_id)->delete($this->_table_amazon_violator_email_settings);
	}
	
	function updateStatus($id, $login_failed=0, $message=''){
		return $this->db
				->set('login_failed', $login_failed)
				->set('message', $message)
				->where('id', $id)
				->update($this->_table_amazon_violator_email_settings);
	}
	
	function getAmazonProxy ($store_id=0) {
		if(isset($this->cache[$store_id])){
			return $this->cache[$store_id];
		}
		$this->db->where("store_id", $store_id); // must always have a default proxy with store_id=0
		$this->db->limit(1);
		$amazon_proxy_setting = $this->db->get("amazon_violator_proxy")->result_array();
		//echo $this->db->last_query()."\n";exit;
		if ( count($amazon_proxy_setting) > 0 ) {
			if ($amazon_proxy_setting[0]['proxy_address'] != '') {
				$this->cache[$store_id] = $amazon_proxy_setting[0];	
				return $amazon_proxy_setting[0];	
			}
		}
		$proxy = $this->getAmazonProxy();
		if(!empty($proxy)){
			return $proxy;
		}
		
		return FALSE;
	}
}