<?php

/**
 *
 * @package  mvFormat
 *
 *
 *
 */
class mvFormat {

	/**
	 *
	 * function violationSummaryByDate
	 *
	 * @param <array>     $data
	 *
	 *
	 */
	public static function violationSummaryByDate($gData, $request) {
		$merchantsCount = array();
		$productsMerchants = array();
		$series = array();

		$cat = createDateRangeArray($request['fromDate'], $request['toDate']);

		foreach ($gData as $data) {
			$date = date('m/d/y', $data['dt']);
			$merchantsCount[$date][] = $data['merchant'];

			if (!isset($productsMerchants[$data['prod_id']])) {
				$productsMerchants[$data['prod_id']] = array(
					'title' => $data['title'],
					'merchants' => array(
					)
				);
			}

			$productsMerchants[$data['prod_id']]['merchants'][] = $data['merchant'];
		}

		foreach ($merchantsCount as &$data) {
			$data = count(array_unique($data));
		}

		$cat = array_unique($cat);
		sort($cat);

		$i = 0;
		$priceD = array();
		foreach ($cat as &$cval) {
			if (!isset($merchantsCount[$cval])) {
				unset($cval);
			} else {
				$priceD[] = $merchantsCount[$cval];
			}
		}

		$series[] = array('name' => 'Merchants Count', 'data' => $priceD, 'dashStyle' => 'Dot', 'color' => Color_handler::get_next($i)->get_hex(), 'id' => 'merchant_count');

		foreach ($cat as &$val) {
			$val = date('n/j/Y', strtotime($val));
		}

		if (count($cat) > 10) {
			foreach ($cat as &$val) {
				$val = ' ';
			}
		}

		$graph_data = array(
			'data' => $series,
			'y_title' => 'Price',
			'x_title' => 'Date',
			'cat' => $cat
		);

		return array('graph' => $graph_data, 'products' => $productsMerchants);
	}

	/**
	 *
	 * function violationProductsByDate
	 *
	 * @param <array>     $data
	 *
	 *
	 */
	public static function violationProductsByDate($gData, $request) {
		$merchantsCount = array();
		$series = array();

		$i = 0;

		$cat = createDateRangeArray($request['fromDate'], $request['toDate']);
		$all_markets = array();

		foreach ($gData['data'] as $date => $marketplace) {
			foreach ($marketplace as $market => $row) {
				foreach ($row['products'] as $prod_id => $merchants) {
					$prod_info = $gData['products'][$prod_id];
					$timestamp = date('m/d/y', $row['date']);

					if (!isset($merchantsCount[$prod_id]['date'][$timestamp][$market])) {
						$merchantsCount[$prod_id]['date'][$timestamp][$market] = 0;
					}
					if (!in_array($market, $all_markets)) {
						$all_markets[] = $market;
					}

					$merchantsCount[$prod_id]['date'][$timestamp][$market] += count($merchants);
					$merchantsCount[$prod_id]['product'] = $prod_info;
				}
			}
		}

		foreach ($request['product_ids'] as $key => $val) {
			$colorCount = 0;
			if (isset($merchantsCount[$val]['date']) && is_array($merchantsCount[$val]['date'])) {
				foreach ($merchantsCount[$val]['date'] as $date => $markets) {
					$colorCount = 1;
					foreach ($all_markets as $market_) {
						if (isset($markets[$market_])) {
							$graph_data[$val][$date][$market_] = $markets[$market_];
						} else {
							$graph_data[$val][$date][$market_] = 0;
						}
					}
				}
			}
			$i++;
		}

		$cat = array_unique($cat);
		sort($cat);

		$i = 0;
		foreach ($graph_data as $prod_id => $dArr) {
			$priceD1 = array();
			$priceD2 = array();
			$priceD3 = array();
			foreach ($cat as $ckey => &$cval) {
				if (!isset($dArr[$cval])) {
					unset($cat[$ckey]);
				} else {
					$priceD1[] = (isset($dArr[$cval]['amazon'])) ? $dArr[$cval]['amazon'] : 0;
					$priceD2[] = (isset($dArr[$cval]['google'])) ? $dArr[$cval]['google'] : 0;
					$priceD3[] = (isset($dArr[$cval]['Shopping.com'])) ? $dArr[$cval]['Shopping.com'] : 0;
				}
			}

			if (in_array('all', $request['api_type'])) {
				$series[] = array('name' => getProductsTitle($prod_id, 'Amazon'), 'data' => $priceD1, 'color' => Color_handler::get_next(0)->get_hex(), 'id' => $prod_id);
				$series[] = array('name' => getProductsTitle($prod_id, 'Google'), 'data' => $priceD2, 'color' => Color_handler::get_next(1)->get_hex(), 'id' => $prod_id);
				$series[] = array('name' => getProductsTitle($prod_id, 'Shopping.com'), 'data' => $priceD3, 'color' => Color_handler::get_next(2)->get_hex(), 'id' => $prod_id);
			} else {
				if (in_array('Amazon', $request['api_type']) || in_array('amazon', $request['api_type'])) {
					$series[] = array('name' => getProductsTitle($prod_id, 'Amazon'), 'data' => $priceD1, 'color' => Color_handler::get_next(0)->get_hex(), 'id' => $prod_id);
				}
				if (in_array('Google', $request['api_type']) || in_array('google', $request['api_type'])) {
					$series[] = array('name' => getProductsTitle($prod_id, 'Google'), 'data' => $priceD2, 'color' => Color_handler::get_next(1)->get_hex(), 'id' => $prod_id);
				}

				if (in_array('Shopping.com', $request['api_type']) || in_array('shopping.com', $request['api_type']) || in_array('shopping', $request['api_type']) || in_array('Shopping', $request['api_type'])) {
					$series[] = array('name' => getProductsTitle($prod_id, 'Shopping.com'), 'data' => $priceD3, 'color' => Color_handler::get_next(2)->get_hex(), 'id' => $prod_id);
				}
			}
			$i++;
		}

		foreach ($cat as &$val) {
			$val = date('n/j/Y', strtotime($val));
		}

		if (count($cat) > 10) {
			foreach ($cat as &$val) {
				$val = ' ';
			}
		}

		$graph_data = array(
			'data' => $series,
			'y_title' => 'Merchants',
			'x_title' => 'Date',
			'cat' => $cat
		);

		//$graphImagename = $this->generateGraphImage($fArray, $hourFlag, 'Violation Report');
		//$fArray['graphImageName'] = $graphImagename;

		/* For Google Charts */
		$googledataColorArray = array();
		$googleDataArray = array();
		$googleDataArray[0][] = 'Date';
		$maxValue = 0;
		$prCount = count($merchantsCount);
		$keys = array_keys($merchantsCount);
		for ($prcounter = 0; $prcounter < $prCount; $prcounter++) {
			$color = Color_handler::get_next($prcounter)->get_hex();

			if (in_array('all', $request['api_type'])) {
				$googleDataArray[0][] = 'Amazon: ' . getProductsTitle($keys[$prcounter]);
				$googleDataArray[0][] = 'Google:' . getProductsTitle($keys[$prcounter]);
				$googleDataArray[0][] = 'Shopping.com:' . getProductsTitle($keys[$prcounter]);
				$googledataColorArray[] = $color;
				$googledataColorArray[] = $color;
				$googledataColorArray[] = $color;
			} else {
				if (in_array('Amazon', $request['api_type']) || in_array('amazon', $request['api_type'])) {
					$googleDataArray[0][] = 'Amazon: ' . getProductsTitle($keys[$prcounter]);
					$googledataColorArray[] = $color;
				}
				if (in_array('Google', $request['api_type']) || in_array('google', $request['api_type'])) {
					$googleDataArray[0][] = 'Google:' . getProductsTitle($keys[$prcounter]);
					$googledataColorArray[] = $color;
				}

				if (in_array('Shopping.com', $request['api_type']) || in_array('shopping.com', $request['api_type']) || in_array('shopping', $request['api_type']) || in_array('Shopping', $request['api_type'])) {
					$googleDataArray[0][] = 'Shopping.com:' . getProductsTitle($keys[$prcounter]);
					$googledataColorArray[] = $color;
				}
			}
		}

		foreach ($cat as $keyCat => $vCat) {
			$googleDataArray[$keyCat + 1][] = $vCat;
			foreach ($series as $seriesKey => $seriesData) {

				$valueForDisplay = isset($seriesData['data'][$keyCat]) ? $seriesData['data'][$keyCat] : 0;
				$googleDataArray[$keyCat + 1][] = $valueForDisplay;
				if ($valueForDisplay > $maxValue) {
					$maxValue = $valueForDisplay;
				}
			}
		}

		/* END FOR Google Charts */
		return array('graph' => $graph_data, 'products' => $merchantsCount, 'googleData' => $googleDataArray, 'googleDataColors' => $googledataColorArray, 'type' => 'column', 'maxValue' => $maxValue);
	}

	/**
	 *
	 * function violationProductsByDate24
	 *
	 * @param <array>     $data
	 *
	 *
	 */
	public static function violationProductsByDate24($gData, $request) {
		$merchantsCount = array();
		$series = array();
		$cat = array();
		$x_labels = array();

		$i = 0;
		foreach ($gData['data'] as $data) {
			$date = date('m/d/y h:i A', $data['dt']);
			$merchantsCount[$data['prod_id']]['date'][$date][] = $data['merchant'];
			$merchantsCount[$data['prod_id']]['product'] = $data;
			$cat[] = $date;
			$x_labels[$date] = $data['dt'];
		}

		foreach ($request['product_ids'] as $key => $val) {
			if (isset($merchantsCount[$val])) {
				foreach ($merchantsCount[$val]['date'] as $date => $data) {
					$graph_data[$val][$date] = count($data); //array_unique(
				}
			}

			$i++;
		}

		$cat = array_unique($cat);
		sort($cat);

		$i = 0;
		foreach ($graph_data as $key => $dArr) {
			$priceD = array();
			foreach ($cat as &$cval) {
				if (!isset($dArr[$cval])) {
					unset($cval);
				} else {
					$priceD[] = $dArr[$cval];
				}
			}

			$series[] = array('name' => $merchantsCount[$key]['product']['prod_title'], 'data' => $priceD, 'dashStyle' => 'Dot', 'color' => Color_handler::get_next($i++)->get_hex(), 'id' => $merchantsCount[$key]['product']['prod_id']);
		}

		foreach ($cat as &$val) {
			$val = date('n/j/Y h:i A', $x_labels[$val]);
		}

		if (count($cat) > 10) {
			foreach ($cat as &$val) {
				$val = ' ';
			}
		}

		$graph_data = array(
			'data' => $series,
			'y_title' => 'Merchants',
			'x_title' => 'Date',
			'cat' => $cat,
		);

		//$graphImagename = $this->generateGraphImage($fArray, $hourFlag, 'Violation Report');
		//$fArray['graphImageName'] = $graphImagename;

		/* For Google Charts */
		$googledataColorArray = array();
		$googleDataArray = array();
		$googleDataArray[0][] = 'Date';
		$maxValue = 0;
		$prCount = count($merchantsCount);
		$keys = array_keys($merchantsCount);
		for ($prcounter = 0; $prcounter < $prCount; $prcounter++) {
			$googleDataArray[0][] = 'Merchants: ' . getProductsTitle($keys[$prcounter]);
			$googledataColorArray[] = Color_handler::get_next($prcounter)->get_hex();
		}

		foreach ($cat as $keyCat => $vCat) {
			$googleDataArray[$keyCat + 1][] = $vCat;
			foreach ($series as $seriesKey => $seriesData) {
				$valueForDisplay = isset($seriesData['data'][$keyCat]) ? $seriesData['data'][$keyCat] : 0;
				$googleDataArray[$keyCat + 1][] = $valueForDisplay;
				if ($valueForDisplay > $maxValue) {
					$maxValue = $valueForDisplay;
				}
			}
		}

		/* END FOR Google Charts */
		return array('graph' => $graph_data, 'products' => $merchantsCount, 'timestamps' => $x_labels, 'googleData' => $googleDataArray, 'googleDataColors' => $googledataColorArray, 'type' => 'column', 'maxValue' => $maxValue);
	}

	/**
	 *
	 * function whoIsSellingMyProductByDate
	 *
	 * @param array     $data
	 *
	 *
	 */
	public static function whoIsSellingMyProductByDate($gData) {
		$series = array();
		$cat = array();

		foreach ($gData as $pID => $data) {
			$i = 0;
			foreach ($data as $marketplace => $val) {
				$values = array();
				foreach ($val['date'] as $date => $value) {
					$values[] = (int) count($value['merchants']);
					$cat[$date] = 0;
				}

				$series[] = array('name' => getProductsTitle($pID, marketplace_display_name($marketplace)), 'data' => $values, 'dashStyle' => 'Dot', 'color' => marketplace_graph_color($marketplace), 'id' => 'merchant_count');
			}
		}

		$cat = array_keys($cat);

		// For removing dates if more than 10
		if (count($cat) > 10) {
			foreach ($cat as &$val) {
				$val = ' ';
			}
		}

		$graph_data = array(
			'data' => $series,
			'y_title' => 'Merchants',
			'x_title' => 'Date',
			'cat' => $cat
		);

		return $graph_data;
	}

	/**
	 * function whoIsSellingMyProductPieChart
	 *
	 */
	public static function whoIsSellingMyProductPieChart($upc_data) {
		$gData = array('violoation' => 0, 'non_violoation' => 0);
		if (count($upc_data) > 0) {
			foreach ($upc_data as $upc => $data) {
				if ($data['price'] < $data['map']) {

					$gData['violoation']++;
				} else {
					$gData['non_violoation']++;
				}
			}
		}

		$gDataNew = array($gData['non_violoation'] . ' Non Violation' => $gData['non_violoation'], $gData['violoation'] . ' Violation' => $gData['violoation']);
		/* For Googl Graph */
		$googleDataArray = array();
		$googleDataArray[0] = array('State', 'Count');
		$googleDataArray[] = array('Non Violation', $gData['non_violoation']);
		$googleDataArray[] = array('Violation', $gData['violoation']);
		$gDataNew['googleData'] = $googleDataArray;
		$gDataNew['type'] = 'pie';
		return $gDataNew;
	}

	/**
	 *
	 * function whoIsSellingMyProductDefault
	 *
	 * @param array     $gData
	 *
	 *
	 */
	public static function whoIsSellingMyProductDefault($gData) {
		$series = $includedCols = $includedColors = array();
		foreach ($gData as $val) {
			$series[] = array(
				'name'      => marketplace_display_name($val['marketplace']),
				'data'      => array((int)$val['total_products']),
				'dashStyle' => 'Dot',
				'color'     => '#' . marketplace_graph_color($val['marketplace']),
				'id'        => 'merchant_count'
			);
			array_push($includedCols, $val['marketplace']);
			array_push($includedColors, '#' . marketplace_graph_color($val['marketplace']));
		}

		/* For Google Charts */
		$googleDataArray = array(
			array_merge(
				array('Date'),
				array_map(
					'marketplace_display_name',
					$includedCols
				)
			)
		);

		$maxValue = 0;
		$cat = array(date('Y-m-d'));
		foreach ($cat as $keyCat => $vCat) {
			$googleDataArray[$keyCat + 1][] = $vCat;
			foreach ($series as $seriesData) {
				$valueForDisplay = isset($seriesData['data'][$keyCat]) ? $seriesData['data'][$keyCat] : 0;
				$googleDataArray[$keyCat + 1][] = $valueForDisplay;
				if ($valueForDisplay > $maxValue) {
					$maxValue = $valueForDisplay;
				}
			}
		}

		$graph_data = array(
			'data' => $series,
			'y_title' => 'Products',
			'x_title' => 'Date',
			'cat' => $cat,
			'type' => 'column',
			'googleData' => $googleDataArray,
			'googleDataColors' => array_values($includedColors),
			'maxValue' => $maxValue
		);
		/* END FOR Google Charts */
		return $graph_data;
	}

	/**
	 *
	 * function formatWhoIsSellingMerchantdata
	 */
	public static function whoIsSellingMerchantFormatdata($data){

		$column = array();
		$column[] = "My date";
		$row = array();
		$row[] = date('Y-m-d');
		$max = 0;
		foreach ($data as $record) {
			$column[] =  $record['original_name'];
			$row[] = (int) $record['total_products'];

			if($max < $record['total_products'])
			{
				$max = $record['total_products'];
			}
		}
		$graph_data = array(
			'y_title' => 'Product Count',
			'x_title' => 'Date',
			'type' => 'column'
		);

		$graph_data['googleData'] = array($column, $row);
		$graph_data['maxValue'] = $max;

		return $graph_data;
	}

	/**
	 *
	 * function productViolationGraph
	 *
	 * @param <array>     $data
	 *
	 *
	 */
	public static function productViolationGraph($gData, $request_info) {
		$graph_data = $gData['violations'];
		$series = array();
		$i = 0;

		$cat = createDateRangeArray($request_info['fromDate'], $request_info['toDate']);

		$cat = array_unique($cat);

		$tempArray = array();
		sort($cat);
		foreach ($graph_data as $key => $dArr) {
			$priceD1 = array();
			$priceD2 = array();
			$priceD3 = array();
			$k = 1;
			foreach ($cat as $ckey => $cval) {
				if (!isset($dArr[$cval])) {
					unset($cat[$ckey]);
				} else {
					if (!in_array($cat[$ckey], $tempArray)) {
						$tempArray[] = $cat[$ckey];
					}

					$priceD1[] = (isset($dArr[$cval]['amazon'])) ? $dArr[$cval]['amazon'] : 0;
					$priceD2[] = (isset($dArr[$cval]['google'])) ? $dArr[$cval]['google'] : 0;
					$priceD3[] = (isset($dArr[$cval]['Shopping.com'])) ? $dArr[$cval]['Shopping.com'] : 0;
				}
			}

			if (in_array('all', $request_info['api_type'])) {
				$series[] = array('name' => getProductsTitle($key, 'Amazon'), 'data' => $priceD1, 'color' => Color_handler::get_next(0)->get_hex(), 'id' => $key);
				$series[] = array('name' => getProductsTitle($key, 'Google'), 'data' => $priceD2, 'color' => Color_handler::get_next(1)->get_hex(), 'id' => $key);
				$series[] = array('name' => getProductsTitle($key, 'Shopping.com'), 'data' => $priceD3, 'color' => Color_handler::get_next(2)->get_hex(), 'id' => $key);
			} else {
				if (in_array('Amazon', $request_info['api_type']) || in_array('amazon', $request_info['api_type']))
					$series[] = array('name' => getProductsTitle($key, 'Amazon'), 'data' => $priceD1, 'color' => Color_handler::get_next(0)->get_hex(), 'id' => $key);
				if (in_array('Google', $request_info['api_type']) || in_array('google', $request_info['api_type']))
					$series[] = array('name' => getProductsTitle($key, 'Google'), 'data' => $priceD2, 'color' => Color_handler::get_next(1)->get_hex(), 'id' => $key);
				if (in_array('Shopping.com', $request_info['api_type']) || in_array('shopping.com', $request_info['api_type']) || in_array('Shopping', $request_info['api_type']) || in_array('shopping', $request_info['api_type']))
					$series[] = array('name' => getProductsTitle($key, 'Shoping.com'), 'data' => $priceD3, 'color' => Color_handler::get_next(2)->get_hex(), 'id' => $key);
			}
			$i++;
		}

		$cat = $tempArray;
		foreach ($cat as $val) {
			$mcat[] = date('n/j', strtotime($val));
		}

		$cat = $mcat;

		if (count($cat) > 10) {
			foreach ($cat as $ind => &$val) {
				$val = ' ';
			}
		}

		$fArray = array(
			'data' => $series,
			'y_title' => 'Violations',
			'x_title' => 'Date',
			'cat' => $cat,
			'type' => 'column'
		);

		/* For Google Charts */
		$googledataColorArray = array();
		$googleDataArray = array();
		$googleDataArray[0][] = 'Date';
		$maxValue = 0;

		$prCount = count($graph_data);
		$keys = array_keys($graph_data);

		for ($prcounter = 0; $prcounter < $prCount; $prcounter++) {
			$color = Color_handler::get_next($prcounter)->get_hex();
			if (in_array('all', $request_info['api_type'])) {
				$googleDataArray[0][] = 'Amazon: ' . getProductsTitle($keys[$prcounter]);
				$googleDataArray[0][] = 'Google:' . getProductsTitle($keys[$prcounter]);
				$googleDataArray[0][] = 'Shopping.com:' . getProductsTitle($keys[$prcounter]);
				$googledataColorArray[] = $color;
				$googledataColorArray[] = $color;
				$googledataColorArray[] = $color;
			} else {
				if (in_array('Amazon', $request_info['api_type']) || in_array('amazon', $request_info['api_type'])) {
					$googleDataArray[0][] = 'Amazon: ' . getProductsTitle($keys[$prcounter]);
					$googledataColorArray[] = $color;
				}

				if (in_array('Google', $request_info['api_type']) || in_array('google', $request_info['api_type'])) {
					$googleDataArray[0][] = 'Google:' . getProductsTitle($keys[$prcounter]);
					$googledataColorArray[] = $color;
				}
				if (in_array('Shopping.com', $request_info['api_type']) || in_array('shopping.com', $request_info['api_type']) || in_array('Shopping', $request_info['api_type']) || in_array('shopping', $request_info['api_type'])) {
					$googleDataArray[0][] = 'Shopping.com:' . getProductsTitle($keys[$prcounter]);
					$googledataColorArray[] = $color;
				}
			}
		}

		foreach ($cat as $keyCat => $vCat) {
			$googleDataArray[$keyCat + 1][] = $vCat;
			foreach ($series as $seriesKey => $seriesData) {
				$valueForDisplay = isset($seriesData['data'][$keyCat]) ? $seriesData['data'][$keyCat] : 0;
				$googleDataArray[$keyCat + 1][] = $valueForDisplay;
				if ($valueForDisplay > $maxValue) {
					$maxValue = $valueForDisplay;
				}
			}
		}

		$fArray['googleData'] = $googleDataArray;
		$fArray['googleDataColors'] = $googledataColorArray;
		$fArray['maxValue'] = $maxValue;

		/* END FOR Google Charts */
		return $fArray;
	}

}