<?php
class Crawlers extends MY_Controller
{
    public function __construct(){
        parent::__construct();
		$this->load->model('marketplace_m', 'Marketplaces');
    }
    
    public function index(){
        $this->data->marketplaces = $this->Marketplaces->get_marketplaces('*',true,true);
    }
}

?>