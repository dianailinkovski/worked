<?php

/**
 * @property Crowl_m $Crowl_model
 * @property Amzdb $amzdb

 */

if (!class_exists('simple_html_dom_node')) require_once(dirname(BASEPATH).'/system/application/libraries/simple_html_dom.php');
class Name extends MY_Controller {

	var $tables, $fromAr;
	function Name() {
		parent::__construct();

	}

	function updateSellerNames() {
		$markets = array('google');
		$result = $this->db
		->where_not_in('marketplace', $markets)
		//  ->limit(20);
		->group_by('seller_id')
		->get('crowl_merchant_name_new')
		->result();
		//echo $this->db->last_query()."<br>";

		if (count($result) > 0) {
			foreach ($result as $row) {
				$merchantName = '';
				$row->seller_id = trim($row->seller_id);
				$row->marketplace = trim($row->marketplace);

				if ($row->seller_id) {
					switch ($row->marketplace) {
					case 'amazon':
						//echo "<br>Amazon";
						$sellerID = substr($row->seller_id, -14);
						$merchantName = $this->getSellerNameAmazon($row->seller_id);
						break;
					case 'shopping':
						//echo "<br>Shopping";
						$merchantName = $this->getSellerNameShopping($row->seller_id);
						break;
					}
				}

				if ($merchantName) {
					$uparray = array('original_name'=>html_entity_decode($merchantName, ENT_QUOTES));
					$this->db
					->where('seller_id', $row->seller_id)
					->update('crowl_merchant_name_new', $uparray);

					//echo "<br>".$this->db->last_query();
				}

				//echo "<br>".$row->id;
			}
		}
		else {
			echo "No data Found";
		}

		exit;
	}

	function getSellerNameAmazon($sellerID) {
		$sellerName = '';
		if ($sellerID) {
			$url = 'http://www.amazon.com/gp/aag/main?ie=UTF8&asin=&isAmazonFulfilled=0&isCBA=&marketplaceID=ATVPDKIKX0DER&seller='.$sellerID;
			$html = file_get_html($url);

			if (is_object($html)) {
				$div = $html->find('div[id="aag_header"]', 0);
				if ($div) {
					$h1 = $div->find('h1', 0);
					if ($h1) {
						$sellerName =  $h1->plaintext;
					}
				}
				$html->clear();
			}
		}

		return $sellerName;
	}

	function getSellerNameShopping($sellerID) {
		$sellerName = '';
		if ($sellerID) {
			$url = 'http://www.shopping.com/xSI-~MRD-'.$sellerID;
			$html = file_get_html($url);
			if (is_object($html)) {
				$h1 = $html->find('h1[class="pageTitle"]', 0);
				if ($h1) {
					$sellerName =  $h1->plaintext;
				}
				$html->clear();
			}
		}

		return $sellerName;
	}
}
