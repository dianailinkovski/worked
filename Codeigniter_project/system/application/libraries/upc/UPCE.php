<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Universal Product Code - Type E
 *
 * Length: 8-digit
 * Format: 0-xxxxxx-x
 *
 * Description: A shorthand representation of a UPC-A
 * belonging to the 0th number system
 */
class UPCE extends UPC
{
	protected $upca;
	
	public function __construct($upc = NULL)
	{
		parent::__construct($upc, NULL, NULL, 8);

		$this->type = 'E';
	}

	/**
	 * Conform all upcs to expected format. For upc-e verify
	 * that it starts with a 0
	 */
	protected function prep_upc()
	{
		parent::prep_upc();

		$this->upc = self::prep($this->upc);
	}

	protected function parse_upc()
	{
		parent::parse_upc();

		$this->toUPCA();

		if ($this->upca)
		{
			$this->man = substr($this->upca, 1, 5);
			$this->prod = substr($this->upca, 6, 5);
		}
	}

	public static function prep($upc)
	{
		$upc = parent::prep($upc);

		if ($upc{0} !== '0')
		{
			if (strlen($upc) <= 7)
				$upc = '0' . $upc;
			else
				throw new RangeException('UPC-E must belong to the 0th number system (Start with 0).', 2304); // More than max of 09999999
		}

		if (strlen($upc) === 7)
			$upc = $upc . self::check_sum($upc);

		return $upc;
	}

	/**
	 * Calculate the check sum digit for a UPC-E
	 *
	 * @param String $upce
	 * @return int
	 */
	public static function check_sum($upce)
	{
		$len = strlen($upce);
		if ($len > 8)
		{
			if ($len === 12)
				$upce = substr($upce, 0, 11);
		}
		else
		{
			if ($upce{0} !== 0)
				$upce = '0' . $upce;

			if (strlen($upce) === 8)
				$upce = substr($upce, 0, 7);
		}

		$len = strlen($upce);
		if ($len !== 7 AND $len !== 11)
			return FALSE;
		
		$check = 0;
		//$even = 0;
		//$odd = 0;
		for ($i = 1; $i <= $len; $i++)
		{
			if ($i&1)
			{
				$check += $upce{$i - 1} * 7; // odd position digits multiplied by 7
				//$odd += $upce{$i - 1};
			}
			else
			{
				$check += $upce{$i - 1} * 9; // even position digits multiplied by 9
				//$even += $upce{$i - 1};
			}
		}

		//$check2 = 10 - ((3*$odd + $even) % 10);

		return $check % 10;
	}

	/**
	 * Decompress this to UPC-A
	 *
	 * @return String
	 */
	public function toUPCA()
	{
		$this->upca = self::e2a($this->upc);

		return $this->upca;
	}

	/**
	 * Convert a UPC-E to UPC-A
	 *
	 * (Code from taltech.com. Logic appears incorrect)
	 *
	 * @param String $upce
	 * @return String
	 */
	public static function e2a($upce)
	{
		$upca = FALSE;
		$len = strlen($upce);
		$upce_str = '';
		$man = '';
		$prod = '';

		if(is_numeric($upce))
		{
			switch ($len)
			{
				case 6:
					$upce_str = $upce;
					break;
				case 7:
					$upce_str = substr($upce, 1, 6);
					break;
				case 8:
					$upce_str = substr($upce, 1, 6);
					break;
				default :
					throw new UnexpectedValueException('UPC-E is not formatted properly.', 2516, $previous); // Unexpected String length

			}

			switch ($upce_str{5})
			{
				case "0":
				case "1":
				case "2":
					$man = $upce_str{0} . $upce_str{1} . $upce_str{5} . "00";
					$prod = "00" . $upce_str{2} . $upce_str{3} . $upce_str{4};
				break;

				case "3":
					$man = $upce_str{0} . $upce_str{1} . $upce_str{2} . "00";
					$prod = "000" . $upce_str{3} . $upce_str{4};
				break;

				case "4":
					$man = $upce_str{0} . $upce_str{1} . $upce_str{2} . $upce_str{3} . "0";
					$prod = "0000" . $upce_str{4};
				break;

				default:
					$man = $upce_str{0} . $upce_str{1} . $upce_str{2} . $upce_str{3} . $upce_str{4};
					$prod = "0000" . $upce_str{5};
				break;

			}

			$check = self::check_sum("0" . $man . $prod);
			if (is_int($check))
				$upca = "0" . $man . $prod . $check;
		}

		return $upca;
	}
}
