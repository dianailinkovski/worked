<?php
/**
 * This accesses the daily_price_average table which contains only today's prices
 * 	and daily_price_average_archive table which will be pretty huge, with millions of records.
 * This data is redundant (ie, you can find it in other tables).  This is purposely so that we do not need to do any table joins.
 * The table must be carefully indexed (um, dt, upc), and carefully selected
 * Scalability Optimizations:
 * - mysql partitioning by date column
 * - fixed length columns
 * - mysql cache
 * - no table joins 
 */

class Daily_price_average_m extends MY_Model
{
	/**
	 * Get a daily_price_average record by an index
	 *
	 * @param String $key
	 * @param String $type { default : 'upc' }
	 * @return obj Active Record
	 */
	protected function _get($key, $type = 'upc') {
		switch ($type) {
			case '':
				$this->db->where('', $key);
				break;
			case 'upc':
			default:
				$this->db->where('upc', $key);
		}
		
		$this->db->mysql_cache(); // only on production server?
		return $this->db->get($this->_dynamo_daily_price_average, null, null, false);
	}
	
	public function update_average_price() {
		$date = date('Y-m-d'); //, strtotime("-1 day")
		/**
		 * TODO: replace with these two queries
		$qStr = "INSERT INTO `" . $this->_dynamo_daily_price_average_archive . "` select * from `" . $this->_dynamo_daily_price_average . "` where `date` < '$date'"
		$query = $this->db->query($qStr);
		if($query){
			$qStr = "delete from `" . $this->_dynamo_daily_price_average . "`  where `date` < '$date' ";
			$query = $this->db->query($qStr);
		}
		 */
		$query = $this->db->query("select * from `" . $this->_dynamo_daily_price_average . "` where `date` < '$date'");
		foreach ($query->result() as $row) {
			$params = array(
				'upc'			 => $row->upc,
				'marketplace'	 => $row->marketplace,
				'date'			 => $row->date,
				'price_total'	 => $row->price_total,
				'seller_total'	 => $row->seller_total,
			);
			if($this->db->insert($this->_dynamo_daily_price_average_archive, $params)){
				$qStr = "delete from `" . $this->_dynamo_daily_price_average . "` where `id` = '" . $row->id . "'";
				$query = $this->db->query($qStr);
			}
			else{
				log_message('error', 'failed to insert _dynamo_daily_price_average_archive record');
			}
		}
	}


}