<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Chart_m extends MY_Model
{
	/**
	 * Pre process the data to use in google charts
	 *
	 * @param array $data
	 * @param array $report_where
	 * @param String $chartType
	 * @return array
	 */
	public function prepGoogleData($data, $report_where, $chartType){
		
		if(empty($data)){
			$data = array();
		}
		
		$store_id = get_instance()->store_id;

		//define chart type, colors come from global config
		//use productId as indexes for data=>result keys
		$googleData = array(
			'type' => '',
			'date' => array('start' => time(),
			'end' => time()),
			'data' => array('size' => 0)
		);

		$marketColors = $this->config->item('market_colors');

		$prepData = array();
		switch($chartType){
			case 'scatter':
				/* example price array - only take what we need for the google data
		array('price' => '17.59', 'retail' => '30.85', 'wholesale' => '17.56', 'map' => '21.6', 'count' => '1', 'upc' => '658010113403', 'title' => 'GARDEN OF LIFE 100% Organic Extra Virgin Coconut Oil 32 fl.oz', 'marketplace' => 'Shopping.com', 'url' => 'http://www.allstarhealth.com/de_p_ref/21115/DT21115/GARDEN_OF_LIFE_100Percent_Organic_Extra_Virgin_Coconut_Oil.htm', 'dt' => '1340074876', 'prod_id' => '391', 'timestamp' => '1340074876', 'hash_key' => 'AllStarHealth#658010113403', 'merchant' => 'AllStarHealth', 'original_name' => 'AllStarHealth', 'merchant_id' => '9183', 'date' => '06/18/2012 20:01:16')*/

				//scatterchart
				$c = 0;
				$googleData['type'] = 'scatter';
				//we can't have the start & end be the same for the scatter chart
				$googleData['date']['start'] = $report_where['date_from'];
				$googleData['date']['end'] = ($report_where['date_to'] == $report_where['date_from']) ? $report_where['date_from']+86400: $report_where['date_to'];
				$googleData['date']['earliest'] = time();
				$googleData['data']['size'] = sizeof($data);
				$num_outliers = 0;
				foreach($data as $prodId=>$prodData){

					// create the prepData and calculate average on pass 1
					$avg = 0;
					for($i=0, $n=sizeof($prodData); $i<$n; $i++){
						$price = (float)$prodData[$i]['price'];
						$map = (float)$prodData[$i]['map'];
						$violation = ($price < $map);
						$avg += $price;

						$prepData[$prodId][] = array(
							'price' => $price,
							'map' => $map,
							'marketplace' => $prodData[$i]['marketplace'],
							'merchant' => getMerchantName($prodData[$i]['merchant_id']),
							'merchant_id' => $prodData[$i]['merchant_id'],
							'timestamp' => $prodData[$i]['timestamp'],
							'violation' => $violation
						);
						//reset earliest date if necessary
						if($prodData[$i]['timestamp']<$googleData['date']['earliest']){
							$googleData['date']['earliest'] = $prodData[$i]['timestamp'];
						}
					}

					// Calculate standard deviation on pass 2
					$avg = $n > 0 ? round($avg/$n, 2) : 0;
					$sd = 0;
					for ($i=0; $i<$n; $i++){
						$sd += pow($prepData[$prodId][$i]['price'] - $avg, 2);
					}
					$sd = $n > 0 ? round(sqrt($sd/$n), 2) : round(sqrt($sd), 2);

					// find outliers on pass 3
					$thresh = 3*$sd;
					$outliers = array();
					for ($i=0; $i<$n; $i++){
						if (abs($prepData[$prodId][$i]['price'] - $avg) > $thresh) {
							$outliers[] = $i;
							$num_outliers++;
						}
					}

					// build data column
					$productInfo = getProductUPCByID($prodId, $this->ci->store_id);
					$mapPrice = getPricingHistory($productInfo['upc_code'], $this->ci->store_id, 'price_floor', $googleData['date']['start'], $googleData['date']['end']);
					$retailPrice = getPricingHistory($productInfo['upc_code'], $this->ci->store_id, 'retail_price', $googleData['date']['start'], $googleData['date']['end']);
					$wholeSalePrice = getPricingHistory($productInfo['upc_code'], $this->ci->store_id, 'wholesale_price', $googleData['date']['start'], $googleData['date']['end']);
					$Color = Color_handler::get_next();
					$color_info = array('hex' => $Color->get_hex(), 'string' => $Color->get_string());
					$this->ci->data->colors[] = $color_info;

					$googleData['data']['columns'][$prodId] = array(
						'type' => 'number',
						'color' => $color_info,
						'name' => getProductsTitle($prodId),
						'pricing' => array(
							'map' => $mapPrice,
							'retail' => $retailPrice,
							'wholesale' => $wholeSalePrice
						),
						'stats' => array(
							'avg' => $avg,
							'sd' => $sd,
							'thresh' => $thresh,
							'outliers' => $outliers,
						)
					);

					$c++;
				}
				$googleData['data']['outliers'] = $num_outliers;
				$googleData['data']['result'] = $prepData;
				break;

		case 'line':
		default:
			/* example price array - only take what we need for the google data
				array(391 => array(0 => array("marketplace" => "amazon", "upc" => "658010113403", "price" => "30.20", "wholesale" => array(0 => array("start" => "0000-00-00 00:00:00", "stamp" => 1340631116, "price" => 17.56)), "retail" => array(0 => array("start" => "0000-00-00 00:00:00", "stamp" => 1340631116, "price" => 30.85)), "map" => array(0 => array("start" => "0000-00-00 00:00:00", "stamp" => 1340631116, "price" => 21.6)), "dt" => 1340089200, "prod_id" => "391"))) */

			$googleData['type'] = 'line';
			$googleData['date']['start'] = $report_where['date_from'];
			$googleData['date']['end'] = $report_where['date_to'];

			$size = $c = 0;

			foreach ($data as $prodId=>$data){
				$marketColorArray = array();
				$upc = getProductsUPC($prodId);
				$marketColorArray[$c] = array();
				$Color = Color_handler::get_next();
				$color_info = array('hex' => $Color->get_hex(), 'string' => $Color->get_string());
				$this->ci->data->colors[] = $color_info;

				$googleData['data']['columns'][$prodId] = array(
					'type' => 'number',
					'color' => $color_info,
					'name' => getProductsTitle($prodId),
					'pricing' => array(
						'map' => getPricingHistory($upc, $store_id, 'price_floor', $report_where['date_from'], $report_where['date_to']),
						'retail' => getPricingHistory($upc, $store_id, 'retail_price', $report_where['date_from'], $report_where['date_to']),
						'wholesale' => getPricingHistory($upc, $store_id, 'wholesale_price', $report_where['date_from'], $report_where['date_to'])
					)
				);

				foreach($data as $market=>$pricing){
					$size++;
					$marketColorArray['market'][$market] = isset($marketColors[$market]) ? $marketColors[$market] : stringToColorCode($market);
					foreach($pricing as $priceArr){
						$prepData[$prodId][$market][] = array(
							'upc' => $priceArr['upc'],
							'price' => (float)$priceArr['price'],
							'dt' => $priceArr['dt'],
							'date' => date('Y-m-d', $priceArr['dt'])
						);
					}
				}
				array_push($googleData['data']['columns'][$prodId]['color'], $marketColorArray['market']);
				$c++;
			}
			$googleData['data']['size'] = $size;
			$googleData['data']['result'] = $prepData;
			break;
		}

		
		if ( $googleData['data']['size'] == 0 ) {
			$googleData['type'] = "empty";
		}
		
		//echo "googs<br>\n";
		//var_dump($googleData);exit;
		return $googleData;
	}
}
