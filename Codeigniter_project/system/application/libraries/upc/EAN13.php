<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * A European Article Number - Type 13
 *
 * Length: 13-digit
 * Format: xx-xxxxx-xxxxx-x
 *
 * Description: A superset of the UPC-A used internationally
 */
class EAN13 extends UPC
{	
	public function __construct($upc = NULL)
	{
		parent::__construct($upc, NULL, NULL, 13);

		$this->type = '13';
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

	public static function parse_system($ean)
	{
		return substr($ean, 0, 2);
	}

	/**
	 * Get the 5 digit manufacturer's id from a UPC-A
	 *
	 * @param String $upca
	 * @return String
	 */
	public static function parse_man($ean)
	{
		return substr($ean, 2, 5);
	}

	/**
	 * Get the 5 digit product code from a UPC-A
	 *
	 * @param String $upca
	 * @return String
	 */
	public static function parse_prod($ean)
	{
		return substr($ean, 7, 5);
	}

	/**
	 * Calculate the check sum digit for a EAN-13
	 *
	 * @param String $ean
	 * @return int
	 */
	public static function check_sum($ean)
	{
		$check = 0;
		for ($i = 1; $i <= 12; $i++)
		{
			if ($i&1)
			{
				$check += $ean{$i - 1} * 7; // odd position digits multiplied by 7
			}
			else
			{
				$check += $ean{$i - 1} * 9; // even position digits multiplied by 9
			}
		}

		return $check % 10;
	}
}
