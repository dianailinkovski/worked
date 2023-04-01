<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crawl_data_m extends MY_Model
{
	public $last_crawl_cache = array();
	public $last_crawl_cache_all_flag = false;
	public $crawl_range_cache = array();
	
	/**
	 * 
	 * @todo fix this function so that it always returns one data type - Chris (??)
	 * @author unknown, Chris
	 * @param unknown_type $type
	 * @param unknown_type $days
	 * @return multitype:unknown
	 */
	public function last_crawl($type = 'all', $days = 2)
	{
		$date = date('Y-m-d',strtotime("-{$days} days")); // from midnight so we can cache result
		
		$ret = array();
		
		switch ($type)
		{
			// warning: returns array
			case 'all-deprecated':
				$types = array_to_lower(getMarketArray(TRUE));

				foreach ($types as $t)
				{
					$res = $this->last_crawl($t);
					if ( ! empty($res))
						$ret[$t] = $res;
				}
				break;
			// make this more performant, using IN() statement and RAM cache
			case 'all':
			    
				if ($this->last_crawl_cache_all_flag)
				{
					$ret = $this->last_crawl_cache;
				}
				else
				{
					if (!empty($this->store_id))
					{
						$types = get_marketplaces_by_storeid_using_categories($this->store_id);
						
						$api_list = "'".implode("','", $types)."'";
					}
					else
					{
						$api_list = "SELECT distinct name FROM marketplaces where is_active='1'";
					}

					// SQL_CACHE and SQL_NO_CACHE in subquery are no longer permitted in MySQL 5.5.3
					// http://stackoverflow.com/questions/24320137/mysql-syntax-error-after-upgrade

					$sql = "select SQL_CACHE x.* 
						from (
						  SELECT c.id, c.api_type, c.start_datetime, c.end_datetime
							FROM cron_log c
							WHERE c.api_type is not null
							AND	c.start_datetime > '{$date}'
							AND c.api_type IN ({$api_list})
							AND c.tasks = ''
							ORDER BY c.end_datetime desc, c.api_type
						) x
						GROUP BY x.api_type 
					";
					
					$cron_logs = $this->db->query($sql)->result_object();
					
					foreach ($cron_logs as $t)
					{
						$ret[$t->api_type] = $t;
					}
					
					$this->last_crawl_cache = $ret;
					$this->last_crawl_cache_flag = TRUE;					
				}
				break;
			// warning: returns object
			default:
			    
				if (isset($this->last_crawl_cache[$type]))
				{
					$ret = $this->last_crawl_cache[$type];
				}
				else
				{
					$sql = "SELECT SQL_CACHE
								id, start_datetime, end_datetime, api_type
							FROM
								{$this->_table_cron_log}
							WHERE
								api_type=?
						  AND tasks = ''
						  AND start_datetime > '{$date}'
							ORDER BY
								datetime desc
							LIMIT 1";
								
					$variables = array(
						$type
					);
					
					$ret = $this->db->query($sql, $variables)->row();
					
					$this->last_crawl_cache[$type] = $ret;
				}
		}
		
		return $ret;
	}

	/**
	 * 
	 * @author unknown
	 * @param string $type
	 * @return array
	 */
	public function last_crawl_range($type = 'all')
	{
		$ret = array();
		
		if (isset($this->crawl_range_cache[$type]))
		{
			$ret = $this->crawl_range_cache[$type];
		}
		else
		{
			$times = $this->last_crawl($type);
			
			if (is_object($times))
			{
				$times = array($type => $times);
			}
			
			$from = date('Y-m-d H:i:s');
			//$to = '0000-00-00 00:00:00';
			$to = '2000-00-00 00:00:00';
			
			foreach ($times as $market => $data)
			{
				if (empty($data))
				{
					continue;
				}
				
				if ($data->start_datetime < $from)
				{
					$from = $data->start_datetime;
				}
				
				if ($data->end_datetime > $to)
				{
					$to = $data->end_datetime;
				}
			}
			
			$ret = array(
				'from' => $from,
				'to'   => $to
			);
			
			$this->crawl_range_cache[$type] = $ret;
		}
		
		return $ret;
	}

	public function get_crawl_by_time($timestamp, $marketplace, $fields = array())
	{
		if ( ! is_array($fields))
			$fields = array($fields);
		if ( ! empty($fields) AND ! in_array('*', $fields))
			$this->db->select(implode(',', $fields));

		$date = date('Y-m-d H:i:s', $timestamp);

		$ret = $this->db
			->where('api_type', strtolower($marketplace))
			->where('start_datetime <=', $date)
			->where('end_datetime >=', $date)
			->get($this->_table_cron_log)
			->row_array();

		return $ret;
	}

	/**
	 * Read a single row from crowl_merchant_name by id
	 *
	 * @param int $id
	 * @return stdClass/False
	 */
	public function crowlMerchantByID($id)
	{
		return $this->_readCrowlMerchant($id, 'id');
	}

	/**
	 * Read a single row from crowl_merchant_name by seller id
	 *
	 * @param String $sellerID
	 * @return stdClass/False
	 */
	public function crowlMerchantBySellerID($sellerID)
	{
		return $this->_readCrowlMerchant($sellerID, 'seller_id');
	}
	
	/**
	* Read a single row from crowl_merchant_name by merchantname
	*
	* @param String $merchantName
	* @return stdClass/False
	*/
	public function crowlMerchantByMerchantName($merchantName)
	{
		return $this->_readCrowlMerchant($merchantName, 'merchant_name');
	}
	
	/**
	* Read a single row from crowl_merchant_name by url
	*
	* @param String $url
	* @return stdClass/False
	*/
	public function crowlMerchantByUrl($url)
	{
		return $this->_readCrowlMerchant($url, 'url');
	}

	/**
	 * Read a single row from crowl_merchant_name by id or seller id
	 *
	 * @param mixed $val
	 * @param String $key
	 * @return stdClass/False
	 */
	private function _readCrowlMerchant($val, $key = 'id')
	{
		switch ($key)
		{
			case 'id':
				$this->db->where('id', (int)$val);
				break;
			case 'seller_id':
				$this->db->where('seller_id', $val);
				break;
			case 'merchant_name':
				$this->db->like('merchant_name', $val);
				break;
			case 'url':
				$this->db->like('merchant_url', $val);
				break;
			default:
				return false;
		}

		return $this->db->get($this->_table_crowl_merchant_name)->row();
	}

	/**
	 * Read a single row from crowl_product_list by id
	 *
	 * @param int $id
	 * @return stdClass/False
	 */
	public function crowlProductByID($id)
	{
		return $this->_readCrowlProduct($id, 'id');
	}

	/**
	 * Read a single row from crowl_product_list by merchant/upc combo
	 *
	 * @param int $merchant
	 * @param String $upc
	 * @return stdClass/False
	 */
	public function crowlProductByMerchant($merchant_id, $upc)
	{
		return $this->_readCrowlProduct(
			array(
				'merchant_name_id' => $merchant_id,
				'upc' => $upc
			),
			'merchant'
		);
	}

	/**
	 * Read a single row from crowl_product_list by id or merchant/upc combo
	 *
	 * @param mixed $val
	 * @param String $key
	 * @return stdClass/False
	 */
	private function _readCrowlProduct($val, $key = 'id')
	{
		switch ($key)
		{
			case 'id':
				$this->db->where('id', (int)$val);
				break;
			case 'merchant':
				if ( ! isset($val['merchant_name_id']) AND ! isset($val['upc']))
					return false;
				$this->db->where($val);
				break;
			default:
				return false;
		}

		return $this->db->get($this->_table_crowl_product_list)->row();
	}
}
