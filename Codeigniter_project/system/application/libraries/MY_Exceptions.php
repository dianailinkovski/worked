<?php

class MY_Exceptions extends CI_Exceptions
{
	public function __construct()
	{
		parent::CI_Exceptions();
	}

	function show_404($page = '')
	{
		$this->config =& get_config();
		$base_url = $this->config['base_url'];

		log_message('error', '404 Page Not Found ----> '.$page);

		header('location: ' . $base_url . 'error/error_404/');
		exit;
	}
	
}
