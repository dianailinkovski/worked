<?php

/**
 *
 * function confirm_dialog
 *
 *
 */
function confirm_dialog() {
	return '<div id="confirm-dialog" style="display: none">
    <div class="dlg-content">
      <div class="dlg-row mt10" id="confirm_message">
      </div>
    </div>
    <div id="dlg-bottom-btns" class="dlg-row">
      <label>&nbsp;</label>
      <a class="qaw-buton-gray qaw-button mr10" href="javascript:;" onclick="document.body.style.cursor = \'\';hideJModalDialog(\'confirm-dialog\');"><span>Cancel</span></a>
      <a class="qaw-buton-gray qaw-button mr10" href="javascript:;" id="confirm_ok" ><span>Ok</span></a>
    </div>
  </div>';
}

/**
 *
 * function alert_dialog
 *
 *
 */
function alert_dialog() {
	return '<div id="alert-dialog" style="display: none">
    <div class="dlg-content">
      <div class="dlg-row mt10" id="alert_message">
      </div>
    </div>
    <div id="dlg-bottom-btns" class="dlg-row">
      <label>&nbsp;</label>
      <a class="qaw-buton-gray qaw-button mr10" href="javascript:;" onclick="document.body.style.cursor = \'\';hideJModalDialog(\'alert-dialog\');"><span>Ok</span></a>
    </div>
  </div>';
}

/**
 *
 * function getPageTitle
 *
 *
 */
function getPageTitle() {
	$CI = & get_instance();

	$page_index = $CI->uri - segment(1).'/'.$CI->uri - segment(2);
	$title = array(
		'catalog/index' => 'Catalog',
		'settings/edit_store' => 'Settings',
		'pricingoverview/' => 'Reports',
		'reports/' => 'Reports',
		'reports/show' => 'Reports',
		'' => ''
	);

	return isset($title[$page_index]) ? 'TrackStreet :: '.$title[$page_index] : 'TrackStreet';
}

/* global utlitly */

function use_javascript($file) {
	$CI = & get_instance();

	$file = trim($file);

	if (strpos($file, 'http://') === false) {
		$file = base_url().'js/'.$file;
	}

	if (substr($file, strlen($file) - 3, 3) != '.js') {
		$file .= '.js';
	}

	if (!isset($CI->javascript_array)) {
		$CI->javascript_array = array();
	}

	if (!in_array($file, $CI->javascript_array)) {
		$CI->javascript_array[] = $file;
	}
}

function include_javascript() {
	$CI = & get_instance();

	if (!isset($CI->javascript_array))
		return false;

	foreach ($CI->javascript_array as $js_file) {
		echo '<script type="text/javascript" src="'.$js_file.'"></script>';
	}
}

/**
 *
 * function pie_graph_marketplace_products
 *
 * @param <array>     $marketplace_products
 *
 */
function pie_graph_marketplace_products($marketplace_products) {
	$graphData = array();
	$total = 0;

	foreach ($marketplace_products as $key => $data) {
		$total += $data['total_products'];
	}

	foreach ($marketplace_products as $key => $data) {
		$graphData[ucfirst($data['marketplace'])] = round($data['total_products'] / $total * 100, 2);
	}

	return json_encode($graphData);
}

/**
 *
 * function myproducts_group_by_market
 *
 * @param <array>     $products
 *
 */
function myproducts_group_by_market($products) {
	$data = array();
	$total = 0;

	foreach ($products as $key => $row) {
		$data[ucfirst($row['marketplace'])][] = $row;
	}

	return $data;
}

/**
 *
 * function myproducts_products_request
 *
 * @param <string>     $marketplace
 * @param <string>     $merchant
 *
 */
function myproducts_products_request($marketplace, $merchantId, $store_id = '', $whois = 0) {
	$merchantexp = explode(',', $merchantId);
	$merchant = $merchantexp[0];
	return array(
		'api_type' => array($marketplace),
		'merchants' => array($merchant),
		'fromDate' => date('Y-m-d'),
		'toDate' => date('Y-m-d'),
		'time_frame' => '24',
		'store_id' => $store_id,
		'cron_ids' => getLast24HoursCronIds('', '', $marketplace, $whois)//add market place optional
	);
}

/**
 *
 * function myproducts_merchant_product_request
 *
 * @param <string>     $marketplace
 * @param <string>     $merchant
 * @param <string>     $to
 * @param <string>     $from
 *
 */
function myproducts_merchant_product_request($marketplace, $merchant, $product_id, $store_id, $to, $from, $time_frame = '24') {

	return array(
		'api_type' => array($marketplace),
		'merchants' => array($merchant),
		'fromDate' => $from,
		'toDate' => $to,
		'productIds' => array($product_id),
		'time_frame' => $time_frame,
		'store_id' => $store_id,
		'cron_ids' => getLast24HoursCronIds(strtotime($from), strtotime($to), $marketplace)//add market place optional
	);
}

/**
 *
 * function compress_HTML
 *
 * @param <string>     $buffer
 *
 */
function compress_HTML($buffer) {
	$search = array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s');
	$replace = array('>', '<', '\\1');
	$buffer = preg_replace($search, $replace, $buffer);
	return $buffer;
}

/**
 *
 * function pdf_write
 *
 * @param <array|string>     $html
 * @param <string>    $file_name
 *
 */
function pdf_write($html, $file_name, $barcode = '') {
	// create new PDF document
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->setPrintHeader(false);
	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
	// set header and footer fonts
	$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', '7px'));
	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	if (trim($barcode)) {
		$pdf->setBarcode($barcode);
	}

	//set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set default font subsetting mode
	$pdf->setFontSubsetting(true);

	// Set font
	// dejavusans is a UTF-8 Unicode font, if you only need to
	// print standard ASCII chars, you can use core fonts like
	// helvetica or times to reduce file size.
	$pdf->SetFont('helvetica', '', 5, '', true);

	// Add a page
	// This method has several options, check the source code documentation for more information.
	// Print text using writeHTML()
	if (is_array($html)) {
		foreach ($html as $htm_) {
			$pdf->AddPage();
			$pdf->writeHTML(compress_HTML($htm_), true, false, true, false);
		}
	} else {
		$pdf->AddPage();
		$html = compress_HTML($html);
		$html = str_replace(array('<head>', '<body>', '</head>', '</body>', '</html>', '<html>', '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">', '<meta http-equiv="content-type" content="text/html; charset=utf-8" />'), array('', '', '', '', '', '', '', ''), $html);

		$pdf->writeHTML($html, true, false, true, false);
	}

	$filePath = dirname(BASEPATH)."/warehouse/$file_name";

	// Close and output PDF document
	$pdf->Output($file_name, 'I');
}

/**
 *
 *
 *
 *
 */
function marketplace_display_name($marketplace) {
	$marketplace = strtolower($marketplace);
	$ci = get_instance();

	//
	// Check if we have accessed the display name already
	//

	$display_names = $ci->config->item('display_names');
	if ( ! isset($display_names[$marketplace])) {

		if ( ! isset($ci->Marketplace))
			$ci->load->model('marketplace_m', 'Marketplace');

		//
		// Save the display name for future use
		//

		$display_names[$marketplace] = $ci->Marketplace->display_name($marketplace);
		$ci->config->set_item('display_names', $display_names);
	}

	return $display_names[$marketplace];
}

/**
 * function marketplace_graph_color
 *
 */
function marketplace_graph_color($marketplace) {
	$CI =& get_instance();
	$colors = $CI->config->item('market_colors');
	return isset($colors[$marketplace]) ? $colors[$marketplace] : stringToColorCode($marketplace);
}

function stringToColorCode($str) {
  $code = dechex(crc32($str));
  $code = substr($code, 0, 6);
  //$code = colourBrightness($code, 0.75);
  return $code;
}
function colourBrightness($hex, $percent) {
	// Work out if hash given
	$hash = '';
	if (stristr($hex,'#')) {
		$hex = str_replace('#','',$hex);
		$hash = '#';
	}
	/// HEX TO RGB
	$rgb = array(hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2)));
	//// CALCULATE 
	for ($i=0; $i<3; $i++) {
		// See if brighter or darker
		if ($percent > 0) {
			// Lighter
			$rgb[$i] = round($rgb[$i] * $percent) + round(255 * (1-$percent));
		} else {
			// Darker
			$positivePercent = $percent - ($percent*2);
			$rgb[$i] = round($rgb[$i] * $positivePercent) + round(0 * (1-$positivePercent));
		}
		// In case rounding up causes us to go to 256
		if ($rgb[$i] > 255) {
			$rgb[$i] = 255;
		}
	}
	//// RBG to Hex
	$hex = '';
	for($i=0; $i < 3; $i++) {
		// Convert the decimal digit to hex
		$hexDigit = dechex($rgb[$i]);
		// Add a leading zero if necessary
		if(strlen($hexDigit) == 1) {
		$hexDigit = "0" . $hexDigit;
		}
		// Append to the hex string
		$hex .= $hexDigit;
	}
	return $hash.$hex;
}

/**
 * function p_array
 */
if (!function_exists('p_array')) {

	function p_array($arr, $exit = false) {
		echo '<pre>';
		print_r($arr);
		echo '</pre>';

		if ($exit)
			exit();
	}

}

/**
 * function getDiffBwDates
 */
if (!function_exists('getDiffBwDates')) {

	function getDiffBwDates($to, $from, $return = 'day') {

		if ($to > $from) {
			$dateDiff = $to - $from;
		} else {
			$dateDiff = $from - $to;
		}


		$fullDays = floor($dateDiff / (60 * 60 * 24));
		$fullHours = floor(($dateDiff - ($fullDays * 60 * 60 * 24)) / (60 * 60));
		$fullMinutes = floor(($dateDiff - ($fullDays * 60 * 60 * 24) - ($fullHours * 60 * 60)) / 60);

		if ($return == 'day')
			return $fullDays;
		else if ($return == 'hr')
				return $fullHours;
			else if ($return == 'min')
					return $fullMinutes;
				else
					return array($fullMinutes, $fullHours, $fullDays);
	}

}

/**
 *
 * function array_map_time_stamp
 *
 *
 */
function array_map_time_stamp($value) {
	return strtotime($value);
}

/**
 *
 * function array_map_date
 *
 *
 */
function array_map_date($value) {
	return date('m/d/y h:i A', $value);
}

/**
 *
 * function mv_sort_timestamps
 *
 *
 */
function mv_sort_timestamps($data) {
	$data = array_map('array_map_time_stamp', $data);
	sort($data);

	return array_map('array_map_date', $data);
}

function get_merchant_logo_url($logo) {
	$CI = & get_instance();
	if ($logo != "") {
		list($pre, $ext) = explode('.', $logo);
		$logo = $CI->config->item('s3_cname').'stickyvision/brand_logos/'.$pre.'_thumb.'.$ext;
		if (@fopen($logo, 'r')) {
			return $logo;
		}else {
			//i don't like this image placeholder
			$logo = '';//base_url().'images/no_bio_image.gif';
		}
	} else {
		//i don't like this image placeholder
		$logo = '';//base_url().'images/no_bio_image.gif';
	}
	return $logo;
}

/**
 * function getQueryStringFromUrl
 *
 */
function getQueryStringFromUrl($url) {
	$parts = parse_url($url);

	if ( ! $parts) // try to recover from a parse error
		{
		if (strpos($url, '/') === 0) {
			$url = str_replace('://', '%3A%2F%2F', $url);
			$parts = parse_url($url);
		}
	}

	$output = array();
	if (isset($parts['query'])) {
		parse_str(urldecode(str_replace('&amp;', '&', $parts['query'])), $output);
	}

	return $output;
}


/**
 * function clearnSellerId
 */
function clearnSellerId($seller_id) {
	$pattern = array("/\n(.*)\n/", "/\/(.*)/", "/\r(.*)\r/", "/\n/", "/\r/", "/_(.*)_/", "/[^A-Z0-9]/");
	$replace = '';
	$preg = preg_replace($pattern, $replace, $seller_id);
	return $preg;
}

/**
 * function clearnSellerName
 */
function clearnSellerName($seller_name) {
	$pattern = array("/\n(.*)\n/", "/\/(.*)/", "/\r(.*)\r/", "/\n/", "/\r/");
	$replace = array('', '', '', '', '');
	$preg = preg_replace($pattern, $replace, $seller_name);
	return $preg;
}

function getSellerNameAmazon($sellerID) {
	if (!class_exists('simple_html_dom_node')) require_once(dirname(BASEPATH).'/system/application/libraries/simple_html_dom.php');
	//require_once(dirname(BASEPATH).'/application/libraries/simple_html_dom.php');

	$sellerName = '';
	if ($sellerID != '') {
		$url = 'http://www.amazon.com/gp/aag/main?ie=UTF8&asin=&isAmazonFulfilled=0&isCBA=&marketplaceID=ATVPDKIKX0DER&seller='.$sellerID;
		$html = file_get_html($url);

		if (is_object($html)) {
			$div = $html->find('div[id="aag_header"]', 0);
			if ($div) {
				$h1 = $div->find('h1', 0);
				if ($h1) {
					$sellerName = $h1->plaintext;
				}
			}
			$html->clear();
		}
	}
	return $sellerName;
}

/**
 * get minimul valye from array
 *
 */

function getMinIdFromString($str) {

	$ids = explode(',', $str);

	sort($ids);

	return $ids[0];

}

function renderSelectBox($name, $values, $selected = 0, $style = '') {
	$str = '<select name="'.$name.'" id="'.$name.'" class="'.$style.'">';
	foreach ($values as $key => $val) {
		$check = '';
		if ($key == $selected) {
			$check = ' selected="selected"';
		}
		$str .= '<option value="'.$key.'"'.$check.'>'.$val.'</option>';
	}
	$str .= "</select>";
	return $str;
}

function renderHourDropDown($name, $selected = 1) {
	$values = range(1, 12);
	$values = fillArryKeyWithValue(addLeadingZeroInArray($values));
	return renderSelectBox($name, $values, $selected);
}

function renderMinuteDropDown($name, $selected = 1) {
	$values = range(0, 55, 5);
	$values = fillArryKeyWithValue(addLeadingZeroInArray($values));
	return renderSelectBox($name, $values, $selected);
}

function renderSecondDropDown($name, $selected = 1) {
	$values = range(0, 59);
	$values = fillArryKeyWithValue(addLeadingZeroInArray($values));
	return renderSelectBox($name, $values, $selected);
}

function addLeadingZeroInArray($arr) {
	foreach ($arr as $key => $val) {

		if ($val > 10)
			break;

		$value = checkForLeadingZero($val);
		$arr[$key] = $value;
	}

	return $arr;
}

function checkForLeadingZero($val) {
	if ($val < 10)
		return '0'.$val;
	else
		return $val;
}

function fillArryKeyWithValue($array) {
	$new_array = array();

	foreach ($array as $val) {
		$new_array[$val] = $val;
	}
	return $new_array;
}


function getNextCronTime($base = '') {
	if ($base == '') {
		$base = date('Y-m-d h:i a');
	}
	$time = strtotime($base);
	$min = date('i', strtotime($base));
	$first_diff = $min % 5;
	$add_mins = 5;
	if ($first_diff != 0) {
		$add_mins = $add_mins + (5 - $first_diff);
	}

	return date('Y-m-d h:i a', strtotime("+".$add_mins." mins", $time));
}