<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * php_helper.php
 *
 * Basic functions missing from PHP
 */

/**
 * Determines if a variable is iterable
 *
 * @param mixed $var
 * @return boolean
 */
function is_iterable($var)
{
    return (is_array($var) OR $var instanceof stdClass);
}

/**
 * Generate a random hash (sha1)
 */
function random_hash()
{
	mt_srand();
	$result = "";
	$charPool = '0123456789abcdefghijklmnopqrstuvwxyz';
	$len = strlen($charPool)-1;
	for($p = 0; $p < 20; $p++)
		$result .= $charPool[mt_rand(0,$len)];
	$result .= mt_rand();

	return sha1($result);
}

/**
 * Return a new array with the values set to the key/value pair glued together
 *
 * @param String $glue
 * @param Iterable $pieces
 *
 * @return array
 */
function implode_assoc($glue, $pieces)
{
	if ( ! is_iterable($pieces))
		throw new UnexpectedValueException('Data must be iterable.', 2526);	// iterable expected

	$ret = array();
	foreach ($pieces as $key => $val) {
		$ret[] = $key . $glue . $val;
	}

	return $ret;
}

/**
 * Check that a phone number is in the
 * form 555-555-1234 or 1-800-555-1234
 *
 * @param String $phone
 *
 * @return bool
 */
function is_valid_phone($phone)
{
	return preg_match("/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $phone);
}

/**
 * Strip all non digits and the leading 1 from a phone number.
 * Returns true if the resulting number is 10 digits.
 *
 * @param String &$phone
 *
 * @return bool
 */
function prep_phone(&$phone)
{
	$phone = preg_replace('/[\D]/', '', $phone);
	if (strlen($phone) > 10 AND $phone{0} === '1')
		$phone = substr($phone, 1);

	return strlen($phone) === 10;
}

/**
 * Format a US phone number
 *
 * @param String $phone
 * @param String $delimiter { default : NULL }
 *
 * @return String
 */
function format_phone($phone, $delimiter = NULL)
{
	$tmp = preg_replace('/[\D]/', '', $phone);

	if (strlen($tmp) != 10)
		return $phone;

	$area = substr($tmp, 0, 3);
	$prefix = substr($tmp, 3, 3);
	$line = substr($tmp, -4);

	if (is_null($delimiter)) {
		$delimiter = '-';
		$phone = '(' . $area . ') ' . $prefix;
	}
	else {
		$phone = $area . $delimiter . $prefix;
	}

	return $phone . $delimiter . $line;
}

/**
 * Make a hashmap from an array
 *
 * @param array $lookup
 * @return array
 */
function array_lookup(array $lookup)
{
	return ! empty($lookup) ? array_combine($lookup, $lookup) : array();
}

/**
 * Convert an array to lowercase
 *
 * @param array $arr
 * @return array
 */
function array_to_lower(array $arr)
{
	return ! empty($arr) ? unserialize(strtolower(serialize($arr))) : $arr;
}

/**
 * Specify default values for array keys
 *
 * @param array $arr
 * @param array $defaults
 * @return array
 */
function array_default(array $arr, array $defaults)
{
	foreach ($defaults as $key => $value)
		if ( ! isset($arr[$key]))
			$arr[$key] = $value;

	return $arr;
}

/**
 * Get a value in an array. If the value is an array
 * then simply return the value
 *
 * @param mixed $val
 * @return array
 */
function ensure_array($val)
{
	return is_array($val) ? $val : array($val);
}

/**
 * Check if an array is associative
 *
 * @param array $array
 * @return boolean
 */
function is_assoc(array $array)
{
	return (boolean)count(array_filter(array_keys($array), 'is_string'));
}

/**
 * Make a string safe to be a filename
 *
 * @param String $str
 * @return String
 */
function strtofn($str) {
	$replace = array(
		'#'  => '_',
		' '  => '_',
		"'"  => '',
		'"'  => '',
		'__' => '_',
		'&'  => 'and',
		'/'  => '_',
		'\\' => '_',
		'?'  => ''
	);

	$str = strtolower($str);
	$str = str_replace(array_keys($replace), array_values($replace), $str);

	return $str;
}

/**
 * Print data in json format and exit the application
 *
 * @param mixed $data
 * @param boolean $send_header { default : TRUE }
 */
function ajax_return($data, $send_header = TRUE)
{
	if ($send_header)
		header("Content-type: application/json");

		//don't cache ajax responses
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	echo json_encode($data);
	exit;
}

/**
 * Convert any type variable to a string representation
 *
 * @param mixed $data
 * @param String $type { default : 'print_r' }
 * @return String
 */
function data_to_string($data, $type = 'print_r')
{
	switch ($type)
	{
		case 'var_dump':
			ob_start();
			var_dump($data);
			$data = ob_get_contents();
			ob_end_clean();
			break;
		case 'var_export':
			$data = var_export($data, TRUE);
			break;
		case 'print_r':
		default:
			if ($data === FALSE)
				$data = '(boolean)FALSE';
			elseif ($data === TRUE)
				$data = '(boolean)TRUE';
			elseif ($data === NULL)
				$data = 'NULL';
			else
				$data = print_r($data, TRUE);
	}

	return $data;
}

/**
 * Generate a query string with the option to exclude key/value pairs
 *
 * @param array/String $exclude
 * @param array $arr
 * @param String $glue
 * @return String
 */
function query_string($exclude = array(), array $arr = NULL, $glue = '&')
{
	$ret = '';

	if ( ! is_array($exclude))
		$exclude = array((string)$exclude);
	$exclude = array_lookup($exclude);

	if ( ! isset($arr))
		$arr = $_GET;

	$get = array();
	foreach ($arr as $key => $val) {
		if ( ! isset($exclude[$key]))
			$get[$key] = $val;
	}

	if ( ! empty($get))
		$ret = implode($glue, implode_assoc('=', $get));

	return $ret;
}

/**
 * Print data within a &lt;pre&gt; tag
 *
 * @param mixed $data
 * @param boolean $die { default : FALSE }
 * @param boolean $var_dump { default : FALSE }
 */
function pre($data, $die = FALSE, $type = 'print_r', $styled = TRUE, $title = 'Debug Data')
{
	$out = '<pre>' . data_to_string($data, $type) . '</pre>';
	if ($styled) {
		$out = '<div style="margin: 5px; padding: 5px; border: 1px solid blue;">
			<h3 style="text-align:center; color: gray;">' . $title . '</h3>' .
			$out .
		'</div>';
	}
	echo $out;
	$die ? die() : '';
}

/**
 * Output debug information using printf
 * Ex: debug("New user object: %s", $user);
 *
 * @global DEBUG
 */
function printd()
{
    if (is_defined('DEBUG') AND DEBUG)
    {
		$args = func_get_args();
		$args[0] = "<pre>" . $args[0] . "</pre>\n";
		for ($i = 1, $l = count($args); $i < $l; $i++) {
			$args[$i] = htmlspecialchars(var_export($args[$i], true));
		}
		call_user_func_array('printf', $args);
    }
}

/**
 * Log data to the javascript console
 *
 * @param mixed $data
 * @param String $type { default : 'print_r' }
 */
function console_log($data, $type = 'print_r')
{
	$data = data_to_string($data, $type);
	$out = addslashes(preg_replace('/\s+/', ' ', $data));
	echo '<script type="text/javascript">console.log("' . $out . '");</script>';
}

function convert_number_to_name($number){
    $number = $number-1;
    $data = array('First','Second','Third','Fourth','Fifth','Sixth','Seventh','Eighth','Nineth','Tenth');
    return isset($data[$number])?$data[$number]:$number.'th';
}
function excerpt($str='',$length = 20){
    if(strlen($str)<=$length) return $str;
    else return substr($str,0,$length).' ...';
}