<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crawler_m extends MY_Model {

	public static $tableName = 'crawlers';

	/**
	* Get active crawlers
	*
	* @return array
	*/
	public function get_active_crawlers()
	{
		$this->db->where('active', 1);
		$result = $this->db->get(self::$tableName)->result_array();
		return $result;
	}
}