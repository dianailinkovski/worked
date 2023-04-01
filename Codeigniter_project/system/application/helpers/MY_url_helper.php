<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MY_url_helper.php
 *
 * An extension of the CI URL helper
 */


/**
 * Test a URL to see if it exists and can be downloaded
 */
function is_url_exist($url){
    $ch = curl_init($url);    
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if($code == 200){
		$status = true;
    }else{
echo "<!-- FOO $url, $code -->"; 
		$status = false;
    }
    curl_close($ch);
	return $status;
}


/**
 * Generate a dropdown of the months in the Gregorian Calendar
 * with option values of 1-12
 *
 * @param String $name
 * @param int $selected
 * @param String $extra
 * @return String
 */
function monthDropdown($name = "month", $selected = null, $extra = null) {
	if ($extra == null)
		$dd = '<select name="' . $name . '" id="' . $name . '">';
	else
		$dd = '<select name="' . $name . '" id="' . $name . '" onChange="' . $extra . '">';

	$months = array(
		1 => 'January',
		2 => 'February',
		3 => 'March',
		4 => 'April',
		5 => 'May',
		6 => 'June',
		7 => 'July',
		8 => 'August',
		9 => 'September',
		10 => 'October',
		11 => 'November',
		12 => 'December');
	/*	 * * the current month ** */
	$selected = is_null($selected) ? date('n', time()) : $selected;

	for ($i = 1; $i <= 12; $i++) {
		$dd .= '<option value="' . $i . '"';
		if ($i == $selected) {
			$dd .= ' selected';
		}
		/*		 * * get the month ** */
		$dd .= '>' . $months[$i] . '</option>';
	}
	$dd .= '</select>';

	return $dd;
}

/**
 * Generate a dropdown of years
 *
 * @param int $start_year
 * @param int $end_year
 * @param String $id
 * @param int $selected
 * @return String
 */
function createYears($start_year, $end_year, $id = 'year_select', $selected = null) {

	/*	 * * the current year ** */
	$selected = is_null($selected) ? date('Y') : $selected;

	/*	 * * range of years ** */
	$r = range($start_year, $end_year);

	/*	 * * create the select ** */
	$select = '<select name="' . $id . '" id="' . $id . '">';
	foreach ($r as $year) {
		$select .= "<option value=\"$year\"";
		$select .= ($year == $selected) ? ' selected="selected"' : '';
		$select .= ">$year</option>\n";
	}
	$select .= '</select>';

	return $select;
}

/**
 * Front URL
 *
 * Returns the "base_url_deals_images" item from your config file
 *
 * @access	public
 * @return	string
 */
function frontImageUrl() {
	$CI = & get_instance();
	return $CI->config->slash_item('base_url_front_images');
}

/**
 * Generate the URL for a public facing page
 *
 * @alias MY_Config::public_url()
 * @param String $uri { defaul : '' }
 * @return String
 */
function public_url($uri = '')
{
	$CI =& get_instance();
	return $CI->config->public_url($uri);
}

function create_sub($controller, $id, $sub_id) {
	//title="The package is Subscribed. Click this link to Un-Subscribe."
	//title="The package is not Subscribed. Click this link to Subscribe."
	if ($id != NULL || $id != 0)
		return '<a href="javascript:void(0);" onclick="javascript:unSubscribe(\'' . $controller . '\', ' . $id . ', ' . $sub_id . ');">UnSubscribe</a>';
	else
		return '<a href="javascript:void(0);" onclick="javascript:Subscribe(\'' . $controller . '\', ' . $id . ', ' . $sub_id . ');">Subscribe</a>';
}

function javascriptUrl() {
	$CI = & get_instance();
	return $CI->config->slash_item('base_url_javascript');
}

function fronbaseUrl() {
	return str_replace("index.php", "", site_url());
}

function check_enable($uri, $value, $id, $act = 0) {
	if ($value == 0) {
		if ($act == 0)
			return '<a href="javascript:void(0);" class="clickTip exampleTip" title="This is turned off, click to turn on." onclick="javascript:enable_it(' . $id . ', \'0\', \'' . $uri . '\');"><img src="' . frontImageUrl() . 'ico_red.png" width="17" height="17" /></a>';
		else
			return '<a href="javascript:void(0);" class="clickTip exampleTip" title="This is turned off, click to turn on." onclick="javascript:enable_it(' . $id . ', \'1\', \'' . $uri . '\');"><img src="' . frontImageUrl() . 'ico_red.png" width="17" height="17" /></a>';
	}
	else {
		if ($act == 0)
			return '<a href="javascript:void(0);" class="clickTip exampleTip" title="This is now on, click to turn off." onclick="javascript:disable_it(' . $id . ', \'0\', \'' . $uri . '\');"><img src="' . frontImageUrl() . 'ico_green.png" width="17" height="17" /></a>';
		else
			return '<a href="javascript:void(0);" class="clickTip exampleTip" title="This is now on, click to turn off." onclick="javascript:disable_it(' . $id . ', \'1\', \'' . $uri . '\');"><img src="' . frontImageUrl() . 'ico_green.png" width="17" height="17" /></a>';
	}
}

////------------Connect FTP-------------------------------////////////
function connect_ftp($host = '', $user = '', $pass = '', $dir_path = '', $file = '', $remote_file = '') {
	$CI = & get_instance();

	$connection = @ftp_connect($host) or die('Could not connect to the FTP.');
	if (@ftp_login($connection, $user, $pass)) {
		//echo ftp_pwd($connection)."<br>";
		$pwd = @ftp_pwd($connection);
		if (strlen($pwd) == 1 && $pwd == '/') {
			if (substr($dir_path, 0, 1) == '/')
				$dir_path = substr($dir_path, 1, -1);
		}
		else {
			if (substr($dir_path, 0, 1) != '/')
				$dir_path = '/' . $dir_path;
		}
		$chdir = @ftp_chdir($connection, @ftp_pwd($connection) . $dir_path);
		if (!$chdir) {
			$dir = split("/", @ftp_pwd($connection) . $dir_path);
			$path = "";
			$ret = true;

			for ($i = 0; $i < count($dir); $i++) {
				$path.="/" . $dir[$i];
				// echo "$path<br>";
				if (!@ftp_chdir($connection, $path)) {
					@ftp_chdir($connection, "/");
					if (!@ftp_mkdir($connection, $path)) {
						$ret = false;
						break;
					}
				}
			}
			//@ftp_mkdir($connection, @ftp_pwd($connection).$dir);
			//$chdir = @ftp_chdir($connection, ftp_pwd($connection).$dir);
		}
		//echo ftp_pwd($connection)."<br>";
		$csv_upload_path = $CI->config->item('csv_upload_path');
		if (@ftp_put($connection, $file, $csv_upload_path . $remote_file, FTP_ASCII)) {
			@ftp_close($connection);
			@unlink($csv_upload_path . $remote_file);
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

/**
 * Get the domain of a URL
 *
 * @param String $domain
 * @return String
 */
function extract_domain($domain)
{
	if (strpos($domain, '://') === false) {
		$domain = 'http://' . $domain;
	}
	$parts = parse_url($domain);
	$domain = isset($parts['host']) ? $parts['host'] : '';
    if(preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $domain, $matches))
        return $matches['domain'];
    else
        return $domain;
}

/**
 * Get the subdomains of a URL
 *
 * @param String $domain
 * @return String
 */
function extract_subdomains($domain)
{
    $subdomains = $domain;
    $domain = extract_domain($subdomains);

    $subdomains = rtrim(strstr($subdomains, $domain, true), '.');

    return $subdomains;
}

/* End of file MY_url_helper.php */
/* Location: ./system/application/helpers/MY_url_helper.php */