<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * An abstraction of a Universal Product Code
 */
class UPC
{
	protected $upc;
	protected $man;
	protected $prod;
	protected $len;
	protected $system;
	protected $check_sum;
	protected $type;
	protected $min_len;
	protected $max_len;
	protected $exact_len;

	public function __construct($upc = NULL, $min_len = NULL, $max_len = NULL, $exact_len = NULL)
	{
		$this->set_length($min_len, $max_len, $exact_len);

		if (isset($upc))
			$this->process($upc);
	}

	/* Parsing */

	/**
	 * Verify the upc format and parse into its components
	 *
	 * @param String $upc
	 */
	public function process($upc)
	{
		$this->upc = $upc;

		$this->prep_upc();
		$this->verify();
		$this->parse_upc();
	}

	/**
	 * Conform all upcs to expected format. For upcs in general
	 * verify that it consists of digits and is of type String
	 */
	protected function prep_upc()
	{
		$this->upc = self::prep($this->upc);
	}

	protected function parse_upc()
	{
		$this->system = self::parse_system($this->upc);
		$this->check_sum = self::parse_check_sum($this->upc);

		// Most likely 2-6 and 7-11 are manufacturing id and product code
		// but not always
		if ($this->len > 5)
			$this->man = self::parse_man($this->upc);
		if ($this->len > 10)
			$this->prod = self::parse_prod($this->upc);
	}

	/**
	 * Get the 5 digit manufacturer's id from a UPC-A
	 *
	 * @param String $upca
	 * @return String
	 */
	public static function parse_man($upca)
	{
		return substr($upca, 1, 5);
	}

	/**
	 * Get the 5 digit product code from a UPC-A
	 *
	 * @param String $upca
	 * @return String
	 */
	public static function parse_prod($upca)
	{
		return substr($upca, 6, 5);
	}

	protected static function parse_system($upc)
	{
		return substr($upc, 0, 1);
	}

	protected static function parse_check_sum($upc)
	{
		return substr($upc, -1);
	}

	protected static function prep($upc)
	{
		if ( ! is_numeric($upc))
			throw new UnexpectedValueException('UPC may contain digits only.', 2525); // Numeric Expected

		return (int)$upc . '';
	}

	/**
	 * Verify that the length of a given upc meets the given constraints
	 *
	 * @param String $upc
	 * @param int $min { default : NULL }
	 * @param int $max { default : NULL }
	 * @param int $exact { default : NULL }
	 * @return 0/NULL/FALSE/TRUE
	 */
	protected static function check_length($upc, $min = NULL, $max = NULL, $exact = NULL)
	{
		$len = strlen($upc);
		if (isset($min))
			if ($len < $min)
				return 0;

		if (isset($max))
			if ($len > $max)
				return NULL;

		if (isset($exact))
			if ($len != $exact)
				return FALSE;

		return TRUE;
	}

	/**
	 * Verify that the length of this upc meets this contraints or
	 * throw an Unexpected Value Exception
	 */
	protected function verify_length()
	{
		if ( ! isset($this->upc))
			$this->upc = '';

		$this->len = strlen($this->upc);

		$response = self::check_length($this->upc, $this->min_len, $this->max_len, $this->exact_len);

		switch (true)
		{
			case ($response === 0): // too short
				throw new UnexpectedValueException('UPC must be at least ' . $this->min_len . ' digits', 2518); // String Too Short
				break;
			case ($response === NULL): // too long
				throw new UnexpectedValueException('UPC must be at most ' . $this->max_len . ' digits', 2517); // String Too Long
				break;
			case ($response === FALSE): // not exact
				throw new UnexpectedValueException('UPC must be exactly ' . $this->exact_len . ' digits', 2516); // Unexpected String Length
				break;
		}
	}

	/**
	 * Verify that this upc is a string and meets the length constraints
	 */
	protected function verify()
	{
		if ( ! is_string($this->upc))
			throw new UnexpectedValueException('UPC must be a string. ' . gettype($upc) . ' given.', 2519); // String Expected

		$this->verify_length();
	}

	public function check()
	{
		$class = get_class($this);
		if (method_exists($class, 'check_sum'))
		{
			$check = call_user_func(array($class, 'check_sum'), $this->upc);
			return $this->check_sum == $check;
		}
	}

	/* Setting and Getting */
	public function length()
	{
		if ( ! isset($this->len))
			$this->len = strlen($this->upc);

		return $this->len;
	}

	public function get_system()
	{
		return $this->system;
	}

	public function get_check_sum()
	{
		return $this->check_sum;
	}

	public function set_type($type)
	{
		$this->type = $type;
	}

	public function get_type()
	{
		return $this->type;
	}

	public function set_length($min = NULL, $max = NULL, $exact = NULL)
	{
		$this->min_len = isset($min) ? (int)$min : NULL;
		$this->max_len = isset($max) ? (int)$max : NULL;
		$this->exact_len = isset($exact) ? (int)$exact : NULL;
	}

	public function __get($name)
	{
		if ($name == 'upc')
			return $this->upc;
		elseif ($name == 'man')
			return $this->man;
		elseif ($name == 'prod')
			return $this->prod;
	}

	public function __isset($name)
	{
		return ($this->$name !== NULL);
	}
}
