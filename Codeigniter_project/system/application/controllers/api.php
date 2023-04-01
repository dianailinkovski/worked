<?php
/**
 * @property user_api $user_api_m
 * @property user_auth $user_auth_m
 */

/*
	Error Codes

	1 - Email is Not Available In Request
	2 - Invalid Email ID
	3 - Email Doesn't Exists in Our Application
	4 - No Store against Specified Email ID

*/
class Api extends MY_Controller
{
  function __construct()
  {
    parent::__construct();

		$this->load->model("store_m", 'Store');
		$this->load->model("Products_m", 'Product');
		$this->load->model("report_m", 'Report');
		$this->load->model("Users_m", 'User');
		$this->load->model('violator_m', 'Violator');
	    $this->load->model("merchant_products_m");
	    $this->load->library('session');
	    $this->load->helper('http_authentication');
		$users = array('admin' => 'mypass');
    http_authentication($users);
  }

   function getUserDashBoard($email = '',$test = 0)
   {
		 $error					= array();
		 $result				= array();
		 $storeData			= array();
		 $overViewData	=	array();
	   	//debug('Reponse:', $_POST, 2);
			if(!$test)
			{
					$email = $this->input->post('email');
			}

			if($email=='' || $email == NULL)
			{
				 $error['message'] = "Email Is Empty Or Null";
				 $error['code'] 	 = "1";
				 if($test)
				 {
						debug('Error',$error,2);
				 }
				 else
				 {
						exit(json_encode($error));
				 }
			}
			else if($email !='')
			{
					 $valid = filter_var($email,FILTER_VALIDATE_EMAIL);
					 if($valid && $valid!= '')
					 {
							//Verify User
							$this->db->where('email',$email);
							$result	=	$this->db->get($this->_table_users)->result('array');
              //echo $this->db->last_query();
							if(count($result) > 0)
							{
									$user = $result[0];
									//verify whehter store exists or not
									$this->db->where('user_id',$user['id']);
									$storeData = $this->db->get('store')->result('array');
                  //echo $this->db->last_query();
									if(count($storeData) > 0)
									{
                      //debug('store data', $storeData, 2);
											$store = $storeData[0];
											$overViewData = $this->getStoreStatistics($store['id']);
											//debug('overview data', $overViewData, 2);
											if($test)
											{
												debug('Response',$overViewData,2);
											}
											else
											{
												exit(json_encode($overViewData));
											}

									}
									else
									{
										$error['message'] = "Store Does Not Exist For Email : ".$email;
										$error['code'] 	 	= "4";
										if($test)
										{
											debug('Error',$error,2);
										}
										else
										{
								 			exit(json_encode($error));
										}
									}

							}
							else
							{
									$error['message'] = "Email Does Not Exist : ".$email;
									$error['code'] 	  = "3";
							 		if($test)
									{
											debug('Error',$error,2);
									}
									else
									{
								 			exit(json_encode($error));
									}
							}

					 }
					 else
					 {
							 $error['message'] = "Invalid Email ID : ".$email;
							 $error['code'] 	 = "2";
							 if($test)
							 {
									debug('Error',$error,2);
							 }
							 else
							 {
										exit(json_encode($error));
							 }

					 }
			}
   }

	 private function getStoreStatistics($store_id)
	 {
		  $storeData = array('report_name'	=>	'StickVision Overview','store_id'=>$store_id);
			$storeData['number_of_merchants']	= getNumberOfMerchants($store_id);
			$lastTrackedTime 								= getTrackedTime($store_id);
			$storeData['last_tracked_date'] 		= trackingDateFormat($lastTrackedTime);
			$storeData['endTrackedTIme'] 		= getEndTrackTime($lastTrackedTime);
			$storeData['totalProducts']  		= count($this->Product->getByStore($store_id));
			$request_info['fromDate'] 			= strtotime($lastTrackedTime);
			$request_info['toDate'] 				= strtotime($storeData['endTrackedTIme']);
			//$storeData 											= $this->Store->get_store_info($store_id);

			$storeData['total_violations'] 		= $this->Violator->countViolatedProducts($store_id);
			$notViolation = $storeData['totalProducts'] - $storeData['total_violations'];
			$notViolation = ($notViolation < 0)?0:$notViolation;
			$storeData['notViolation']			=	$notViolation;

			$violationPercentage = 0;
			if($storeData['totalProducts'] > 0)
			{
				$violationPercentage = number_format(($storeData['total_violations']/$storeData['totalProducts'])*100,2);
			}

			$remaing = number_format(100 - (float)$violationPercentage,2);
			$violationPercentage = (float)$violationPercentage;
			$remaing			 = (float)$remaing;
			$gData = array($storeData['total_violations'].' In violations'=>0,$notViolation.' Not In Violation'=>$remaing);//,' Not In Violations'=>0	 //$notViolation
		//echo "$violationPercentage=======$remaing";
			if($storeData['totalProducts'] > 0)
			{
		  	 $gData[$storeData['total_violations'].' In violations'] = $violationPercentage; //In violation
		   	 $gData[$notViolation.' Not In Violation'] = $remaing;
		  // $gData[$this->data->totalProducts.' products'] = $this->data->totalProducts;//$notViolation  $notViolation Not in violation

			}

			$googleDataArray = array();
			$googleDataArray[0] = array('State','Count');
			$googleDataArray[]  =  array('Non Violation',$notViolation);
			$googleDataArray[]  =  array('Violation',(int)$storeData['total_violations']);
			$gData['googleData'] = $googleDataArray;
			$gData['type']		 = 'pie';
			$gData['width']		 = '220';
			$gData['height']	 = '150';



			$storeData['gData'] = $gData;

    // who's selling my products - marketplace data
    	$storeData['marketplace_products'] = $this->merchant_products_m->getCountByMarketplace($store_id);

    // get violations count for each market
    	$storeData['market_violations'] 		= $this->Violator->getViolatedMarkets($store_id);

    // get count for products monitored
    	$storeData['products_monitored'] = $this->Product->getProductsMonitoredCount($store_id);

		  return $storeData;

	 }

}
