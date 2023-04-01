<?php
class Run_cron extends MY_Controller {


	function Run_cron()
	{
		parent::__construct();
		//$this->load->library('AmazonECS');
		$this->load->model("report_m", 'Report');
		$this->load->model('crowl_m', 'Crowl_model');
		$this->layout='none';
	}
	function index()
	{

	}
}

/* End of file run_cron.php */
/* Location: ./system/application/controllers/run_cron.php */