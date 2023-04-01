<?php
class Savedreports extends MY_Controller
{
	function Savedreports()
	{
		parent::__construct();

		$this->load->model("report_m", 'Report');

		$this->javascript('views/savedreports.js.php');
	}

	function index(){
		$this->data->savedReports = array();

		$reports = $this->Report->get_report_list();
		for($i=0, $n=sizeof($reports); $i<$n; $i++){
			$this->data->savedReports[$i] = $reports[$i];
			$this->data->savedReports[$i]->hasSchedule = false;
			$this->data->savedReports[$i]->report_datetime = $this->data->savedReports[$i]->hh = $this->data->savedReports[$i]->mm = $this->data->savedReports[$i]->ampm = $this->data->savedReports[$i]->report_recursive_frequency = '';
			$this->data->savedReports[$i]->email_addresses = array();

			//if... something to find report id parameter
			$where = json_decode($reports[$i]->report_where, true);
			$this->data->savedReports[$i]->rlink = $this->_construct_report_link($reports[$i]->controller, $reports[$i]->controller_function, $reports[$i]->id);

			//get schedule info if exists
			if(($schedule = $this->Report->get_schedule($reports[$i]->id))){
				$this->data->savedReports[$i]->hasSchedule = true;
				$sch = $schedule[0];
				$stamp = strtotime($sch->report_datetime);
				$this->data->savedReports[$i]->report_datetime = date("m-d-Y", $stamp);
				$this->data->savedReports[$i]->hh = date("h", $stamp);
				$this->data->savedReports[$i]->mm = date("i", $stamp);
				$this->data->savedReports[$i]->ampm = date("a", $stamp);
				$this->data->savedReports[$i]->report_recursive_frequency = $sch->report_recursive_frequency;
				$this->data->savedReports[$i]->email_addresses = explode(',', $sch->email_addresses);
			}
		}
	}

	public function save_report(){
		$this->_response_type('json');

		//information from the form
		$report_id = $this->input->post('report_id');
		$report_is_recursive = (int)$this->input->post('report_recursive_frequency')>0 ? true : false;

		$stamp = date("Y-m-d H:i:s");

		//information for the db
		$rData['report_name'] = $this->input->post('report_name');
		$rData['controller'] = $this->input->post('controller');
		$rData['controller_function'] = $this->input->post('controller_function');
		$rData['store_id'] = $this->store_id;
		$rData['datetime'] = $stamp;

		//TODO - the report_where needs some help
		//it's not all that consistent from form to form
		//when saving from this page, the "report_where" should stay mostly in-tact
		//the user only has the option to alter recurrance - leave everything else alone
		$rData['report_where'] = array();
		$where = $this->input->post('report_where');
		if(!empty($where)){
			$rData['report_where'] = json_decode($where, true);
		}
		$rData['report_where'] = json_encode($rData['report_where']);

		if ($report_is_recursive) {
			$scheData['report_recursive_frequency'] = $this->input->post('report_recursive_frequency');
			$scheData['datetime'] = $stamp;
			$scheData['email_addresses'] = is_array($this->input->post('email_addresses')) ? join(',', $this->input->post('email_addresses')) : $this->input->post('email_addresses');

			$scheData['report_datetime'] = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $this->input->post('report_datetime')).' '.$this->input->post('hh').':'.$this->input->post('mm').':00'.' '.$this->input->post('ampm')));
			/* Added for handling time if passed time is selected */
			$timeSelected = strtotime($scheData['report_datetime']);
			$actualTime = time();
			if ($timeSelected < $actualTime) {
				$sec5min = 300;
				$scheData['report_datetime'] = date('Y-m-d H:i:00', ceil(($actualTime)/$sec5min)*$sec5min);
			}
		}

		if($report_id){
			//update exising report
			$this->Report->update_report($rData, $report_id);
			if($report_is_recursive){
				$scheData['saved_reports_id'] = $report_id;
				$this->Report->update_schedule_report($scheData, $report_id);
			}else{
				$this->Report->delete_schedule_report($report_id);
			}
			$this->data->message = 'Report successfully updated.';
		}else{
			//insert new report
			$report_id = $this->Report->add_report($rData);
			if($report_is_recursive){
				$scheData['saved_reports_id'] = $report_id;
				$this->Report->add_schedule_report($scheData);
			}
			$this->data->message = 'Report successfully added.';
		}

		$this->data->status = 'success';
	}

	function delete(){
		$this->_response_type('json');

		if(($id = $this->input->post('id'))){
			$this->Report->delete_report($id);
			$this->data = array(
				'result'=>'success',
				'message'=>'<span style="color: green">Report has been successfully deleted.</span>'
			);
		}else{
			$this->data = array(
				'result'=>'error',
				'message'=>'<span class="error">There is some error while deleting report please try later.</span>'
			);
		}
	}

	/*
	* @param
	* $c = controller
	* $cf = controller function
	• $id = id of report/merchant/etc.
	*/
	private function _construct_report_link($c, $cf, $id = NULL){
		$link = site_url($c.( ! empty($cf)?'/'.$cf:''));
		$savedReport = $this->Report->get_save_report_by_id($id);
		if ($savedReport)
			$where = json_decode($savedReport['report_where'], TRUE);

		//need to account for the old 'edit' cf
		//$link .= 'reports/edit/'.urlencode(base64_encode($data->id));
		switch($c){
			case 'overview':
				$link = 'overview/index';
				break;
			case 'reports':
				$link = 'reports/view';
				break;
			case 'violations':
				$link = 'violations/view';
				break;
			case 'violationoverview':
				switch($cf){
					case 'violator_report':
						$link .= '/'.$where['merchant_id'];
					case 'violated_product':
						if($cf == 'violated_product') $link .= '/'.$where['product_id'];
						break;
					case 'report_marketplace':
						$link .= '/'.$where['marketplace'];
						break;
					case 'index':
					default:

						break;
				}
				break;
			case 'whois':
				switch($cf){
					case 'report_marketplace':
						$link .= '/'.$where['marketplace'];
						break;
					case 'report_merchant':
						$link .= '/'.$where['marketplace'].'/'.$where['merchant_id'];
						break;
					case 'index':
						break;
				}
				break;
			}

		if($id) $link .= '/' . urlencode(base64_encode($id));
		//$link = site_url($link);

		return $link;
	}

}