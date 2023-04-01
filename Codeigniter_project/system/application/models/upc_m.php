<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class UPC_m extends MY_Model
{
	public function __construct()
	{
		parent::__construct();

		$this->load->library('upc/UPC');
		$this->load->library('upc/EAN13');
		$this->load->library('upc/UPCA');
		$this->load->library('upc/UPCE');
	}

	public function get_upc_type($upc)
	{
		switch (strlen($upc))
		{
			case 13:
				$type = '13';
				break;
			case 12:
				$type = 'A';
				break;
			case 8:
				$type = 'E';
				break;
			default:
				$type = '';
		}

		return $type;
	}

	public function get_upc($upc)
	{
		switch ($this->get_upc_type($upc))
		{
			case 'A':
				$upc = new UPCA($upc);
				break;
			case 'E':
				$upc = new UPCE($upc);
				break;
			case '13':
				$upc = new EAN13($upc);
				break;
			default:
				$upc = new UPC($upc);
		}

		return $upc;
	}
}
