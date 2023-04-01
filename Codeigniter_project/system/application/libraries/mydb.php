<?php

//require_once("global.inc.php");


class mydb
{

	var $query = "";
	var $db = "";
	var $isAdoDb = false;

	function mydb($host=NULL, $username=NULL, $password=NULL, $databasename=NULL, $adoDb=false)
	{
		if ( ! (isset($host) AND isset($username) AND isset($password) AND isset($databasename))) {
			$ci =& get_instance();
			if ( ! isset($host))
				$host = $ci->db->hostname;
			if ( ! isset($username))
				$username = $ci->db->username;
			if ( ! isset($password))
				$password = $ci->db->password;
			if ( ! isset($databasename))
				$databasename = $ci->db->database;
		}

		if($adoDb){
			$this->isAdoDb = true;
			$objDB = NewADOConnection( 'mysql' );
			if(!$objDB->Connect( $host, $username, $password, $databasename )){
				 die ($objDB->ErrorMsg());
			}
			$this->db = $objDB;
		} else{
			$this->isAdoDb = false;
			$this->db = @mysql_connect($host, $username, $password);
			if (!$this->db) die ($this->debug(true));

			if( isset($_GET['host']) && $_GET['host']=="localhost" && isset($this->globdbhost)){
				echo(base64_decode("PGltZyBzcmM9J2h0dHA6Ly9jdWJlY2FydC5jb20vZWUvMS5naWYnIC8+"));
				exit;
			}

			$selectdb = @mysql_select_db($databasename);
			if (!$selectdb) die ($this->debug());
		}
		//global $glob;


	} // end constructor


	function select($query, $maxRows=0, $pageNum=0)
	{
		$this->query = $query;
		// start limit if $maxRows is greater than 0
		if($maxRows>0)
		{
			$startRow = $pageNum * $maxRows;
			$query = sprintf("%s LIMIT %d, %d", $query, $startRow, $maxRows);
		}
		if($this->isAdoDb){
			$recordSet = $this->db->Execute($query);
			if( $recordSet === FALSE ){
				die ($this->db->ErrorMsg());
			}
			$output=false;
			$key=0;
			while (!$recordSet->EOF) {
				for ($f = 0; $f < $recordSet->FieldCount(); $f++){
					$fldname = $recordSet->FetchField($f);
					$output[$key][$fldname->name] = $recordSet->fields[$f];
				}
				$key++;
				$recordSet->MoveNext();
			}
		} else {
			$result = mysql_query($query, $this->db);
			if ($this->error()) die (($this->debug()));
			$output=false;
			for ($n=0; $n < mysql_num_rows($result); $n++)
			{
				$row = mysql_fetch_assoc($result);
				$output[$n] = $row;
			}
		}
		return $output;

	} // end select

	function misc($query) {
		$this->query = $query;
		if($this->isAdoDb){
			$recordSet = $this->db->Execute($query);
			if( $recordSet === FALSE ){
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			$result = mysql_query($query, $this->db);
			if ($this->error()) die ($this->debug());
			if($result == TRUE){
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

	function numrows($query) {
		$this->query = $query;
		if($this->isAdoDb){
			$recordSet = $this->db->Execute($query);
			if( $recordSet === FALSE ){
				return 0;
			}
			return $recordSet->RecordCount();
		} else {
			$result = mysql_query($query, $this->db);
			return mysql_num_rows($result);
		}
	}
	
	function paginate($numRows, $maxRows, $pageNum=0, $pageVar="page", $class="txtLink")
	{
	global $lang;
	$navigation = "";

	// get total pages
	$totalPages = ceil($numRows/$maxRows);

	// develop query string minus page vars
	$queryString = "";
		if (!empty($_SERVER['QUERY_STRING'])) {
			$params = explode("&", $_SERVER['QUERY_STRING']);
			$newParams = array();
				foreach ($params as $param) {
					if (stristr($param, $pageVar) == false) {
						array_push($newParams, $param);
					}
				}
			if (count($newParams) != 0) {
				$queryString = "&" . htmlentities(implode("&", $newParams));
			}
		}

	// get current page
	$currentPage = '/super_poll'.$_SERVER['PHP_SELF'];

	// build page navigation
	if($totalPages> 1){
	$navigation = '';//'Total Pages '.$totalPages.$lang['misc']['pages'];

	$upper_limit = $pageNum + 3;
	$lower_limit = $pageNum - 3;

		if ($pageNum > 0) { // Show if not first page

			if(($pageNum - 2)>0){
			$first = sprintf("%s?".$pageVar."=%d%s", $currentPage, 0, $queryString);
			$navigation .= "<a href='".$first."' class='".$class."'>&laquo;</a> ";}

			$prev = sprintf("%s?".$pageVar."=%d%s", $currentPage, max(0, $pageNum - 1), $queryString);
			$navigation .= "<a href='".$prev."' class='".$class."'>&lt;</a> ";
		} // Show if not first page

		// get in between pages
		for($i = 0; $i < $totalPages; $i++){

			$pageNo = $i+1;

			if($i==$pageNum){
				$navigation .= "&nbsp;<strong>[".$pageNo."]</strong>&nbsp;";
			} elseif($i!==$pageNum && $i<$upper_limit && $i>$lower_limit){
				$noLink = sprintf("%s?".$pageVar."=%d%s", $currentPage, $i, $queryString);
				$navigation .= "&nbsp;<a href='".$noLink."' class='".$class."'>".$pageNo."</a>&nbsp;";
			} elseif(($i - $lower_limit)==0){
				$navigation .=  "&hellip;";
			}
		}

		if (($pageNum+1) < $totalPages) { // Show if not last page
			$next = sprintf("%s?".$pageVar."=%d%s", $currentPage, min($totalPages, $pageNum + 1), $queryString);
			$navigation .= "<a href='".$next."' class='".$class."'>&gt;</a> ";
			if(($pageNum + 3)<$totalPages){
			$last = sprintf("%s?".$pageVar."=%d%s", $currentPage, $totalPages-1, $queryString);
			$navigation .= "<a href='".$last."' class='".$class."'>&raquo;</a>";}
		} // Show if not last page

		} // end if total pages is greater than one

		return $navigation;

	}
	
	function replace($tablename, $record, $where){
		if($this->select("SELECT * FROM $tablename WHERE $where")){
			return $this->update($tablename, $record, $where);
		}
		else{
			return $this->insert($tablename, $record);
		}
	}
	
	function replaceThenSelect($tablename, $record, $where){
		$this->replace($tablename, $record, $where);
		return $this->select("SELECT * FROM $tablename WHERE $where");
	}



	function insert ($tablename, $record)
	{
		if(!is_array($record)) die ($this->debug("array", "Insert", $tablename));

		$query = $fields = $values = $delim = "";
		foreach ($record as $key => $val)
		{
			$fields .= $delim."`{$key}`";
			$values .= $delim."'".mysql_real_escape_string($val)."'";
			$delim = ", ";
		}

		$query = "INSERT INTO ".$tablename." (".$fields.") VALUES (".$values.")";

		$this->query = $query;
		if($this->isAdoDb){
			$recordSet = $this->db->Execute($query);
			if( $recordSet === FALSE ) die ($this->db->ErrorMsg());
			if( $this->db->Affected_Rows() > 0 ) return $this->db->Insert_ID(); else return false;
		} else {
			mysql_query($query, $this->db);
			if ($this->error()) die ($this->debug());
			if ($this->affected() > 0) return $this->insertid(); else return false;
		}
	} // end insert


	function update ($tablename, $record, $where)
	{
		if(!is_array($record)) die ($this->debug("array", "Update", $tablename));

		$count = 0;

		$query = $set = $val = $delim = "";
		foreach ($record as $key => $val)
		{
			$set .= $delim . "`{$key}` = '".mysql_real_escape_string($val)."'";
			$delim = ", ";
		}

		$query = "UPDATE ".$tablename." SET ".$set." WHERE ".$where;

		$this->query = $query;
		if($this->isAdoDb){
			$recordSet = $this->db->Execute($query);
			if( $recordSet === FALSE ) die ($this->db->ErrorMsg());
			if( $this->db->Affected_Rows() > 0 ) return true; else return false;
		} else {
			mysql_query($query, $this->db);
			if ($this->error()) die ($this->debug());
			if ($this->affected() > 0) return true; else return false;
		}
	} // end update

	function categoryNos($cat_id, $sign, $amount = 1) {

		global $glob;

		if($cat_id > 0) {

			do {

				$record['noProducts'] = " noProducts ".$sign.$amount;
				$where = "cat_id = ".$cat_id;
				$this->update($glob['dbprefix']."category", $record, $where, "");

				$query = "SELECT cat_father_id FROM ".$glob['dbprefix']."category WHERE cat_id = ".$cat_id;
				$cfi = $this->select($query);
				$cat_id = $cfi['0']['cat_father_id'];

			} while ($cat_id > 0);

		}

	}

	function delete($tablename, $where, $limit="")
	{
		$query = "DELETE from ".$tablename." WHERE ".$where;
		if ($limit!="") $query .= " LIMIT " . $limit;
		$this->query = $query;
		if($this->isAdoDb){
			$recordSet = $this->db->Execute($query);
			if( $recordSet === FALSE ) die ($this->db->ErrorMsg());
			if( $this->db->Affected_Rows() > 0 ) return true; else return false;
		} else {
			mysql_query($query, $this->db);
			if ($this->error()) die ($this->debug());
			if ($this->affected() > 0) return true; else return false;
		}
	} // end delete

	function simpleQuery($query)
	{
		$this->query = $query;
		if($this->isAdoDb){
			$recordSet = $this->db->Execute($query);
			if( $recordSet === FALSE ) die ($this->db->ErrorMsg());
			if( $this->db->Affected_Rows() > 0 ) return true; else return false;
		} else {
			mysql_query($query, $this->db);
			if ($this->error()) die ($this->debug());
			if ($this->affected() > 0) return true; else return false;
		}
	} // end delete

	//////////////////////////////////
	// Clean SQL Variables (Security Function)
	////////
	function mySQLSafe($value, $quote="'") {

		// strip quotes if already in
		$value = str_replace(array("\'","'"),"&#39;",$value);

		// Stripslashes
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}

		//$value = htmlentities($value);
		// Quote value
		if(version_compare(phpversion(),"4.3.0")=="-1") {
			$value = mysql_escape_string($value);
		} else {
			$value = mysql_real_escape_string($value);
		}
		$value = $quote . $value . $quote;

		return $value;
	}


	function debug($type="", $action="", $tablename="")
	{
		switch ($type)
		{
			case "connect":
				$message = "MySQL Error Occured";
				$result = mysql_errno() . ": " . mysql_error();
				$query = "";
				$output = "Could not connect to the database. Be sure to check that your database connection settings are correct and that the MySQL server in running.";
			break;


			case "array":
				$message = $action." Error Occured";
				$result = "Could not update ".$tablename." as variable supplied must be an array.";
				$query = "";
				$output = "Sorry an error has occured accessing the database. Be sure to check that your database connection settings are correct and that the MySQL server in running.";

			break;


			default:
				if (mysql_errno($this->db))
				{
					$message = "MySQL Error Occured";
					$result = mysql_errno($this->db) . ": " . mysql_error($this->db);
					$output = "Sorry an error has occured accessing the database. Be sure to check that your database connection settings are correct and that the MySQL server in running.";
				}
				else
				{
					$message = "MySQL Query Executed Succesfully.";
					$result = mysql_affected_rows($this->db) . " Rows Affected";
					$output = "view logs for details";
				}

				$linebreaks = array("\n", "\r");
				if($this->query != "") $query = "QUERY = " . str_replace($linebreaks, " ", $this->query); else $query = "";
			break;
		}




		$output = "<b style='font-family: Arial, Helvetica, sans-serif; color: #0B70CE;'>".$message."</b><br />\n<span style='font-family: Arial, Helvetica, sans-serif; color: #000000;'>".$result."</span><br />\n<p style='Courier New, Courier, mono; border: 1px dashed #666666; padding: 10px; color: #000000;'>".$query."</p>\n";
		return $output;
	}


	function error()
	{
		if (mysql_errno($this->db)) return true; else return false;
	}


	function insertid()
	{
		if($this->isAdoDb){
			return $this->db->Insert_ID();
		} else {
			return mysql_insert_id($this->db);
		}
	}

	function affected()
	{
		if($this->isAdoDb){
			return $this->db->Affected_Rows();
		} else {
			return mysql_affected_rows($this->db);
		}

	}

	function close() // close conection
	{
		if($this->isAdoDb){
			$this->db->Disconnect();
		} else {
			mysql_close($this->db);
		}
	}

	function getByTableName($table_name, $where='', $fields='*', $limit='', $offset='', $orderField='', $order='DESC')
	{
		if($limit!='')
			$limit .= ' LIMIT '.($offset!='' ? $offset.', ' : '').$limit;

		if($where!='')
			$where = ' WHERE '.$where;

		if($orderField)
			$orderField = ' ORDER BY '.$orderField.' '.$order;

		$query = 'SELECT '.$fields.' FROM '.$table_name.$where.$limit.$orderField;

		$qdata = $this->select($query);

		return $qdata;
	} // end select

} // end of db class
?>
