<?php
/**
 * NoSqlDb Interface class
 * Provides a clean application programming interface to any nosql adapter
 * Add any new nosql adapter to the __construct() switch.
 */

// TODO:  find all stray DynamoDb instances in the app and move them into amzdb.php.
//		This has to be done first, because function params will change, and new functions created.
// TODO:  eliminate ->S and ->N from the app code
// TODO:  convert all "->amzdb->" strings in app to "->nosql->"
// TODO:  get dynamo running in svn-new
// TODO:  autoload all, some?
// TODO:  write nosqlMysql
// TODO:  sanity-test incoming and outgoing data are formatted properly, or at least document it in this interface
// TODO:  
// TODO:  
// TODO:  
class noSqlDb extends MY_Model{
	private $_nosql;
	public static $tableName = 'products_trends_new';
	
	function _construct(){
		parent::MY_Model();
		
		log_message('debug', 'noSqlDb Class Initialized');
		
		//select which nosql adapter to use
		switch($this->config->item('nosql_db')){
			case 'dynamodb':
				$this->_nosql = $this->ci->load->library('amzdb');
				break;
			case 'mysql':
			default:
				$this->_nosql = $this->ci->load->library('nosqlMysql');
				break;
		}
	}
	// should we extend CI?
	
	// pass-thru functions, "interface"
	function insertData($tableName, $dataArray, $marketplace=''){
		return $this->_nosql->insertData($tableName, $dataArray, $marketplace);
	}

	function batchGetItem($table, $dataArray){
		if(!is_array($dataArray)) return false;
		return $this->_nosql->batchGetItem($table, $dataArray);
	}

	function executeQuery($tableName,$hashKey,$range=''){
		return $this->_nosql->executeQuery($tableName,$hashKey,$range);
	}
	
	function query($tableName,$hashKey,$range=''){
		return $this->executeQuery($tableName,$hashKey,$range);
	}
	
	function getQueryWithoutRange($tableName, $hashKey){
		return $this->executeQuery($tableName,$hashKey);
	}
	
	
	// these two functions belong in their own models
	function getDailyAverage($tableName, $key, $filters){
		return $this->_nosql->getDailyAverage($tableName, $key, $filters);
	}
	function getQueryViolation($tableName, $hashKey, $list){
		return $this->_nosql->getQueryViolation($tableName, $hashKey, $list);
	}

	// this function is called by only Report_m->merchant_products(), and it seems to be deprecated
	//function scanTableByFilters($tableName, $filters){
	//	return $this->_nosql->scanTableByFilters($tableName, $filters);
	//}

}
?>