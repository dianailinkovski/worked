<?php
// !! this runs on the production server, because that's where we need the product images downloaded !!
class Workers extends MY_Controller
{
	protected $_acl = array(
		'*' => 'cli'
	);
    
    public function __construct(){
        parent::__construct();
		$this->load->model('rejected_m', 'Rejected');
		$this->load->model('products_m', 'Products');
		$this->load->model('products_trends_m', 'ProductsTrends');
		$this->load->model('products_candidates_m', 'ProductsCandidates');
		$this->load->model('products_m', 'Products');
		$this->load->model('store_m', 'Store');
    }
    
    public function index(){
        echo "index\n";
        exit;
    }
	
    public function load_tasks(){
		// TODO: add flag to turn off workers queue for a store
		//$this->load_rejected();
		$this->load_brandpage_leads();
		exit;//cli
    }
	
	// todo: change of plans.  we will pull from brands details page crawled data.
	public function load_brandpage_leads()
	{
echo "------------------------------------\n";
		$candidates = $this->ProductsCandidates->get_all_products();
        foreach($candidates as $candidate)
        {
			// get best product match - we fetch only one
			$matching_products = $this->Products->fulltext_search($candidate['title'], 3);
echo "best_products\n";
print_r($matching_products); 
			
			////TODO: measure the score and do something if high or low thresholds reached
			$best_product = $matching_products[0]; 
			echo "score {$best_product->score}\n"; 
			
            $data = array(
				'user_id' => 6, // "nobody" user
                'brand' => $candidate['store_name'],
                'marketplace_id' => $candidate['marketplace_id'],
                'product_image' => $candidate['url'],
                'product_name' => $candidate['title'],
                'product_url' => $candidate['url'],
                'search_url' => $candidate['search_url'],
				//'product_info' => $this->format_product_info($candidate),
				'created_at' => date('Y-m-d H:i:s',time())
            );
			// redundant table design here
			// TODO: if match relevancy not high enough, skip it
			// TODO: compare price for sanity check?
			// todo: move to products model
			foreach($matching_products as $i => $top_product){
				// TODO: match SKU and UPC to candidate title (using word boundaries)
				$i++;
			//$image = $this->fetch_image($top_product->upc_code);
				$data["catalog_".$i."_image"] = '';// $this->fetch_image($top_product->upc_code);// = $top_product->upc_code.".jpg";
				$data["catalog_".$i."_info"] = $this->format_product_info($top_product);
				$data["catalog_".$i."_select"] = 0;
				$data["catalog_".$i."_upc"] = $top_product->upc_code; 
			}

echo "candidate\n";
print_r($candidate);
echo "data\n";
print_r($data); 
            $insert_ignore_flag = true;
			//Product Match  match the product on the left with one of the products on the right
            $this->db->insert('tb_work_product_match', $data, $insert_ignore_flag);
//echo $this->db->last_query()."\n";
exit;
        }
	}
	
	function format_product_info($product){
		$product = (array)$product;
		$txt = "";
		$exclude = array('id', 'store_id', 'brand', 'price_floor', 'is_tracked', 'created_at', 'wholesale_price', 'status', 'is_archived', 'is_violated', 'deleted_at', 'is_processed', 'marketplace_id');
		foreach($product as $k => $v){
			if(in_array($k, $exclude)) continue;
			$txt .= "$k: $v\n";
		}
		return $txt;
	}
    
	public function load_rejected(){
        $tasks = $this->Rejected->get_worker_queue();
        foreach($tasks as $task)
        {
            // get search url
            $search_url = $task['brands_url'] ? $task['brands_url'] : false;
            if(!$search_url and $task['url']){
                $parts = parse_url($task['url']);
                $search_url = 'http://'.$parts['host']; 
            }
            
            // get product_image
		//$image = $this->fetch_image($task['upc']);
            $data = array(
				'user_id' => 6, // "nobody" user
                'brand' => $task['store_name'],
                'marketplace_id' => $task['mpId'],
                'product_image' => $image,
                'product_name' => $task['title'],
                'product_url' => $task['url'],
                'retail_price' => $task['retail_price'],
                'search_url' => $search_url,
                'upc' => $task['upc'],
				'created_at' => date('Y-m-d H:i:s',time())
            );
print_r($data); 
            $insert_ignore_flag = true;
            $this->db->insert('tb_work_queue', $data, $insert_ignore_flag);
exit;
        }
	}
	
	public function process_results(){
		$this->process_product_match_results();
		$this->process_URL_lookup_results();
	}
	
	// todo: move into model file
	public function process_product_match_results(){
		$completed = $this->db->query("SELECT * FROM tb_work_product_match where complete=1 and logged=0")->result();//print_r($completed); exit;
		
		foreach($completed as $complete){
			$upc = null;
			if($complete->catalog_1_select==1)
				$upc = $complete->catalog_1_upc;
			elseif($complete->catalog_2_select==1)
				$upc = $complete->catalog_2_upc;
			elseif($complete->catalog_3_select==1)
				$upc = $complete->catalog_3_upc;
				
			$url = $this->strip_fragment($complete->product_url);
			$brand = $complete->brand;
			
			$sql = "
				INSERT INTO products_lookup SET 
					marketplace_id = '{$complete->marketplace_id}',
					product_id = (SELECT p.id FROM products p
								JOIN store s on (p.store_id = s.id)
								WHERE p.upc_code = '{$upc}'
								AND s.store_name='{$brand}'),
					upc = '{$upc}',
					url = '{$url}'
				ON DUPLICATE KEY UPDATE
					fails = 0,
					url = '{$url}'
			";
			echo "$sql\n";		exit;
			$this->db->query($sql);
			
			//todo: copy row into log and delete
		}
		
	}
	
	// todo: move into model file
	public function process_URL_lookup_results(){
		$completed = $this->db->query("SELECT * FROM tb_work_queue where complete=1 and logged=0")->result();//print_r($completed); exit;
		
		foreach($completed as $complete){
			$upc = $complete->upc;
			$url = $this->strip_fragment($complete->product_url);
			$status = $complete->status;
			$brand = $complete->brand;
			
			if($upc and $url and $status=='found'){
				$sql = "
					INSERT INTO products_lookup SET 
						marketplace_id = '{$complete->marketplace_id}',
						product_id = (SELECT p.id FROM products p
									JOIN store s on (p.store_id = s.id)
									WHERE p.upc_code = '{$upc}'
									AND s.store_name='{$brand}'),
						upc = '{$upc}',
						url = '{$url}'
					ON DUPLICATE KEY UPDATE
						fails = 0,
						url = '{$url}'
				";
				echo "$sql\n";		exit;
				$this->db->query($sql);
			}
			elseif($upc and $status=='not found'){
				$sql = "
					DELETE FROM products_lookup
						WHERE marketplace_id = '{$complete->marketplace_id}'
						AND upc = '{$upc}'
				";
				echo "$sql\n";		exit;
				$this->db->query($sql);
			}
			//elseif() // TODO: figure out how to handle "unsure" status
			
			//todo: copy row into log and delete
		}
		
	}
    
	public function fetch_image($upc){
        $image = $this->Products->fetch_image($upc);
        if($image){
            return $image;
        }
		// else file not found yet
        $image = $this->download_best_file($upc);
        if($image){
            return $image;
        }
        return "";
    }
	
	public function fetch_all_images(){
		$products = $this->Products->get_all_active_upcs();
		foreach($products as $product){
			$img = $this->fetch_image($product->upc_code);
			echo "$img ";
		}
	}

    function download_best_file($upc){
		$candidates = $this->ProductsTrends->get_images($upc);
        $array = array();
        foreach ($candidates as $product){
            $arr = $this->getjpegsize($product->img);
            if(is_array($arr) and count($arr)==3 and $arr[0]>100){
                $array[] = $arr;
            }
        }
        //var_export($array);exit;
        if(count($array)>=1){
            usort($array, function($a, $b) {            // sort by size
                return $a[0] - $b[0];
            });
            //$temp = $array[intval(count($array)/2)+1];//get the one in the middle //$temp = $array[count($array)-1]; //get the largest one
            for($i=count($array)-1; $i>=0; $i--){       // try all, starting with largest
                $temp = $array[$i];
                $url = $temp[2];
                $img = $this->config->item('product_image_upload_path').$upc.".jpg";
                file_put_contents($img, file_get_contents($url));
                if($this->Products->fetch_image($upc)){
                    return $upc.".jpg";
                }
            }
        }
        return "";
    }
	
	public function seed_product_search(){
		$stores = $this->Store->get_stores(TRUE);
		foreach($stores as $store){
			$store_id = $store->id;
			echo "store: $store->store_name\n";
			$products = $this->Products->getByStore($store_id);
			foreach($products as $product){
				$pid = $product['id'];
				$upc = $product['upc_code'];
				$search = $product['search'];
				// start from where we left off
				//if(strlen($search)>10){
				//	echo ",";
				//	continue; 
				//}
				$seeds = $this->ProductsTrends->get_concat_title_by_upc($upc);
				$fulltext = $this->unique_words_from_string($search . ' '. $seeds);
				$data = array('search' => $fulltext);
				$this->Products->update_product($pid, $data);
				echo ".";
				//print_r($data); exit;
				//sleep(2); // slow down, buddy
			}
		}
		die();//cli
	}
	
	public function unique_words_from_string($string){
		$string = str_replace(array(","), ' ', $string);
		$string = preg_replace('/\s/', ' ', $string);
		$string = strtolower($string);
		$arr = explode(' ', $string);
		$uniq = array_unique($arr);
		$output = implode(' ', $uniq);
		return $output;
	}

	// URL helper function
	function strip_fragment($url){
		if(strpos($url, '#'))
			list($url,$junk) = @explode('#', $url);
		return $url;
	}
    
    /**********************************************************************************************/
    // TODO: put this into utility file
    // Retrieve JPEG width and height without downloading/reading entire image.
    function getjpegsize($img_loc) {
        $handle = @fopen($img_loc, "rb");
        if(!$handle){
            //echo("Invalid file stream.\n");
            return;
        }
        $new_block = NULL;
        if(!feof($handle)) {
            $new_block = fread($handle, 32);
            $i = 0;
            if($new_block[$i]=="\xFF" && $new_block[$i+1]=="\xD8" && $new_block[$i+2]=="\xFF" && $new_block[$i+3]=="\xE0") {
                $i += 4;
                if($new_block[$i+2]=="\x4A" && $new_block[$i+3]=="\x46" && $new_block[$i+4]=="\x49" && $new_block[$i+5]=="\x46" && $new_block[$i+6]=="\x00") {
                    // Read block size and skip ahead to begin cycling through blocks in search of SOF marker
                    $block_size = unpack("H*", $new_block[$i] . $new_block[$i+1]);
                    $block_size = hexdec($block_size[1]);
                    while(!feof($handle)) {
                        $i += $block_size;
                        $new_block .= fread($handle, $block_size);
                        if(!empty($new_block[$i]) && $new_block[$i]=="\xFF") {
                            // New block detected, check for SOF marker
                            $sof_marker = array("\xC0", "\xC1", "\xC2", "\xC3", "\xC5", "\xC6", "\xC7", "\xC8", "\xC9", "\xCA", "\xCB", "\xCD", "\xCE", "\xCF");
                            if(in_array($new_block[$i+1], $sof_marker)) {
                                // SOF marker detected. Width and height information is contained in bytes 4-7 after this byte.
                                $size_data = $new_block[$i+2] . $new_block[$i+3] . $new_block[$i+4] . $new_block[$i+5] . $new_block[$i+6] . $new_block[$i+7] . $new_block[$i+8];
                                $unpacked = unpack("H*", $size_data);
                                $unpacked = $unpacked[1];
                                $height = hexdec($unpacked[6] . $unpacked[7] . $unpacked[8] . $unpacked[9]);
                                $width = hexdec($unpacked[10] . $unpacked[11] . $unpacked[12] . $unpacked[13]);
                                return array($width, $height, $img_loc);
                            } else {
                                // Skip block marker and read block size
                                $i += 2;
                                $block_size = unpack("H*", $new_block[$i] . $new_block[$i+1]);
                                $block_size = hexdec($block_size[1]);
                            }
                        } else {
                            return FALSE;
                        }
                    }
                }
            }
        }
        return FALSE;
    }    
}


		//$sql = "
		//	SELECT p.upc_code, m.name, m.id as mpId, s.store_name, p.retail_price, p.title, ms.url, m.brands_url
		//	FROM marketplaces_stores ms
		//		JOIN store s ON (ms.store_id = s.id)
		//		JOIN marketplaces m ON (m.id = ms.marketplace_id)
		//		JOIN products p ON (p.store_id = s.id)
		//	WHERE s.id > 0
		//	AND s.store_enable = '1'
		//	AND s.worker_process = 1
		//	AND m.upc_lookup = '0'
		//	AND (p.upc_code, m.id) NOT IN
		//	(
		//		SELECT upc as upc_code, marketplace_id as id
		//		FROM products_lookup
		//	)
		//	AND (p.upc_code, m.id) NOT IN
		//	(
		//		SELECT upc as upc_code, marketplace_id as id
		//		FROM tb_work_queue
		//	)
		//";

		//$sql = "
		//	SELECT m.name, m.id as mpId, s.store_name, m.brands_url, ms.url
		//	FROM marketplaces_stores ms
		//		JOIN store s ON (ms.store_id = s.id)
		//		JOIN marketplaces m ON (m.id = ms.marketplace_id)
		//	WHERE s.id > 0
		//	AND s.store_enable = '1'
		//	AND s.worker_process = 1
		//	AND m.upc_lookup = '0'
		//	group by mpId
		//";
		
?>
