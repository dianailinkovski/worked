<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends MY_Controller {

	public function index() {
		$this->session->unset_userdata('user_id');
		$this->session->unset_userdata('permission_id');
		$this->session->unset_userdata('store_id');
		$this->session->unset_userdata('store_data');
		$this->session->unset_userdata('brands');
		$this->session->unset_userdata('user_brand_default');
		$this->session->destroy();
		redirect($this->config->item('gsession_logout') . '?from=' . base_url());
	}
        
}
