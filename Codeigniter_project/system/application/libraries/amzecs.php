<?php
/**
 * Amazon ECS Class
 * http://www.amazon.com
 * =====================
 *
 * This library estends the Product Advertising API by Amazon (formerly ECS).
 *
 * Requirement is the PHP extension SOAP.
 *
 */

require(APPPATH.'libraries/AmazonECS.php');

class Amzecs extends AmazonECS
{
	var $CONFIG;
	var $CON;

	var $cookies;
	var $proxies;
	var $scrape;

	function Amzecs(){
		$this->CI =& get_instance();
		$this->CI->load->database();

		$this->CI->config->load('db_tables');
		$db_tables = $this->CI->config->item('db_tables');

		$db_tables = $db_tables[$db_tables['environment']];
		$mysql_tables = $db_tables['mysql'];
		foreach ($mysql_tables as $name => $table) {
			$this->{'_table_'.$name} = $table;
		}

	  $accessKey = $this->CI->config->item('amazon_ecs_access_key');
	  $secretKey = $this->CI->config->item('amazon_ecs_secret_key');
	  $countryCode = $this->CI->config->item('amazon_ecs_country');
	  $associateTag = $this->CI->config->item('amazon_ecs_associate_tag');

		parent::__construct($accessKey, $secretKey, $countryCode, $associateTag);

		//require_once(dirname(BASEPATH).'/system/application/libraries/httpSockets.class.php');
		//we are not going to attempt to use PEAR...
		require_once 'HTTP/Request2.php';

		$this->cookies = array();
		$this->agents = array('Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)',
													'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)',
													'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1); Trident/5.0',
													'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22',
													'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/536.26.17 (KHTML, like Gecko) Version/6.0.2 Safari/536.26.17',
													'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:19.0) Gecko/20100101 Firefox/19.0',
													'Mozilla/5.0 (Windows; U; Windows NT 5.1; en) AppleWebKit/522.11.3 (KHTML, like Gecko) Version/3.0 Safari/522.11.3',
													'Mozilla/5.0 (Windows; U; Windows NT 5.1; nl; rv:1.8) Gecko/20051107 Firefox/1.5',
													'Opera/9.80 (Macintosh; Intel Mac OS X 10.8.2) Presto/2.12.388 Version/12.14',
													'Opera/9.0 (Windows NT 5.0; U; en)');

		$this->warn_max = $this->CI->config->item('warn_max');
		$this->warn_minutes = $this->CI->config->item('warn_minutes');
		$this->warn_period = $this->CI->config->item('warn_period');

	}

	private function _scrape_page($url){
		$this->proxies = getProxyIPS(TRUE, 500);

		require_once 'HTTP/Request2.php';

		if(count($this->proxies) > 0){
			foreach($this->proxies as $index => $proxy){
				$now = time();

				$totalAttempts = $proxy->fails + $proxy->connects;
				$failureRate = ($totalAttempts == 0) ? 0 : ($proxy->fails / $totalAttempts) * 100;
				$gracePeriod = ($now - strtotime($proxy->last_warn_time));

				$scheme = 'http';
				switch($proxy->scheme){
					case 'https':
					case 'HTTPS':
						//Request2 only takes http, but proxy should still work
						$scheme = 'http';
						break;
					case 'socks4':
					case 'socks5':
						$scheme = 'socks5';
						break;
				}

				$proxyString = $scheme.'://'.$proxy->proxy_host.":".$proxy->proxy_port;

				//we don't want to mess with the ones that have too many warnings
				//let's try 25% failure/connect ratio should exclude this proxy temporarily
				if($failureRate > 25 && $this->warn_period > $gracePeriod){
					log_message('error', "Skipping $proxyString due to $failureRate% failure rate.\nTime calc: $gracePeriod period: {$this->warn_period}");
					continue;
				}

				try{
					$options = array("timeout" => "5",
													"follow_redirects" => true,
													"max_redirects" => 3);

					$scrape = new HTTP_Request2($url, HTTP_Request2::METHOD_GET, $options);
					$scrape->setAdapter('curl');
					$scrape->setConfig(array('proxy_host' => trim($proxy->proxy_host),
																	'proxy_port' => trim($proxy->proxy_port)));

					$aKey = array_rand($this->agents);
					$scrape->setHeader(array('Accept-Charset' => 'utf-8',
																	'User-Agent' => $this->agents[$aKey]));

					// send http request
					$response = $scrape->send();
					$body = $response->getBody();
					$status = $response->getStatus();

					$source = parse_url($url);
					$upData = array();

					//echo "From: $proxyString\nResponse status: ".$response->getStatus()."\n";
					$this->CI->db->where("id", $proxy->id);
					$this->CI->db->set('connects', 'connects+1', FALSE);
					$this->CI->db->update($this->_table_proxy_ips);

					$updateFlag = $html = false;
					$title = '';

					if($status == '200' || $status == '304'){
						$body = preg_replace('/[^[:print:]]/', '', $body);
						$html = str_get_html($body);
						if(!$html){
							$headerLog = '';
							foreach ($response->getHeader() as $k => $v) {
								$headerLog .= "\t{$k}: {$v}\n";
							}
							log_message('error', "!method_exists\n".$response->getStatus()."\ntitle: $title\nheaders: $headerLog\n$proxyString\n$url");
						}else{
							$title = $html->find('title', 0);
							$title = ($title) ? strtolower($title->plaintext) : '';
							$html->clear();
							unset($html);
							//echo "got: $url\ntitle: $title\n";
						}
					}

					//find any known phantom sites
					if(strpos($title, 'onlinecollegesuniversity.com') || strpos($title, 'articlesdigest.info') || strpos($title, 'ihowandwhy.com')){
						$updateFlag = true;

						if((int)$proxy->warns >= ($this->warn_max*2)){
							log_message('error', "Ban status Phantom $title:\n$proxyString\n$url");
							$upData = array('use_flag' => 0, 'ban_source' => $source['host'].' - '.$title, 'ban_type' => $status, 'ban_agent' => $this->agents[$aKey]);
							unset($this->proxies[$index]);
						}else{
							log_message('error', "Phantom site $title:\n$proxyString\n$url");
						}
					}elseif ($status >= 500){
						// Server Error -- assume this is ban
						$updateFlag = true;
						log_message('error', "Ban status $status:\n$proxyString\n$url");
						$upData = array('use_flag' => 0, 'ban_source' => $source['host'], 'ban_type' => $status, 'ban_agent' => $this->agents[$aKey]);
						unset($this->proxies[$index]);
					}elseif($status == 404){
						$updateFlag = true;
						if((int)$proxy->warns >= ($this->warn_max*2)){
							log_message('error', "Ban status $status:\n$proxyString\n$url");
							$upData = array('use_flag' => 0, 'ban_source' => $source['host'], 'ban_type' => $status, 'ban_agent' => $this->agents[$aKey]);
						}else{
							log_message('error', "Warning $status:\n$proxyString\n$url");
						}
					}elseif($status >= 400){
						$updateFlag = true;
						if((int)$proxy->warns >= $this->warn_max){
							log_message('error', "Ban status $status:\n$proxyString\n$url");
							$upData = array('use_flag' => 0, 'ban_source' => $source['host'], 'ban_type' => $status, 'ban_agent' => $this->agents[$aKey]);
						}else{
							log_message('error', "Warning $status:\n$proxyString\n$url");
						}
					}

					if($updateFlag){
						$this->CI->db->set('warns', 'warns+1', FALSE);
						$this->CI->db->set('last_warn_time', 'now()', FALSE);
						$this->CI->db->where("id", $proxy->id);
						$this->CI->db->update($this->_table_proxy_ips, $upData);
					}

					foreach ($response->getCookies() as $c) {
						/* echo "\tname: {$c['name']}, value: {$c['value']}".(empty($c['expires'])? '': ", expires: {$c['expires']}").(empty($c['domain'])? '': ", domain: {$c['domain']}").(empty($c['path'])? '': ", path: {$c['path']}").", secure: ".($c['secure']? 'yes': 'no')."\n";*/
					  $scrape->addCookie($c['name'], $c['value']);
					}
					unset($scrape);
					return ($status == '200' || $status == '304') ? $body : false;

				}catch(HTTP_Request2_Exception $e){
					//do proxy deactivation here...

					//once we have a good sample & connection failure is > 75% - kill proxy
					if((int)$proxy->fails > 10 && ($failureRate > 75 || (int)$proxy->connects == 0)){
						log_message('error', "Connection Ban status {$e->getNativeCode()}:\n$proxyString\n$url\n".$e->getMessage()."\nFails: {$proxy->fails} - $failureRate%");
						$this->CI->db->where('id', $proxy->id);
						$this->CI->db->set('fails', 'fails+1', FALSE);
						$this->CI->db->set('last_warn_time', 'now()', FALSE);
						$this->CI->db->update($this->_table_proxy_ips, array('use_flag' => 0, 'ban_source' => $proxy->proxy_host.':'.$proxy->proxy_port, 'ban_type' => "Connection: ".$e->getNativeCode(), 'ban_agent' => $this->agents[$aKey]));
						unset($this->proxies[$index]);
					}else{
						log_message('error', "Connection Warning {$e->getNativeCode()}:\n$proxyString\nFails: {$proxy->fails} rate: $failureRate\n$url");
						$this->CI->db->where('id', $proxy->id);
						$this->CI->db->set('fails', 'fails+1', FALSE);
						$this->CI->db->set('last_warn_time', 'now()', FALSE);
						$this->CI->db->update($this->_table_proxy_ips);
					}
					unset($scrape);
					//return false;
				}
			}
		}else{
			log_message('error', 'We are out of proxies');
			email_alertToTeam('amzecs _scrape_page() error', 'We are out of proxies');
		}

		log_message('error', "amzecs _scrape_page() error - Neither success or failure\n$proxyString");
		email_alertToTeam('amzecs _scrape_page() error', "Neither success or failure\n$proxyString");
		return false;
	}

	/**
	 *
	 **/
	function getOffers($totalofferpages, $asin){
		$offerData = array();
		for($i=1; $i<=$totalofferpages; $i++){
			$this->optionalParameters(array('MerchantId'=>'All','Condition'=>'All','Sort'=>'price','OfferPage'=>$i));
			$response = $this->responseGroup('Large')->lookup($asin);
			if(isset($response->Items->Item->Offers->Offer)){
				$offers = $response->Items->Item->Offers->Offer;
				$offerData = array_merge($offerData, $this->getOffersData($offers));
			}
		}
		return $offerData;
	}

	function getOffersByUPCArray($UPC){
		try
		{
			if(is_array($UPC)){
				$Items = $myItems = $Offers = $asinAray = $upcFlagAr = array();
				$totalOffersPage = $totalPages = 0;
				/*Lookup from amazon by ASIN of Item*/

				$this->optionalParameters(array('MerchantId'=>'All','Condition'=>'All','IdType'=>'UPC','Sort'=>'SalesRank','SearchIndex'=>'All'));
				$response = $this->responseGroup('Large')->lookup(implode(',',$UPC));
				$this->requestCount++;
				/*Item important information*/
				if(isset($response->Items->Item)&& is_array($response->Items->Item)){
					$key = 0;
					foreach($response->Items->Item as $Item){
						$totalOffersPage = $totalPages = 0;
						$Offers[$key]['ASIN'] = (isset($Item->ASIN)) ? $Item->ASIN : $asin;
						$Offers[$key]['Title'] = (isset($Item->ItemAttributes->Title)) ? $Item->ItemAttributes->Title : '';
						$Offers[$key]['Brand'] = (isset($Item->ItemAttributes->Brand)) ? $Item->ItemAttributes->Brand : '';
						$Offers[$key]['ListPrice'] = (isset($Item->ItemAttributes->ListPrice->FormattedPrice)) ? $Item->ItemAttributes->ListPrice->FormattedPrice :'';
						$Offers[$key]['SalesRank'] = (isset($Item->SalesRank)) ? $Item->SalesRank : '';
						$Offers[$key]['Manufacturer'] = (isset($response->Items->Item->ItemAttributes->Manufacturer)) ? $Item->ItemAttributes->Manufacturer : '';
						$Offers[$key]['LargeImage'] = (isset($Item->LargeImage->URL)) ? $Item->LargeImage->URL : '';
						$Offers[$key]['MPN'] = (isset($Item->ItemAttributes->MPN)) ? $Item->ItemAttributes->MPN : '';

						if(isset($Item->ItemAttributes->UPCList->UPCListElement) && is_array($Item->ItemAttributes->UPCList->UPCListElement)){
							foreach($Item->ItemAttributes->UPCList->UPCListElement as $holdUPC){
								if(in_array($holdUPC,(array)$response->Items->Request->ItemLookupRequest->ItemId)){
									$Offers[$key]['UPC'] = $holdUPC;
									break;
								}
							}
						}else if(isset($Item->ItemAttributes->UPCList->UPCListElement)){
							$Offers[$key]['UPC'] = (isset($Item->ItemAttributes->UPCList->UPCListElement)) ?$Item->ItemAttributes->UPCList->UPCListElement : '';
						}else{
							$Offers[$key]['UPC'] = (isset($Item->ItemAttributes->UPC)) ? $Item->ItemAttributes->UPC : '';
						}

						if (isset($Offers[$key]['UPC'])) $upcFlagAr[] = $Offers[$key]['UPC'];
						$Offers[$key]['Department'] = (isset($Item->ItemAttributes->Department)) ? $Item->ItemAttributes->Department : '';
						$Offers[$key]['ProductGroup'] = (isset($Item->ItemAttributes->ProductGroup)) ? $Item->ItemAttributes->ProductGroup : '';
						$Offers[$key]['ProductTypeName'] = (isset($Item->ItemAttributes->ProductTypeName)) ? formatText($Item->ItemAttributes->ProductTypeName) : '';
						$Offers[$key]['Model'] = (isset($Item->ItemAttributes->Model)) ? $Item->ItemAttributes->Model : '';
						/*Offers important information*/
						$Offers[$key]['TotalOfferPages'] = (isset($Item->Offers->TotalOfferPages)) ? $Item->Offers->TotalOfferPages : 0;
						$Offers[$key]['TotalOffers'] = (isset($Item->Offers->TotalOffers)) ? $Item->Offers->TotalOffers : 0;
						$Offers[$key]['TotalNew'] = (isset($Item->OfferSummary->TotalNew)) ? $Item->OfferSummary->TotalNew : 0;
						$Offers[$key]['TotalUsed'] = (isset($Item->OfferSummary->TotalUsed)) ? $Item->OfferSummary->TotalUsed : 0;
						$Offers[$key]['LowestNewPrice'] = (isset($Item->OfferSummary->LowestNewPrice->FormattedPrice)) ? $Item->OfferSummary->LowestNewPrice->FormattedPrice : '';

						$asinAray[$key] = $Offers[$key]['ASIN'];
						$totalPages = $Offers[$key]['TotalNew'] + $Offers[$key]['TotalUsed'];

						if($totalPages > $totalOffersPage){
							$totalOffersPage = $totalPages;
							$Offers[$key]['TotalOffers'] = $totalPages;
						}

						//"http://www.amazon.com/gp/offer-listing/".$Offers[$key]['ASIN'];
						$urlTolink = 'http://www.amazon.com/gp/offer-listing/'.$Offers[$key]['ASIN'].'/ref=olp_seeall_fm?ie=UTF8&shipPromoFilter=0&startIndex=0&sort=sip&me=&condition=new';
						$Offers[$key]['product_link'] = $urlTolink;

						$parseData = $this->parseOfferPages($Offers[$key]['ASIN'],$totalOffersPage,$Offers[$key]['UPC']);

						$Offers[$key]['OffersData'] = $parseData;
						$key++;
					}
				}else if(isset($response->Items->Item)&& !is_array($response->Items->Item)){
						$Item = $response->Items->Item;
						$key =0;

						$Offers[$key]['ASIN'] = (isset($Item->ASIN)) ? $Item->ASIN : $asin;
						$Offers[$key]['Title'] = (isset($Item->ItemAttributes->Title)) ? $Item->ItemAttributes->Title : '';
						$Offers[$key]['Brand'] = (isset($Item->ItemAttributes->Brand)) ? $Item->ItemAttributes->Brand : '';
						$Offers[$key]['ListPrice'] = (isset($Item->ItemAttributes->ListPrice->FormattedPrice)) ? $Item->ItemAttributes->ListPrice->FormattedPrice :'';
						$Offers[$key]['SalesRank'] = (isset($Item->SalesRank)) ? $Item->SalesRank : '';
						$Offers[$key]['Manufacturer'] = (isset($response->Items->Item->ItemAttributes->Manufacturer)) ? $Item->ItemAttributes->Manufacturer : '';
						$Offers[$key]['LargeImage'] = (isset($Item->LargeImage->URL)) ? $Item->LargeImage->URL : '';
						$Offers[$key]['MPN'] = (isset($Item->ItemAttributes->MPN)) ? $Item->ItemAttributes->MPN : '';
						$Offers[$key]['UPC'] = (isset($Item->ItemAttributes->UPCList->UPCListElement)) ? $Item->ItemAttributes->UPCList->UPCListElement : '';
						$Offers[$key]['Department'] = (isset($Item->ItemAttributes->Department)) ? $Item->ItemAttributes->Department : '';
						$Offers[$key]['ProductGroup'] = (isset($Item->ItemAttributes->ProductGroup)) ? $Item->ItemAttributes->ProductGroup : '';
						$Offers[$key]['ProductTypeName'] = (isset($Item->ItemAttributes->ProductTypeName)) ? formatText($Item->ItemAttributes->ProductTypeName) : '';
						$Offers[$key]['Model'] = (isset($Item->ItemAttributes->Model)) ? $Item->ItemAttributes->Model : '';
						//Offers important information
						$Offers[$key]['TotalOfferPages'] = (isset($Item->Offers->TotalOfferPages)) ? $Item->Offers->TotalOfferPages : 0;
						$Offers[$key]['TotalOffers'] = (isset($Item->Offers->TotalOffers)) ? $Item->Offers->TotalOffers : 0;
						$Offers[$key]['TotalNew'] = (isset($Item->OfferSummary->TotalNew)) ? $Item->OfferSummary->TotalNew : 0;
						$Offers[$key]['TotalUsed'] = (isset($Item->OfferSummary->TotalUsed)) ? $Item->OfferSummary->TotalUsed : 0;
						$Offers[$key]['LowestNewPrice'] = (isset($Item->OfferSummary->LowestNewPrice->FormattedPrice)) ? $Item->OfferSummary->LowestNewPrice->FormattedPrice : '';

						$upcFlagAr[] = $Offers[$key]['UPC'];
						$asinAray[$key] = $Offers[$key]['ASIN'];
						$totalPages = $Offers[$key]['TotalNew'] + $Offers[$key]['TotalUsed'];

						if($totalPages > $totalOffersPage){
							$totalOffersPage = $totalPages;
							$Offers[$key]['TotalOffers'] = $totalPages;
						}

						$Offers[$key]['product_link'] = "http://www.amazon.com/gp/offer-listing/".$Offers[$key]['ASIN'];

						$parseData = $this->parseOfferPages($Offers[$key]['ASIN'],$totalOffersPage);
						$Offers[$key]['OffersData'] = $parseData;
				}else{
					log_message('error', 'UPC lookup returned no results - UPCs: '.implode(', ', $UPC));
				}

				if(!$Offers) sleep(1);

				return $Offers ? $Offers : false;
			}else{
				return $this->getItemsByUPC($UPC);
			}
		}
		catch(Exception $e){
			log_message('error', 'UPC lookup failed - '.$e->getMessage().' - UPCs: '.implode(', ', $UPC));
			sleep(1);
		}
	}

	public function getItemsByUPC($upc){
		try
		{
			$Items = array();
			$myItems = array();
			/*Lookup from amazon by UPC of Item*/
			$this->optionalParameters(array('MerchantId'=>'All','Condition'=>'All','IdType'=>'UPC','Sort'=>'SalesRank','OfferPage'=>1,'SearchIndex'=>'All'));
			$response = $this->responseGroup('Large')->lookup($upc);

			if(!isset($response->Items->Request->Errors)){
				$Items = $response->Items->Item;

				if(!isset($Items->ASIN)){
					foreach($Items as $key=>$value){
						$myItems[] = array('ASIN'=>(isset($Items[$key]->ASIN)) ? $Items[$key]->ASIN : '',
							'Title'=>(isset($Items[$key]->ItemAttributes->Title)) ? $Items[$key]->ItemAttributes->Title : '',
							'Brand'=>(isset($Items[$key]->ItemAttributes->Brand)) ? $Items[$key]->ItemAttributes->Brand : '',
							'SalesRank'=>(isset($Items[$key]->SalesRank)) ? $Items[$key]->SalesRank : '',
							'ListPrice'=>(isset($Items[$key]->ItemAttributes->ListPrice->FormattedPrice)) ? $Items[$key]->ItemAttributes->ListPrice->FormattedPrice : '',
							'Manufacturer'=>(isset($Items[$key]->ItemAttributes->Manufacturer)) ? $Items[$key]->ItemAttributes->Manufacturer : '',
							'LargeImage'=>(isset($Items[$key]->LargeImage->URL)) ? $Items[$key]->LargeImage->URL : '',
							'MPN'=>(isset($Items[$key]->ItemAttributes->MPN)) ? $Items[$key]->ItemAttributes->MPN : '',
							'UPC'=>(isset($Items[$key]->ItemAttributes->UPC)) ? $Items[$key]->ItemAttributes->UPC : '',
							'Department'=>(isset($Items[$key]->ItemAttributes->Department)) ? $Items[$key]->ItemAttributes->Department : '',
							'ProductGroup'=>(isset($Items[$key]->ItemAttributes->ProductGroup)) ? $Items[$key]->ItemAttributes->ProductGroup : '',
							'ProductTypeName'=>(isset($Items[$key]->ItemAttributes->ProductTypeName)) ? $this->formatText($Items[$key]->ItemAttributes->ProductTypeName) : '',
							'Model'=>(isset($Items[$key]->ItemAttributes->Model)) ? $Items[$key]->ItemAttributes->Model : '',
							'TotalOfferPages'=>(isset($Items[$key]->Offers->TotalOfferPages)) ? $Items[$key]->Offers->TotalOfferPages : '',
							'TotalOffers'=>(isset($Items[$key]->Offers->TotalOffers)) ? $Items[$key]->Offers->TotalOffers : '',
							'TotalNew'=>(isset($Items[$key]->OfferSummary->TotalNew)) ? $Items[$key]->OfferSummary->TotalNew : '',
							'TotalUsed'=>(isset($Items[$key]->OfferSummary->TotalUsed)) ? $Items[$key]->OfferSummary->TotalUsed : '',
						);
					}
				}else{
					//echo "else";
					$myItems[] = array('ASIN'=>(isset($Items->ASIN)) ? $Items->ASIN : '',
						'Title'=>(isset($Items->ItemAttributes->Title)) ? $Items->ItemAttributes->Title : '',
						'Brand'=>(isset($Items->ItemAttributes->Brand)) ? $Items->ItemAttributes->Brand : '',
						'SalesRank'=>(isset($Items->SalesRank)) ? $Items->SalesRank : '',
						'ListPrice'=>(isset($Items->ItemAttributes->ListPrice->FormattedPrice)) ? $Items->ItemAttributes->ListPrice->FormattedPrice : '',
						'Manufacturer'=>(isset($Items->ItemAttributes->Manufacturer)) ? $Items->ItemAttributes->Manufacturer : '',
						'LargeImage'=>(isset($Items->LargeImage->URL)) ? $Items->LargeImage->URL : '',
						'MPN'=>(isset($Items->ItemAttributes->MPN)) ? $Items->ItemAttributes->MPN : '',
						'UPC'=>(isset($Items->ItemAttributes->UPC)) ? $Items->ItemAttributes->UPC : '',
						'Department'=>(isset($Items->ItemAttributes->Department)) ? $Items->ItemAttributes->Department : '',
						'ProductGroup'=>(isset($Items->ItemAttributes->ProductGroup)) ? $Items->ItemAttributes->ProductGroup : '',
						'ProductTypeName'=>(isset($Items->ItemAttributes->ProductTypeName)) ? $this->formatText($Items->ItemAttributes->ProductTypeName) : '',
						'Model'=>(isset($Items->ItemAttributes->Model)) ? $Items->ItemAttributes->Model : '',
						'TotalOfferPages'=>(isset($Items->Offers->TotalOfferPages)) ? $Items->Offers->TotalOfferPages : '',
						'TotalOffers'=>(isset($Items->Offers->TotalOffers)) ? $Items->Offers->TotalOffers : '',
						'TotalNew'=>(isset($Items->OfferSummary->TotalNew)) ? $Items->OfferSummary->TotalNew : '',
						'TotalUsed'=>(isset($Items->OfferSummary->TotalUsed)) ? $Items->OfferSummary->TotalUsed : '',
					);
				}
			}else{
				email_alertToTeam('amzecs getItemsByUPC',"Error in Lookup for UPC ".$upc);
			}

			if(count($myItems) == 1){
				$myItems[0]['OffersData'] = $this->getOffers($myItems[0]['TotalOfferPages'],$myItems[0]['ASIN']);
			}else if(count($myItems) > 1){
				for($i=0; $i<count($myItems); $i++){
					$myItems[$i]['OffersData'] = $this->getOffers($myItems[$i]['TotalOfferPages'],$myItems[$i]['ASIN']);
				}
			}
			if($myItems)
				return $myItems;
			else
				return false;
		}
		catch(Exception $e){
			email_alertToTeam('amzecs getItemsByUPC',$e->getMessage());
		}
	}

	function getOffersData($offers){
		$offerData = array();
		$status = '';
		if(count($offers) > 1){
			foreach($offers as $key=>$value){
				$price = '';
				if(isset($offers[$key]->OfferListing->SalePrice->FormattedPrice)){
					$price = $offers[$key]->OfferListing->SalePrice->FormattedPrice;
					$status = 'Buy Box';
				}elseif(isset($offers[$key]->OfferListing->Price->FormattedPrice)){
					$price = $offers[$key]->OfferListing->Price->FormattedPrice;
				}

				$offerData[] = array('FormattedPrice'=>$price,
					'Rating'=>isset($offers[$key]->Merchant->AverageFeedbackRating) ? $offers[$key]->Merchant->AverageFeedbackRating : '',
					'Condition'=>isset($offers[$key]->OfferAttributes->Condition) ? $offers[$key]->OfferAttributes->Condition : '',
					'ConditionRank'=>isset($offers[$key]->OfferAttributes->Condition) ? $this->getConditionRank($offers[$key]->OfferAttributes->Condition) : '',
					'ExchangeId'=>isset($offers[$key]->OfferListing->ExchangeId)?$offers[$key]->OfferListing->ExchangeId:'',
					'Availability'=>isset($offers[$key]->OfferListing->Availability)?$offers[$key]->OfferListing->Availability:'',
					'Quantity'=>isset($offers[$key]->OfferListing->Quantity)?$offers[$key]->OfferListing->Quantity:'',
					'MerchantId'=>isset($offers[$key]->Merchant->MerchantId)?$offers[$key]->Merchant->MerchantId:'',
					'GlancePage'=>isset($offers[$key]->Merchant->GlancePage)?$offers[$key]->Merchant->GlancePage:'',
					'TotalFeedback'=>isset($offers[$key]->Merchant->TotalFeedback)?$offers[$key]->Merchant->TotalFeedback:'',
					'Shipping'=>'',
					'SellerName'=>'',
					'SellerImage'=>'',
					'FBRank'=>'',
					'Status'=>$status,
				);
				$status = '';
			}
		}else{
			$price = '';
			if(isset($offers->OfferListing->SalePrice->FormattedPrice)){
				$price = $offers->OfferListing->SalePrice->FormattedPrice;
				$status = 'Buy Box';
			}elseif(isset($offers->OfferListing->Price->FormattedPrice)){
				$price = $offers->OfferListing->Price->FormattedPrice;
			}

			$offerData[] = array('FormattedPrice'=>$price,
				'Rating'=>isset($offers->Merchant->AverageFeedbackRating) ? $offers->Merchant->AverageFeedbackRating : '',
				'Condition'=>isset($offers->OfferAttributes->Condition) ? $offers->OfferAttributes->Condition : '',
				'ConditionRank'=>isset($offers->OfferAttributes->Condition) ? $this->getConditionRank($offers->OfferAttributes->Condition) : '',
				'ExchangeId'=>isset($offers->OfferListing->ExchangeId)?$offers->OfferListing->ExchangeId:'',
				'Availability'=>isset($offers->OfferListing->Availability)?$offers->OfferListing->Availability:'',
				'Quantity'=>isset($offers->OfferListing->Quantity)?$offers->OfferListing->Quantity:'',
				'MerchantId'=>isset($offers->Merchant->MerchantId)?$offers->Merchant->MerchantId:'',
				'GlancePage'=>isset($offers->Merchant->GlancePage)?$offers->Merchant->GlancePage:'',
				'TotalFeedback'=>isset($offers->Merchant->TotalFeedback)?$offers->Merchant->TotalFeedback:'',
				'Shipping'=>'',
				'SellerName'=>'',
				'SellerImage'=>'',
				'FBRank'=>'',
				'Status'=>$status,
			);
		}
		return $offerData;
	}

	function parsingData($url, $order = 0, $upc = 0){
	   echo 'dsdfsfd';
		$items = array();

		//let's do a number of scrape attempts before moving on...
		$try = 0;
		do {
			$body = $this->_scrape_page($url);
			$try++;

			if($body){
				$body = preg_replace( '/[^[:print:]]/', '', $body);
				$html = str_get_html($body);
				if(!$html){
					email_alertToTeam(__CLASS__.'::'.__FUNCTION__.'() Alert', "Try $try - ".'unable to find html = str_get_html() for UPC '.$upc.' at '.$url, 1, $html);
					$body = false;
					$html->clear();
					unset($html);
				}

				//don't assume we have what we need...
				if(($results = $html->find('div[id=olpOfferList]', 0))){
					if(!$html->find('div[class=olpOffer]')){
						//this should be okay because we landed on a page that is valid, but contains no table
						email_alertToTeam(__CLASS__.'::'. __FUNCTION__.'() Alert', "Try $try - ".'div[class=olpOffer] not found in for UPC '.$upc.' at '.$url, 1, $results);
						$body = false;
						$html->clear();
						unset($html);
						return $items;
					}
				}else{
					email_alertToTeam(__CLASS__.'::'.__FUNCTION__.'() Alert', "Try $try - ".'div[id=olpOfferList] not found for UPC '.$upc.' at '.$url, 1, $html);
					$body = false;
					$html->clear();
					unset($html);
				}
			}

			if(!$body && $try == 9){
				log_message('error', "Unable to scrape UPC $upc after 10 attempts\n$url");
			}
		} while(!$body || $try > 9);

		//we didn't find anything after all that looping...
		if(!$body){
			//going to have to come up with plan B...
			//try to load via file_get_html()
			$try2 = 0;
			do {
				$body = file_get_html($url);
				$try2++;

				if($body){
					$body = preg_replace( '/[^[:print:]]/', '', $body);
					$html = str_get_html($body);
					if(!$html){
						email_alertToTeam(__CLASS__.'::'.__FUNCTION__.'() Alert', "file_get_html() try $try2 - ".'unable to find html = str_get_html() for UPC '.$upc.' at '.$url, 1, $html);
						$body = false;
						$html->clear();
						unset($html);
					}

					//don't assume we have what we need...
					if(($results = $html->find('div[id=olpOfferList]', 0))){
						if(!$html->find('div[class=olpOffer]')){
							//this should be okay because we landed on a page that is valid, but contains no table
							email_alertToTeam(__CLASS__.'::'. __FUNCTION__.'() Alert', "file_get_html() try $try2 - ".'div[class=olpOffer] not found in for UPC '.$upc.' at '.$url, 1, $results);
							$body = false;
							$html->clear();
							unset($html);
							return $items;
						}
					}else{
						email_alertToTeam(__CLASS__.'::'.__FUNCTION__.'() Alert', "file_get_html() try $try2 - ".'div[id=olpOfferList] not found for UPC '.$upc.' at '.$url, 1, $html);
						$body = false;
						$html->clear();
						unset($html);
					}
				}

				if(!$body && $try2 == 9){
					log_message('error', "Unable to scrape UPC $upc after 10 file_get_html() attempts\n$url");
				}
			} while(!$body || $try2 > 9);
			$body->clear();
			unset($body);
		}

		// Find Offer table
		if( ! empty($html)){
			$count = 0;
			//don't assume we have what we need...
			if($results = $html->find('div[id=olpOfferList]', 0)){
				if(($offers = $html->find('div[class=olpOffer]'))){
					foreach ($offers as $offer) {
						$count++;
						$price = 0;
						$flag = false;
						$condition = $seller_name = $seller_image = $sellerId = $seller_rank = '';

						if($price = $offer->find('span[class=olpOfferPrice]', 0)){
							$shipping = $offer->find('p span[class=olpShippingPrice]', 0);
							$ship = $shipping ? ltrim(trim($shipping->plaintext), '$') : '0.00';

							if(($seller = $offer->find('p[class=olpSellerName] a b', 0))){
								$seller_name = $seller->plaintext;
							}elseif(($imgSeller	= $offer->find('p[class=olpSellerName] a img', 0))){
								$seller_name = $imgSeller->alt;
								$seller_image = $imgSeller->src;
							}elseif(($imgSeller	= $offer->find('p[class=olpSellerName] img', 0))){
								$seller_name = $imgSeller->alt;
								$seller_image = $imgSeller->src;
							}

							if(!empty($seller_name)) $seller_name = clearnSellerName($seller_name);
							if(strtolower($seller_name) == 'amazon.com' || strtolower($seller_name) == 'amazon') $sellerId = 'Amazon.com';

							if(($rank = $offer->find('p span[class=olpSellerRating] a b', 0))){
								$seller_rank = substr($rank->plaintext, 0, 4);
							}

							$link = $offer->find('p[class=olpSellerName] a', 0);
							if($seller_name != 'Amazon.com' && isset($link->href)){
								//implement explode here - now 2 types:
								//w/o image: http://www.amazon.com/gp/aag/main/ref=olp_merch_name_1?ie=UTF8&asin=B001B4TS7U&isAmazonFulfilled=0&seller=A3QNLMW248O1LJ
								//w/ image: http://www.amazon.com/shops/A1KTH924LYM2YH/ref=olp_merch_name_2
								$url = parse_url($link->href);
								if(strpos($url['path'], 'shops') !== FALSE){
									$path = explode('/', $url['path']);
									$sellerId = isset($path[2]) ? $path[2] : '';
								}elseif(isset($url['query']) && strpos($url['query'], 'seller') !== FALSE){
									$queryParts = explode('&amp;', $url['query']);
							    foreach ($queryParts as $param) {
						        $item = explode('=', $param);
										if($item[0] == 'seller'){
											$sellerId = $item[1];
											break;
										}else{
											continue;
										}
							    }
								}
								$sellerId =	clearnSellerId($sellerId);
							}elseif($seller_name == 'Amazon.com'){
								//not sure...
								$sellerId = 'Amazon.com';
							}else{
								log_message('error', "Unable to find seller link\n".var_export($seller_name, true));
							}

							$parent = $offer->parent();
							$h2 = $parent->find('h2', 0);

							if(($cond = $offer->find('h3[class=olpCondition]',0))){
								$condition = trim($cond->plaintext);
							}

							$items[] = array(
								'price'=>ltrim(trim($price->plaintext), '$'),
								'shipping'=>$ship,
								'status'=>(strpos($h2->innertext, 'New') !== FALSE)?'Secondary':'Featured',
								'SellerID'=>$sellerId,
								'SellerName'=>$seller_name,
								'SellerImage'=>$seller_image,
								'condition'=>$condition,
								'FBRank'=>trim($seller_rank),
								'ListOrder'=>++$order,
							);
						}
					}
				}else{
					//we received some other content we're unsure of
					email_alertToTeam(__CLASS__.'::'. __FUNCTION__.'() Alert', 'Offer not found in div[class=olpOffer] for UPC '.$upc.' at '.$url, 1, var_export($results, true));
				}
			}else{
				//we received some other content we're unsure of
				email_alertToTeam(__CLASS__.'::'.__FUNCTION__.'() Alert', 'div[id=olpOfferList] not found for UPC '.$upc.' at '.$url, 1, $html);
			}
			$html->clear();
			unset($html);
		}else{
			email_alertToTeam(__CLASS__.'::'.__FUNCTION__.'() Alert', 'HTML not found for UPC '.$upc.' at '.$url, 1, var_export($html, true));
		}

		//echo "returning items:\n";var_dump($items);
		return $items;
	}

	function parseOfferPages($asin, $totalOffers, $upc=0){
		/*
try
		{
*/
			$myParseData = array();
			//amazon defaults to 10 per page
			$page = ceil($totalOffers / 10);
			$startIndex = 10;
			$page = ceil($page);
			if($page >= 1){
				for($i=1; $i<=$page; $i++){
					if($i == 1){
						$url = 'http://www.amazon.com/gp/offer-listing/'.$asin.'/ref=olp_seeall_fm?ie=UTF8&shipPromoFilter=0&startIndex=0&sort=sip&me=&condition=new';
						$parsedDataArray = $this->parsingData($url,0,$upc);
						if(count($parsedDataArray) > 0)
							$myParseData = array_merge($myParseData,$parsedDataArray);
					}else{
						$url = 'http://www.amazon.com/gp/offer-listing/'.$asin.'/ref=olp_page_next?ie=UTF8&shipPromoFilter=0&startIndex='.$startIndex.'&sort=sip&me=&condition=new';
						$index = (count($myParseData)>0)?count($myParseData)-1:0;
						$parsedDataArray = array();
						if(isset($myParseData[$index])){
							$parsedDataArray = $this->parsingData($url,$myParseData[$index]['ListOrder'],$upc);
						}else if($index=='0'){
							$parsedDataArray = $this->parsingData($url,0,$upc);
						}
						if(count($parsedDataArray) > 0) $myParseData = array_merge($myParseData,$parsedDataArray);
						$startIndex = $startIndex + 10;
					}
				}
			}
		/*
}
		catch(Exception $e){
			email_alertToTeam('Amzecs parseOfferPages',$e->getMessage());
		}
*/

		return $myParseData;
	}

	private function formatText($str){
		$str = str_replace(array('-', '_', '.','"'), ' ', $str);
		$str = trim($str);
		$str =ucwords(strtolower($str));

		return $str;
	}

	/*
	* DEPRECATED?
	*/
	function updateOffersData($offersData){
		if ( ! is_array($offersData)) $offersData = array();

		if( ! empty($offersData)){
			for ($i = 0, $n = count($offersData); $i < $n; $i++){
				$offer = $offersData[$i];
				$offer['price'] = isset($offer['price']) ? $offer['price'] : 0.00;
				$price_temp = explode('$',$offer['price']);
				$org_Price = isset($price_temp[1]) ? round($price_temp[1],2) : (float)$offer['price'];
				$offersData[$i] = array(
					'FormattedPrice'=>$offer['price'],
					'Rating'=>isset($offer['Rating'])?$offer['Rating']:($offer['FBRank'] / 100)*5,
					'Condition'=>isset($offer['condition'])?trim($offer['condition']):'',
					'ConditionRank'=>isset($offer['ConditionRank'])?$offer['ConditionRank']:'',
					'ExchangeId'=>isset($offer['ExchangeId'])?$offer['ExchangeId']:'',
					'Availability'=>isset($offer['Availability'])?$offer['Availability']:'',
					'Quantity'=>isset($offer['Quantity'])?$offer['Quantity']:'',
					'MerchantId'=>$offer['SellerID'],
					'TotalFeedback'=>isset($offer['TotalFeedback'])?$offer['TotalFeedback']:'',
					'GlancePage'=>'http://www.amazon.com/gp/help/seller/home.html?seller='.$offer['SellerID'],
					'Shipping'=>isset($offer['shipping'])?(float)$offer['shipping']:0.00,
					'SellerName'=>isset($offer['SellerName'])?$offer['SellerName']:'',
					'SellerImage'=>isset($offer['SellerImage'])?$offer['SellerImage']:'',
					'FBRank'=>isset($offer['FBRank'])?$offer['FBRank']:'',
					'Status'=>isset($offer['status'])?$offer['status']:'none',
					'FBA'=>isset($offer['FBA'])?$offer['FBA']:'',
					'ListOrder'=>isset($offer['ListOrder'])?$offer['ListOrder']:'',
					'OriginalPrice'=>$org_Price,
					'product_link'=>isset($offer['product_link'])?$offer['product_link']:'',
				);
			}
		}

		return $this->msort($offersData,'OriginalPrice');
	}

	function msort($array, $id="order"){
		$temp_array = array();
		while(count($array)>0){
			$lowest_id = 0;
			$index=0;
			foreach ($array as $item){
				if (isset($item[$id]) && $array[$lowest_id][$id]){
					if ($item[$id]<$array[$lowest_id][$id]){
						$lowest_id = $index;
					}
				}
				$index++;
			}
			$temp_array[] = $array[$lowest_id];
			$array = array_merge(array_slice($array, 0,$lowest_id), array_slice($array, $lowest_id+1));
		}

		return $temp_array;
	}

}