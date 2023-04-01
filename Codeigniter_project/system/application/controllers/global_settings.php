<?php
class Global_settings extends MY_Controller {

	function Global_settings()
	{
		parent::__construct();
		$this->load->model("store_m", 'Store');
		$this->load->model("global_settings_m", 'settings');
		$this->load->model("report_m", 'Report');
	}

	function index()
	{
		$data['setting'] = $this->settings->get_settings();
		$this->load->view('front/merchant/global_settings', $data);
	}

	function google_settings()
	{
		$save_info = array();

		$save_info['search_in_days'] = $_POST['crowl_days'];
		$save_info['search_in_hours'] = $_POST['crowl_hours'];
		$save_info['search_in_minutes'] = $_POST['crowl_minutes'];
		$save_info['api_settings'] = 'google';
		if(isset($_POST['google_enable_disable'])){
			$save_info['is_active'] = '1';
		} else {
			$save_info['is_active'] = '0';
		}
		$crowl_data = $this->settings->get_google_settings('google');
		if(count($crowl_data[0]) > 0)
		{
			$this->settings->update_setting($save_info);
			$this->session->set_flashdata("error", 'Google Settings changed Successfully');
	        redirect('global_settings');
		} else
		{
			$this->settings->insert_setting($save_info);
			$this->session->set_flashdata("error", 'Google Settings changed Successfully');
	        redirect('global_settings');
		}

	}

	function amazon_settings()
	{
		$save_info = array();

		$save_info['search_in_days'] = $_POST['crowl_days'];
		$save_info['search_in_hours'] = $_POST['crowl_hours'];
		$save_info['search_in_minutes'] = $_POST['crowl_minutes'];
		$save_info['api_settings'] = 'amazon';

		if(isset($_POST['amazon_enable_disable'])){
			$save_info['is_active'] = '1';
		} else {
			$save_info['is_active'] = '0';
		}

		$crowl_data = $this->settings->get_amazon_settings('amazon');
		if(count($crowl_data[0]) > 0)
		{
			$this->settings->update_setting($save_info);
			$this->session->set_flashdata("error", 'Amazon Settings changed Successfully');
	        redirect(base_url() . 'global_settings');
		} else
		{
			$this->settings->insert_setting($save_info);
			$this->session->set_flashdata("error", 'Amazon Settings changed Successfully');
	        redirect(base_url() . 'global_settings');
		}

	}
}