<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

ini_set('memory_limit', '600M'); // some screenshot images are big 

/**
 * @property Crowl_m $Crowl_model
 * @property Amzdb $amzdb
 */
class Crowl extends MY_Controller {

	public $tables;
	public $fromAr;
	public $bad_phantom_domains;
	public $min_file_size;
	public $domain_of_screenshot;

	protected $_acl = array(
		'*' => 'cli'
	);

	function Crowl() {
		parent::__construct();
		
		$this->min_file_size = 40 * 1024; //kb

		$this->load->library('email');

		$this->load->model('crawl_data_m', 'Crawl_data');
		$this->load->model('products_m', 'Products');
		$this->load->model("report_m", 'Report');
		$this->load->model("store_m", 'Store');
		$this->load->model('violator_m', 'Violator');
		$this->load->model('marketplace_m', 'Market');
		//$this->load->model('crowl_m', 'Crowl_model');
		$this->load->model('proxy_ips_m', 'ProxyIps');

        // get random proxy ip
        $this->ProxyIps->set_random_proxy();

		$this->fromAr = array('cronTab1', 'Manual');

		$this->tables = array(
			$this->_dynamo_products_trends,
			$this->_dynamo_violations
		);
		
		// config- domains that phantomjs fails to rasterize
		$this->bad_phantom_domains = array('goprodroneclub.com', 'topgunsupply.com');
		
		// domains we should not crop the violation screenshot
		$this->bad_cropping_domains = array('vigshealthfood.myshopify.com', 'goprodroneclub.com', 'valuepal.com');//'amazon.com', 
		
		// sets and uses persistent cookies
		$this->cookie_domains = array('garysgunshop.com');

	}

	function index($store_id = 0, $type = 'all', $cronLogID = '0') {
		$store_id = $store_id == 0 ? $this->session->userdata("store_id") : $store_id;
		$this->Crowl_model->crowl_products($store_id, $type, $cronLogID);

		return true;
	}

	function sendViolationEmails($columns, $merchant, $items) {
		$res = $this->db
		->select('id, email')
		->where('id', (int) $merchant)
		->get($this->_table_users)
		->row();
		if ($res) {
			$user_id = $res->id;
			$email_address = $res->email;
		} else {
			$email_address = '';
		}

		$fetch_emails_data = $this->db
		->where('user_id', (int) $merchant)
		->where('section', 'violation')
		->get('notifications_setting')
		->row();

		echo "<br><br>VIOLATIONS EMAILS <pre>";
		print_r($fetch_emails_data);

		if ($fetch_emails_data) {
			$email_ids = explode(',', $fetch_emails_data->email);
		} else {
			$email_ids = array($email_address);
		}

		if (count($email_ids) > 0 && $email_ids[0]) {
			$response = $this->fetch_report_new("", "email", $columns, $items, $email_ids, 'violation', $merchant);
		}
	}


	function creatReportLog($date, $report_type) {
		$data = array(
			'start_date' => $date,
			'end_date' => $date,
			'datetime' => $date,
			'report_type_id' => $report_type
		);
		$this->db->insert('reportlog', $data);
	}

	function getMaxReportLogData($report_type) {
		$sql = "SELECT * FROM reportlog WHERE id=(SELECT max(id) as id FROM reportlog WHERE report_type_id='".$report_type."')";
		return $this->db->query($sql)->result();
	}

	/* For Getting UPC against Store */
	function getStoreUPCAll($storeID) {
		$finalArray = array();
		$upcArray = $this->db
		->select('upc_code')
		->where('store_id', (int) $storeID)
		->get('products')
		->result();

		if (count($upcArray) > 0) {
			foreach ($upcArray as $upc) {
				$finalArray[] = $upc->upc_code;
			}
			return $finalArray;
		} else {
			return 0;
		}
	}

	function getProductUPC($productID) {
		$finalArray = array();
		$upcArray = $this->db
		->select('upc_code')
		->where('id', (int) $productID)
		->get('products')
		->row();

		if ($upcArray) {
			return $upcArray->upc_code;
		} else {
			return 0;
		}
	}


	function getGroupUPCArray($gid) {
		$data = array();
		$dataArray = array();

		$res = $this->db
		->select('p.upc_code')
		->join('group_products gp', 'g.id=gp.group_id', 'left')
		->join('products p', 'p.id=gp.product_id', 'left')
		->where('g.id', (int) $gid)
		->get('groups g');

		if ($res->num_rows() > 0) {
			$data = $res->result_array();
		}
		for ($i = 0, $n = count($data); $i < $n; $i++)
			$dataArray[] = $data[$i]['upc_code'];

		return $dataArray;
	}

	function getGroupProductsArray($gid) {
		$data = array();
		$dataArray = array();

		$res = $this->db
		->select('gp.product_id')
		->join('group_products gp', 'g.id=gp.group_id', 'left')
		->where('g.id', (int) $gid)
		->get('groups g');

		if ($res->num_rows() > 0) {
			$data = $res->result_array();
		}
		for ($i = 0, $n = count($data); $i < $n; $i++)
			$dataArray[] = $data[$i]['product_id'];

		return $dataArray;
	}

	function getColsArray($reportType) {
		if ($reportType == 'violation_report')
			return array('author', 'link', 'alarm_price', 'merchant_price_offered');
		elseif ($reportType == 'price_trend_report')
			return array('datetime_tracked', 'average');
		elseif ($reportType == 'merchant_report')
			return array('author', 'merchant_url', 'average');
	}

	function getMerchantStoreId($user_id) {
		$storeData = $this->db
		->where('user_id', (int) $user_id)
		->get('store')
		->row();
		if ($storeData)
			return $storeData->id;
		else
			return 0;
	}
	/////////////End Cron for Reports////////////////////////////////////////////

	//////////////////////Cron Jobs Section////////////////////////////////////////
    // TODO: remove these redundant functions
	public function run_cron_job_amazon($from = 'no') {
		$this->_run_cron_job('amazon', $from);
	}

	public function run_cron_job_google($from = 'no') {
		$this->_run_cron_job('google', $from);
	}

	public function run_cron_job_shopping($from = 'no') {
		$this->_run_cron_job('shopping', $from);
	}

	public function run_cron_job_price_grabber($from = 'no') {
		$this->_run_cron_job('pricegrabber', $from);
	}

	public function run_cron_job_iherb($from = 'no') {
		$this->_run_cron_job('iherb', $from);
	}

	public function run_cron_job_livamed($from = 'no') {
		$this->_run_cron_job('livamed', $from);
	}

	public function run_cron_job_luckyvitamin($from = 'no') {
		$this->_run_cron_job('luckyvitamin', $from);
	}

	public function run_cron_job_vitacost($from = 'no') {
		$this->_run_cron_job('vitacost', $from);
	}

	public function run_cron_job_vitaminshoppe($from = 'no') {
		$this->_run_cron_job('vitaminshoppe', $from);
	}

	public function run_cron_job_vitanherbs($from = 'no') {
		$this->_run_cron_job('vitanherbs', $from);
	}

	public function run_cron_job_swansonvitamins($from = 'no') {
		$this->_run_cron_job('swansonvitamins', $from);
	}

	protected function _run_cron_job($api, $from = 'no') {
		//if ( ! in_array($from, $this->fromAr)) {
		//	echo "You are not allowed to run this cron";
		//	exit;
		//}
		
		// Count the active proxies
		// Send a warning if 5 or less are active
		$threshold = 10;
		$proxies = getProxyIPS(TRUE, $threshold + 1);
		if (count($proxies) <= $threshold)
			$this->email_alert('Low on proxies', 'The active proxy count has reached ' . $threshold . ' or less as of ' . date('Y-m-d H:i:s'));
			
		$apis = ensure_array($api);
		$storesData = $this->Store->get_stores(TRUE);
		$currentDate = date('Y-m-d H:i:s');

		$this->Crowl_model->stats = array();
        
		// Cycle Through API crons
		for ($j = 0, $n = count($apis); $j < $n; $j++) {
			$this->Crowl_model->stats[$apis[$j]] = array(
				'data_found' => 0,
				'price_found' => 0
			);
			$this->Crowl_model->load_sticky_api($apis[$j]);
			$data = array(
				'api_type' => $apis[$j],
				'key' => generate_rand(32),
				'run_from' => $from,
				'start_datetime' => $currentDate
			);

			try {
				// Update cron log information
				$lastLog = $this->getMaxCronLog($data['api_type']);
				$lastRun = empty($lastLog) ? '0000-00-00 00:00:00' : $lastLog->datetime;

				if ( ! empty($lastLog)) {
					if ($lastLog->datetime === "0000-00-00 00:00:00") {
						$update = array(
							'datetime' => $currentDate,
							'end_datetime' => $currentDate,
						);
						$this->db
							->where('datetime', '0000-00-00 00:00:00')
							->where('api_type', $data['api_type'])
							->update('cron_log', $update);
					}
				}

				if ($lastRun == '0000-00-00 00:00:00' || $currentDate >= $lastRun) { // No entry in cron log table
					$insert_id = $this->create_cron_log($data);
					$this->resetViolationCount();

					// Crawl the products for each store
					for ($i = 0, $z = count($storesData); $i < $z; $i++)
						$this->index($storesData[$i]->id, $data['api_type'], $insert_id);

					$data_edit['end_datetime'] = $data_edit['datetime'] = date("Y-m-d H:i:s");
					$this->update_cron_log($insert_id, $data_edit);
				}
			}
			catch (Exception $e) {
				email_alertToTeam('Run Cron Job ' . ucwords($apis[$j]), $e->getMessage());
			}

			// Now that the crawl has finished,
			// compare these crawl numbers with the last
			// and send out notifications if needed.
			$currentLog = $this->getMaxCronLog($data['api_type']);
			$lastCount = $lastLog ? $lastLog->google_count : 0;
			$currentCount = $currentLog->google_count;

			$percentThresh = 25; // send a notification if completed this percent of the last crawl
			$smallThresh = 10; // send a notification if complete less than this many products

			$msg = '';
			$percentDrop = 100-$percentThresh;
			if ($lastCount > 0 AND ($currentCount*100/$lastCount <= $percentThresh))
				$msg = ' crawl completed ' . $percentDrop . '% less products than its previous crawl.';
			elseif ($currentCount < $smallThresh)
				$msg = ' crawl completed less than ' . $smallThresh . ' products.';

			if ( ! empty($msg)) {
				$msg_txt = 'Crawl Warning: ' . $data['api_type'] . $msg;
				$msg_html = 'Crawl Warning: ' . '<b>' . $data['api_type'] . '</b>' . $msg;
				log_message('error', $msg_txt);
				send_email($this->config->item('alerts'), 'TrackStreet Errors', 'TrackStreet Crawl Warning', $msg_html, $msg_txt);
			}
		}

		$this->_checkNotFoundRatio();
		
		// todo: should we not return to the main script here?
		exit;
	}

	function _checkNotFoundRatio() {
		// Send a notification if this percent or more
		// prices were not found when data was found.
		$notFoundThresh = 10;
		if ( ! empty($this->Crowl_model->stats)) {
			foreach ($this->Crowl_model->stats as $api => $stats) {
				if ($stats['data_found'] > 0) {
					$notFound = ($stats['data_found'] - $stats['price_found']) * 100 / $stats['data_found'];
					if ($notFound >= $notFoundThresh) {
						$msg = $notFound . '% of prices were not found when product data existed';
						$msg_txt = $api . ' Crawl Warning: ' . $msg;
						$msg_html = '<b>' . $api . '</b>' . ' Crawl Warning: ' . $msg;
						log_message('error', $msg_txt);
						send_email($this->config->item('alerts'), 'TrackStreet Errors', 'TrackStreet Crawl Warning', $msg_html, $msg_txt);
					}
				}
			}
		}
	}

	function getMaxCronLog($api_type) {
		$maxLogDate = array();

		$cronLogNumRows = $this->db
		->select_max('id')
		->where('api_type', $api_type)
		->group_by('api_type')
		->get('cron_log');

		if ($cronLogNumRows->num_rows() > 0) {
			$cronLogMaxId = $cronLogNumRows->row();
			$maxLogDate = $this->db
			->where('id', $cronLogMaxId->id)
			->get('cron_log')
			->row();
		}

		return $maxLogDate;
	}

	function update_cron_log($id, $data) {
		$this->db
		->where('id', (int) $id)
		->update('cron_log', $data);
	}

	function create_cron_log($data) {
		$this->db->insert('cron_log', $data);

		return $this->db->insert_id();
	}


//   global settings table no longer in use
//	function getCronData($api_type) {
//		$cronData = $this->db
//		->where(array('api_settings' => $api_type, 'is_active' => '1'))
//		->get('global_settings')
//		->row();
//
//		if ($cronData)
//			return $cronData;
//	}

	//////////////////End Cron Job Section/////////////////////////////////////////


	/** Use bluga webthumb generate service **/
	public function blugaWebThumbs($fileNames, $urls, $user_id = '') {
		require_once(BASEPATH.'libraries/Bluga/Autoload.php');
		$postRequest = false;
		$apiKey = '0ead01a59d8f241798b9f8827871a365';
		$dir = $path = $this->config->item('csv_upload_path');
		if ($user_id != '' && !is_dir($dir)) {
			mkdir($dir, 0777);
			chmod($dir, 0777);
		}

		try {
			$webthumb = new Bluga_Webthumb();
			$webthumb->setApiKey($apiKey);
			$i = 0;
			$jobs = array();
			$count = 0;
			foreach ($urls as $url) {
				try {
					if (!file_exists($dir.$fileNames[$i].'.png')) {
						//echo "File not exists if ".$dir.$fileNames[$i].'.png';
						$j = $webthumb->addUrl($url['url'], 'large', 1200, 900, $url['width'], $url['height']);
						$j->file = $fileNames[$i];
						$jobs[$count++] = $j;
						$postRequest = true;
					} else {
						//echo "File not exists else";
					}
				} catch (Exception $e) {
					echo $e->getMessage();
				}
				$i++;
			}

			if ($postRequest) {
				$webthumb->submitRequests();
				while (!$webthumb->readyToDownload()) {
					//@ob_flush();
					//flush();
					sleep(6);
					//echo "<font color='red'>Checking Job Status For graph Thumb</font><br>\n";
					$webthumb->checkJobStatus();
				}

				$webthumb->fetchAll($dir);
				foreach ($webthumb->failedJobs as $url => $job) {
					echo "No job submitted for: $url\n";
				}
			}
			//echo "FILENAME : ".$fileNames[0];
			return $fileNames[0];
		} catch (Exception $e) {

		}
	}

	function email_alert($function_name, $body) {
		$this->email->from('support@juststicky.com', 'TrackStreet');
		$this->email->to($this->config->item('alerts'));
		$this->email->subject('Alert Email'.$function_name);
		$this->email->message($body);
		$this->email->send();
	}

	function resetViolationCount() {
		$this->updateLastViolationProductCount();
		$this->db->update('store', array('last_violation_count' => 0));
		$this->db->update('products', array('is_violated' => 0));
	}

	function updateLastViolationProductCount() {
		$rs = $this->db
		->select('store_id, count(*) as cnt')
		->where('is_violated', 1)
		->group_by('store_id')
		->get('products')
		->result();

		if (count($rs) > 0) {
			foreach ($rs as $productCount) {
				$this->db
				->where('id', (int) $productCount->store_id)
				->update('store', array('last_violation_product_count' => $productCount->cnt));
			}
		}
	}
	
	// Kill process if running for more than x minutes
	function killOldProcess($processName){
		// ps -lf | grep `whoami` | grep ssProcess |  perl -ane '($h,$m,$s) = split /:/,$F[13]; kill 9, $F[3] if ($h > 1);'
		$cmd = "ps -lf | ";
		$cmd .= "grep `whoami` | ";
		$cmd .= "grep $processName | ";
		$cmd .= "grep -v 'grep' | ";
		$cmd .= 'perl -ane \'($h,$m,$s) = split /:/,$F[13]; ';
		$cmd .= 'kill 9, $F[3] if ($m >= 20);\' ';
echo "$cmd\n";
		shell_exec($cmd);
	}

	/** function checkProcessState - check to see if there is a valid running instance of ssProcess **/
	function checkProcessState($process_no = 0)
	{
		//check if process is stuck
		$this->killOldProcess('phantomjs');
		$this->killOldProcess('ssProcess');
		//$this->killOldProcess('wkhtml');
		
		// TODO: could put a loop here to launch all parallel crawlers with one command, subject to ssProcess $total
		$process_srch = '/crowl/ssProcess';
		if (isset($process_no)) 
			$process_srch .= '/'.$process_no;

		$cmd = "ps aux | grep `whoami` | grep '$process_srch' | grep -v 'grep'";
echo "$cmd\n";
		$find = shell_exec($cmd);
		if (empty($find)) {
			echo "Process Relaunch \n";
			$this->ssRelaunch(0, $process_no);
		} else {
			echo "Found: $find\n";
			echo "Process is already running. Exit.... \n";
		}
		die(); // prevent view rendering
	}
	public function domain_is_bad_for_phantomjs(){
		if(in_array($this->domain_of_screenshot, $this->bad_phantom_domains)){
			echo "bad_phantom_domain: {$this->domain_of_screenshot} \n";
			return true;
		}
		return false;
	}
	
	public function domain_is_bad_for_cropping(){
		if(in_array($this->domain_of_screenshot, $this->bad_cropping_domains)){
			echo "bad_cropping_domains: {$this->domain_of_screenshot} \n";
			return true;
		}
		return false;
	}
	
	public function domain_needs_cookies(){
		if(in_array($this->domain_of_screenshot, $this->cookie_domains)){
			echo "domain_needs_cookies: {$this->domain_of_screenshot} \n";
			return true;
		}
		return false;
	}
	
	
	function get_http_response_code($theURL) {
		$headers = get_headers($theURL);
		#print_r($headers); 
		return substr($headers[0], 9, 3);
	}
	
	/* !New Screenshot Processer */
	function ssProcess($process_no = null) {
		$path = $this->config->item('csv_upload_path');
		
		// pull one screenshot record with this clever mod
		$total = 10;
		if(isset($process_no) and $process_no !== null) {
			$sql = "SELECT * FROM screen_shots WHERE status='pending'
					AND(MOD(id,{$total})={$process_no} AND retries=0)
					OR(retries >= 1 AND DATE_SUB(NOW(), INTERVAL 1 HOUR) > retry_time)
					ORDER BY queue_time DESC LIMIT 1";

		} else {
			$sql = "SELECT * FROM screen_shots WHERE
					status='pending' AND retries=0
					OR(retries >= 1 AND DATE_SUB(NOW(), INTERVAL 1 HOUR) > retry_time)
					ORDER BY queue_time DESC LIMIT 1";
					
			//$sql = "SELECT * FROM screen_shots WHERE
			//		url not like '%gunbroker%' and
			//		status='pending'
			//		order by rand() limit 1";
		}
echo "$sql\n";//exit;
		$result = $this->db->query($sql);
		$counter = 0;
		if ($result && $result->num_rows() > 0) {
			$data = $result->row();
			//var_export($data);//exit;
			
			// transact this row
			$update_status = $this->db->query("update screen_shots set status='processing' where id = {$data->id}");
			
			// variable init
			$this->domain_of_screenshot = str_replace('www.', '', parse_url($data->url, PHP_URL_HOST));
			$today = date('Y-m-d');
			$phantomjs = $count = 1;
			$wkhtml = $bluga = $success = $fail = $wkhtml_success = $wkhtml_fail = $bluga_success = $bluga_fail = $phantomjs_success = $phantomjs_fail = $file_size = 0;
			$last_url = $data->url;
			$last_id = $data->id;
			$last_image = $data->name;
			$folder_name = $this->config->item('s3_violations_path').date('Ymd', strtotime($data->queue_time)).'/';
			$cname = $this->config->item('s3_cname');
			//need to use a tmp_ name because php won't play nice when trying to upload the image to s3 after stamping it
			$tmpName = "tmp_".$data->name;
			$finalName = $data->name;
			
			// restart failed upload
			$file_size = (file_exists($path.$tmpName)) ? filesize($path.$tmpName) : 0;
			if($file_size > $this->min_file_size){
				echo "We found an orphaned file...\n";
				echo "path: ".$path.$tmpName. "\n filesize: $file_size\n";
			}
			
			// 1. try phantomjs
			if(!$this->domain_is_bad_for_phantomjs()
			   and ($file_size < $this->min_file_size)
			   and $data->retries < 5)
			{
				$script_path = $this->config->item('file_root').'js/phantomjs/';
				$script_name = 'rasterize.js';
				$host = 'http://' . parse_url($data->url,PHP_URL_HOST);
				$which_phantomjs = trim(`which phantomjs`);
				$phantomjs_exe = !empty($which_phantomjs) ? $which_phantomjs : '/usr/local/bin/phantomjs';
				
				$proxy = $proxy_auth = '';
				if(!empty($this->ProxyIps->proxy_host)){
					$proxy = '--proxy='.$this->ProxyIps->proxy_host.':'.$this->ProxyIps->proxy_port;
					$proxy_auth = '--proxy-auth='.$this->ProxyIps->user.':'.$this->ProxyIps->pass;
				}
				
				$cache = "--disk-cache=true --max-disk-cache-size=100000"; //kilobytes
				
				// Test the connection first, then send
				$connected = false;
				$connection_attempts = 0;
				while(!$connected){
					$connection_attempts++;
					$rcode = (string)$this->get_http_response_code($host);
					echo "rcode: $rcode\n";
					if($rcode == '200')
					{
						$connected = true;
	
						// preload home page cookie
						$cookie = "";
						if($this->domain_needs_cookies()){
							$cookiePath = "/tmp/{$host}cookie.txt";
							$cookie = "--cookies-file={$cookiePath}";
							if(!file_exists($cookiePath)){
								$cmd = "$phantomjs_exe $cookie $cache $proxy $proxy_auth $script_path$script_name \"$host/\" /tmp/devnull.png";
								echo shell_exec($cmd);
							}
						}
						
						// take the screen_shot
						$cmd = "$phantomjs_exe $cookie $cache $proxy $proxy_auth $script_path$script_name \"{$data->url}\" $path$tmpName"; //nice -n 19 
						echo "$cmd\n";
						//exit;
						
						echo shell_exec($cmd);
						//break;
					}
					else{
						echo "Oops! Connection failed.\n";
						if($connection_attempts > 3){
							echo "Exiting phantom\n";
							break;
						}
						else{
							echo "Sleeping 10 seconds ...\n";
							sleep(1);
						}
					}
				}
				
				
				$test = 'No Image';
				$file_size = (file_exists($path.$tmpName)) ? filesize($path.$tmpName) : 0;
				echo "path: ".$path.$tmpName. "\n filesize: $file_size\n";
			}

			$phantomjs_fail = 1;
			$phantomjs_success = 0;
			if ($file_size > $this->min_file_size) {
				chmod($path.$tmpName, 0666);
				$test = 'from phantomjs';
				$this->_ssUrlStamp($data->url, $path.$tmpName, strtotime($data->queue_time));
				if($this->_upload_to_amazon($finalName, $path, $folder_name)){
					$phantomjs_success = 1;
					$phantomjs_fail = 0;
				}
			}
			echo "phantomjs success: $phantomjs_success \n";
			//test one
			//exit;
			
			// 2. try wkhtmltoimage
			if ($phantomjs_fail and !$this->domain_needs_cookies()) {
				$this->delete_file($path.$tmpName);
				
				//// WkhtmlToImage does not currently work. :(
				//$wkhtmlProxy = '';
				//if(!empty($this->ProxyIps->proxy_host)){ //http://user:password@myproxyserver:8080
				//	$wkhtmlProxy = '--proxy http://'.$this->ProxyIps->user.':'.$this->ProxyIps->pass.'@'.$this->ProxyIps->proxy_host.':'.$this->ProxyIps->proxy_port;
				//}
				//$cmd = "nice -n 19 wkhtmltoimage {$wkhtmlProxy} --quality 1 \"{$data->url}\" $path$tmpName";
				//exec($cmd);
				//$wkhtml = 1;
				//
				//$file_size = (file_exists($path.$tmpName)) ? filesize($path.$tmpName) : 0;
				//if ($file_size > $this->min_file_size) {
				//	//chmod($path.$tmpName, 0666);
				//	//$test = 'from wkhtmltoimage';
				//	//$this->_ssUrlStamp($data->url, $path.$tmpName, strtotime($data->queue_time));
				//	//$this->_upload_to_amazon($finalName, $path, $folder_name);
				//	//$wkhtml_success = 1;
				//}
				//else {
					// 3. try bluga
					echo "trying bluga\n";
					$bluga = 1;
					$bluga_fail = 1;
					$bluga_success = 0;

					//$wkhtml_fail = 1;
					$this->delete_file($path.$tmpName);
					$file_name[] = str_replace('.png', '', $tmpName);
					$urls = array(array('url' => $data->url, 'outputType' => 'png', 'width' => 1200, 'height' => 900));
					try {
						$snapshot_name = $this->blugaWebThumbs($file_name, $urls);
					} catch (Exception $ex) {
					}

					$file_size = (file_exists($path.$tmpName)) ? filesize($path.$tmpName) : 0;
					if ($file_size > $this->min_file_size) {
						$test = 'from blugaWebThumbs';
						$this->_ssUrlStamp($data->url, $path.$tmpName, strtotime($data->queue_time));
						if($this->_upload_to_amazon($finalName, $path, $folder_name)){
							$bluga_success = 1;
							$bluga_fail = 0;
						}
					} 
					echo "bluga success: $bluga_success \n";
				//}
			}
			
			$upload_image = $cname . $folder_name . $finalName;
			
			if (($bluga_success == 1 || $wkhtml_success == 1 || $phantomjs_success == 1)) {
				$success = 1;
				// append #OK to image name, used by front end to activate image links
				$dt = strtotime($data->queue_time);
				$ss = date('Ymd', $dt) . '/' .$finalName;
				$t1 = $dt - 60;
				$t2 = $dt + 60;
				$sql = "UPDATE products_trends_new SET ss = CONCAT(`ss`, '', '#OK') WHERE (dt BETWEEN $t1 AND $t2) AND ss='{$ss}'";
				$this->db->query($sql);
echo "$sql\n";				
			} else {
				$fail = 1;
				if($data->retries <= 6){
					$sql = "UPDATE screen_shots SET status = 'pending', retries = (retries+1), retry_time=NOW() WHERE id = {$last_id}";
					$update_status = $this->db->query($sql);
				}
			}

			if($success or $data->retries > 6){
				$this->db->where('id', $data->id);
				$this->db->delete('screen_shots');
			}
			
			$this->delete_file($path.$finalName);
			$this->delete_file($path.$tmpName);
			
			$qStr = "INSERT LOW_PRIORITY INTO screen_shot_stats set
                `today` = '$today',
                `wkhtml` = $wkhtml,
                `bluga` = $bluga,
                `phantomjs` = $phantomjs,
                `success` = $success,
                `wkhtml_success` = $wkhtml_success,
                `wkhtml_fail` = $wkhtml_fail,
                `bluga_success` =  $bluga_success,
                `bluga_fail` = $bluga_fail,
                `phantomjs_success` =  $phantomjs_success,
                `phantomjs_fail` = $phantomjs_fail,
                `count` = $count,
                `fail` = $fail,
                `last_url` =  '$last_url',
                `last_id` = $last_id,
                `last_upload_image` = '$upload_image',
                `last_image` = '$last_image'
              ON DUPLICATE KEY update  
                `wkhtml` = `wkhtml` + $wkhtml,
                `bluga` = `bluga` + $bluga,
                `phantomjs` = `phantomjs` + $phantomjs,
                `success` = `success` + $success,
                `wkhtml_success` = `wkhtml_success` + $wkhtml_success,
                `wkhtml_fail` = `wkhtml_fail` + $wkhtml_fail,
                `bluga_success` =  `bluga_success` + $bluga_success,
                `bluga_fail` = `bluga_fail` + $bluga_fail,
                `phantomjs_success` =  `phantomjs_success` + $phantomjs_success,
                `phantomjs_fail` = `phantomjs_fail` + $phantomjs_fail,
                `count` = `count` + $count,
                `fail` = `fail` + $fail,
                `last_url` =  '$last_url',
                `last_id` = $last_id,
                `last_upload_image` = '$upload_image',
                `last_image` = '$last_image'";
				
//echo "$qStr\n"; //exit;
			$this->db->query($qStr);
			
			// relaunch forever until pending screenshots exhausted
//exit;
			if($success)
				$this->ssRelaunch(0, $process_no);
			else
				$this->ssRelaunch(10, $process_no); // could be a local temporary network outage
				
		} else {
			log_message('info', "nothing found in queue...");
			echo "nothing found in queue.\n";
		}

		log_message('info', 'ssProcess End: '.date('Y-m-d H:i:s')." - DONE");
		die();
	}

	function ssRelaunch($wait = 0, $process_no = 0) {
		if ($wait > 0) {
			echo "sleeping {$wait} seconds"."\n";
			sleep($wait);
		}
		echo "respawning...\n";

		$cmd = 'echo fubar';
		if (isset($process_no)) {
			$cmd = 'php -q '.$this->config->item('file_root').'crons.php /crowl/ssProcess/'.$process_no.' &> /dev/null &';
			//$cmd = 'php '.$this->config->item('file_root').'crons.php /crowl/ssProcess/'.$process_no.' &>> ~/screenshots/'.$process_no.'.txt &';
		} else {
			$cmd = 'php -q '.$this->config->item('file_root').'crons.php /crowl/ssProcess &> /dev/null &';
			//$cmd = 'php '.$this->config->item('file_root').'crons.php /crowl/ssProcess &>> ~/screenshots/_.txt &';
		}
		echo "$cmd\n";
		exec($cmd);
		die();
	}

	// stamp image with URL, and crop it to a square
	protected function _ssUrlStamp($url, $file, $timestamp) {
		$image = false;
		$stamp_text = $url." ".date('Y-m-d H:i:s', $timestamp);
echo "stamp_text: $stamp_text\n";		
		if(file_exists($file))
			$image = imagecreatefrompng($file);
			
		//to handle the issue with empty image sent to s3
		$newName = str_replace('tmp_', '', $file);

		if ($image) {
			$iWidth = imagesx($image);

			$font = 4;
			$fontWidth = imagefontwidth($font);
			$fontheight = imagefontheight($font);
			while( ($fontWidth*strlen($stamp_text)) > $iWidth && $font > 1){
				//decrease font-width accordingly to fit image width
				$font--;
				$fontWidth = imagefontwidth($font);
				$fontheight = imagefontheight($font);
			}

			$backgroundColor = imagecolorallocate($image, 255, 255, 255);
			$textColor = imagecolorallocate($image, 255, 0, 0);
			imagefilledrectangle($image, 0, 0, $iWidth, $fontheight + 2, $backgroundColor);
			imagestring($image, $font, 1, 1, $stamp_text, $textColor);
			
			//crop
			if(!$this->domain_is_bad_for_cropping()){
				$crop_height = (int)$iWidth*0.75;
				$image_p = imagecreatetruecolor(1200, 900);
				imagecopyresampled($image_p, $image, 0, 0, 0, 0, 1200, 900, $iWidth, $crop_height);
				//$image_p = imagecreatetruecolor($iWidth, $crop_height);
				//imagecopyresampled($image_p, $image, 0, 0, 0, 0, $iWidth, $crop_height, $iWidth, $crop_height);
				$image = $image_p;
			}
			
			$created = imagepng($image, $newName);
			if($created) $this->delete_file($file);
			imagedestroy($image);
			
			// reduce filesize by converting colors to simple pallete
			if(file_exists($newName)){
				$this->convertPNGto8bitPNG($newName, $newName);
			}
			
		}else{
			@copy($file, $newName);
			$this->delete_file($file);
		}
	}
	
	// reduce file size by up to 80% !
	function convertPNGto8bitPNG($sourcePath, $destPath) {
		$srcimage = imagecreatefrompng($sourcePath);
		list($width, $height) = getimagesize($sourcePath);
		$img = imagecreatetruecolor($width, $height);
		$bga = imagecolorallocatealpha($img, 0, 0, 0, 127);
		imagecolortransparent($img, $bga);
		imagefill($img, 0, 0, $bga);
		imagecopy($img, $srcimage, 0, 0, 0, 0, $width, $height);
		imagetruecolortopalette($img, false, 255);
		imagesavealpha($img, true);
		imagepng($img, $destPath, 8);
		imagedestroy($img);
	}
	
	protected function _upload_to_amazon($file_name, $path = '', $folder = false) {
		$this->load->library('S3');

		$bucket_name = $this->config->item('s3_bucket_name');
		if ($path == '') $path = $this->config->item('csv_upload_path');
		$folder_name = '';
		if ($folder) $folder_name = $folder;

		$s3 = new S3($this->config->item('s3_access_key'), $this->config->item('s3_secret_key'));
		echo "in _upload_to_amazon(): \n"
		//."  path: ". $path."\n"
		//."  bucket_name: $bucket_name\n"
		//."  folder: $folder_name\n"
		//."  file_name: $file_name\n"
		."  URL: http://{$bucket_name}/{$folder_name}{$file_name}\n";
		
		if (file_exists($path.$file_name)) {
			echo "final file size: ".filesize($path.$file_name)."\n";
			try {
				$s3->putObjectFile($path.$file_name, $bucket_name, $folder_name.$file_name, S3::ACL_PUBLIC_READ);
				unset($s3);
			} catch (Exception $e) {
				echo '_upload_to_amazon exception: ',$e->getMessage(), "\n$file_name\n";
				log_message('error', '_upload_to_amazon exception: ',$e->getMessage(), "\n$file_name");
				unset($s3);
				return false;
			}
		}
		//exit;
		return true;
	}

	/* prune any stuck images on the server */
	public function pruneImages(){
		//get all images that are at LEAST 5 days old
		$curShots = $this->db->query("select id, name, queue_time from screen_shots where queue_time < '".date("Y-m-d", strtotime("-5 days"))."' order by queue_time asc limit 20000 for update")->result();
		$imagePath = $this->config->item('csv_upload_path');
		foreach($curShots as $shot){
			$tmpName = "tmp_".$shot->name;
			$oldName = $shot->name;
			$shotDate = date("Ymd", strtotime($shot->queue_time));
			$folderName = $this->config->item('s3_violations_path').$shotDate.'/';

			if(file_exists($imagePath.$oldName)){
				$file_size = filesize($imagePath.$oldName);
				if($file_size > $this->min_file_size){
					$iInfo = getimagesize($imagePath.$oldName);
					if($iInfo[0] > 0 && $iInfo[1] > 0){
						if($this->_upload_to_amazon($oldName, $this->config->item('csv_upload_path'), $folderName) !== false){
							log_message('info', "uploaded: $oldName, {$this->config->item('csv_upload_path')}, $folderName");
							log_message('info', "deleting screen_shots id: {$shot->id}");
							$this->db->where('id', $shot->id);
							$this->db->delete('screen_shots');
							$this->delete_file($imagePath.$oldName);
						}else{
							log_message('info', "image upload failed: $oldName, {$this->config->item('csv_upload_path')}, $folderName");
						}
					}else{
						log_message('info', "no image info ".var_export($iInfo, true).": update screen_shots set status='pending' where id=".$shot->id);
						$this->db->query("update screen_shots set status='pending' where id=".$shot->id);
						$this->delete_file($imagePath.$oldName);
					}
				}elseif($file_size == 0){
					log_message('info', "no size $file_size: update screen_shots set status='pending' where id=".$shot->id);
					$this->db->query("update screen_shots set status='pending' where id=".$shot->id);
					$this->delete_file($imagePath.$oldName);
				}
			}else{
				$sUrl = $this->config->item('s3_cname').$folderName.$oldName;
				log_message('info', "file does not exist: $imagePath$oldName\nChecking S3: $sUrl");
                
				if (@fopen($sUrl, 'r')) {
					log_message('info', "found $sUrl");
					$this->db->where('id', $shot->id);
					$this->db->delete('screen_shots');
				}else{
					//at this point the image is probably a lost cause
					if(time() - strtotime($shot->queue_time) > (5 * 86400)){
						//image should have been taken more than 5 days ago
						//echo (time() - strtotime($shot->queue_time))." >".(5 * 86400)." > 5 days\n".time()." ".strtotime($shot->queue_time)."\n".$shot->queue_time."\n";
						$this->db->where('id', $shot->id);
						$this->db->delete('screen_shots');
					}else{
						log_message('info', "$sUrl not found\nupdate screen_shots set status='pending' where id=".$shot->id);
						$this->db->query("update screen_shots set status='pending' where id=".$shot->id);
					}
				}
			}
            /*elseif(file_exists($imagePath.$tmpName)){
				//we'll need this when we're handling stalled pre-stamped images
				$file_size = filesize($imagePath.$tmpName);
			}*/
		}

		//now manually prune images from directory
		$this->_imgDirectoryCleanup();
		die();
	}

	protected function _imgDirectoryCleanup(){
		$path = $this->config->item('csv_upload_path');

		$date = strtotime("-5 days");
		$date1 = strtotime("-1 day");
		$images = scandir($path);

		for($i=0, $n=sizeof($images); $i<$n; $i++){
			if(is_file($path.$images[$i]) && strrpos($path.$images[$i], '.png', -4)){
				$fName = $images[$i];
				$fTime = filemtime($path.$images[$i]);
				$now = time();

				if(strpos($fName, 'tmp_') !== false && ( ($now-$fTime) > (1*86400) && ($now-$fTime) < (5*86400) )){
					//check to see if there's a tmp version that is older than 1 day that stalled prior to stamping
					//these were from the endless loop of font reduction - should be correctid now
					$oName = str_replace('tmp_', '', $fName);
					$shot = $this->db->query("select * from screen_shots where name='$oName'")->result();
					if(count($shot) > 0){
						$curImg = $shot[0];
						$this->_ssUrlStamp($curImg->url, $path.$fName, strtotime($curImg->queue_time));
						$this->_upload_to_amazon($oName, $path, $this->config->item('s3_violations_path').date('Ymd', strtotime($curImg->queue_time)).'/');
						$this->db->where('id', $curImg->id);
						$this->db->delete('screen_shots');
						$this->delete_file($path.$oName);
					}else{
						$this->delete_file($path.$fName);
					}
				}elseif(($date-$fTime) > (5*86400)){
					//file is older than your 5 day pruning window
					//20 kb seems to show non-blank images
					if( filesize($path.$images[$i]) <= $this->min_file_size){
						//delete & remove from db
						$this->delete_file($path.$images[$i]);
						$this->db->query("delete from screen_shots where name ='".$images[$i]."' and queue_time > '".date("Y-m-d 00:00:00", $fTime)."' and queue_time < '".date("Y-m-d 23:59:59", $fTime)."'");
					}else{
						$iInfo = getimagesize($path.$images[$i]);
						if($iInfo[0] > 0 && $iInfo[1] > 0){
							//upload to amazon
							$this->_upload_to_amazon($images[$i], $path, $this->config->item('s3_violations_path').date("Ymd", $fTime).'/');
						}
						$this->delete_file($path.$images[$i]);
						$this->db->query("delete from screen_shots where name ='".$images[$i]."'");
					}
				}
			}
		}
	}
	
	function delete_file($fname){
		if(file_exists($fname)){
			unlink($fname);
		}
	}

	// @deprecated
	// HTTP HEAD request, to see if the page exists or not.
	// it's better to test the min file size, and cheaper
	/* usage:
				if(!$this->test_fetch_http_header($data->url)){
					exit;
					return;
				}
	*/
	//public function test_fetch_http_header($url){
	//	$ch = curl_init($url);
	//	curl_setopt($ch, CURLOPT_NOBODY, true);
	//	if(!empty($this->ProxyIps->proxy_host)){
	//		curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
	//		curl_setopt($ch, CURLOPT_PROXY          , $this->ProxyIps->proxy_host.':'.$this->ProxyIps->proxy_port);
	//		curl_setopt($ch, CURLOPT_PROXYUSERPWD   , $this->ProxyIps->user.':'.$this->ProxyIps->pass);
	//	}
	//	curl_exec($ch);
	//	$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // $retcode >= 400 -> not found, $retcode = 200, found.
	//	echo "retcode: $retcode\n";
	//	curl_close($ch);
	//	if((int)$retcode == 404){
	//		return false;
	//	}
	//	return true;
	//}

	
	/**
	 * Screen Shot function
	 * @deprecated
	 **/
	
	/** Making Call for Graph Thumbnail **/
	//public function generateScreenshots() {
	//	sfLoader::loadHelpers(array('Url'));
	//	$urls = array(array('url' => url_for('user/graphic?type=sentiment&id='.$this->procInstId.'&start_date='.$this->prevTime.'&end_date='.$this->currentTime, array('absolute' => true)), 'width' => 910, 'height' => 310),
	//		array('url' => url_for('user/graphic?type=sentiment&id='.$this->procInstId.'&social=facebook&start_date='.$this->prevTime.'&end_date='.$this->currentTime, array('absolute' => true)), 'width' => 910, 'height' => 310),
	//		array('url' => url_for('user/graphic?type=sentiment&id='.$this->procInstId.'&social=twitter&start_date='.$this->prevTime.'&end_date='.$this->currentTime, array('absolute' => true)), 'width' => 910, 'height' => 310),
	//		array('url' => url_for('instance/map?visualmap=1&region=world&id='.$this->procInstId.'&mapwidth=695&mapheight=270&zoomin=030', array('absolute' => true)), 'width' => 695, 'height' => 270),
	//		array('url' => url_for('instance/map?visualmap=1&region=world&id='.$this->procInstId.'&mapwidth=695&mapheight=270&zoomin=155', array('absolute' => true)), 'width' => 695, 'height' => 270),
	//		array('url' => url_for('instance/map?visualmap=1&region=US&id='.$this->procInstId.'&mapwidth=695&mapheight=270', array('absolute' => true)), 'width' => 695, 'height' => 270)
	//	);
	//
	//	$fileNames = array('mention', 'facebook', 'twitter', 'map_asia', 'map_europe', 'map_usa');
	//	$i = 0;
	//
	//	$trackedItems = $this->getValue($this->procInstId, 'trackedItems');
	//	if ($trackedItems) {
	//		foreach ($trackedItems as $item) {
	//			$urls[] = array('url' => url_for('user/graphic?type=term&id='.$this->procInstId.'&start_date='.$this->prevTime.'&end_date='.$this->currentTime, array('absolute' => true)).'/term/'.urlencode($item), 'width' => 910, 'height' => 310);
	//			$fileNames[] = 'term_'.$i;
	//			$urls[] = array('url' => url_for('user/graphic?type=term&id='.$this->procInstId.'&start_date='.$this->prevTime.'&end_date='.$this->currentTime, array('absolute' => true)).'/term/'.urlencode($item).'/chart/twitter', 'width' => 910, 'height' => 310);
	//			$fileNames[] = 'term_rate_'.$i;
	//			$i++;
	//		}
	//	}
	//
	//	$this->blugaWebThumbs($fileNames, $urls);
	//}
}
