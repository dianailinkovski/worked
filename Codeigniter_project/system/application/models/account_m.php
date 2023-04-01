<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Account_m extends MY_Model
{

	function Account_m()
	{
		parent::MY_Model();
	}

	function get_brand_info($storeId){
		$result = $this->db->where('id', $storeId)
												->get($this->_table_store)
												->result('array');
		$brand = count($result) == 1 ? $result[0] : NULL;

		return $brand;
	}

	function upload_logo($storeId, $logo_name)
	{
		$this->db->where('id', $storeId);
		$this->db->update('store', array('brand_logo' => $logo_name));
	}

	function get_merchant_logo($storeId){
		$query = "SELECT brand_logo from store WHERE id = '".$storeId."'";
		$result = $this->db->query($query)->result('array');

		if(count($result) > 0 && trim($result[0]['brand_logo']) != ""){
			$logo =  $result[0]['brand_logo'];
		} else {
			$logo =  '';
		}
		return $logo;
	}

	function get_merchant_thumb($storeId){
		$query = "SELECT brand_logo from store WHERE id = ".$this->db->escape($storeId);
		$result = $this->db->query($query)->result('array');

		if(count($result) > 0 && trim($result[0]['brand_logo']) != ""){
			list($pre, $ext) = explode('.', $result[0]['brand_logo']);
			$logo =  $pre . '_thumb.' . $ext;
		} else {
			$logo =  '';
		}
		return $logo;
	}
}