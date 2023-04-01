<?php
class Whackprices extends MY_Controller
{
    public function __construct(){
        parent::__construct();
		$this->load->model('products_trends_m', 'ProductsTrends');
    }
    
    public function index(){
        $this->data->products = $this->ProductsTrends->get_latest_whack_prices();
    }
}

?>
