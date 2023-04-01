<?php

/**
 * Extends CI's encrypt library to use more secure
 * encryption methods by default
 *
 * @author J$
 */
class MY_Encrypt extends CI_Encrypt
{

	public function __construct()
	{
		parent::__construct();
		//$this->set_mode('MCRYPT_MODE_CFB');
	}

}
