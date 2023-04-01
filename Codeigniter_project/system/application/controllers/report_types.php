<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_types extends MY_Controller
{

	public function byproduct(){
		$this->data->report = 'byproduct';
		$this->data->proMerchants = getProductMerchant($this->store_id);
		$this->_build_options_array($this->data->report);
	}

	public function bymerchant(){
		$this->data->report = 'bymerchant';
		$this->data->proMerchants = getProductMerchant($this->store_id);
		$this->_build_options_array($this->data->report);
	}

	public function bymarket(){
		$this->data->report = 'bymarket';
		$this->data->proMerchants = getProductMerchant($this->store_id);
		$this->_build_options_array($this->data->report);
	}

	public function bygroup(){
		$this->data->report = 'bygroup';
		$this->data->proMerchants = getProductMerchant($this->store_id);
		$this->data->product_groups = $this->Product->getGroups($this->store_id);
		$this->_build_options_array($this->data->report);
	}

	public function bycompetition(){
		$this->data->report = 'bycompetition';
		$this->data->show_comparison = FALSE;
		$this->_build_options_array($this->data->report);
	}

	protected function _build_options_array($by_which){
		$this->data->by_which = $by_which;
		$this->data->url_modifier = ($by_which  === 'bycompetition') ? '0/bycompetition' : '';
		$bydate = array(
			'display' => (boolean)$this->data->submitted,
			'date_from' => $this->data->dateStartField,
			'date_to' => $this->data->dateEndField,
			'time_frame' => $this->data->time_frame,
			'is_first' => FALSE,
			'next_block' => 'filters'
		);
		$byproduct = array(
			'display' => (boolean)$this->data->submitted,
			'searchProducts' => $this->data->searchProducts,
			'is_first' => FALSE,
			'display' => TRUE,
			'next_block' => 'filters'
		);
		$filters = array('display' => (boolean)$this->data->submitted);
		//use appropriate options order
		$optArray = array();
		switch($by_which){
			case 'bydate':
				$bydate['is_first'] = TRUE;
				$bydate['display'] = TRUE;
				$bydate['next_block'] = 'byproduct';
				$optArray = array(
					'bydate' => $bydate,
					'byproduct' => $byproduct,
					'filters' => $filters
				);
				break;
			case 'byproduct':
				$byproduct['is_first'] = TRUE;
				$byproduct['display'] = TRUE;
				$byproduct['next_block'] = 'bydate';
				$optArray = array(
					'byproduct' => $byproduct,
					'bydate' => $bydate,
					'filters' => $filters
				);
				break;
			case 'bymerchant':
				$byproduct['next_block'] = 'bydate';
				$optArray = array(
					'bymerchant' => array(
						'display' => TRUE,
						'is_first' => TRUE,
						'next_block' => 'byproduct'
					),
					'byproduct' => $byproduct,
					'bydate' => $bydate,
					'filters' => $filters
				);
				break;
			case 'bymarket':
				$byproduct['next_block'] = 'bydate';
				$optArray = array(
					'bymarket' => array(
						'display' => TRUE,
						'is_first' => TRUE,
						'next_block' => 'byproduct',
						'markets' => isset($this->data->markets) ? $this->data->markets : NULL
					),
					'byproduct' => $byproduct,
					'bydate' => $bydate,
					'filters' => $filters
				);
				break;
			case 'bygroup':
				$optArray = array(
					'bygroup' => array(
						'display' => TRUE,
						'is_first' => TRUE,
						'next_block' => 'bydate',
						'product_groups' => isset($this->data->product_groups) ? $this->data->product_groups : NULL,
						'group_id' => isset($this->data->group_id) ? $this->data->group_id : NULL
					),
					'bydate' => $bydate,
					'filters' => $filters
				);
				break;
			case 'bycompetition':
				$optArray = array(
					'bycompetition' => array(
						'display' => TRUE,
						'is_first' => TRUE,
						'next_block' => 'show_comparison',
						'searchProducts' => $this->data->searchProducts
					),
					'show_comparison' => array(
						'display' => (boolean)$this->data->submitted,
						'is_first' => FALSE,
						'next_block' => 'bydate',
						'selected' => $this->data->show_comparison
					),
					'bydate' => $bydate,
					'filters' => $filters
				);
				break;
			default:
				$byproduct['is_first'] = TRUE;
				$optArray = array(
					'byproduct' => $byproduct,
					'bydate' => $bydate,
					'filters' => $filters
				);
				break;
		}

		$this->data->optArray = $optArray;
	}
}

/* End of file report_options.php */
/* Location: ./system/application/libraries/report_options.php */