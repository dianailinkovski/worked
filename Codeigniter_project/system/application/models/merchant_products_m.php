<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Merchant_products_m extends MY_Model
{

	public static $tableName = 'crowl_product_list_new';

	function Merchant_products_m() {
		parent::MY_Model();
		$this->load->library('amzdb');
	}
	
    /**
     * Find all products connected to a merchant.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @return array
     */
    public function get_products_by_merchant($merchant_id, $store_id, $is_tracked = 1, $is_archived = 0)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        
        $this->db->select('p.*');
        $this->db->from('crowl_product_list_new cpl');
        $this->db->join('products p', 'p.upc_code = cpl.upc');
        $this->db->where('cpl.merchant_name_id', $merchant_id);
        $this->db->where('p.store_id', $store_id);
        $this->db->where('p.is_tracked', $is_tracked);
        $this->db->where('p.is_archived', $is_archived);
        
        $query = $this->db->get();
         
        return $query->result_array();
    }	
    
    /**
     * Get the URL to the merchant's profile either on marketplace or our site.
     * 
     * @author Christophe
     * @param array $merchant_info
     * @param string $marketplace_name
     * @return string
     */
    public function get_marketplace_seller_url($merchant_info, $marketplace_name)
    {
        $marketplace = isset($merchant_info['marketplace']) ? $merchant_info['marketplace'] : $marketplace_name;
        
        switch($marketplace)
        {
            case 'amazon':
                if (!isset($merchant_info['seller_id']))
                {
                    $url = '/violationoverview/report_marketplace/' . $marketplace_name;
                }
                else
                {
                    $url = 'http://www.amazon.com/gp/help/seller/home.html?seller=' . $merchant_info['seller_id'];
                }
                
                break;
            case 'gunbroker':
                $url = 'http://www.gunbroker.com/Auction/ViewUserFeedback.aspx?User=' . $merchant_info['seller_id'];
                break;
            case 'walmart':

                $walmart_pieces = explode('_', $merchant_info['seller_id']);

                if (isset($walmart_pieces[1]))
                {
                    $url = 'https://www.walmart.com/reviews/seller/' . $walmart_pieces[1];
                }
                else
                {
                    $url = 'https://www.walmart.com';
                }
                
                break;
            case 'ebay':
                
                $url = 'http://www.ebay.com/usr/' . $merchant_info['seller_id'];    
                
                break;
            case 'n-a':

                break; 
            case '':
                
                if (isset($merchant_info['merchant_url']))
                {
                    $url = $merchant_info['merchant_url'];
                }
                else
                {
                    $url = '/whois/report_merchant/' . $merchant_info['marketplace'] . '/' . $merchant_info['id'];
                }
                
                break;
            default:

                $url = '/whois/report_merchant/' . $merchant_info['marketplace'] . '/' . $merchant_info['id'];
                
                break;
        }
        
        return $url;
    }

	/**
	 * Find product trend data for marketplaces.
	 * 
	 * @todo this function is super slow and should probably be rewritten, but it is used a lot... - Christophe
   * @author unknown, Chris?
	 */
	function getCountByMarketplace($store_id, $marketPlace='all', $startTime = null, $endTime = null, $violated = NULL) 
	{
		$start = ($startTime) ? strtotime($startTime) : strtotime('-24 hours');
		$end = ($endTime) ? strtotime($endTime) : time();
		
		//$countQuery = "SELECT COUNT(distinct(cpl.merchant_name_id)) as total_listing, COUNT(distinct(cpl.upc)) as total_products, cpl.marketplace, m.display_name, m.is_retailer
		//							FROM {$this->_table_crowl_product_list} cpl
		//							LEFT JOIN products p ON p.upc_code = cpl.upc
		//							LEFT JOIN {$this->_table_marketplaces} m ON cpl.marketplace = m.name
		//							WHERE cpl.last_date >= $start
		//							AND cpl.last_date <= $end
		//							AND p.store_id IN (" . getStoreIdList($store_id, TRUE) . ")
		//							AND cpl.marketplace IS NOT NULL";
		//if ($marketPlace!='all') {
		//	$countQuery .= " AND cpl.marketplace='$marketPlace'";
		//}
		
		$mpQuery = "";
		
		if ($marketPlace != 'all') 
		{
			$mpQuery = " AND ptn.ar = '$marketPlace'";
		}
		
		$violatedOnly = "";
		
		if (!is_null($violated))
		{
			$gtlt = (intval($violated) == 1) ? '>' : '<=';
			
			$violatedOnly = " AND ptn.ap {$gtlt} ptn.mpo";
		}
		
		// shortcut the subquery if we are selecting only violated products
		$violated_products_query = "";
		
		if (intval($violated) == 1)
		{
			$violated_products_query = "COUNT(*)";
		}
		elseif ((intval($violated) == 0) || TRUE)
		{
			$violated_products_query = "
				SELECT COUNT(*)
				FROM {$this->_table_products_trends} ptn
					LEFT JOIN products p ON p.id = ptn.pid
					LEFT JOIN {$this->_table_marketplaces} m ON ptn.ar = m.name
				WHERE ptn.dt >= $start
					AND ptn.dt <= $end
					AND p.store_id IN (" . getStoreIdList($store_id, TRUE) . ")
					AND ptn.ar IS NOT NULL
					AND ptn.ap > ptn.mpo
					$mpQuery
			";
		}

		$countQuery = "
          SELECT COUNT(distinct(ptn.mid)) as total_listing, COUNT(distinct(ptn.pid)) as total_products,
    				ptn.ar as marketplace, m.display_name, m.is_retailer,
    				($violated_products_query
    				) AS violated_products,
    				DATE_FORMAT(FROM_UNIXTIME($end), '%e %b %Y, %H:%m') AS last_tracked
    			FROM {$this->_table_products_trends} ptn
    				LEFT JOIN products p ON p.id = ptn.pid
    				LEFT JOIN {$this->_table_marketplaces} m ON ptn.ar = m.name
    			WHERE ptn.dt >= $start
    				AND ptn.dt <= $end
    				AND p.store_id IN (" . getStoreIdList($store_id, TRUE) . ")
    				AND ptn.ar IS NOT NULL
    				$mpQuery
    				$violatedOnly
		";
							
		// not necessary? $countQuery .= " GROUP BY ptn.ar";
		//echo "$countQuery\n";
		
		$result =  $this->db->query($countQuery)->result_array();
		
		if (empty($result[0]['display_name']))
		{
			$result = array();
		}
		
		//echo $this->db->last_query(); exit();
		
		return $result;
		
	}

	function getMerchantDetailsByMarketplace($marketplace) {
		return $this->db
		->where('marketplace', $marketplace)
		->mysql_cache()
		->get($this->_table_crowl_merchant_name)
		->result_array();
	}

	function getMerchantDetailsByName($name) {
		return $this->db
		->where('merchant_name', $name)
		->mysql_cache()
		->limit(1)
		->get($this->_table_crowl_merchant_name)
		->result_array();
	}

	function getMerchantDetailsById($id) {
		return $this->db
		->where('id', (int)$id)
		->limit(1)
		->mysql_cache()
		->get($this->_table_crowl_merchant_name)
		->row_array();
	}

	function getMerchantDetailsBySellerId($id, $marketPlace) {
		$where = array(
			'seller_id' => $id,
			'marketplace' => $marketPlace
		);

		return $this->db
		->where($where)
		->mysql_cache()
		->limit(1)
		->get($this->_table_crowl_merchant_name)
		->row_array();
	}

	/**
	 *
	 * function getHashKeysForDynamo
	 *
	 *
	 */

	 //if you are having problems with this function it's because it was never updated to use the proper table (crowl_merchant_name_new)
	 //update it appropriately because the 'merchant_products' table has been DROPPED
	 //it doesn't appear as if the product hashes are being created accuratly anyways
	function getHashKeysForDynamo($products = array(), $merchants = array(), $marekts = array(), $show = false) {

		$where = " p.id IN (".implode(',', $products).")" ;

		if (count($merchants) > 0 && $merchants[0] != 'all') {
			$strMerchants = "'" . implode("','", $merchants) . "'";
			$where .= " AND mn.merchant_name IN (".$strMerchants.")";
		}

		if (count($marekts) > 0 && $marekts[0] != 'all') {
			$strMarkets = "'" . implode("','", $marekts) . "'";
			$where .= " AND pl.marketplace IN (".strtolower($strMarkets).")";
		}

		//note that due to the products_pricing table, the value from the products table might not necessarily be accurate
		//query needs to be adjusted accordingly
		$qStr = "SELECT
						 concat(mn.seller_id,'#',pl.upc) as hash_key, mn.marketplace, mn.original_name, p.upc_code, p.id, p.price_floor, p.retail_price, p.wholesale_price, mn.merchant_name
					FROM
						 {$this->_table_crowl_merchant_name_new} mn
					LEFT JOIN {$this->_crowl_product_list_new} pl ON pl.merchant_name_id = mn.id
					LEFT JOIN {$this->_products} p on p.upc_code = pl.upc
					WHERE $where
					group by hash_key";

		if ($show) echo $qStr . '<br />';

		$result = $this->db->query($qStr)->result_array();
		//echo $this->db->last_query();

		return $result;
	}

	/**
	 *
	 * function getCountByMerchant
	 *
	 *
	 */
	function getCountByMerchant($store_id, $marketplace = null, $startTime = null, $endTime = null) {
		$this->db
		->select('COUNT(cpl.id) as total_products, mpn.id, mpn.merchant_name, mpn.marketplace, mpn.original_name')
		->join($this->_table_products . ' p', 'p.upc_code=cpl.upc', 'left')
		->join($this->_table_crowl_merchant_name . ' mpn', 'mpn.id=cpl.merchant_name_id', 'left')
		->where_in('p.store_id', getStoreIdList($store_id));

		$where = array();
		if ($marketplace)
			$where['mpn.marketplace'] = $marketplace;
		$start = ($startTime) ? strtotime($startTime) : strtotime('-24 hours');
		$end = ($endTime) ? strtotime($endTime) : time();

		$where['cpl.last_date >='] = $start;
		$where['cpl.last_date <='] = $end;
		$result = $this->db
		->where($where)
		->group_by('mpn.merchant_name')
		->get($this->_table_crowl_product_list . ' cpl')
		->result_array();

		return $result;
	}

	/**
	 * function getResultFromDynamoDB
	 *
	 */
	function getResultFromDynamoDB($key, $from, $to) {
		$filters = array(array(AmazonDynamoDB::TYPE_NUMBER => "$from"), array(AmazonDynamoDB::TYPE_NUMBER => "$to"));
		$this->amzdb->getQueryViolation($this->dynamo_tables['products_trends'], $key, $filters);
		return $result;
	}

}