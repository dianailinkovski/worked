<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Global_settings_m extends MY_Model
{
	
	function Global_settings_m()
	{
		parent::MY_Model();
	}
	
	function get_settings()
	{
		$query = $this->db->query("Select * from global_settings");
		return $query->result();
	}
	
	function get_google_settings($type)
	{
		$query = $this->db->query("Select * from global_settings where api_settings = '".$type."'");
		return $query->result();
	}
	
	function get_amazon_settings($type)
	{
		$query = $this->db->query("Select * from global_settings where api_settings = '".$type."'");
		return $query->result();
	}
	
	function insert_setting($data=array())
	{
		$this->db->insert('global_settings', $data);
		//$this->db->query("INSERT INTO settings set search_in_days='".$data['crowl_days']."',search_in_hours='".$data['crowl_hours']."',search_in_minutes='".$data['crowl_minutes']."',api_settings='".$data['api_settings']."'");
		
	}
	
	function update_setting($data=array())
	{
		$this->db->where('api_settings', $data['api_settings']);
		$this->db->update('global_settings', $data);
	}
}