<?php

class Crawler_log_m extends MY_Model 
{

	public static $tableName = 'crawler_log';

	public function create_log($start_time)
	{
		$this->db->insert(self::$tableName, array(
			'datetime' => date('Y-m-d H:i:s'),
			'start_datetime' => $start_time
		));
		return $this->db->insert_id();
	}

	public function update_log($id, $data)
	{
		return $this->db
				->where('id', (int) $id)
				->update(self::$tableName, $data);
	}
}