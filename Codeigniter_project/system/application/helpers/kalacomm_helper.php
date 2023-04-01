<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------
if (!function_exists('remove_non_ascii')) {
	function remove_non_ascii($data) {
		$data = preg_replace('/[^(\x20-\x7F)]*/', '', $data);
		return $data;
	}
}
if (!function_exists('generateHeaderArray')) {
	function generateHeaderArray($var_to_match) {
		$CI = & get_instance();
		$sql = "SELECT * FROM csv_default_columns WHERE array_values LIKE '$var_to_match'";
		$result = $CI->db->query($sql)->result();
		if (count($result) > 0) {
			return $result[0]->array_values;
		}else {
			return '';
		}
	}
}
if (!function_exists('generateHeaderPostArray')) {
	function generateHeaderPostArray($array) {
		$arrayToRet = array();
		$arrayToReturn = array();
		$exp = explode("&", $array);
		if (count($exp) > 0) {
			for ($i = 0; $i < count($exp); $i++) {
				$exp_1 = explode("=>", $exp[$i]);
				$exp_2 = explode("~", $exp_1[1]);
				$arrayToReturn[$exp_1[0]][] = str_replace('%', '', $exp_2[0]);
				$arrayToReturn[$exp_1[0]][] = $exp_2[1];
			}
		}
		if (count($arrayToReturn)) {
			for ($i = 0; $i < count($arrayToReturn); $i++) {
				$arrayToRet[0][] = $arrayToReturn[$i][1];
				$arrayToRet[1][] = $arrayToReturn[$i][0];
			}
		}
		return $arrayToRet;
	}
}
/**
 * Bitwise Selection - Takes a weightage as input and compare it with an array
 using bitwise &(AND) operator and returns an array where
 result true.
 *
 * @access public
 * @param array
 * @return mixed depends on what the array contains
 */
if (!function_exists('bitwise_weightage')) {
	function bitwise_weightage($weightage) {
		if (isset($weightage)) {
			$bitarray = array(0, 1, 2, 4, 8, 16, 32, 64, 128, 256, 512, 1024, 2048, 4096, 8192, 16384, 32768, 65536, 131072, 262144, 524288, 1048576, 2097152, 4194304, 8388608, 16777216, 33554432, 67108864, 134217728, 268435456, 536870912);
			$array = array();
			$i = 0;
			foreach ($bitarray as $value) {
				$result = $value & $weightage;
				if ($result > 0) {
					$array[$i] = $result;
					$i++;
				}
			}
			return $array;
		}
	}
}

if (!function_exists('createThumbs')) {
	function createThumbs($pathToImages, $pathToThumbs, $fname, $type, $thumbWidth=100) {
		// load image and get image size
		switch ($type) {
		case "jpg":
			$img = imagecreatefromjpeg("{$pathToImages}");
			break;
		case "jpeg":
			$img = imagecreatefromjpeg("{$pathToImages}");
			break;
		case "gif":
			$img = imagecreatefromgif("{$pathToImages}");
			break;
		case "png":
			$img = imagecreatefrompng("{$pathToImages}");
			break;
		}
		$width = imagesx($img);
		$height = imagesy($img);
		// calculate thumbnail size
		$new_width = $thumbWidth;
		$new_height = floor($height * ( $thumbWidth / $width ));
		// create a new temporary image
		$tmp_img = imagecreatetruecolor($new_width, $new_height);
		// copy and resize old image into new image
		imagecopyresized($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		// save thumbnail into a file
		imagejpeg($tmp_img, "{$pathToThumbs}{$fname}");
	}
}
if (!function_exists('generate_rand')) {
	function generate_rand($ch) {
		$abc = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$chars = '';
		for ($i = 1; $i <= $ch; $i++) {
			$chars .= substr($abc, rand(0, strlen($abc) - 1), 1);
		}
		return $chars;
	}
}

/*
 * input  : name, id , selected
 * output : list of coutries with selected item if given
 */
if (!function_exists('countryListByName')) {
	function countryListByName($name='country', $id='country', $check='United States of America') {
		$ci = & get_instance();
		$ci->load->database();
		$sql = "select * from countries";
		$query = $ci->db->query($sql);
		$row = $query->result();
		$listofcountries = '';
		$listofcountries .= '<select name="'.$name.'" id="'.$id.'">';
		$listofcountries .= '<option value="">Select One</option>';
		foreach ($row as $country) {
			if ($check == $country->country_name)
				$selected = 'selected="selected"';
			else
				$selected = '';
			$listofcountries .= '<option value="'.$country->country_name.'" '.$selected.'>'.$country->country_name.'</option>';
		}
		$listofcountries .= '</select>';
		return $listofcountries;
	}
}
/*
 * input  : name, id , selected
 * output : list of coutries with selected item if given
 */
if (!function_exists('stateListByName')) {
	function stateListByName($name='state', $id='state', $check='') {
		$ci = & get_instance();
		$ci->load->database();
		$sql = "select * from states";
		$query = $ci->db->query($sql);
		$row = $query->result();
		$listofstates = '';
		$listofstates .= '<select name="'.$name.'" id="'.$id.'">';
		$listofstates .= '<option value="">Select One</option>';
		foreach ($row as $state) {
			if ($check == $state->state_name)
				$selected = 'selected="selected"';
			else
				$selected = '';
			$listofstates .= '<option value="'.$state->state_name.'" '.$selected.'>'.$state->state_name.'</option>';
		}
		$listofstates .= '</select>';
		return $listofstates;
	}
}
/*
 * input  : month list
 * output : list of moonths with selected item if given
 */
if (!function_exists('monthList')) {
	function monthList($name='month', $newkey=0) {
		$curr_month = date("m");
		if ($newkey)
			$curr_month = $newkey;
		$month = array(1 => "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		$select = '<select name="'.$name.'">\n';
		foreach ($month as $key => $val) {
			$select .= "\t<option value=\"".str_pad($key, 2, '0', STR_PAD_LEFT)."\"";
			if ($key == $curr_month) {
				$select .= " selected=\"selected\">".$val."</option>\n";
			}else {
				$select .= ">".$val."</option>\n";
			}
		}
		$select .= "</select>";
		return $select;
	}
}
/*
 * input  : year list
 * output : list of moonths with selected item if given
 */
if (!function_exists('yearList')) {
	function yearList($years=5, $name='', $newkey=0) {
		if ($name == '')
			$name = 'year';

		$curr_year = date("Y");
		if ($newkey)
			$curr_year = $newkey;
		$select = '<select name="'.$name.'">\n';
		for ($i = date("Y"); $i <= (date("Y") + $years); $i++) {
			$select .= "\t<option value=\"".substr($i, 2)."\"";
			$comp = substr($i, 2);
			if ($comp == $curr_year) {
				$select .= " selected=\"selected\">".$i."</option>\n";
			}else {
				$select .= ">".$i."</option>\n";
			}
		}
		$select .= "</select>";
		return $select;
	}
}
/* date formatter function */
if (!function_exists('hbm_date')) {
	function hbm_date($date='') {
		if ($date == '')
			$date = time();
		return date('M dS Y', $date);
	}
}
/* compare cutomer address
 * returns fasle if the addresses are equal
 */
if (!function_exists('hbm_compareAddress')) {
	function hbm_compareAddress($personal, $shipping) {
		if ($personal->first_name != $shipping[0]->shipping_first || $personal->last_name != $shipping[0]->shipping_last || $personal->address1 != $shipping[0]->shipping_address || $customer->address2 != $shipping[0]->shipping_address2 or $customer->city != $shipping[0]->shipping_city || $personal->state != $shipping[0]->shipping_state || $personal->zip != $order_history[0]->shipping_zip || $personal->phone != $shipping[0]->shipping_phone || $personal->email != $shipping[0]->shipping_email)
			return true;
		else
			return false;
	}
}
if (!function_exists('hbm_curPageURL')) {
	function hbm_curPageURL() {
		$pageURL = 'http';
		if (@$_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
}

/* for making date */
if (!function_exists('hmb_mktime')) {
	function hmb_mktime($day = -30) {
		return mktime(0, 0, 0, date('m'), $day, date('Y'));
	}
}

/*
 * Get CMS Pages links
 *
 */
if (!function_exists('cmsPagesMenu')) {
	function cmsPagesMenu() {
		$ci = & get_instance();
		$ci->load->database();
		$sql = "SELECT * FROM `cms_pages`";
		$query = $ci->db->query($sql);
		$menu = $query->result();
		if ($menu) {
			return $menu;
		}
	}
}


// ------------------------------------------------------------------------
/**
 * Custom date
 *
 * Returns a cutom date.
 * This is a helper function
 *
 * @access public
 * @param string day, month
 * @return time string in unix time format
 */
if (!function_exists('custom_date')) {
	function custom_date($day='', $month='', $year='', $current_date='current_date') {
		if ($current_date == 'current_date') {
			$cur_day = date('j');
			$cur_month = date('n');
			$cur_year = date('Y');
		}else {
			$cur_day = date('j', $current_date);
			$cur_month = date('n', $current_date);
			$cur_year = date('Y', $current_date);
		}
		// makinh Day
		if ($day == '')
			$day = $cur_day;
		else
			$day += $cur_day;
		// making Month
		if ($month == '')
			$month = $cur_month;
		else
			$month += $cur_month;
		// making Year
		if ($year == '')
			$year = $cur_year;
		else
			$year += $cur_year;
		return mktime(0, 0, 0, $month, $day, $year);
	}
}

// ------------------------------------------------------------------------
/**
 * Custom date
 *
 * Returns a cutom date.
 * This is a helper function
 *
 * @access public
 * @param string day, month
 * @return time string in unix time format
 */
if (!function_exists('reports_custom_date')) {
	function reports_custom_date($day='', $month='', $year='') {
		if ($day == '')
			$day = date('j');
		if ($month == '')
			$month = date('n');
		if ($year == '')
			$year = date('Y');
		return mktime(0, 0, 0, $month, $day, $year);
	}
}
// ------------------------------------------------------------------------
/**
 * Custom date
 *
 * Returns a cutom date.
 * This is a helper function
 *
 * @access public
 * @param string day, month
 * @return time string in unix time format
 /* getting email by type */
if (!function_exists('getEmailTemplateByType')) {
	function getEmailTemplateByType($type) {
		$ci = & get_instance();
		$ci->load->database();
		$where = array('type' => $type, 'is_active' => 1);
		$query = $ci->db->get_where('email_templates', $where, 1);
		return $query->row();
	}
}


/*
 * text formatter
 * input string
 * output string without -,_ and dots
 */
if (!function_exists('formatText')) {
	function formatText($str) {
		return str_replace(
			array('-', '_', '.', '"', ' '),
			'',
			ucwords(strtolower($str))
		);
	}
}
if (!function_exists('formatNumber')) {
	function formatNumber($number) {
		$str = number_format($number, 2);
		return ucwords($str);
	}
}
if (!function_exists('formatTime')) {
	function formatTime($number) {
		if ($number == 0)
			return $str = '00';
		elseif ($number <= 9)
			return $str = '0'.$number;
		else
			return $str = $number;
	}
}

/*
 * Days Range to get the records for cron
 *
 */
if (!function_exists('dayRange')) {
	function dayRange() {
		$ci = & get_instance();
		return $ci->config->item('days_range');
	}
}
/*
 * Base Directory Path
 *
 */
if (!function_exists('dirPath')) {
	function dirPath() {
		$ci = & get_instance();
		return $ci->config->item('base_dir_path');
	}
}
/*
 * Clean the data from html tags and entities.
 *
 */
if (!function_exists('cleanData')) {
	function cleanData($data) {
		$ci = & get_instance();
		$search = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
			'@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
			'@([\r\n])[\s]+@', // Strip out white space
			'@&(quot|#34);@i', // Replace HTML entities
			'@&(amp|#38);@i',
			'@&(lt|#60);@i',
			'@&(gt|#62);@i',
			'@&(nbsp|#160);@i',
			'@&(iexcl|#161);@i',
			'@&(cent|#162);@i',
			'@&(pound|#163);@i',
			'@&(copy|#169);@i',
			'@&#(\d+);@e');                    // evaluate as php
		$replace = array(
			'',
			'\1',
			'"',
			'&',
			'<',
			'>',
			' ',
			chr(161),
			chr(162),
			chr(163),
			chr(169),
			'chr(\1)');
		return preg_replace($search, $replace, $data);
	}
}
/*
 * Get product videos links
 */
if (!function_exists('getDateDiff')) {
	function getDateDiff($date1, $date2) {
		$date1 = ($date1) / (24 * 60 * 60);
		$date2 = ($date2) / (24 * 60 * 60);
		if ($date1 > $date2)
			$dateDiff = $date1 - $date2;
		else
			$dateDiff = $date2 - $date1;
		return $dateDiff;
	}
}

/*
 *
 * add emails in queue_emails table
 * which are sent according to the PST time
 */
if (!function_exists('addEmails')) {
	function addEmails($Data) {
		$ci = & get_instance();
		$ci->load->database();
		$ci->db->insert('queue_emails', $Data);
	}
}
/*
 *
 * get all emails in queue_emails table
 * which are sent according to the PST time
 */
if (!function_exists('getEmails')) {
	function getEmails() {
		$ci = & get_instance();
		$ci->load->database();
		$query = $ci->db->get('queue_emails');
		return $query->result();
	}
}
/*
 *
 * delete email from queue_emails table
 * which have been sent according to the PST time
 */
if (!function_exists('deleteEmail')) {
	function deleteEmail($id) {
		$ci = & get_instance();
		$ci->load->database();
		$ci->db->delete('queue_emails', array('id' => $id));
	}
}
/*
 * for Decoding
 */
if (!function_exists('myDecode')) {
	function myDecode($arg) {
		return gzinflate($arg);
	}
}
/*
 * for Encoding
 */
if (!function_exists('myEncode')) {
	function myEncode($arg) {
		return gzdeflate($arg);
	}
}
/*
 * Get Link Name array
 *
 */
if (!function_exists('formatVariables')) {
	function formatVariables($myvariable) {
		if ($myvariable != '') {
			$tmp_var = strtoupper(str_replace(array(' '), '_', $myvariable));
			return '{'.$tmp_var.'}';
		}
	}
}


/*
  to get difference between two dates
 */
if (!function_exists('dateDiff')) {
	function dateDiff($time1, $time2, $precision = 6) {
		// If not numeric then convert texts to unix timestamps
		if (!is_int($time1)) {
			$time1 = strtotime($time1);
		}
		if (!is_int($time2)) {
			$time2 = strtotime($time2);
		}
		// If time1 is bigger than time2
		// Then swap time1 and time2
		if ($time1 > $time2) {
			$ttime = $time1;
			$time1 = $time2;
			$time2 = $ttime;
		}
		// Set up intervals and diffs arrays
		$intervals = array('year', 'month', 'day', 'hour', 'minute', 'second');
		$diffs = array();
		// Loop thru all intervals
		foreach ($intervals as $interval) {
			// Set default diff to 0
			$diffs[$interval] = 0;
			// Create temp time from time1 and interval
			$ttime = strtotime("+1 ".$interval, $time1);
			// Loop until temp time is smaller than time2
			while ($time2 >= $ttime) {
				$time1 = $ttime;
				$diffs[$interval]++;
				// Create new temp time from time1 and interval
				$ttime = strtotime("+1 ".$interval, $time1);
			}
		}
		$count = 0;
		$times = array();
		// Loop thru all diffs
		foreach ($diffs as $interval => $value) {
			// Break if we have needed precission
			if ($count >= $precision) {
				break;
			}
			// Add value and interval
			// if value is bigger than 0
			if ($value > 0) {
				// Add s if value is not 1
				$interval .= "s";
				// Add value and interval to times array
				$times[$interval] = $value; //." ".$interval
				$count++;
			}
		}
		// Return string with times
		return $times;
	}
}

/* Get week dates */
if (!function_exists('calculateWeekDates')) {
	function calculateWeekDates($dateS, $dateE) {
		if (strtotime($dateS) > strtotime($dateE)) {
			$tmp = $dateS;
			$dateS = $dateE;
			$dateE = $tmp;
		}
		$j = 0;
		$dS = '';
		$de = '';
		$dates = array();
		$bot = '';
		for (;;) {
			$j++;
			$expS = explode('-', $dateS);
			$n = date('N', mktime(0, 0, 0, $expS[1], $expS[2], $expS[0]));
			$n = ($n - 1);
			$startdate = date('Y-m-d', mktime(0, 0, 0, $expS[1], $expS[2] - $n, $expS[0]));
			$startdateBot = date('F d', mktime(0, 0, 0, $expS[1], $expS[2] - $n, $expS[0]));
			$bot .= ( $bot == '') ? '\''.$startdateBot.'\'' : ',\''.$startdateBot.'\'';
			$stEXP = explode('-', $startdate);
			$enddate = date('Y-m-d', mktime(0, 0, 0, $stEXP[1], $stEXP[2] + 6, $stEXP[0]));
			$dateS = date('Y-m-d', mktime(0, 0, 0, $stEXP[1], $stEXP[2] + 7, $stEXP[0]));
			$dates['dates'][] = array('start_date' => $startdate, 'end_date' => $enddate);
			if ($j == '1') {
				$dS = date('F d,Y', mktime(0, 0, 0, $expS[1], $expS[2] - $n, $expS[0]));
			}
			if ($enddate >= $dateE) {
				$de = date('F d,Y', mktime(0, 0, 0, $expS[1], $expS[2] - $n, $expS[0]));
				break;
			}
		}//end of for loop
		$dates['bot'] = $bot;
		return $dates;
	}
	//end of function
}

/* Get Month Dates */
if (!function_exists('calculateMonthsDates')) {
	function calculateMonthsDates($date, $endDate) {
		if (strtotime($date) > strtotime($endDate)) {
			$tmp = $date;
			$date = $endDate;
			$endDate = $tmp;
		}
		$date_exp = explode('-', $date);
		$end_Date_exp = explode('-', $endDate);
		$dates = array();
		$bot = '';

		for ($i = $date_exp[1]; $i <= $end_Date_exp[1]; $i++) {
			$m = (strlen($i) == '1') ? '0'.$i : $i;
			$startdate = date('Y').'-'.$m.'-01';
			$end = date('Y-m-d', strtotime('-1 second', strtotime('+1 month', strtotime($m.'/01/'.date('Y').' 00:00:00'))));
			$enddate = $end;
			$dates['dates'][] = array('start_date' => $startdate, 'end_date' => $enddate);
			$month = date('M-y', strtotime($startdate));
			$bot .= ( $bot == '') ? '\''.$month.'\'' : ',\''.$month.'\'';
		}
		$dates['bot'] = $bot;
		return $dates;
	}
}

/*
  Get Year dates
 */
if (!function_exists('calculateYearsDates')) {
	function calculateYearsDates($date, $endDate) {
		if (strtotime($date) > strtotime($endDate)) {
			$tmp = $date;
			$date = $endDate;
			$endDate = $tmp;
		}
		$date_exp = explode('-', $date);
		$end_Date_exp = explode('-', $endDate);
		$dates = array();
		$bot = '';
		for ($i = $date_exp[0]; $i <= $end_Date_exp[0]; $i++) {
			$startdate = $i.'-01-01';
			$end = $i.'-12-31';
			$enddate = $end;
			$dates['dates'][] = array('start_date' => $startdate, 'end_date' => $enddate);
			$bot .= ( $bot == '') ? '\''.$i.'\'' : ',\''.$i.'\'';
		}
		$dates['bot'] = $bot;
		return $dates;
	}
}

/*
  Get Days Dates
 */
if (!function_exists('calculateDaysDates')) {
	//2011-06-23 2011-06-30
	function calculateDaysDates($dateS, $dateE, $days) {
		if (strtotime($dateS) > strtotime($dateE)) {
			$tmp = $dateS;
			$dateS = $dateE;
			$dateE = $tmp;
		}
		$exp = explode('-', $dateS);
		$dates = array();
		$bot = '';
		$count = 0;
		for ($i = 0; $i <= $days; $i++) {
			$count++;
			$startdate = date('Y-m-d', mktime(0, 0, 0, $exp[1], $exp[2] + $i, $exp[0]));
			$dates['dates'][] = array('start_date' => $startdate, 'end_date' => $startdate);
			$expStart = explode('-', $startdate);
			$botttom = $expStart[1].'/'.$expStart[2];
			$bot .= ( $bot == '') ? '\''.$botttom.'\'' : ',\''.$botttom.'\'';
		}
		$dates['bot'] = $bot;
		return $dates;
	}
}

/*
  GET SPECIFIC METHOD
 */
if (!function_exists('getDateMethod')) {
	function getDateMethod($dateArray, $request_info) {
		if (isset($dateArray['years']) && $dateArray['years'] == 1) {
			//group by month
			$dateArray = calculateYearsDates($request_info['fromDate'], $request_info['toDate']);
		} else if (isset($dateArray['years']) && $dateArray['years'] > 1) {
				// group by years
				$dateArray = calculateYearsDates($request_info['fromDate'], $request_info['toDate']);
			} else if (isset($dateArray['months']) && $dateArray['months'] == 1) {
				//group by weeks
				$dateArray = calculateWeekDates($request_info['fromDate'], $request_info['toDate']);
			} else if (isset($dateArray['months']) && $dateArray['months'] > 1) {
				// group by months
				$dateArray = calculateMonthsDates($request_info['fromDate'], $request_info['toDate']);
			}else {
			$dateArray = calculateDaysDates($request_info['fromDate'], $request_info['toDate'], (isset($dateArray['days']) ? $dateArray['days'] : 0));
		}
		return $dateArray;
	}
}

function getProductMerchant($store_id = 0) {
	$ci =& get_instance();

	$qStr = "
          SELECT DISTINCT(m.merchant_name), m.original_name, GROUP_CONCAT(m.marketplace) market_place, m.id
          FROM {$ci->_table_products} p
          INNER JOIN {$ci->_table_crowl_product_list} l on l.upc = p.upc_code
          INNER JOIN {$ci->_table_crowl_merchant_name} m on l.merchant_name_id = m.id
          WHERE p.store_id IN (" . getStoreIdList($store_id, TRUE) . ")
          GROUP BY m.merchant_name
          ORDER BY m.merchant_name
    ";
	$result = $ci->db->query($qStr)->result();

	return $result;
}

function getProductsDrp($store_id) {
	$ci =& get_instance();
	$result = $ci->db
	->select('id,title')
	->where('is_tracked', 1)
	->where('title !=', '')
	->where_in('store_id', getStoreIdList($store_id, TRUE))
	->get($ci->_table_products)
	->result();

	return $result;
}

function getProductsUPC($id) {
	$ci = & get_instance();
	$ci->load->database();
	$sql = "SELECT upc_code FROM products WHERE id = {$id}";
	$result = $ci->db->query($sql)->result();
	return (isset($result[0])) ? $result[0]->upc_code : '';
}

function getProductsTitle($id, $text='') {
	$ci = & get_instance();
	$ci->load->database();
	$sql = "SELECT title FROM products WHERE id = {$id}";
	$result = $ci->db->query($sql)->result();
	$title = (isset($result[0])) ? $result[0]->title.' '.$text : '';
	return trim(html_entity_decode($title));
}

function getTimeFrame($timeFrame, $samedates='') {
	$timeFrameArr = array(
		'date_from' => '',
		'date_from' => ''
	);

	$curTime = time();
	switch ($timeFrame) {
	case '24':
		$calcTime = $curTime - 86400;
		$timeFrameArr['date_from'] = $calcTime;
		$timeFrameArr['date_to'] = $curTime;
		break;
	case '1':
		$calcTime = custom_date(-1);
		$timeFrameArr['date_from'] = $calcTime;
		$timeFrameArr['date_to'] = $curTime;
		break;
	case '7':
		$calcTime = custom_date(-6);
		$timeFrameArr['date_from'] = $calcTime;
		$timeFrameArr['date_to'] = $curTime;
		break;
	case '30':
		$calcTime = custom_date(-29);
		$timeFrameArr['date_from'] = $calcTime;
		$timeFrameArr['date_to'] = $curTime;
		break;
	case '90':
		$calcTime = custom_date(-90);
		$timeFrameArr['date_from'] = $calcTime;
		$timeFrameArr['date_to'] = $curTime;
		break;
	default:
		$calcTime = custom_date(-365);
		$timeFrameArr['date_from'] = $calcTime;
		$timeFrameArr['date_to'] = $curTime;
		break;
	}

	return $timeFrameArr;
}

/**
 * Get the names of all the active markets (marketplaces and retailers)
 *
 * @return array
 */
function getMarketArray($all = FALSE) {
	$ci = get_instance();
	if ( ! isset($ci->Marketplace))
		$ci->load->model('marketplace_m', 'Marketplace');

	$markets = $ci->Marketplace->get_marketplaces(array('name', 'is_retailer'));
	$ret = array();
	for ($i = 0, $n = count($markets); $i < $n; $i++){
		//if ($all or ! $markets[$i]['is_retailer'] or $ci->check_addon($markets[$i]['name']))
			$ret[] = ucfirst($markets[$i]['name']);
	}
	return $ret;
}

function get_marketplaces_by_storeid_using_categories($store_id){
	$ci = get_instance();
	if ( ! isset($ci->Marketplace))
		$ci->load->model('marketplace_m', 'Marketplace');

	$markets = $ci->Marketplace->get_marketplaces_by_storeid_using_categories($store_id);
	$ret = array();
	for ($i = 0, $n = count($markets); $i < $n; $i++){
		//if ($all or ! $markets[$i]['is_retailer'] or $ci->check_addon($markets[$i]['name']))
			$ret[] = ucfirst($markets[$i]['name']);
	}
	return $ret;
}

/**
 * Get the entries in the marketplaces table in the form api_name => display_name
 *
 * @param boolean $active_only { default : FALSE }
 * @return array
 */
function get_market_lookup($active_only = TRUE, $all = FALSE) {
	$ci = get_instance();
	if ( ! isset($ci->Marketplace)){
		$ci->load->model('marketplace_m', 'Marketplace');
	}
	$markets = $ci->Marketplace->get_marketplaces(array('name', 'display_name', 'is_retailer'), TRUE, ! $active_only);
	$ret = array();
	for ($i = 0, $n = count($markets); $i < $n; $i++){
		//if ($all or ! $markets[$i]['is_retailer'] or $ci->check_addon($markets[$i]['name']))
			$ret[$markets[$i]['name']] = $markets[$i]['display_name'];
	}
	return $ret;
}

/**
 * Get the names of all the active retailers
 *
 * @param bool all (optional)
 * @param int storeId (optional)
 * @return array
 */
function getRetailerArray($all=false, $storeId=null) {
	$ci = get_instance();
	if ( ! isset($ci->Marketplace))
		$ci->load->model('marketplace_m', 'Marketplace');

	if($storeId){
		$isRetailer=1;
		$markets = $ci->Marketplace->get_marketplaces_by_store_id($storeId, $isRetailer);
	}
	else{
		$markets = $ci->Marketplace->get_retailers('name');
	}
	
	$ret = array();
	for ($i = 0, $n = count($markets); $i < $n; $i++){
		//if ($all or $ci->check_addon($markets[$i]['name']))
			$ret[] = ucfirst($markets[$i]['name']);
	}
	return $ret;
}

/**
 * Get the names of all the active marketplaces
 *
 * @param int storeId (optional)
 * @return array
 */
function getMarketplaceArray($storeId=null) 
{
	$ci = get_instance();
	
	if ( ! isset($ci->Marketplace))
	{
		$ci->load->model('marketplace_m', 'Marketplace');
	}
	
	if ($storeId)
	{
		$isRetailer = 0;
		
		$markets = $ci->Marketplace->get_marketplaces_by_store_id($storeId, $isRetailer);
	}
	else
	{
		$markets = $ci->Marketplace->get_marketplaces('name', false);
	}
	
	$ret = array();
	
	for ($i = 0; $i < count($markets); $i++)
	{
		$ret[] = ucfirst($markets[$i]['name']);
	}
	
	return $ret;
}

/*
 * Debug Function.
 */
if (!function_exists('debug')) {
	function debug($message = '', $array = array(), $mode = 0) {
		if ($mode == 1) {
			echo $message.' '.date('Y-m-d H:i:s')."<br>";
			flush();
			@ob_flush();
		} elseif ($mode == 2) {
			echo $message.' '.date('Y-m-d H:i:s')."<br>";
			if (!empty($array)) {
				echo "<pre>";
				print_r($array);
				echo "</pre>";
			}
			flush();
			@ob_flush();
		}else {
			echo $message.' : '.$array.'<br />';
			flush();
			@ob_flush();
		}
	}
}

function extractDomainByURL($url) {
	$url = urldecode($url);
	$parts = parse_url($url);
	return isset($parts['host']) ? $parts['host'] : '';
}

function hideColumnsJsArray($storeColumns, $allColumns) {
	$storeColumnIds = array();

	if (count($storeColumns) > 0) {
		foreach ($storeColumns as $storeColumn) {
			$storeColumnIds[] = $storeColumn->id;
		}
	}
	$html = "<script>var newArr = new Array();";
	if (count($allColumns) > 0) {
		$j = 0;
		foreach ($allColumns as $allColumn) {
			//echo "<br>".$allColumn->db_name;
			if (!in_array($allColumn->id, $storeColumnIds)) {
				$html .='newArr['.$j.'] = \''.trim($allColumn->db_name).'\';';
				$j++;
			}
		}
	}
	$html .='//end hideColumnsJsArray</script>'."\n";
	return $html;
}

function formatInputText($products, $skip_in = false) {
	$text = '';
	$cnt = count($products);
	$i = 0;
	if ($products) {
		foreach ($products as $prod) {
			if (trim($prod) != '') {
				$upc = getProductsTitle($prod);
				if ($cnt == 1)
					$text = ''.$upc.' pricing activity'.($skip_in ? '' : ' in the');
				elseif ($cnt == 2) {
					if ($i == 0)
						$text .= ''.$upc.'';
					else
						$text .= ' and '.$upc.' pricing activity'.($skip_in ? '' : ' in the');
				}
				else {
					if ($i == 0)
						$text .= ''.$upc.'';
					elseif ($i == ($cnt - 1))
						$text .= ' and '.$upc.' pricing activity'.($skip_in ? '' : ' in the');
					else
						$text .= ', '.$upc.'';
				}
				$i++;
			}
		}
	}
	//echo "TEXT : ".$text;
	return $text;
}

if (!function_exists('displayQaWidget')) {
	function displayQaWidget($itemId) {
		//temporarily remove
		return '';

		$html = '<script type="text/javascript">
				 	var qawiki_owner_id = "7";
					var qawiki_store_id = "6";
					var qawiki_item_id = "'.$itemId.'";
					var qawiki_customer_name = "";
					var qawiki_customer_email = "";</script>
					<div id="qaw-widget"></div>';
		return $html;
	}
}

function getUniqueKeywordsFromString($string) {
	$array = explode(',', $string);
	return implode(',', array_unique($array));
}

function getMarketHTML($markertArr, $markets, $mode=0) {
	//debug('Markets',$markets,2);
	//debug('MarketsArr',$markertArr,2);
	//echo "MODE=>".$mode;
	if ($mode == 1) {
		$html = '<div class="select_report" id="market_container">
				<div class="select_report_left" style="margin-top:4px;">
					<p>Select Markets</p>
				</div>
				<div class="select_report_right">
				<div class="input_bg fl" style="margin:0;">
				<span class="lft_area"></span><span class="rgt_area"><div class="fl data_txt" id="market_box_txt">ALL MARKETS</div> <a href="javascript:void(0);" onclick="ShowFilterOptions(\'all_markets\');" class="arrow"></a></span>
				</div>
				<div class="clear"></div>
				<div class="check_list2" id="all_markets" style="display:none;">
				<ul>';
	}else {
		$html = ' <div class="fl">
            <div class="input_bg fl">
            <span class="lft_area"></span><span class="rgt_area"><div class="fl data_txt" id="market_box_txt">ALL MARKETS</div> <a href="javascript:void(0);" onclick="ShowFilterOptions(\'all_markets\');" class="arrow"></a></span>
            </div>
            <div class="clear"></div>
            <div class="check_list" id="all_markets" style="display:none;">
			<ul>';
	}
	if ($markertArr) {
		if (isset($markets) && in_array('all', $markets))
			$html .= '<li class="merchant_all"><input type="checkbox" class="mrChk" name="markets[]" value="all"  checked="checked" onclick="textChange(this,\'mrChk\',\'market_box_txt\',\'ALL MARKETS\');" rel="all" /> ALL MARKETS</li>';
		else if (isset($markets) && count($markets) > 0 && !in_array('all', $markets))
				$html .= '<li class="merchant_all"><input type="checkbox" class="mrChk" name="markets[]" value="all"  onclick="textChange(this,\'mrChk\',\'market_box_txt\',\'ALL MARKETS\');" rel="all" /> ALL MARKETS</li>';
			else
				$html .= '<li class="merchant_all"><input type="checkbox" class="mrChk" name="markets[]" value="all" checked="checked" onclick="textChange(this,\'mrChk\',\'market_box_txt\',\'ALL MARKETS\');" rel="all" /> ALL MARKETS</li>';
			foreach ($markertArr as $val) {
				$checked = '';
				if (isset($markets) && (in_array(strtolower($val), $markets)|| in_array($val, $markets)))
					$checked = 'checked="checked"';
				$html.='<li class="merchant_'. strtolower($val) .'"><input type="checkbox" class="mrChk" name="markets[]" value="'.$val.'" '.$checked.' onclick="textChange(this,\'mrChk\',\'market_box_txt\',\'ALL MARKETS\');" rel="'. strtolower($val) .'"/> '.marketplace_display_name($val).'</li>';
			}
	}
	if ($mode == 1)
		$html.='</ul></div></div></div>';
	else
		$html.='</ul></div></div>';
	return $html;
}

function getStoreIdList($store_id, $as_list = FALSE) {
	$ci =& get_instance();
	if ($store_id === 'all') {
		$ids = $ci->allVisibleStoreIds;
	}
	elseif (is_array($store_id)) {
		foreach ($store_id as $id) {
			if ($id !== 'all')
				$ids[] = $id;
			else
				$ids = array_merge($ids, getStoreIdList($id));
		}
	}
	else {
		$ids = array($store_id);
	}

	$ret = $ids;
	if ($as_list) {
		$ret = implode(',', array_map('intval', $ids));
	}

	return $ret;
}

function getNumberOfMerchants($store_id = 0, $startTime = NULL, $endTime = NULL) {
	$ci =& get_instance();

	$start = ($startTime) ? strtotime($startTime) : strtotime('-24 hours');
	$end = ($endTime) ? strtotime($endTime) : time();

	// JMM 11-17-2013:  Added m.is_active to filter unused retailers from merchant count
	$q = "SELECT COUNT(DISTINCT(cpl.merchant_name_id)) as total_listing, COUNT(distinct(p.id)) as total_products, cpl.marketplace, m.is_retailer
	      FROM " . $ci->_table_crowl_product_list . " cpl
	      JOIN " . $ci->_table_products . " p ON cpl.upc = p.upc_code AND p.store_id IN (" . getStoreIdList($store_id, TRUE) . ")
		  JOIN " . $ci->_table_marketplaces." m ON m.name=cpl.marketplace
	      WHERE cpl.last_date >= $start
				AND cpl.last_date <= $end
				AND cpl.marketplace IS NOT NULL
                                AND m.is_active = 1
	      GROUP BY cpl.marketplace";

	$rs = $ci->db->query($q)->result('array');
	$count = 0;

	foreach ($rs as $data):
		//if ($data['is_retailer'] == '0' || $ci->check_addon($data['marketplace'])):
			$count += $data['total_listing'];
		//endif;
	endforeach;

	return $count;
}

function getTrackedTime($id) {
	$ci = & get_instance();
	$ci->load->database();
	$sql = "SELECT tracked_at FROM store WHERE id = {$id}";
	$result = $ci->db->query($sql)->result();
	return (isset($result[0])) ? $result[0]->tracked_at : '';
}

function getEndTrackTime($startDate) {
	$ci = & get_instance();
	$ci->load->database();
	$sql = "SELECT end_datetime FROM cron_log WHERE start_datetime = '".$startDate."'";
	$result = $ci->db->query($sql)->result();
	return (isset($result[0])) ? $result[0]->end_datetime : '';
}

function trackingDateFormat($lastTrackedDate) {
	$crntDate = date('m/d/y');
	$strDate = 'No Tracking Run Yet.';
	if ($lastTrackedDate != '0000-00-00 00:00:00') {
		$lastDate = date('m/d/y', strtotime($lastTrackedDate));
		if ($lastDate == $crntDate)
			$strDate = 'Today @ '.date('h:i A', strtotime($lastTrackedDate));
		else
			$strDate = $lastDate.' @ '.date('h:i A', strtotime($lastTrackedDate));
	}
	return $strDate;
}

function msort($array, $id="order") {
	/* echo "Sort <pre>";
    print_r($array);
    echo "</pre>";
    exit; */
	$temp_array = array();
	while (count($array) > 0) {
		$lowest_id = 0;
		$index = 0;
		foreach ($array as $item) {
			if (isset($item[$id]) && $array[$lowest_id][$id]) {
				if ($item[$id] < $array[$lowest_id][$id]) {
					$lowest_id = $index;
				}
			}
			$index++;
		}
		$temp_array[] = $array[$lowest_id];
		$array = array_merge(array_slice($array, 0, $lowest_id), array_slice($array, $lowest_id + 1));
	}
	return $temp_array;
}

function getBrandName($id) {
	if ($id === 'all')
		return 'All Brands';

	$ci = & get_instance();
	$res = $ci->db
	->select('store_name')
	->where('id', (int)$id)
	->get($ci->_table_store)
	->row();

	return isset($res->store_name) ? $res->store_name : '';
}

function makeURL($url, $upc) {
	$temp_url = $url;
	$domain = extractDomainByURL($url);
	if (trim($domain) == 'www.amazon.com') {
		$temp_url = 'http://www.amazon.com/s/ref=nb_sb_noss?url=search-alias%3Daps&field-keywords='.$upc.'&x=0&y=0';
	}
	return $temp_url;
}

function createDateRangeArray($strDateFrom, $strDateTo) {
	// takes two dates formatted as YYYY-MM-DD and creates an
	// inclusive array of the dates between the from and to dates.
	// could test validity of dates here but I'm already doing
	// that in the main script
	$strDateFrom = date('Y-m-d', $strDateFrom);
	$strDateTo = date('Y-m-d', $strDateTo);
	$aryRange = array();
	$iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
	$iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));
	if ($iDateTo >= $iDateFrom) {
		array_push($aryRange, date('m/d/y', $iDateFrom)); // first entry
		while ($iDateFrom < $iDateTo) {
			$iDateFrom+=86400; // add 24 hours
			array_push($aryRange, date('m/d/y', $iDateFrom));
		}
	}
	return $aryRange;
}

function getTitleReporting($request_info, $year_format = 'y') {
	if (empty($request_info['date_from']) or $request_info['date_from'] === 'Start')
		$request_info['date_from'] = strtotime('today');
	if (empty($request_info['date_to']) or $request_info['date_to'] === 'Stop')
		$request_info['date_to'] = strtotime('now');

	$fromDate = date('n/j/'.$year_format, $request_info['date_from']);
	$fromDatetime = date('n/j/'.$year_format.' h:i:A', $request_info['date_from']);
	$toDate = date('n/j/'.$year_format, $request_info['date_to']);
	$toDatetime = date('n/j/'.$year_format.' h:i:A', $request_info['date_to']);

	$timeText = '';
	$skip_in = false;
	$rptInfo = array('title' => '', 'date' => '');

	if (isset($request_info['time_frame'])) {
		switch ($request_info['time_frame']) {
		case '24':
			$ts_24h = strtotime('-24 hours');
			$is_last_24h = ($request_info['date_from'] > $ts_24h);

			$timeText = ($is_last_24h) ? 'last 24 hours ' : 'period ';
			$timeText .= $fromDatetime . ' - ' . $toDatetime;

			$rptInfo['date'] = ($fromDate === $toDate) ? $fromDate : $fromDate . ' - ' . $toDate;
			break;
		case '1':
			$timeText = 'Today';
			$rptInfo['date'] = $timeText;
			break;
		case '':
			$timeText = ($fromDate === $toDate) ? $fromDate : $fromDate . ' - ' . $toDate;
			$rptInfo['date'] = $timeText;
			$skip_in = true;
			break;
		default:
			$timeText = 'Last '.$request_info['time_frame'].' days ' . $fromDatetime . ' - ' . $toDatetime;
			$rptInfo['date'] = $fromDate . ' - ' . $toDate;
		}
	}

	if ($request_info['report_type'] === 'pricingoverview')
		$rptInfo['title'] = 'Pricing Overview' . ' ' . $timeText;
	elseif ($request_info['report_type'] === 'violationoverview')
		$rptInfo['title'] = 'Violation Overview' . ' ' . $timeText;
	elseif ($request_info['report_type'] == 'whois') {
		//$rptInfo['title'] = (bool)$request_info['is_retailer'] ? marketplace_display_name($request_info['marketplace']).' Product Listing' : marketplace_display_name($request_info['marketplace']).' Seller: '.marketplace_display_name($request_info['merchant']).' Product Listing';
        $rptInfo['title'] = "Who's Selling My Products";
    } else {
        if ( isset($request_info['product_ids']) ) {
            $rptInfo['title'] = formatInputText($request_info['product_ids'], $skip_in).' '.$timeText;
        } else {
            $rptInfo['title'] = "Price";
        }
    }

	return $rptInfo;
}

function get24ReportTitleAndDate($request_info, $year_format = 'y') {
	$rptInfo = array('title' => '', 'date' => '');
	$tF = getTimeFrame($request_info['time_frame']);
	$rptInfo['date'] = date('n/j/'.$year_format, $tF['date_from']).' - '.date('n/j/'.$year_format, $tF['date_to']);
	$timeText = 'Last 24 hours '
		.date('n/j/'.$year_format.' h:i:A', $request_info['fromDate']).' - '
		.date('n/j/'.$year_format.' h:i:A', $request_info['toDate']);
	$rptInfo['title'] = $request_info['title'].' '.$timeText;
	return $rptInfo;
}

if (!function_exists("insertErrorLog")) {
	function insertErrorLog($type='', $message='', $method='') {
		log_message('error', 'TYPE: ' . $type . ' MESSAGE: ' . $message . ' METHOD: ' . $method);
	}
}

function createHourRangeArray($strDateFrom, $strDateTo) {
	$strDateFrom = date('Y-m-d H:i:s', $strDateFrom);
	$strDateTo = date('Y-m-d H:i:s', $strDateTo);
	$aryRange = array();
	$aryRange2 = array();
	$iDateFrom = strtotime($strDateFrom);
	$iDateTo = strtotime($strDateTo);
	if ($iDateTo >= $iDateFrom) {
		$mkTime = mktime(date('H', $iDateFrom), 0, 0, date('m', $iDateFrom), date('d', $iDateFrom), date('Y', $iDateFrom));
		array_push($aryRange, $mkTime); // first entry
		array_push($aryRange2, date('m/d/y h:i A', $iDateFrom)); // first entry
		while ($iDateFrom < $iDateTo) {
			$iDateFrom+=14400; // add 24 hours
			$mkTime = mktime(date('H', $iDateFrom), 0, 0, date('m', $iDateFrom), date('d', $iDateFrom), date('Y', $iDateFrom));
			array_push($aryRange, $mkTime);
			array_push($aryRange2, date('m/d/y h:i A', $iDateFrom));
		}
	}
	return array($aryRange, $aryRange2);
}

function getLast24HoursCronIds($from='', $to='', $market='', $whois = 0) {
	$CI = & get_instance();
	$where = '';
	if
	($market != '' && $market !='all') {
		$st = '';
		if
		( (is_array($market) && !empty($market)) && !in_array('all', $market)) {
			$st = "'".implode("','", $market)."'";
		}else if
			(is_string($market)) {
				$st = '\''.$market.'\'';
			}
		if
		($st !='') $where .= ' AND api_type IN('.$st.')';
	}
	if ($from != '') {
		$from = date('Y-m-d', $from);
		$where .= ' AND start_datetime LIKE \'%'.$from.'%\'';
		$sql = "SELECT * FROM `cron_log` WHERE end_datetime != '0000-00-00 00:00:00' $where ORDER BY id DESC";
	}else {
		$from  =  date('Y-m-d H:i:s', strtotime('-24 Hours'));
		$to    =  date('Y-m-d H:i:s');
		$where .= ' AND start_datetime >= \''. $from.'\' AND start_datetime <= \''.$to.'\'';
		if
		(!$whois)
			$sql = "SELECT * FROM `cron_log` WHERE end_datetime != '0000-00-00 00:00:00' $where ORDER BY id DESC";
		else
			$sql = "SELECT * FROM `cron_log` WHERE 1 $where ORDER BY id DESC";
	}
	//echo $sql."<br>\n";
	$res = $CI->db->query($sql)->result();
	$ids = array();
	if ($res) {
		foreach ($res as $rec) {
			$ids[] = $rec->id;
		}
	}
	return implode(',', $ids);
}

function getLast24HoursCronLog($ids) {
	$CI = & get_instance();
	if ($ids != '') {
		$sql = "SELECT * FROM `cron_log` WHERE id IN ($ids)";
		$res = $CI->db->query($sql)->result();
		return $res;
	}else {
		return '';
	}
}

function last24HourCat($gdata, $split = false) {
	$range = array();
	if ($gdata) {
		foreach ($gdata as $key => $arr) {
			if
			($split) {
				$key = explode('-', $key);
				$key = $key[0];
			}
			$range[] = $key;
		}
	}
	return $range;
}

function createDateRangeArrayNew($strDateFrom, $strDateTo) {
	// takes two dates formatted as YYYY-MM-DD and creates an
	// inclusive array of the dates between the from and to dates.
	// could test validity of dates here but I'm already doing
	// that in the main script
	$strDateFrom = date('Y-m-d', $strDateFrom);
	$strDateTo = date('Y-m-d', $strDateTo);
	$aryRange = array();
	$iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
	$iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));
	if ($iDateTo >= $iDateFrom) {
		array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
		while ($iDateFrom < $iDateTo) {
			$iDateFrom+=86400; // add 24 hours
			array_push($aryRange, date('Y-m-d', $iDateFrom));
		}
	}
	return $aryRange;
}

function getPricingHistory($upc, $storeID, $type, $dateFrom, $dateTo) {
	$CI = & get_instance();
	$priceHistory = array();
	$pd = getProductIdByUPC($upc, $storeID);
	if
	(count($pd) < 1) return $priceHistory;
	//we should be getting exact time values - so don't do 24 hour day period
	/*$dateFrom = $date." 00:00:00";
	$dateTo = $date." 23:59:59";*/
	//pull original record from products table for camparison
	$query = 'select '.$type.', created_at from products WHERE id = \''.$pd['id'].'\'';
	$prodPricing = $CI->db->query($query)->result();
	//use the record if applicable
	if (count($prodPricing) == 1 && ($prodPricing[0]->created_at > $dateFrom && $prodPricing[0]->created_at < $dateTo) ) {
		$priceHistory[] = array('start' => $prodPricing[0]->created_at,
			'stamp' => ($prodPricing[0]->created_at != '0000-00-00 00:00:00' ? strtotime($prodPricing[0]->created_at) : 0),
			'price' => (float)$prodPricing[0]->$type);
	}

	$query = "SELECT pricing_value, pricing_start
			FROM products_pricing
			WHERE product_id = '{$pd['id']}'
				AND pricing_type='{$type}'
				AND pricing_start BETWEEN '{$dateFrom}' AND '{$dateTo}'
			ORDER BY  pricing_start ASC";
	$ph = $CI->db->query($query)->result();
	
	if (count($ph) > 0) {
		for ($i=0, $n=sizeof($ph); $i<$n; $i++) {
			$priceHistory[] = array(
									'start' => $ph[$i]->pricing_start,
									'stamp' => ($ph[$i]->pricing_start != '0000-00-00 00:00:00' ? strtotime($ph[$i]->pricing_start) : 0),
									'price' => (float)$ph[$i]->pricing_value
								);
		}
	}
	elseif ($prodPricing[0]->created_at < $dateFrom) {
		
		//pull original record from products table - should not already be present in $priceHistory
		$priceHistory[] = array(
								'start' => $prodPricing[0]->created_at,
								'stamp' => ($prodPricing[0]->created_at != '0000-00-00 00:00:00' ? strtotime($prodPricing[0]->created_at) : 0),
								'price' => (float)$prodPricing[0]->$type
							);
	}
	return $priceHistory;
}

// The "Promotional Pricing" function
//a better way to get current price value/type
// TODO: create a cache, or find a less busy lookup strategy.
// 2015-01-08: bypass _table_products_pricing.
function getPricePoint($upc, $storeId, $type = 'retail_price', $current = '', $range = array()) {

	$CI = & get_instance();
	
	// bypass products_pricing - use values in the products table
	$pricing = $CI->db
		->select($type)
		->where('upc_code', $upc)
		->where('store_id', $storeId)
		->limit(1)
		->get($CI->_table_products)
		->result();
		
	return (!empty($pricing[0]->$type)) ? $pricing[0]->$type : false;

	/*********************************************************/
	
	//only use products_pricing - requires abandonment of these values in the products table
	$point = 0.00;
	$curStart = '0000-00-00 00:00:00';
	//expect timestamp
	$current = $current == '' ? time(): $current;
	$current = date("Y-m-d H:i:s", $current);

	// look for the point within the start and end
	$where = array(
		'p.upc_code' => $upc,
		'ph.pricing_type' => $type,
		'ph.pricing_start <=' => $current,
		'ph.pricing_end >=' => $current
	);
	if ( ! empty($range)) {
		if(is_numeric($range['start'])){
			$range['start'] = date("Y-m-d H:i:s", $range['start']);
		}
		if(is_numeric($range['end'])){
			$range['end'] = date("Y-m-d H:i:s", $range['end']);
		}
		$where['ph.pricing_start >='] = $range['start'];
		$where['ph.pricing_start <='] = $range['end'];
	}

	$pricing = $CI->db
	->select('ph.*, p.upc_code, p.store_id')
	->join($CI->_table_products . ' p', 'p.id=ph.product_id', 'left')
	->where($where)
	->where_in('p.store_id', getStoreIdList($storeId))
	->order_by('ph.pricing_start', 'ASC')
	->get($CI->_table_products_pricing . ' ph')
	->result();
	
	if(sizeof($pricing) > 0)
	{
		for ($i=0, $n=sizeof($pricing); $i<$n; $i++)
		{
			if ($current >= $pricing[$i]->pricing_start && $current >= $curStart) {
				$point = $pricing[$i]->pricing_value;
				$curStart = $pricing[$i]->pricing_start;
			}
			elseif ($current <= $pricing[$i]->pricing_start && $current <= $curStart) {
				break;
			}
		}
	}
	else { // there is no record within the range, look normally
		$where = array(
			'p.upc_code' => $upc,
			'ph.pricing_type' => $type,
			'ph.pricing_end' => NULL
		);
		if ( ! empty($range)) {
			$where['ph.pricing_start >='] = $range['start'];
			$where['ph.pricing_start <='] = $range['end'];
		}

		$pricing = $CI->db
		->select('ph.*, p.upc_code, p.store_id')
		->join($CI->_table_products . ' p', 'p.id=ph.product_id', 'left')
		->where($where)
		->where_in('p.store_id', getStoreIdList($storeId))
		->order_by('ph.pricing_start', 'ASC')
		->get($CI->_table_products_pricing . ' ph')
		->result();

		if
		(sizeof($pricing) > 0) {
			for
			($i=0, $n=sizeof($pricing); $i<$n; $i++) {
				if
				($current >= $pricing[$i]->pricing_start && $current >= $curStart) {
					$point = $pricing[$i]->pricing_value;
					$curStart = $pricing[$i]->pricing_start;
				}elseif
				($current <= $pricing[$i]->pricing_start && $current <= $curStart) {
					break;
				}
			}
		}else { // finally, nothing has been found, pull the last record
			$where = array(
				'p.upc_code' => $upc,
				'ph.pricing_type' => $type,
			);
			if ( ! empty($range)) {
				$where['ph.pricing_start <='] = $range['end'];
			}

			$price = $CI->db
			->select('ph.*, p.upc_code, p.store_id')
			->join($CI->_table_products . ' p', 'p.id=ph.product_id', 'left')
			->where($where)
			->where_in('p.store_id', getStoreIdList($storeId))
			->order_by('ph.pricing_start', 'DESC')
			->limit(1)
			->get($CI->_table_products_pricing . ' ph')
			->result();

			$point = isset($price[0]) ? $price[0]->pricing_value : 0.00;
		}
	}

	return $point;
}

function loadPricePoints(array &$products, $ts) {
	if ( ! empty($products)) {
		for ($i = 0, $n = count($products); $i < $n; $i++) {
			$map = getPricePoint($products[$i]['upc_code'], $products[$i]['store_id'], 'price_floor', $ts);
			$retail = getPricePoint($products[$i]['upc_code'], $products[$i]['store_id'], 'retail_price', $ts);
			$wholesale = getPricePoint($products[$i]['upc_code'], $products[$i]['store_id'], 'wholesale_price', $ts);
			$product[$i]['price_floor'] = $map;
			$product[$i]['retail_price'] = $retail;
			$product[$i]['wholesale_price'] = $wholesale;
		}
	}
}

function getProductIdByUPC($upc, $storeID) {
	$CI =& get_instance();
	$rs = $CI->db
	->select('id,retail_price,wholesale_price,price_floor')
	->where('upc_code', $upc)
	->where_in('store_id', getStoreIdList($storeID))
	->limit(1)
	->get($CI->_table_products)
	->result();

	$ar = array();
	if ( ! empty($rs)) {
		$ar = array(
			'id' => $rs[0]->id,
			'wholesale_price' => $rs[0]->wholesale_price,
			'retail_price' => $rs[0]->retail_price,
			'price_floor' => $rs[0]->price_floor,
		);
	}
	return $ar;
}

function getProductUPCByID($id) {
	$CI = & get_instance();
	$query = 'SELECT id,upc_code,store_id,retail_price,wholesale_price,price_floor from products where id=\''.(int)$id.'\'';
	$rs = $CI->db->query($query)->result();
	$ar = array();
	if (count($rs) > 0) {
		$ar = array(
			'id' => $rs[0]->id,
			'store_id' => $rs[0]->store_id,
			'wholesale_price' => $rs[0]->wholesale_price,
			'retail_price' => $rs[0]->retail_price,
			'price_floor' => $rs[0]->price_floor,
			'upc_code' => $rs[0]->upc_code,
		);
	}
	return $ar;
}

function autocomp_get_team_members($store_id, $json = true) {
	$CI = &get_instance();
	$data = array();

	$teamResult = $CI->db->select('distinct(us.user_id) as user_id, u.user_name, u.email, u.first_name, u.last_name')
	->join('users u', 'us.user_id=u.id', 'left')
	->where('us.store_id', $store_id)
	->get($CI->_table_users_store.' us')
	->result_array();

	for ($i=0, $n=sizeof($teamResult); $i<$n; $i++) {
		$data[] = array('value' => $teamResult[$i]['email'],
			'label' => trim($teamResult[$i]['user_name']).' ('.$teamResult[$i]['email'].')',
			'name' => $teamResult[$i]['user_name'],
			'email' => $teamResult[$i]['email'],
			'id' => $teamResult[$i]['user_id']);
	}

	return ($json) ? json_encode($data) : $data;
}

function email_alertToTeam($function_name, $body = 'AMAZON CRAWLING ERROR', $flag=0, $description=NULL) {
	log_message('error', 'SUBJECT: ' . $function_name . ' MESSAGE: ' . $body . ' DESCRIPTION: ' . $description);
}

if (!function_exists('get_violation_image')) {
	//TODO: we need to re-think opening a page to test file existance
	//this is available in new aws sdk
	// $range is actually just a single timestamp
	function get_violation_image($data)
	{
		$CI = & get_instance();
		
		if(!empty($data['shot']))
		{
			$shot = $data['shot'];
			
			// TODO: soon to be deprecated
			if(strpos($shot, 'http:')!==false){
				if (@fopen($shot, 'r')) 
					return $shot;
				else
					return false;
			}
			
			$full_url = $CI->config->item('s3_cname') . 'stickyvision/violations/' . (string)$shot;
			
			if( substr($shot, -3,3)=='#OK'){
				return $full_url;
			}
	
			// TODO: soon to be deprecated
			if (@fopen($full_url, 'r')) {
				return $full_url;
			}
			
			return false;
		}
		
		// use this wasteful method as a last resort
		$CI->load->model('products_trends_m', 'ProductsTrends');
		
		try {
			$hash_key = $data['hash_key'];
			$range = $data['timestamp'];
			$priceTrends = $CI->ProductsTrends->get_by_hashkey_and_date_range($hash_key, $range, $range);
			foreach($priceTrends->result_object() as $response){
				if (!empty($response->ss)) {
					$full_path = 'stickyvision/violations/' . (string)$response->ss;
					$full_url = $CI->config->item('s3_cname') . $full_path;
					if (@fopen($full_url, 'r')) {
						return $full_url;
					}
				}
			}
			return false;
		}
		catch (Exception $e) {
			log_message('error', 'get_violation_image() error - ' . $e->getMessage());
			return false;
		}
		return false;
	}
}

if (!function_exists('get_bucket_image')) {
	function get_bucket_image($image_name) {
		if (trim($image_name)) {
			$CI = & get_instance();
			$cname = $CI->config->item('s3_cname');
			//echo $cname.$image_name;
			if (@fopen($cname.$image_name, 'r')) {
				return $cname.$image_name;
			}
		}
		return false;
	}
}

function getProxyIPS($use_flag = NULL, $limit = 1, $offset = 0) {
	$CI =& get_instance();

	if ( ! is_null($use_flag))
		$CI->db->where('use_flag', (boolean)$use_flag);

	$ret = $CI->db
	->order_by('use_flag', 'random')
	->get($CI->_table_proxy_ips, $limit, $offset)
	->result();

	return $ret;
}

function updateProxyFlag($proxy_id = '', $flag = TRUE) {
	$CI =& get_instance();

	$ret = $CI->db
	->set('use_flag', $flag ? '1' : '0')
	->where('id', (int)$proxy_id)
	->update($CI->_table_proxy_ips);

	return $ret;
}

function upload_to_amazon_graphImage($file_name, $path = '', $sub_folder_name = '') {
	$CI = & get_instance();
	$CI->load->library('S3');
	$bucket_name = $CI->config->item('s3_bucket_name');
	if ($path == '') {
		$path = $CI->config->item('csv_upload_path');
	}
	$s3 = new S3($CI->config->item('s3_access_key'), $CI->config->item('s3_secret_key'));
	$folder_name = rtrim('stickyvision/graph_images/' . $sub_folder_name, '/') . '/';
	$ret = false;
	if (file_exists($path.$file_name)) {
		if ($s3->putObjectFile($path.$file_name, $bucket_name, $folder_name.$file_name, S3::ACL_PUBLIC_READ)) {
			unlink($path.$file_name);
			$ret = $file_name;
		}
	}

	return $ret;
}

if
(!function_exists('in_array_r')) {
	function in_array_r($needle, $haystack, $strict = true) {
		foreach ($haystack as $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
				return true;
			}
		}
		return false;
	}
}

function getSecondsArray($time1, $time2) {
	//echo "TIME : .$time1=>$time2";
	$start = $time1;
	$end   = $time2;
	$secondsarray = array();
	$count = 0;
	for
	($start;$start<=$end;) {
		$count++;
		$secondsarray[] = $start;
		$start = strtotime('+1 seconds', $start);
		if
		($count == 1000)break;
	}
	//debug('Second Array',$secondsarray,2);
	return $secondsarray;
}

function getMerchantName($merchantId) {
	$CI = & get_instance();
	$CI->db->mysql_cache();
	$rs = $CI->db->query("select original_name FROM crowl_merchant_name_new where seller_id='$merchantId'")->result();
	return trim($rs[0]->original_name);
}

function randomColor() {
	return substr(md5(rand()), 0, 6);
}

function requestInfoWhereValues(array $request_info, &$marketFilter = NULL, &$order_by = NULL) {
	$ci =& get_instance();
	$where = array();

	//
	// Product WHERE statement
	//
	if
	( ! empty($request_info['product_ids'])) {
		$request_info['product_ids'] = array_filter($request_info['product_ids']);
		$strProduct = implode(',', $request_info['product_ids']);
		$where['products'] = "IN (".$ci->db->escape_str($strProduct).")" ;
		// TODO: fix orderby
		//$order_by = " ORDER BY FIELD(products.id, ".trim($strProduct, ',').")";
	}

	//
	// Marketplaces WHERE statement
	//
	$marketFilter = array();
	if
	(isset($request_info['api_type']) && is_array($request_info['api_type'])) {
		foreach
		($request_info['api_type'] as $val) {
			if
			($val !== 'all')
				$marketFilter[] = strtolower($val);
		}
		if
		(isset($request_info['api_type'][0]) && $request_info['api_type'][0]!='all') {
			$strMarkets = implode(",", array_map(array($ci->db, 'escape'), $marketFilter));
			$where['marketplaces'] = "IN (".$strMarkets.")";
		}
	}

	//
	// Merchants WHERE statement
	//
	if
	(isset($request_info['merchants'][0]) && $request_info['merchants'][0]!='all') {
		$strMerchants = implode(",", array_map(array($ci->db, 'escape'), $request_info['merchants']));
		$where['merchants'] = "IN (".$strMerchants.")";
	}

	//
	// Store WHERE statement
	//

	if
	( ! empty($request_info['competitor_store_id']) && $request_info['competitor_store_id'][0] !== false) {
		$where['store'] = 'IN (' . getStoreIdList($request_info['competitor_store_id'], TRUE) . ')';
	}
	elseif
	( ! empty($request_info['store_id'])) {
		$where['store'] = 'IN (' . getStoreIdList($request_info['store_id'], TRUE) . ')';
	}

	return $where;
}

function getBadUPCs($store_id) {
	$CI =& get_instance();
	$CI->load->model('products_m');

	return $CI->products_m->getBadUpcByStoreId($store_id);
}

/**
 * Get the field names of a table with the option to exclude
 *
 * @param String $table
 * @param array $exclude
 * @return array/FALSE
 */
function getTableFields($table, array $exclude = array()) {
	$CI =& get_instance();
	$ret = $CI->db->list_fields($table);
	if ( ! empty($exclude) and ! empty($ret)) {
		$ret = array_flip($ret);
		foreach ($exclude as $key)
			unset($ret[$key]);
		$ret = array_flip($ret);
	}

	return $ret;
}

function daysBetween($start, $end) {
	$date1 = new DateTime($start);
	$date2 = new DateTime($end);
	$interval = $date1->diff($date2);

	return $interval->days;
}


function createCSV(array $data, $name = 'catalog') {
	//$csv = '';

	$delimiter = ',';
	$enclosure = '"';
	
	// Open a memory "file" for read/write...
	$fp = fopen('php://temp', 'r+');
	
	for ($i = 0, $n = count($data); $i < $n; $i++) {
		if ( ! is_array($data[$i]))
			throw new UnexpectedValueException('CSV data must be an array of arrays where each array is a row.');
		
		// ... write the $input array to the "file" using fputcsv()...
		fputcsv($fp, $data[$i], $delimiter, $enclosure);
		//$csv .= implode(',', $data[$i]) . "\n";
	}
	
	// ... rewind the "file" so we can read what we just wrote...
	rewind($fp);
	
	// ... read the entire line into a variable...
	$csv = fread($fp, 1048576);
	
	// ... close the "file"...
	fclose($fp);

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"$name.csv\";" );
	header("Content-Transfer-Encoding: binary");

	echo $csv;
}

function createPDF(array $data, $name = 'catalog') {
	$CI =& get_instance();
        $html = '';
        $html .='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                </head>
                <body style="padding-bottom: 50px;">
                    <div style=" margin:0 auto; width:760px; color:#666; font-family:Arial, Helvetica, sans-serif">
                        <table border="0" cellpadding="0" cellspacing="0" width="760">
                            <tr>
                                <td width="515" align="left">
                                    <table cellpadding="0" cellspacing="5" width="465" border="0">
					<tr>
                                            <td width="175" valign="bottom"></td>
					</tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td width="660" style="height:8px; border-bottom: yellow;" colspan="2">&nbsp;</td>
                            </tr>
                            <tr>
                                <td height="10" colspan="2"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-size:35px;color:#ff; padding:5px;" bgcolor="#00a0d1">
                                    <table cellspacing="0" cellpadding="0" width="760">
                                        <tr>
                                            <td width="10">&nbsp;</td>
                                            <td width="640">Products</td>
                                            <td width="10">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td height="10" colspan="2"></td>
                            </tr>
                            <p style="margin:0px 0; color:#000; font-size:30px;">Products</p>
                            <table cellpadding="2" cellspacing="0" width="100%" style=" background:#fff; font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#666666;">
                            ';
                            $count = 0;
                            for ($i = 0, $n = count($data); $i < $n; $i++) {
                                if(is_array($data[$i]) && count($data[$i])) {
                                    $bgcolor = (!$count%2) ? 'bgcolor="#E7E7E8"' : '';
                                    $html .= '<tr $bgcolor>';
                                    //foreach($data[$i] as $d) {
                                    for ($j = 0; $j < count($data[$i]); $j++) {
                                        if(!$count) {
                                            $html.="<th>".$data[$i][$j]."</th>";
                                        }
                                        else {
                                            $html.="<td>".$data[$i][$j]."</td>";
                                        }
                                    }
                                    $html .= "</tr>";
                                }
                                $count++;
                            }
                        $html .='<tr><td colspan="6" height="10"></td></tr>'
                            . '</table>
                               <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td height="10"></td>
                                    </tr>
                                    <tr>
                                        <td width="660" style="height:8px; display:block; border-bottom: yellow;">&nbsp;</td>
                                    </tr>
                                </table>
                            </div>
                        </body>
                    </html>';
        $CI->load->helper('pdf');
        $file = $name.".pdf";
        echo tcpdf_write($html, $file, tcpdf_options('reports'));
        
        header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"$file\";" );
	header("Content-Transfer-Encoding: binary");
        exit;
}

/* End of file kalacomm_helper.php */
/* Location: ./system/application/helpers/kalacomm_helper.php */
