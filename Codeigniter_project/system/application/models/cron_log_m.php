<?php

class Cron_log_m extends MY_Model
{
	public static $tableName = 'cron_log';

	public function get_by_id($id) {
		$res = $this->db
		->where('id', (int) $id)
		->get(self::$tableName)
		->result_object();
		if($res){
			return $res[0];
		}
		return false;
	}
	
	public function create_log($data){
		$this->db->insert(self::$tableName, $data);
		return $this->db->insert_id();
	}

	public function update($id, $data) {
		$this->db
		->where('id', (int) $id)
		->update(self::$tableName, $data);
	}

	function getMaxCronLog($api_type) {
		$maxLogDate = array();
		
		$cronLogNumRows = $this->db
			->select_max('id')
			->where('api_type', $api_type)
			->group_by('api_type') // TODO: necessary?
			->get(self::$tableName);

		if ($cronLogNumRows->num_rows() > 0) {
			$cronLogMaxId = $cronLogNumRows->row();
			$maxLogDate = $this->db
				->where('id', $cronLogMaxId->id)
				->get(self::$tableName)
				->row();
		}

		return $maxLogDate;
	}
	
	public function getLatestMerchants(){
		$sql = "SELECT * FROM(
					SELECT api_type as marketplace, start_datetime as start_time, end_datetime as end_time, google_count as found
					FROM ".self::$tableName."
					WHERE api_type IN (
						SELECT name FROM marketplaces
						WHERE is_retailer = '0'
							AND is_active='1'
					)
					ORDER BY datetime DESC 
				) AS derived
				GROUP BY marketplace";
				
		return $this->db->query($sql)->result_array();
	}

	// for this crawler, fix ALL crashed crawls!
	function fix_all_crashed($api, $currentDate){
		$update = array(
			'datetime' => $currentDate,
			'end_datetime' => $currentDate,
		);
		$this->db
			->where('datetime', '0000-00-00 00:00:00')
			->where('api_type', $api)
			->update(self::$tableName, $update);
	}
	

	public function all_tasks_done($id){
		$lastLog = $this->get_by_id($id);
		$lastLog->tasks = trim($lastLog->tasks, ' ,');
		if(strlen($lastLog->tasks) == 0){
			return true;
		}
		return false;
	}

	function scratch_task($store_id, $cron_log_id){
		$sql = "update cron_log set tasks = REPLACE(tasks, '{$store_id},', '') where id={$cron_log_id}";
		$this->db->query($sql);
	}					
	
}