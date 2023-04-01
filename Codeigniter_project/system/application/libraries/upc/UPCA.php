<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * A Universal Product Code - Type A
 *
 * Length: 12-digit
 * Format: x-xxxxx-xxxxx-x
 *
 * Description: The most common UPC type
 */
class UPCA extends UPC
{
	protected $upce;
	
	public function __construct($upc = NULL)
	{
		parent::__construct($upc, NULL, NULL, 12);

		$this->type = 'A';
	}

	/**
	 * Parse the components of a UPC. For UPC-A
	 * get the manufacturer's id and the product code
	 */
	protected function parse_upc()
	{
		parent::parse_upc();

		$this->man = self::parse_man($this->upc);
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

	/**
	 * Calculate the check sum digit for a UPC-A
	 *
	 * @param String $upca
	 * @return int
	 */
	public static function check_sum($upca)
	{
		$check = 0;
		for ($i = 1; $i <= 11; $i++)
		{
			if ($i&1)
			{
				$check += $upca{$i - 1} * 7; // odd position digits multiplied by 7
			}
			else
			{
				$check += $upca{$i - 1} * 9; // even position digits multiplied by 9
			}
		}

		return $check % 10;
	}

	public function toUPCE()
	{
		$this->upce = self::a2e($this->upc);

		return $this->upce;
	}

	/**
	 * Convert a UPC-A to UPC-E
	 *
	 * @param String $upca
	 * @return String/FALSE
	 */
	public static function a2e($upca)
	{
		$manufacturer = self::parse_man($upca);
		$product = self::parse_prod($upca);
		$upce = '';
		if ((substr($manufacturer, 2) == "000") || (substr($manufacturer, 2) == "100") || (substr($manufacturer, 2) == "200"))
		{
			$upce = substr($manufacturer, 0, 2) . substr($product, 2, 3) . substr($manufacturer, 2, 1);
		}
		elseif (substr($manufacturer, 3) == "00")
		{
			$upce = substr($manufacturer, 0, 3) . substr($product, 3, 2) . "3";
		}
		elseif (substr($manufacturer, 4) == "0")
		{
			$upce = substr($manufacturer, 0, 4) . substr($product, 4, 1) . "4";
		}
		else
		{
			$upce = substr($manufacturer, 0, 5) . substr($product, 4, 1);
		}

		$upce = '0' . $upce . substr($upca, 11, 1);

		return (strlen($upce) === 8) ? $upce : FALSE;
	}
}
