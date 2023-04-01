<?php
/**
 * @property AmazonDynamoDB $amazonDB
 */
class Amzdb
{
	var $My;
	var $amazonDB;
	var $queue;

	public $_dynamo_daily_price_average;
	public $_dynamo_products_trends;
	public $_dynamo_violations;

	function Amzdb(){
		$this->My=&get_instance();
		$this->My->load->library('session');
		include_once(dirname(BASEPATH).'/system/application/libraries/sdk.class.php');
		include_once(dirname(BASEPATH).'/system/application/libraries/config.inc.php');
		$this->amazonDB = new AmazonDynamoDB(array('credentials' => 'development'));
		$this->queue  = new CFBatchRequest();
		$this->queue->use_credentials($this->amazonDB->credentials);

		/************************************************************************
		  * Instantiate Implementation of MarketplaceWebService
		  *
		  * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants
		  * are defined in the .config.inc.php located in the same
		  * directory as this sample
		  ***********************************************************************/

		$this->load_tables();
	}

	function load_tables(){
		$this->My->config->load('db_tables');
		$db_tables = $this->My->config->item('db_tables');
		$db_tables = $db_tables[$db_tables['environment']];
		$dynamo_tables = $db_tables['dynamo'];

		// dynamo tables
		$this->_dynamo_daily_price_average = $dynamo_tables['daily_price_average'];
		$this->_dynamo_products_trends = $dynamo_tables['products_trends'];
		$this->_dynamo_violations = $dynamo_tables['violations'];
	}

	function describeTable($tableName){
		$describe_response = '';
		do {
			sleep(1);
			$describe_response = $this->amazonDB->describe_table(array('TableName' => $tableName));
		}
		while ((string) $describe_response->body->Table->TableStatus !== 'ACTIVE');

		return $describe_response;
	}

	function insertData($tableName,$dataArray,$marketplace=''){
		$ItemArray = array();
		$response = false;

		if(count($dataArray)){
			foreach($dataArray as $key=>$value){
				if($value == ''){
					$ItemArray[$key] = array(AmazonDynamoDB::TYPE_STRING=>'NULL');
				}else if($key =='dt' || $key =='ap' || $key =='mpo' || $key =='msp' || $key == 'price_total' || $key == 'seller_total'){
					$value  = preg_replace('/[^0-9\.]/','',strip_tags($value));
					$ItemArray[$key] = array(AmazonDynamoDB::TYPE_NUMBER=>"$value");
				}else{
					$ItemArray[$key] = array(AmazonDynamoDB::TYPE_STRING=>(string)$value);
				}
			}

			try{
				if(!is_object($this->amazonDB)){
					$this->resetObjects();
				}
				$response = $this->amazonDB->put_item(array('TableName' => $tableName,
															'Item' => $ItemArray));
				/*******/
				if(isset($response->body->__type)){
					$type = $response->body->__type;
					$typeExpl = explode('#',$type);
					if(isset($typeExpl[1]) && trim($typeExpl[1])=='ProvisionedThroughputExceededException'){
						insertErrorLog($response->body->__type,$response->body->message,'INSERT In DYNAMO DB Table Name'.$tableName." Marketplace ".$marketplace);
						sleep(3);
						$response = $this->amazonDB->put_item(array('TableName' => $tableName,
																	'Item' => $ItemArray));
					}else if(isset($typeExpl[1]) && trim($typeExpl[1])=='com.amazon.coral.service#ExpiredTokenException'){
							$this->resetObjects();
							$response = $this->amazonDB->put_item(array('TableName' => $tableName,
																		'Item' => $ItemArray));
					}else{
						insertErrorLog($response->body->__type,$response->body->message,'INSERT In DYNAMO DB Table Name'.$tableName." Marketplace ".$marketplace);
					}
				}
				/**********/
			}
			catch(Exception $e){
				insertErrorLog("Exception",$e->getMessage(),'INSERT In DYNAMO');
				echo "<b>Exception in INSERT</b> ".$e->getMessage();
			}
		}

		$this->resetObjects();
		//debug('Response in amzDB',$response,2);
		return $response;
	}

	function resetObjects(){
		unset($this->amazonDB);
		unset($this->queue);
		$this->amazonDB = new AmazonDynamoDB(array('credentials' => 'development'));
		$this->queue  = new CFBatchRequest();
		$this->queue->use_credentials($this->amazonDB->credentials);
	}

	function scanTableViolations(){
		$violtionArrayByMerchant = array();
		$scan_response = $this->amazonDB->scan(array('TableName' => 'live_violations',
													'ScanFilter' => array('is_emailsend' => array('ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
																									'AttributeValueList' => array(array(AmazonDynamoDB::TYPE_STRING => '0'))
																								)
																		)
													)
											);
		$count = $scan_response->body->Count;
		if($count > 0){
			for($i=0;$i<$count;$i++){
				$violatedItems = $scan_response->body->Items->{$i};
				$merchant_id = (string)$violatedItems->merchant_id->S;
				$alarm_price = (string)$violatedItems->alarm_price->S;
				$violated_price= (string)$violatedItems->merchant_price->S;
				$id = (string)$violatedItems->id->S;
				$violtionArrayByMerchant["$merchant_id"][] = array('id' => $id,
																	'merchant_id' => $merchant_id,
																	'Alarm Price' => $alarm_price,
																	'Merchant Violated Price' => $violated_price);
			}
		}

		return $violtionArrayByMerchant;
	}

	function insertBatch($tableName,$dataArray){
		if(count($dataArray)){
			foreach($dataArray as $key=>$value){
				if($value == ''){
					$ItemArray[$key] = array(AmazonDynamoDB::TYPE_STRING=>'NULL');
				}else if($key =='dt' || $key =='ap' || $key =='mpo' || $key =='msp'){
						$ItemArray[$key] = array(AmazonDynamoDB::TYPE_NUMBER=>$value);
				}else{
					$ItemArray[$key] = array(AmazonDynamoDB::TYPE_STRING=>(string)$value);
				}
			}

			$this->amazonDB->batch($this->queue)->put_item(array('TableName' => $tableName,
																 'Item' 	 => $ItemArray));
		}
	}

	function insertGetBatchQuery($tableName,$hk,$list){
		$this->amazonDB->batch($this->queue)->query(
			array(  'TableName' => $tableName,
					'HashKeyValue' 		=> array( AmazonDynamoDB::TYPE_STRING => $hk),
					'RangeKeyCondition' => array('ComparisonOperator' => AmazonDynamoDB::CONDITION_BETWEEN,
												 'AttributeValueList' => $list
										   )
			)
		);
	}

	function getQueryViolation($tableName,$hk,$list){
		$compOp = sizeof($list) == 1 ? AmazonDynamoDB::CONDITION_EQUAL: AmazonDynamoDB::CONDITION_BETWEEN;

log_message('info', 'amzdb->getQueryViolation query: '.print_r(array('TableName' => $tableName,
																'HashKeyValue' => array(AmazonDynamoDB::TYPE_STRING => $hk),
																'RangeKeyCondition' => array('ComparisonOperator' => $compOp,
																							'AttributeValueList' => $list)
														), true)
);

		$response = $this->amazonDB->query(array('TableName' => $tableName,
												'HashKeyValue' => array(AmazonDynamoDB::TYPE_STRING => $hk),
												'RangeKeyCondition' => array('ComparisonOperator' => $compOp,
																			'AttributeValueList' => $list)
											)
									);

log_message('info', 'amzdb->getQueryViolation response: '.print_r($response,true));

		if(isset($response->body->__type)){
			$type = $response->body->__type;
			$typeExpl = explode('#',$type);
			if(isset($typeExpl[1]) && trim($typeExpl[1])=='ProvisionedThroughputExceededException'){
				insertErrorLog($response->body->__type,$response->body->message,'SELECT In DYNAMO DB Table Name'.$tableName." HashKey ".$hk);
			}else if(isset($typeExpl[1]) && trim($typeExpl[1])=='com.amazon.coral.service#ExpiredTokenException'){
				$this->resetObjects();
				$response = $this->amazonDB->query(array('TableName' => $tableName,
														'HashKeyValue' => array(AmazonDynamoDB::TYPE_STRING => $hk),
														'RangeKeyCondition' => array('ComparisonOperator' => AmazonDynamoDB::CONDITION_BETWEEN,
																					'AttributeValueList' => $list)
													)
											);
				insertErrorLog($response->body->__type,$response->body->message,'SELECT In DYNAMO DB Table Name'.$tableName." HashKey ".$hk);
			}else{
				insertErrorLog($response->body->__type,$response->body->message,'SELECT In DYNAMO DB Table Name'.$tableName." HashKey ".$hk);
			}
		}
log_message('info', "Dynamo conversion, output array of this function:
    File: ".__FILE__."
    Line: ".__LINE__."
    Class: ".__CLASS__."
    Function: ".__FUNCTION__."
    Method: ".__METHOD__."
    " . print_r($response , true)
);

		return $response;
	}

	function sendBatchPutRequest(){
		$responses = $this->amazonDB->batch($this->queue)->send();
		unset($this->amazonDB);
		unset($this->queue);
		$this->amazonDB = new AmazonDynamoDB(array('credentials' => 'development'));
		$this->queue  = new CFBatchRequest();
		$this->queue->use_credentials($this->amazonDB->credentials);

		return $responses;
	}

	function updateisEmailSendItem($value){
		$upArray = array('TableName' => 'live_violations',
						 'Key' => array('HashKeyElement' => array(AmazonDynamoDB::TYPE_STRING =>(string)$value)),
									   'AttributeUpdates' => array('is_emailsend' => array('Action' => AmazonDynamoDB::ACTION_PUT,
																							'Value' => array(AmazonDynamoDB::TYPE_STRING => '1'
																						)
															)
								)
						);

		$update_response = $this->amazonDB->update_item($upArray);
	}

	function updateHashKey($table, $hash, $new_hash){
		$upArray = array('TableName' => (string)$table,
						'Key' 				=> array('HashKeyElement' => array(AmazonDynamoDB::TYPE_STRING => $hash)),
						'AttributeUpdates'	=> array('upc_code' => array('Action'	=> AmazonDynamoDB::ACTION_PUT,
																		 'Value'	=> array(AmazonDynamoDB::TYPE_STRING => $new_hash)
													)
								)
						);

		$update_response = $this->amazonDB->update_item($upArray);
		p_array($upArray);
		p_array($update_response);

	}

	function deleteItem($table_name, $hash_key, $range){
		$opt = array('TableName' => $table_name,
					'Key' => array('HashKeyElement' => array(AmazonDynamoDB::TYPE_STRING => $hash_key),
					'RangeKeyElement' => array(AmazonDynamoDB::TYPE_STRING => $range)
					)
				);
		return $this->amazonDB->delete_item($opt);
	}

	function scanTableScheduleViolationReport($filters){
		$violationFinaldata = array();
		$scan_response = $this->amazonDB->scan(array('TableName' => 'live_violations',
																								'ScanFilter' => $filters));
		$count = $scan_response->body->Count;

		if($count > 0){
			for($i=0;$i<$count;$i++){
				$violatedItems = $scan_response->body->Items->{$i};
				$merchant_id = (string)$violatedItems->merchant_id->S;
				$alarm_price = (string)$violatedItems->alarm_price->S;
				$violated_price= (string)$violatedItems->merchant_price->S;
				$id = (string)$violatedItems->id->S;
				$product_id = (string)$violatedItems->product_id->S;
				$product_trends = (string)$violatedItems->product_trends_id->S;

				$response = $this->amazonDB->get_item(array('TableName' => 'live_products_trends',
															'Key' => array('HashKeyElement' => array(AmazonDynamoDB::TYPE_STRING => (string)$product_trends))
															)
													);

				$trendsData  = $response->body->Item->{0};
				$merchantURL = (string)$trendsData->merchant_url->S;
				$imageLink = (string)$trendsData->image_link->S;
				$title = (string)$trendsData->title->S;
				$author = (string)$trendsData->author->S;

				$violationFinaldata[] = array('product_id'  =>  $product_id,
											'alarm_price' =>  $alarm_price,
											'merchant_url'=>  $merchantURL,
											'image_link'  =>  $imageLink,
											'merchant_price_offered' => $violated_price,
											'merchant_id' => $merchant_id,
											'title' => $title,
											'author' => $author,
											'link' => $merchantURL);
			}
		}

		return $violationFinaldata;
	}

	function scanTableSchedulePriceTrendReport($filters){
		$priceTrendArray = array();
		$finalArray = array();
		$scan_response = $this->amazonDB->scan(array('TableName' => 'live_products_trends',
																								'ScanFilter' => $filters));

		$countRes = $scan_response->body->Count;
		if($countRes > 0){
			for($i=0;$i<$countRes;$i++){
				$priceTrends = $scan_response->body->Items->{$i};
				$tVal =(string)$priceTrends->datetime_tracked->S;
				if(array_key_exists($tVal,$priceTrendArray)){
					$price = (float)$priceTrendArray[$tVal]['price']+(float)$priceTrends->merchant_price_offered->S;
					$count = (int)$priceTrendArray[$tVal]['count']+1;
					$priceTrendArray[(string)$priceTrends->datetime_tracked->S]['price'] = $price;
					$priceTrendArray[(string)$priceTrends->datetime_tracked->S]['count'] = $count;
				}else{
					$price = (string)$priceTrends->merchant_price_offered->S;
					$count = 1;
					$priceTrendArray[$tVal] = array('price' => $price,
																					'count'=>1);
				}
			}

			if(count($priceTrendArray) > 0){
				foreach($priceTrendArray as $key=>$data){
					$finalArray[] = array('datetime_tracked' => date('Y-m-d',$key),
																'average' => number_format(($data['price']/$data['count']),2));
				}
			}
		}

		return $finalArray;
	}

	function scanTableScheduleMerchantReport($filters){
		$priceTrendArray = $finalArray = array();
		$scan_response = $this->amazonDB->scan(array('TableName' => 'live_products_trends',
																								'ScanFilter' => $filters));

		$countRes = $scan_response->body->Count;
		if($countRes > 0){
			for($i=0;$i<$countRes;$i++){
				$priceTrends = $scan_response->body->Items->{$i};

				if(array_key_exists((string)$priceTrends->merchant_id->S,$priceTrendArray)){
					$price = (float)$priceTrendArray[(string)$priceTrends->merchant_id->S]['price']+(float)$priceTrends->merchant_price_offered->S;
					$count = (int)$priceTrendArray[(string)$priceTrends->merchant_id->S]['count']+1;
					$priceTrendArray[(string)$priceTrends->merchant_id->S]['price'] = $price;
					$priceTrendArray[(string)$priceTrends->merchant_id->S]['count'] = $count;
				}else{
					$price = (float)$priceTrends->merchant_price_offered->S;
					$count = 1;
					$priceTrendArray[(string)$priceTrends->merchant_id->S] = array('price' => $price,
																					'count' => 1,
																					'datetime_tracked' => date('Y-m-d',(string)$priceTrends->datetime_tracked->S),
																					'author' => (string)$priceTrends->author->S,
																					'title' => (string)$priceTrends->title->S,
																					'merchant_url' => (string)$priceTrends->merchant_url->S,
																					'image_link' => (string)$priceTrends->image_link->S);
				}
			}

			if(count($priceTrendArray) > 0){
				foreach($priceTrendArray as $key=>$data){
					$finalArray[] = array('datetime_tracked' => $data['datetime_tracked'],
										'average' => number_format(($data['price']/$data['count']),2),
										'merchant_id' => $key,
										'author' => $data['author'],
										'title' => $data['title'],
										'merchant_url' => $data['merchant_url'],
										'image_link' => $data['image_link']);
				}
			}
		}

		return $finalArray;
	}

	function scanTableForGoogleMerchant(){

		$filters['api_reference'] = array('ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
											'AttributeValueList' => array(array(AmazonDynamoDB::TYPE_STRING => 'google')));

		$filters['merchant_image_url'] = array('ComparisonOperator' => AmazonDynamoDB::CONDITION__EQUAL,
												'AttributeValueList' => array(array(AmazonDynamoDB::TYPE_STRING => NULL)));

		$scan_response = $this->amazonDB->scan(array('TableName' => 'live_products_trends',
													'ScanFilter' => $filters));

		return $scan_response;
	}

	function saveURL($id,$url){
		$upArray = array('TableName' => $this->_dynamo_products_trends,
						'Key' => array('HashKeyElement' => array(AmazonDynamoDB::TYPE_STRING =>(string)$id)),
						'AttributeUpdates' => array('mil' => array('Action' => AmazonDynamoDB::ACTION_PUT,
						'Value' => array(AmazonDynamoDB::TYPE_STRING => $url)))
						);

		$update_response = $this->amazonDB->update_item($upArray);
	}

	function scanTableByFilters($tableName,$filters){
		$scan_response = $this->amazonDB->scan(array('TableName' => $tableName,
													'ScanFilter' => $filters));

		return $scan_response;
	}

	function findRecord($tableName, $hashKey, $range = ''){
		$record = $this->amazonDB->get_item(array('TableName' => $tableName,
													'Key' => $this->amazonDB->attributes(array('HashKeyElement' => $hashKey,
																								'RangeKeyElement' => $range))));

		return $record;
	}

	function executeQuery($tableName,$hashKey,$range=''){
		$response = $this->amazonDB->query(array('TableName' => $tableName,
												'HashKeyValue' => array(AmazonDynamoDB::TYPE_STRING => $hashKey,
																								$range),
												'ScanIndexForward' => false,
												'Limit' => 1
												));
		return $response;
	}

	function batchGetItem($table, $dataArray){

		if(!is_array($dataArray)) return false;

		$finalItems = array();

		$dataSize = sizeof($dataArray);
		$bcount = $i = 0;

		$items = array();
		for ($a=0; $a < $dataSize; $a++) {
			$items[] = $this->amazonDB->attributes(array(
				'HashKeyElement' => $dataArray[$a]['HashKeyElement'],
				'RangeKeyElement' => (int)$dataArray[$a]['RangeKeyElement']
			));

			if (($a+1) == $dataSize OR sizeof($items) == 100) {
				$hasMoreItems = false;
				do {
					//die("sending request with " . sizeof($items) . " keys");

log_message('info', 'amzdb->batchGetItem request: '.print_r(array('RequestItems' => array($table => array('Keys' => $items))),true));

					$response = $this->amazonDB->batch_get_item(array('RequestItems' => array($table => array('Keys' => $items))));

log_message('info', 'amzdb->batchGetItem response: '.print_r($response,true));

					$items = array();
					if($response->status == 200){
						//var_dump($response->header['x-aws-body']);echo "<br>\n<br>\n";//exit;
						for($i=0, $n=sizeof($response->body->Responses->{$table}->Items); $i<$n; $i++){
							array_push($finalItems, $response->body->Responses->{$table}->Items[$i]);
						}
						if(isset($response->body->UnprocessedKeys->{$table}) && count($response->body->UnprocessedKeys->{$table}->Keys) > 0){
							for($y=0, $z=sizeof($response->body->UnprocessedKeys->{$table}->Keys); $y<$z; $y++){
								$ind = $response->body->UnprocessedKeys->{$table}->Keys[$y];
								$items[] = $this->amazonDB->attributes(array('HashKeyElement' => $ind->HashKeyElement->S,
																			'RangeKeyElement' => (int)$ind->RangeKeyElement->N));
							}
							$hasMoreItems = true;
						}else{
							$hasMoreItems = false;
						}
					}else{
						$hasMoreItems = false;
					}
				}
				while ($hasMoreItems === true);
			}
		}

		//var_dump($finalItems);//exit;
log_message('info', "Dynamo conversion, output array of this function:
    File: ".__FILE__."
    Line: ".__LINE__."
    Class: ".__CLASS__."
    Function: ".__FUNCTION__."
    Method: ".__METHOD__."
    " . print_r($finalItems , true)
);
		return $finalItems;
	}

	function batchRequest($table, $batchArray = array()){
		//batch the hash key with the dt's
		$batchData = $vArray = array();
		foreach($batchArray as $hash=>$vData){
			for($i=0, $n=sizeof($vData); $i<$n; $i++){
				$curItem = array('um' => array(AmazonDynamoDB::TYPE_STRING => $hash),
												'dt' => array(AmazonDynamoDB::TYPE_NUMBER => $vData[$i]['dt']));
				array_push($vArray, $curItem);
				if($i==10)break;
			}
		}

		$batchData[$table] = array('Keys' => $vArray);
		//echo "sending batch<br>\n";
		var_dump($batchData);//exit;

		$response = $this->amazonDB->batch_get_item(array('RequestItems' => array(array($batchData))));
		/*$table_name => array('Keys' => array($dynamodb->attributes(array(
																		'HashKeyElement'  => 1,
																		'RangeKeyElement' => $current_time)),
																		$dynamodb->attributes(array(
																		'HashKeyElement'  => 2,
																	'RangeKeyElement' => $current_time))),
													                    				))*/
		echo "batch<br>\n";var_dump($response);exit;
	}


	function getQueryWithoutRange($tableName,$hk){
		$response = $this->amazonDB->query(array('TableName' => $tableName,
												'HashKeyValue' => array(AmazonDynamoDB::TYPE_STRING => $hk)
												)
									);
		return $response;
	}

	function insertError($response,$method=''){
		if(isset($response->body->__type)){
			$type = $response->body->__type;
			$typeExpl = explode('#',$type);
			insertErrorLog($response->body->__type,$response->body->message,$method);
		}
	}

	function getDailyAverage($tableName,$hk,$list){
		$response = $this->amazonDB->query(array('TableName' => $tableName,
				'HashKeyValue' => array(AmazonDynamoDB::TYPE_STRING => $hk),
				'RangeKeyCondition' => array('ComparisonOperator' => AmazonDynamoDB::CONDITION_BETWEEN,
											'AttributeValueList' => $list),
															)
												);
		return $response;
	}

	// DEPRECATED
	//function varifyGetBatchItems($ar){
	//	$batch_get_response = $this->amazonDB->batch_get_item(
	//		array('RequestItems' => array($this->_dynamo_products_trends => array('Keys' => $ar)))
	//	);
	//	return $batch_get_response;
	//}
	
	// DEPRECATED
	//function getQueryPriceTrend($tableName,$hk,$list){
	//	$response = $this->amazonDB->query(array('TableName' => $tableName,
	//											'HashKeyValue' => array(AmazonDynamoDB::TYPE_STRING => $hk),
	//											'RangeKeyCondition' => array('ComparisonOperator' => AmazonDynamoDB::CONDITION_LESS_THAN_OR_EQUAL,
	//																		'AttributeValueList' => $list)
	//											)
	//										);
	//	return $response;
	//}
}
?>