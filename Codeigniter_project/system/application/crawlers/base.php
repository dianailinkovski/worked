<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ProductData {

	public $_upc;
	public $_sku;
	public $_price;
	public $_title;
	public $_merchant;
	public $_manufacturer;
	public $_shipping_price;
	public $_product_image;
	public $_merchant_logo;
	public $_url;
	public $_offers = array();
	public $_offerList = array();
	public $_description;
	public $_internal_id;
	public $_merchant_url;

	public function __construct() {

	}

	public function set($name, $value) {
		$param = "_". $name;
		$this->$param = $value;
	}

	public function get($name) {
		$param = "_". $name;
		return $this->$param;
	}
}

abstract class base {

	protected $_identifier;
	protected $_model;

	public function __construct() {
		set_time_limit(0);
		ini_set("memory_limit", "400M");

		/* Load the db adapter class */
		require_once dirname(BASEPATH) . '/system/application/libraries/mydb.php';
	}

	/**
	* Set the identifier
	*
	* @param string $identifier The upc code or another identifier
	*/
	public function setIdentifier($identifier) {
		$this->_identifier = $identifier;
		$this->_model = new ProductData();
		$this->_model->set('upc', $identifier);
	}

	/**
	* Get offers for a upc code
	*
	* @param string $identifier The upc code or another identifier
	* @return array 
	*/
	abstract protected function getOffers($identifier);

	/**
	* Get product information
	* 
	* @param string $identifier The upc code or another identifier
	* @return array
	*/
	abstract protected function getProductInformation($identifier);

	/**
	* Get historical offers
	*
	* @param string $identifier The upc code or another identifier
	* @return array
	*/
	abstract protected function getHistoricalInformation($identifier);

	abstract public function reset();

	/**
	* Get Product
	*
	*/
	public function getProduct() {
		$productInformation = $this->getProductInformation($this->_identifier);
		/* 
		 We are looking for product information that looks like this --- otherwise spit out an error
		*
		*/
		if($productInformation !== false) {
			foreach($productInformation as $key => $value) {
				$this->_model->set($key, $value);
			}
		}
		return $this->_model;
	}

	/**
	* Get all offers
	*
	*/
	public function getAllOffers() {
		return $this->getOffers($this->_identifier);
	}
}