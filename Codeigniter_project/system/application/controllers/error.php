<?php

class Error extends MY_Controller
{
    protected $_acl = array(
    		'*' => 'cli'
    );    
    
	public function __construct()
	{
		parent::__construct();

		$this->layout = 'frontend_inner';
	}

	public function error_404()
	{
		$this->output->set_status_header('404');
		$this->load->view('errors/404');
	}
	
}
