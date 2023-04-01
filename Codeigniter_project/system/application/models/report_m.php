<?php
class Report_m extends MY_Model {
	protected $_dynamo_daily_price_average;
	protected $_dynamo_products_trends;
	protected $_dynamo_violations;
	protected $_table_products;
	protected $_table_crowl_merchant_name;
	protected $_table_crowl_product_list;
	public $finalData;

	function Report_m() {
		parent::MY_Model();
		$this->load->helper('xml');
		$this->load->helper('text');
		$this->load->helper('file');
		//$this->load->library('amzdb');
		$this->ci->load->model('products_trends_m', 'ProductsTrends');
	}

	function get_save_report_by_id($id, $array=true) {
		$query = $this->db
		->select('sr.*, sr.id as report_id, srs.report_datetime, srs.report_recursive_frequency, srs.email_addresses')
		->where('sr.id', $id)
		->from($this->_table_saved_reports.' sr')
		->join($this->_table_saved_reports_schedule.' srs', 'srs.saved_reports_id=sr.id', 'left')
		->get();
        
        if ( $array ) {
            return $query->row_array();
        } else {
            return $query->row();
        }
	}

	function get_all_products($store_id=0) {
		$result = $this->db->get_where('products', array('store_id' => $store_id));
		return $result->num_rows();
	}

	function get_tracked_products($store_id=0) {
		$result = $this->db->get_where('products', array('store_id' => $store_id, 'is_tracked'=>'1'));
		return $result->num_rows();
	}


	/*
		For Getting UPC against Store
	*/
	function getStoreUPCAll($storeID) {
		$finalArray = array();
		$this->db->select('upc_code');
		$upcArray = $this->db->get_where('products', array('store_id'=>$storeID))->result();

		if (count($upcArray) > 0) {
			foreach ($upcArray as $upc) {
				$finalArray[] = $upc->upc_code;
			}
			return $finalArray;
		}else {
			return 0;
		}
	}

	function getProductUPC($productID) {
		$finalArray = array();
		$this->db->select('upc_code');
		$upcArray = $this->db->get_where('products', array('id'=>$productID))->result();

		if (count($upcArray) > 0) {
			return $upcArray[0]->upc_code;
		}else {
			return 0;
		}
	}

	function getProductIDByUPC($UPC, $store) {
		$finalArray = array();
		$this->db->select('id');
		$upcArray = $this->db->get_where('products', array('upc_code'=>$UPC, 'store_id'=>$store))->result();

		if (count($upcArray) > 0) {
			return $upcArray[0]->id;
		}else {
			return 0;
		}
	}

	function getProductRetailPrice($UPC, $store) {
		$finalArray = array();
		$this->db->select('retail_price');
		$upcArray = $this->db->get_where('products', array('upc_code'=>$UPC, 'store_id'=>$store))->result();

		if (count($upcArray) > 0) {
			return $upcArray[0]->retail_price;
		}else {
			return 0;
		}
	}

	function getProductFloorPrice($UPC, $store) {
		$finalArray = array();
		$this->db->select('price_floor');
		$upcArray = $this->db->get_where('products', array('upc_code'=>$UPC, 'store_id'=>$store))->result();

		if (count($upcArray) > 0) {
			return $upcArray[0]->price_floor;
		}else {
			return 0;
		}
	}

	function add_report($data) {
		$this->db->insert($this->_table_saved_reports, $data);
		return $this->db->insert_id();
	}

	function update_report($data, $id) {
		$this->db->where('id', $id);
		$this->db->update($this->_table_saved_reports, $data);
	}

	function add_schedule_report($data) {
		$this->db->insert($this->_table_saved_reports_schedule, $data);
		return $this->db->insert_id();
	}

	function update_schedule_report($data, $id) {
		$current = $this->get_schedule($id);
		if (empty($current)) {
			$this->add_schedule_report($data);
		}else {
			$this->db->where(array('saved_reports_id' => $id));
			$this->db->update($this->_table_saved_reports_schedule, $data);
		}
	}

	function delete_report($id) {
		$this->db->where('id', $id);
		$this->db->delete($this->_table_saved_reports);
		$this->delete_schedule_report($id);

		return true;
	}

	function delete_schedule_report($rId) {
		$this->db->where('saved_reports_id', $rId);
		$this->db->delete($this->_table_saved_reports_schedule);
	}

	function get_report_list() {
		return $this->db->select('sr.*')
		->where('sr.store_id', $this->store_id)
		->order_by('sr.id', 'asc')
		->get($this->_table_saved_reports.' sr')
		->result();
	}

	function get_report_schedule_list($kw = '') {
		$this->db->select('srs.*, sr.*')
        ->from($this->_table_saved_reports_schedule.' srs')
		->join($this->_table_saved_reports.' sr', 'srs.saved_reports_id=sr.id', 'right')
		->where(array('sr.store_id' => $this->store_id));
        //->join($this->_table_saved_reports.' sr', 'srs.saved_reports_id=sr.id', 'left')
		//->where(array('sr.store_id' => $this->store_id, 'report_recursive_frequency' => '>0'));
        if (!empty($kw)) $this->db->like('sr.report_name', $kw);
		$result = $this->db->order_by('sr.id', 'asc')->get()->result();

		return $result;
	}

	function get_schedule($id) {
		return $this->db->where('saved_reports_id', $id)
		->get($this->_table_saved_reports_schedule)
		->result();
	}

	function get_groups($merchant_id) {
		$result = $this->db->get('groups', array('merchant_id' => $merchant_id))->result('array');
		return $result;
	}

	function set_report_type($type) {
		$text = '';
		switch ($type) {
		case 'reports':
			$text = 'Pricing Over Time';
			break;
		case 'violations':
			$text = 'Price Violation';
			break;
		case 'overview':
			$text = 'Pricing Overview';
			break;
		case 'violationoverview':
			$text = 'Violation Overview';
			break;
		default:
			$text = $type;
		}
		return $text;
	}

	function productPricingReport24($report_where = array(), $skip_product_id = false) {
		$from = @$report_where['date_from'];
		$to = @$report_where['date_to'];
		$last24HoursCronsData = getLast24HoursCronLog($report_where['cron_ids']);

		//create range key from cron log
		if ($last24HoursCronsData) {
			foreach ($last24HoursCronsData as $l24hCD) {
				if (strtotime($l24hCD->start_datetime) < strtotime($from)) $from = strtotime($l24hCD->start_datetime);
				if ($l24hCD->end_datetime != '0000-00-00 00:00:00' && strtotime($l24hCD->end_datetime) > $to) $to = strtotime($l24hCD->end_datetime);
			}
		}

		if ( ! empty($report_where['competitor_map'])) {
			// get the intersection of the two sets of merchants
			$sql = "SELECT cmn.id
				FROM " . $this->_table_products . " p
				LEFT JOIN " . $this->_table_crowl_product_list . " cpl ON cpl.upc=p.upc_code
				LEFT JOIN " . $this->_table_crowl_merchant_name . " cmn ON cmn.id=cpl.merchant_name_id
				WHERE p.id = ?";
			$merchant_query = $sql . " AND cmn.id IN ($sql)";
			$merchants_intersection = array();
			$lookup_merchants = array();
			foreach ($report_where['competitor_map'] as $prodId => $owner_product) {
				$result = $this->db->query($merchant_query, array($prodId, $owner_product['id']))->result_array();
				for ($i = 0, $n = count($result); $i < $n; $i++) {
					$merchant_id = $result[$i]['id'];
					$merchants_intersection[$merchant_id] = true;
				}
			}
			// get the intersection of merchants filter and the set we just made
			if (isset($report_where['merchants'][0]) and $report_where['merchants'][0] !== 'all') {
				foreach ($report_where['merchants'] as $merchant_id) {
					if (isset($merchants_intersection[$merchant_id]))
						$lookup_merchants[$merchant_id] = true;
				}
			}
			else {
				$lookup_merchants = $merchants_intersection;
			}
			$report_where['merchants'] = array_keys($lookup_merchants);
		}

		//only run dynamo queries on products within a crawl that we are searching
		//will require change to dynamo hashes - TBD
		$where = '1=1';
		$orderBy = '';
		$orderByTmp = '';
		$MarketFilter = array();

		$whereVals = requestInfoWhereValues($report_where, $MarketFilter, $orderByTmp);
		if ( ! $skip_product_id and isset($whereVals['products'])) {
			$report_where['product_ids'] = array_filter($report_where['product_ids']);
			$where .= ' AND products.id ' . $whereVals['products'];
			$orderBy = $orderByTmp;
		}
		if (isset($whereVals['marketplaces'])){
			$where .= ' AND crowl_merchant_name_new.marketplace ' . $whereVals['marketplaces'];
			$MarketFilter = $whereVals['marketplaces'];
		}
		if (isset($whereVals['merchants']))
			$where .= ' AND crowl_merchant_name_new.id ' . $whereVals['merchants'];
		if (isset($whereVals['store']))
			$where .= ' AND products.store_id ' . $whereVals['store'];

		$productQuery = "SELECT
			concat(crowl_merchant_name_new.seller_id,'#',crowl_product_list_new.upc) as hashKey,
			crowl_merchant_name_new.seller_id,
			products.title,
			products.upc_code,
			products.id,
			products.price_floor,
			products.retail_price,
			products.wholesale_price,
			products.store_id,
			crowl_merchant_name_new.merchant_name,
			crowl_merchant_name_new.original_name
			FROM
				crowl_product_list_new
			INNER JOIN crowl_merchant_name_new ON crowl_merchant_name_new.id = crowl_product_list_new.merchant_name_id
			LEFT JOIN products ON products.upc_code = crowl_product_list_new.upc
			WHERE $where
			GROUP BY hashKey $orderBy";
		//echo "<pre>$productQuery";exit;
		$products = $this->db->query($productQuery)->result();

		$finalProductsArray = array();
		foreach($products as $product)
		{
			// get the "nosql" data
			$priceTrends = $this->ProductsTrends->get_by_hashkey_and_date_range_and_marketplace($product->hashKey, $from, $to, $MarketFilter, 5); // ceiling of 5 results gives us a good enough average
			foreach($priceTrends->result_object() as $priceTrend)
			{
				//if we're doing violations, create listing of pricing violations for range keys
				if (isset($report_where['violation']) && $report_where['violation']) {
					//safety hack to not show incorrect violations
					if((float)$priceTrend->mpo >= (float)$priceTrend->ap)
						continue;
				}
				//TODO: store these two data inside the product_trends table instead
				$retailPricePoint 	 = getPricePoint($product->upc_code, $product->store_id, 'retail_price', (int)$priceTrend->dt);
				$wholesalePricePoint = getPricePoint($product->upc_code, $product->store_id, 'wholesale_price', (int)$priceTrend->dt);
				
				$priceTrendArray = array(
					'prod_id' 		=> (int)   $product->id,
					'upc_code' 		=> (string)$product->upc_code,
					'retail' 		=> (float) $retailPricePoint,
					'wholesale' 	=> (float) $wholesalePricePoint,
					'price' 		=> (float) $priceTrend->mpo,
					'map' 			=> (float) $priceTrend->ap,
					'title' 		=> (string)$priceTrend->t,
					'marketplace' 	=> (string)$priceTrend->ar,
					'url' 			=> (string)$priceTrend->l,
					'timestamp'		=> (int)   $priceTrend->dt,
					'dt'			=> (int)   $priceTrend->dt,
					'hash_key'		=> (string)$priceTrend->um,
					'merchant_id' 	=> (string)$product->seller_id,
					'date' 			=> (string)date('m/d/Y G:i:s', (int)$priceTrend->dt),
					'shot' 			=> (string)$priceTrend->ss
				);
				$finalProductsArray[$product->id][] = $priceTrendArray;
			}
		}
		return array("data" => $finalProductsArray);
	}

	function productPricingHistory($report_where = array()) {
		$api_typeArr = array();
		$where = $merchantJoin = '';

		$whereVals = requestInfoWhereValues($report_where, $api_typeArr);
		if (isset($whereVals['marketplaces']))
			$where .= ' AND d.marketplace ' . $whereVals['marketplaces'];
		if (isset($whereVals['merchants'])) {
			$merchantJoin = " LEFT JOIN crowl_merchant_name_new cm ON cm.marketplace=d.marketplace ";
			$where .= ' AND cm.merchant_name ' . $whereVals['merchants'];
		}
		if (isset($whereVals['store']))
			$where .= ' AND products.store_id ' . $whereVals['store'];
			
		for ($i=0; $i<sizeof($report_where['product_ids']); $i++) {
			$curProd = getProductUPCByID($report_where['product_ids'][$i]);
			$productUPC = $curProd['upc_code'];
			$lookup_store_id = $curProd['store_id'];

			// TODO: delete this line eventually
			if($report_where['date_from'] < strtotime('2014-04-01')) {
				$report_where['date_from'] = strtotime('2014-04-01');
			}
			
			// query two times: once in the big archive table ...
			$this->_getDailyPriceAverage($this->_dynamo_daily_price_average_archive, $merchantJoin, $report_where, $i, $where, $productUPC, $lookup_store_id);
			
			// ... and query a 2nd time in the little daily table
			if (date('Y-m-d', $report_where['date_to']) == date('Y-m-d', time())) {
				$this->_getDailyPriceAverage($this->_dynamo_daily_price_average, $merchantJoin, $report_where, $i, $where, $productUPC, $lookup_store_id);
			}
		}
		return $this->finalData;
	}
	
	private function _getDailyPriceAverage($tableName, $merchantJoin, $report_where, $i, $where, $productUPC, $lookup_store_id){
		
		$averageQuery = "SELECT d.*, p.title, p.id
			FROM $tableName d
			LEFT JOIN products p ON d.upc = p.upc_code
			$merchantJoin
			WHERE d.date BETWEEN '".date("Y-m-d", $report_where['date_from'])."' AND '".date("Y-m-d", $report_where['date_to'])."'
			AND p.id=".$report_where['product_ids'][$i].$where;

		$average = $this->db->query($averageQuery)->result();
		
		$range['start'] = $report_where['date_from'];
		$range['end'] = $report_where['date_to'];
		
		if (count($average) > 0) {
			foreach ($average as $row) {
				
				$wholeSale = getPricePoint($productUPC, $lookup_store_id, 'wholesale_price', '', $range);
				$retail = getPricePoint($productUPC, $lookup_store_id, 'retail_price', '', $range);
				$map = getPricePoint($productUPC, $lookup_store_id, 'price_floor', '', $range);
										 
				$idxMarketPlace = strtolower($row->marketplace);
				$this->finalData[$report_where['product_ids'][$i]][$idxMarketPlace][] = array(
					'marketplace' => $row->marketplace,
					'upc'         => $row->upc,
					//'price'       => number_format(($row->price_total/$row->seller_total), 2),
					'price'       => floatval(($row->price_total/$row->seller_total)),
					'wholesale'   => $wholeSale,
					'retail'      => $retail,
					'title'		  => $row->title,
					'map'         => $map,
					'dt'          => strtotime($row->date),
					'timestamp'	  => $row->date,
					'date'        => (string)$row->date,
					'prod_id'     => $report_where['product_ids'][$i]);
			}
		}
	}

	function filterData($data) {
		$resultData = array();
		$hashKeyArray = array();
		if (count($data) > 0) {
			for ($m=0;$m<count($data);$m++) {
				$val = htmlentities(trim(preg_replace('/[^0-9 a-zA-Z#]/', '', $data[$m]->hashKey)));
				$data[$m]->hashKey = $val;
				if (!in_array($val, $hashKeyArray)) {
					$resultData[] = $data[$m];
					$hashKeyArray[] = $val;
				}
			}
		}
		return $resultData;
	}

	function makeMerchantName($t) {
		$subName = substr($t, -3);
		if (strtolower($subName)=='com') {
			$t = substr($t, 0, strlen($t)-3);
			$t = $t.'.com';
		}
		return $t;
	}

	/**
	 * Convert a time into an array of clock images
	 *
	 * @param String $lastTrackedTime
	 * @return array
	 */
	public function last_tracked_image($lastTrackedTime) {
		$lastTrackedTime = date('h:i a', strtotime($lastTrackedTime));

		$dir = 'NUMBERS_PUNCTUATION_MARKS/';
		$time = array();
		if ($lastTrackedTime{0} !== '0')
			$time[] = $dir . $lastTrackedTime{0} . '.png';

		$time[] = $dir . $lastTrackedTime{1} . '.png';
		$time[] = $dir . 'COLON.png';
		$time[] = $dir . $lastTrackedTime{3} . '.png';
		$time[] = $dir . $lastTrackedTime{4} . '.png';

		$meridiem = 'DAYS_TIME/';
		$meridiem .= $lastTrackedTime{6} === 'a' ? 'AM.png' : 'PM.png';

		return array('time' => $time, 'meridiem' => $meridiem);
	}

	// DEPRECATED
	//function merchant_products($data, $store_id) {
	//	$inArray = array();
	//	$p_ids = $this->getStoreUPCAll($store_id);
	//	foreach ($p_ids as $PID) {
	//		$inArray[] = array(AmazonDynamoDB::TYPE_STRING => (string)$PID);
	//	}
	//	$filters['upc_code'] =array(
	//		'ComparisonOperator' => AmazonDynamoDB::CONDITION_IN,
	//		'AttributeValueList' => $inArray
	//	);
	//	$filters['merchant_id'] =array(
	//		'ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
	//		'AttributeValueList' => array(array(AmazonDynamoDB::TYPE_STRING =>(string)$merchant_id))
	//	);
	//	$filters['datetime_tracked'] =array(
	//		'ComparisonOperator' => AmazonDynamoDB::CONDITION_BETWEEN,
	//		'AttributeValueList' => array(array(AmazonDynamoDB::TYPE_STRING => (string)strtotime($data['date_from'])), array(AmazonDynamoDB::TYPE_STRING => (string)strtotime($data['date_to'])))
	//	);
	//	$scanData= $this->amzdb->scanTableByFilters($this->_dynamo_products_trends, $filters);
	//	$countRes = $scanData->body->Count;
	//	$priceTrendArray = array();
	//	if ($countRes > 0) {
	//		$d = $scanData->body->Items->{0};
	//		$data['url'] = '<a href="'.$d->merchant_url->S.'" target="_blank" >'.substr($d->merchant_url->S, 0, 35).'...</a>';
	//		$data['products'] = $countRes;
	//	}else {
	//		$data['products'] = 0;
	//		$data['url'] = '';
	//	}
	//	log_message('info', "Dynamo conversion, output array of this function:
	//		File: ".__FILE__."
	//		Line: ".__LINE__."
	//		Class: ".__CLASS__."
	//		Function: ".__FUNCTION__."
	//		Method: ".__METHOD__."
	//		" . print_r($data , true)
	//	);
	//	return $data;
	//}
	
	
	// DEPRECATED
	//function get_violated_products($store_id=0) {
	//	$ar = array('where'=>array('store_id'=>$store_id));
	//	$rows = ViolationsMongo::getWithCriteria($ar);
	//	return count($rows);
	//}

	// DEPRECATED
	//function get_report($report_where=array(), $type='xml') {
	//	$availableUPC = $this->getStoreUPCAll($report_where['storeid']);
	//	$availableUPCSingle = $this->getProductUPC($report_where['upccode']);
	//	if (trim($report_where['upccode']) != '') {
	//		$ar = array(
	//			'where'     => array(
	//				'upc_code' => array('$in'=>$availableUPC),
	//				'track_date.d' =>array('$gte'=>$report_where['date_from'], '$lte'=>$report_where['date_to'])
	//			),
	//			'group'     => array(
	//				'keys'    => array('datetime_tracked' => 1),
	//				'initial' => array("count" => 0, 'avgprice'=>0, 'mprice'=>0, 'upc'=>array(), 'datetime_tracked'=>array(), 'title'=>array()),
	//				'reduce'  => 'function (obj, prev) {prev.count++; prev.mprice+=parseFloat(obj.merchant_price_offered);prev.avgprice = prev.mprice/prev.count;prev.upc.push(obj.upc_code);prev.datetime_tracked.push(obj.datetime_tracked);prev.title.push(obj.title);}'
	//			)
	//		);
	//
	//		$rows = ProductsTrendsMongo::getByGroup($ar);
	//		if ($type=='listing') {
	//			foreach ($rows as $row) {
	//				$resAr[] = array
	//				(
	//					'average'=>$row->avgprice,
	//					'datetime_tracked'=>$row->datetime_tracked[0],
	//					'title'=>$row->title[0]
	//				);
	//			}
	//			return $resAr;
	//		}
	//
	//		$data = '';
	//		$max_price = 0;
	//		foreach ($rows as $row) {
	//			$data .= "<set label='Product' value='".$row->avgprice."' />";
	//			if ( $row->avgprice > $max_price )
	//				$max_price = $row->avgprice;
	//		}
	//		$xml = "<chart caption='".$report_where['upccode']." Datewise Average Price Report' subCaption='From: ".$report_where['date_from']." - To: ".$report_where['date_to']."' xAxisName='Products' yAxisName='Average Price' yAxisMaxValue='".($max_price+50)."' bgColor='406181, 6DA5DB'  bgAlpha='100' baseFontColor='FFFFFF' canvasBgAlpha='0' canvasBorderColor='FFFFFF' divLineColor='FFFFFF' divLineAlpha='100' numVDivlines='10' vDivLineisDashed='1' showAlternateVGridColor='1' lineColor='BBDA00' anchorRadius='4' anchorBgColor='BBDA00' anchorBorderColor='FFFFFF' anchorBorderThickness='2' showValues='0' numberSuffix='%' toolTipBgColor='406181' toolTipBorderColor='406181' alternateHGridAlpha='5'>".
	//			$data.
	//			"</chart>";
	//		return $xml;
	//	}else {
	//		$ar = array(
	//			'where'     => array(
	//				'upc_code' => array('$in'=>$availableUPC),
	//				'datetime_tracked' =>array('$gte'=>$report_where['date_from'], '$lte'=>$report_where['date_to'])
	//			),
	//			'group'     => array(
	//				'keys'    => array('datetime_tracked' => 1),
	//				'initial' => array("count" => 0, 'avgprice'=>0, 'mprice'=>0, 'upc'=>array(), 'datetime_tracked'=>array(), 'title'=>array()),
	//				'reduce'  => 'function (obj, prev) {prev.count++; prev.mprice+=parseFloat(obj.merchant_price_offered);prev.avgprice = prev.mprice/prev.count;prev.upc.push(obj.upc_code);prev.datetime_tracked.push(obj.datetime_tracked);prev.title.push(obj.title);}'
	//			)
	//
	//		);
	//
	//		$rows = ProductsTrendsMongo::getByGroup($ar);
	//		if ($type=='listing') {
	//			$resAr = array();
	//			foreach ($rows as $row) {
	//				$resAr[] = array
	//				(
	//					'average'=>$row->avgprice,
	//					'datetime_tracked'=>$row->datetime_tracked[0],
	//					'title'=>$row->title[0]
	//				);
	//			}
	//			return $resAr;
	//		}
	//		$data = '';
	//		$max_price = 0;
	//		foreach ($rows as $row) {
	//			$data .= "<set label='Product' value='".$row->avgprice."' />";
	//			if ( $row->avgprice > $max_price )
	//				$max_price = $row->avgprice;
	//		}
	//		$xml = "<chart caption='Product Average Price Report' subCaption='From: ".$report_where['date_from']." - To: ".$report_where['date_to']."' xAxisName='Products' yAxisName='Average Price' yAxisMaxValue='".($max_price+50)."' bgColor='406181, 6DA5DB'  bgAlpha='100' baseFontColor='FFFFFF' canvasBgAlpha='0' canvasBorderColor='FFFFFF' divLineColor='FFFFFF' divLineAlpha='100' numVDivlines='10' vDivLineisDashed='1' showAlternateVGridColor='1' lineColor='BBDA00' anchorRadius='4' anchorBgColor='BBDA00' anchorBorderColor='FFFFFF' anchorBorderThickness='2' showValues='0' numberSuffix='%' toolTipBgColor='406181' toolTipBorderColor='406181' alternateHGridAlpha='5'>".
	//			$data.
	//			"</chart>";
	//		return $xml;
	//	}
	//}


	/**
	 * @deprecated
	 */
//	function productPricingReport24_deprecated($report_where = array(), $skip_product_id = false) {
//		$from = $report_where['date_from'];
//		$to = $report_where['date_to'];
//		$last24HoursCronsData = getLast24HoursCronLog($report_where['cron_ids']);
//
//		//create range key from cron log
//		if ($last24HoursCronsData) {
//			foreach ($last24HoursCronsData as $l24hCD) {
//				if (strtotime($l24hCD->start_datetime) < strtotime($from)) $from = strtotime($l24hCD->start_datetime);
//				if ($l24hCD->end_datetime != '0000-00-00 00:00:00' && strtotime($l24hCD->end_datetime) > $to) $to = strtotime($l24hCD->end_datetime);
//			}
//		}
//
//		if ( ! empty($report_where['competitor_map'])) {
//			// get the intersection of the two sets of merchants
//			$sql = "SELECT cmn.id
//				FROM " . $this->_table_products . " p
//				LEFT JOIN " . $this->_table_crowl_product_list . " cpl ON cpl.upc=p.upc_code
//				LEFT JOIN " . $this->_table_crowl_merchant_name . " cmn ON cmn.id=cpl.merchant_name_id
//				WHERE p.id = ?";
//			$merchant_query = $sql . " AND cmn.id IN ($sql)";
//			$merchants_intersection = array();
//			$lookup_merchants = array();
//			foreach ($report_where['competitor_map'] as $prodId => $owner_product) {
//				$result = $this->db->query($merchant_query, array($prodId, $owner_product['id']))->result_array();
//				for ($i = 0, $n = count($result); $i < $n; $i++) {
//					$merchant_id = $result[$i]['id'];
//					$merchants_intersection[$merchant_id] = true;
//				}
//			}
//			// get the intersection of merchants filter and the set we just made
//			if (isset($report_where['merchants'][0]) and $report_where['merchants'][0] !== 'all') {
//				foreach ($report_where['merchants'] as $merchant_id) {
//					if (isset($merchants_intersection[$merchant_id]))
//						$lookup_merchants[$merchant_id] = true;
//				}
//			}
//			else {
//				$lookup_merchants = $merchants_intersection;
//			}
//			$report_where['merchants'] = array_keys($lookup_merchants);
//		}
//
//		//only run dynamo queries on products within a crawl that we are searching
//		//will require change to dynamo hashes - TBD
//		$where = '1=1';
//		$orderBy = '';
//		$orderByTmp = '';
//		$MarketFilter = array();
//
//		$whereVals = requestInfoWhereValues($report_where, $MarketFilter, $orderByTmp);
//		if ( ! $skip_product_id and isset($whereVals['products'])) {
//			$report_where['product_ids'] = array_filter($report_where['product_ids']);
//			$where .= ' AND products.id ' . $whereVals['products'];
//			$orderBy = $orderByTmp;
//		}
//		if (isset($whereVals['marketplaces']))
//			$where .= ' AND crowl_merchant_name_new.marketplace ' . $whereVals['marketplaces'];
//		if (isset($whereVals['merchants']))
//			$where .= ' AND crowl_merchant_name_new.id ' . $whereVals['merchants'];
//		if (isset($whereVals['store']))
//			$where .= ' AND products.store_id ' . $whereVals['store'];
//
//		$productQuery = "SELECT
//			concat(crowl_merchant_name_new.seller_id,'#',crowl_product_list_new.upc) as hashKey,
//			crowl_merchant_name_new.seller_id,
//			products.title,
//			products.upc_code,
//			products.id,
//			products.price_floor,
//			products.retail_price,
//			products.wholesale_price,
//			products.store_id,
//			crowl_merchant_name_new.merchant_name,
//			crowl_merchant_name_new.original_name
//			FROM
//				crowl_product_list_new
//			INNER JOIN crowl_merchant_name_new ON crowl_merchant_name_new.id = crowl_product_list_new.merchant_name_id
//			LEFT JOIN products ON products.upc_code = crowl_product_list_new.upc
//			WHERE $where
//			GROUP BY hashKey $orderBy";
//		$products = $this->db->query($productQuery)->result();
//
//		//if we're doing violations, create listing of pricing violations for range keys
//		if (isset($report_where['violation']) && $report_where['violation']) {
//			//we create a new array of products containing only violations
//			$violationProducts = $vShots = array();
//
//			foreach ($products as $product) {
//				//if we organize these by seller - we could make fewer queries with batching...
//				$filters =array(array(AmazonDynamoDB::TYPE_NUMBER => "$from"), array(AmazonDynamoDB::TYPE_NUMBER =>"$to"));
//				$response = $this->amzdb->getQueryViolation($this->_dynamo_violations, $product->hashKey, $filters);
//				$countRes = (int)$response->body->Count;
//				if ($countRes > 0) {
//					for ($k=0;$k<$countRes;$k++) {
//						$dataViolation = $response->body->Items->{$k};
//						$violationProducts[$product->id][] = array(
//							'hash' => $product->hashKey,
//							'dt' => (int)$dataViolation->dt->N,
//							'ss' => (string)$dataViolation->ss->S,
//							'retail' => $product->retail_price,
//							'wholesale' => $product->wholesale_price,
//							'upc' => $product->upc_code,
//							'prod_id' => $product->id,
//							'merchant' => $this->makeMerchantName($product->merchant_name),
//							'merchant_id' => $product->seller_id
//						);
//					}
//				}
//			}
//			reset($products);
//		}
//
//		$finalProductsArray = $curProduct = array();
//		if ((isset($report_where['violation']) && $report_where['violation'])) {
//			foreach ($violationProducts as $prodId=>$vInfo) {
//				$pRef = array();
//				$batchArray = array($this->_dynamo_products_trends => array());
//				foreach ($vInfo as $ts=>$record) {
//					$batchArray[$this->_dynamo_products_trends][] = array(
//						'HashKeyElement' => $record['hash'],
//						'RangeKeyElement' => $record['dt']);
//					$pRef[$prodId][$record['hash']][$record['dt']] = array(
//						'ss' => $record['ss'],
//						'upc' => $record['upc'],
//						'merchant_id' => $record['merchant_id']);
//				}
//
//				$xxx = 0;
//				$gathering = true;
//				$dynamoArray = array($this->_dynamo_products_trends => array());
//				//can only send 100 requests at a time
//				if (sizeof($batchArray[$this->_dynamo_products_trends]) > 100) {
//					$chunked = array_chunk($batchArray[$this->_dynamo_products_trends], 100);
//					for ($c=0, $h=sizeof($chunked); $c<$h; $c++) {
//						$theBatch = $chunked[$c];
//						$gathering = true;
//
//						while ($gathering) {
//							$violationPricing = $this->amzdb->batchGetItem($this->_dynamo_products_trends, array($this->_dynamo_products_trends => $theBatch));
//							for ($i=0, $n=sizeof($violationPricing->body->Responses->{$this->_dynamo_products_trends}->Items); $i<$n; $i++) {
//								array_push($dynamoArray[$this->_dynamo_products_trends], $violationPricing->body->Responses->{$this->_dynamo_products_trends}->Items[$i]);
//							}
//
//							//queue up more requests if necessary
//							if (isset($violationPricing->body->UnprocessedKeys->{$this->_dynamo_products_trends}) && count($violationPricing->body->UnprocessedKeys->{$this->_dynamo_products_trends}->Keys) > 0) {
//								count($violationPricing->body->UnprocessedKeys->{$this->_dynamo_products_trends}->Keys);
//								$tmpBatch = array();
//								for ($y=0, $z=sizeof($violationPricing->body->UnprocessedKeys->{$this->_dynamo_products_trends}->Keys); $y<$z; $y++) {
//									$ind = $violationPricing->body->UnprocessedKeys->{$this->_dynamo_products_trends}->Keys[$y];
//									$tmpBatch[] = array(
//										'HashKeyElement' => (string)$ind->HashKeyElement->S,
//										'RangeKeyElement' => (int)$ind->RangeKeyElement->N
//									);
//								}
//								$theBatch = $tmpBatch;
//								//echo "sending new batch<br>\n";var_dump($theBatch);
//							}else {
//								$gathering = false;
//							}
//							//stop endless loop during testing
//							$xxx++;
//							if ($xxx == 15) {
//								//echo "exiting due to xxx<br>\n";
//							}
//						}
//					}
//				}else {
//					$gathering = true;
//					$theBatch = $batchArray;
//					while ($gathering) {
//						$violationPricing = $this->amzdb->batchGetItem($this->_dynamo_products_trends, $theBatch);
//						for ($i=0, $n=sizeof($violationPricing->body->Responses->{$this->_dynamo_products_trends}->Items); $i<$n; $i++) {
//							array_push($dynamoArray[$this->_dynamo_products_trends], $violationPricing->body->Responses->{$this->_dynamo_products_trends}->Items[$i]);
//						}
//
//						//queue up more requests if necessary
//						if (isset($violationPricing->body->UnprocessedKeys->{$this->_dynamo_products_trends}) && count($violationPricing->body->UnprocessedKeys->{$this->_dynamo_products_trends}->Keys) > 0) {
//							count($violationPricing->body->UnprocessedKeys->{$this->_dynamo_products_trends}->Keys);
//							$tmpBatch = array();
//							for ($y=0, $z=sizeof($violationPricing->body->UnprocessedKeys->{$this->_dynamo_products_trends}->Keys); $y<$z; $y++) {
//								$ind = $violationPricing->body->UnprocessedKeys->{$this->_dynamo_products_trends}->Keys[$y];
//								$tmpBatch[] = array(
//									'HashKeyElement' => (string)$ind->HashKeyElement->S,
//									'RangeKeyElement' => (int)$ind->RangeKeyElement->N
//								);
//							}
//							$theBatch = array($this->_dynamo_products_trends => $tmpBatch);
//						}else {
//							$gathering = false;
//						}
//						//stop endless loop during testing
//						$xxx++;
//						if ($xxx == 15) {
//							//echo "exiting due to xxx<br>\n";
//						}
//					}
//				}
//
//				for ($k=0, $q = sizeof($dynamoArray[$this->_dynamo_products_trends]); $k<$q; $k++) {
//					$marketPlace = (string)$dynamoArray[$this->_dynamo_products_trends][$k]->ar->S;
//					$marketPlace = (strtolower($marketPlace) == 'shopping') ? 'Shopping.com' : ucfirst(strtolower($marketPlace));
//
//					//maybe if we use seller_id#upc has keys we don't have to do this stupid continue
//					if (!empty($MarketFilter) && !in_array(strtolower((string)$dynamoArray[$this->_dynamo_products_trends][$k]->ar->S), $MarketFilter)) {
//						//echo "conrinuing ".ucfirst((string)$dynamoArray[$this->_dynamo_products_trends][$k]->ar->S)."<br>\n";
//						continue;
//					}
//
//					$um = (string)$dynamoArray[$this->_dynamo_products_trends][$k]->um->S;
//					$ts = (int)$dynamoArray[$this->_dynamo_products_trends][$k]->dt->N;
//					$violationTrendArray = array(//I believe $price = total - shipping
//						'price' => (float)$dynamoArray[$this->_dynamo_products_trends][$k]->mpo->N,
//						'map' => (float)number_format((float)$dynamoArray[$this->_dynamo_products_trends][$k]->ap->N, 2),
//						'title' => (string)$dynamoArray[$this->_dynamo_products_trends][$k]->t->S,
//						'marketplace' => $marketPlace,
//						'url' => (string)$dynamoArray[$this->_dynamo_products_trends][$k]->l->S,
//						'dt' => (int)$dynamoArray[$this->_dynamo_products_trends][$k]->dt->N,
//						'prod_id' => $prodId,
//						'timestamp'=>(int)$dynamoArray[$this->_dynamo_products_trends][$k]->dt->N,
//						'hash_key'=>(string)$dynamoArray[$this->_dynamo_products_trends][$k]->um->S,
//						'merchant_id' => $pRef[$prodId][$um][$ts]['merchant_id'],
//						'date' => date('m/d/Y G:i:s', (int)$dynamoArray[$this->_dynamo_products_trends][$k]->dt->N),
//						'shot' => $pRef[$prodId][$um][$ts]['ss']
//					);
//					$curProduct[$prodId][] = $violationTrendArray;
//				}
//
//				if (isset($curProduct[$prodId])) {
//					$result = msort($curProduct[$prodId], 'dt');
//					$finalProductsArray[$prodId] = $result;
//				}
//			}
//			//echo "violation record<br>\n";
//			//var_dump($curProduct[$prodId]);exit;
//		}else {
//            $productcount = 0;
//			foreach ($products as $product) {
//				//echo "dynamo request {$product->hashKey} $from to $to<br>\n";
//				$priceTrendArray = array();//for handling different product data
//				$filters = array(array(AmazonDynamoDB::TYPE_NUMBER => "$from"), array(AmazonDynamoDB::TYPE_NUMBER =>"$to"));
//				$response = $this->amzdb->getQueryViolation($this->_dynamo_products_trends, $product->hashKey, $filters);
//				$countRes = (int)$response->body->Count;
//				if ($countRes > 0) {
//					for ($k=0;$k<$countRes;$k++) {
//						$priceTrend = $response->body->Items->{$k};
//						$marketPlace = (string)$priceTrend->ar->S;
//						$marketPlace = (strtolower($marketPlace) == 'shopping') ? 'Shopping.com' : ucfirst(strtolower($marketPlace));
//
//						//maybe if we use seller_id#upc as keys we don't have to do this stupid continue
//						if (!empty($MarketFilter) && !in_array(strtolower((string)$priceTrend->ar->S), $MarketFilter)) {
//							//echo "conrinuing ".ucfirst((string)$priceTrend->ar->S)."<br>\n";
//							continue;
//						}
//
//						$hash = explode('#', (string)$priceTrend->um->S);
//						$wholeSale = getPricePoint($hash[1], $product->store_id, 'wholesale_price', (int)$priceTrend->dt->N);
//						$retail = getPricePoint($hash[1], $product->store_id, 'retail_price', (int)$priceTrend->dt->N);
//						$map = getPricePoint($hash[1], $product->store_id, 'price_floor', (int)$priceTrend->dt->N);
//
//						$priceTrendArray = array(//I believe $price = total - shipping
//							'price'=> (float)$priceTrend->mpo->N,
//							//this is not currently accurate - use price point until crawl is corrected
//							//'map' => (float)number_format((float)$priceTrend->ap->N, 2),
//							'map' => $map,
//							'wholesale' => $wholeSale,
//							'retail' => $retail,
//							'title' => (string)$priceTrend->t->S,
//							'marketplace' => $marketPlace,
//							'url'=>(string)$priceTrend->l->S,
//							'dt'=>(int)$priceTrend->dt->N,
//							'prod_id' => $product->id,
//							'timestamp'=>(int)$priceTrend->dt->N,
//							'hash_key'=>(string)$priceTrend->um->S,
//							'merchant_id' => $product->seller_id,
//							'date'=>date('m/d/Y G:i:s', (int)$priceTrend->dt->N)
//						);
//						$curProduct[$product->id][] = $priceTrendArray;
//					}
//					if (isset($curProduct[$product->id])) {
//						$result = msort($curProduct[$product->id], 'dt');
//						$finalProductsArray[$product->id] = $result;
//					}
//                    
//                    $productcount++;
//                    if ( $productcount >= 5 ) break;
//				}
//			}
//		}//foreach($products)
//
//		$response = array("data"=>$finalProductsArray);
//		//echo "productPricingReport24<br>\n";
//		//var_dump($response);exit;
//log_message('info', "Dynamo conversion, output array of this function:
//    File: ".__FILE__."
//    Line: ".__LINE__."
//    Class: ".__CLASS__."
//    Function: ".__FUNCTION__."
//    Method: ".__METHOD__."
//    " . print_r($response , true)
//);
//		return $response;
//	}

}