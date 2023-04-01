<?php

class Crawler_error_log_m extends MY_Model 
{

	public static $tableName = 'crawler_error_log';

	public function create_log($data)
	{
		$this->db->insert(self::$tableName, $data);
		return $this->db->insert_id();
	}
}