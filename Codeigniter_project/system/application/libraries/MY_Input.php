<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MY_Input
 *
 * Add extra functionality to the CodeIgniter Input class
 */
class MY_Input extends CI_Input
{

	protected $_POST_ORIG;

	public function __construct() {
		$this->_POST_ORIG = $_POST; // save the original before it is modified
		parent::CI_Input();
	}


	/**
	 * Is ajax Request?
	 *
	 * Test to see if a request contains the HTTP_X_REQUESTED_WITH header
	 *
	 * @return 	boolean
	 */
	public function is_ajax_request()
	{
		return ($this->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest');
	}

	/**
	 * Is cli Request?
	 *
	 * Test to see if a request was made from the command line
	 *
	 * @return 	boolean
	 */
	public function is_cli_request()
	{
		return (php_sapi_name() === 'cli') or defined('STDIN');
		}

	/**
	 * Fetch a POST variable from a copy of the $_POST array which has not been
	 * filtered for XSS.
	 *
	 * Note: Make sure you hvae a good reason to not use the filtered $_POST array
	 *
	 * @param mixed $index
	 * @param boolean $xss_clean { default : TRUE }
	 * @return mixed
	 */
	public function post_orig($index = '', $xss_clean = TRUE)
	{
		if ( ! is_array($index))
			return $this->_fetch_from_array($this->_POST_ORIG, $index, $xss_clean);

		$ret = array();
		foreach ($index as $field) {
			$ret[$field] = $this->post_orig($index, $xss_clean);
		}

		return $ret;
	}

	/**
	 * Overrides the post function to work with arrays
	 *
	 * Note: The config option global_xss_clean should always be set to FALSE.
	 * Consider $xss_clean to be global
	 *
	 * @param mixed $index
	 * @param boolean $xss_clean { default : TRUE }
	 * @return mixed
	 */
	public function post($index = '', $xss_clean = TRUE)
	{
		if ( ! is_array($index))
			return parent::post($index, $xss_clean);

		$ret = array();
		foreach ($index as $field) {
			$ret[$field] = parent::post($field, $xss_clean);
		}

		return $ret;
	}

	/**
	 * Retrieve POST values with defaults if the POST variable doesn't exist.
	 *
	 * Arrays can be passed to the function to execute it multiple times.
	 * There are 3 possibilities when using arrays:
	 *
	 * $index is associative in the form field => default,
	 * $index is an array of fields and default is an array of the same size with defaults,
	 * $index is an array of fields and default is a string
	 *
	 * @param mixed $index
	 * @param mixed $default { default : FALSE }
	 * @param boolean $xss_clean { default : TRUE }
	 * @return array
	 */
	public function post_default($index, $default = FALSE, $xss_clean = TRUE)
	{
		if ( ! is_array($index)) {
			$ret = parent::post($index, $xss_clean);
			if ($ret === FALSE)
				$ret = $default;

			return $ret;
		}

		if ( ! is_assoc($index)) {
			if (is_array($default))
				$index = array_combine($index, $default);
			else
				$index = array_fill_keys($index, $default);
		}

		$ret = array();
		foreach ($index as $field => $default) {
			$ret[$field] = $this->post_default($field, $default, $xss_clean);
		}

		return $ret;
	}
}
