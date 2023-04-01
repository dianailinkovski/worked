<?php
// concrete implementation of nosql class
class nosqlMysql{
	
	function _construct(){
		log_message('debug', 'nosqlMysql Class Initialized');
		
	}
	// should we extend CI?
	
	function _query($tableName, $hashKey, $range=''){
		$res = $this->db
			->select('um')
			->where(array('um' => $hashKey,
						  'dt' => $stamp))
			->limit(1)
			->get($this->_dynamo_products_trends)
			->result_array();
	}
	
	function getQueryWithoutRange($tableName, $hashKey){
		return $this->query($tableName,$hashKey);
	}
	
	function getDailyAverage($tableName, $key, $filters){
	}

	function insertData($tableName,$dataArray,$marketplace=''){
		if(count($dataArray)){
		}
		else{
			// error?
			return false;
		}
	}

	function batchGetItem($table, $dataArray){
		if(count($dataArray)){
		}
		else{
			// error?
			return false;
		}
	}

	function getQueryViolation($tableName, $hashKey, $list){
	}

	function executeQuery($tableName,$hashKey,$range=''){
	}
	
	//function scanTableByFilters($tableName, $filters){
	//}

}
?>