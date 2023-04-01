<?php

class Account extends MY_Controller 
{
	private $arrayData;

	function Account()
	{
		parent::__construct();
                $this->load->library('session');
		$this->load->library('form_validation');
		$this->load->model("store_m", 'Store');
		$this->load->model('Users_m',"User");
		$this->load->model("report_m", 'Report');
		$this->load->model('account_m',"account");
		$this->load->library('validation');
	}
	
	function test_function()
	{
	    echo 'nothing'; exit();
	}

	function accept_terms(){
		$this->data->url = $this->config->item('gsession_base_url') . 'terms/index/' . $this->config->item('sticky_app_name');
	}

	function index()
	{
	  /*    
    $this->data->merchant_info = $this->User->get_one($this->user_id);
    
    if($this->User->get_violation_info($this->user_id, 'violation'))
    	$this->data->violation_info = $this->User->get_violation_info($this->user_id, 'violation');
    else
    	$this->data->violation_info = array();
    
    if($this->User->get_summaries_info($this->user_id, 'summaries'))
    	$this->data->summaries_info = $this->User->get_summaries_info($this->user_id, 'summaries');
    else
    	$this->data->summaries_info  = array();
    
    if($this->User->get_team_members($this->user_id))
    	$this->data->team_members_info = $this->User->get_team_members($this->user_id);
    else
    	$this->data->team_members_info  = array();
    
		$this->data->merchant_logo = $this->account->get_merchant_thumb($this->store_id);
		*/

	  redirect('/account/profile');
	  exit();    
	}

    /**
     * Ability for user to switch between stores they are part of.
     * 
     * @author unknown, Christophe
     */
    public function switch_brand()
    {
		    $brandId = $this->input->post('switchBrand');
		    
		    $this->_switch_brand($brandId);
		    
		    redirect($_SERVER['HTTP_REFERER']);
    }

	function refresh_emails(){
		$this->_response_type('json');
		if($this->User->get_violation_info($this->user_id, 'violation'))
			$this->data->violation_info = $this->User->get_violation_info($this->user_id, 'violation');
		else
			$this->data->violation_info = array();

		if($this->User->get_summaries_info($this->user_id, 'summaries'))
			$this->data->summaries_info = $this->User->get_summaries_info($this->user_id, 'summaries');
		else
			$this->data->summaries_info  = array();
		$this->data->violation = '';
		if(count($this->data->violation_info)> 0){
			if(trim($this->data->violation_info[0]->email) != ''){
				$email_list = explode(',', $this->data->violation_info[0]->email);
				$violation_emails = 0;
				for($i=0; $i<count($email_list)-1; $i++){
					$title = '';
					$link = '';
					$violation_emails = ($i +1);
					if($i == 0){
						$title = 'Add Email Address';
						$link = '<div class="accordian_open" style="float:left;margin:0px;" style="float:left"><a href="javascript:void(0);" onclick="render_another_violation_emails();" > </a></div>';
					}else{
						$title = 'Another Email Address';
						$link = '<div class="accordian_delete" style="float:left;margin:0px;"><a onclick="remove_email_summaries_db(\''.$this->data->violation_info[0]->id.'\', \'violation_'.($i+1).'\',\''.trim($email_list[$i]).'\')" style="cursor: pointer;" href="javascript:void(0)"></a></div>';
					}
					$this->data->violation .= '<div class="row_dat" id="violation_'.($i+1).'"><div class="lbel" style="width:155px !important;">'.$title.'</div><div class="lbl_inpuCnt"><input type="text" class="account_med" name="violation_email_'.($i+1).'" id="violation_email_'.($i+1).'"   value="'.trim($email_list[$i]).'" /></div>'.$link.'<div  class="clear"></div></div>';
				}
			}

		}
		$this->data->summaries = '';
		if(count($this->data->summaries_info)> 0){
			if(trim($this->data->summaries_info[0]->email) != ''){
				$email_list = explode(',', $this->data->summaries_info[0]->email);
				$summeries_emails = 0;
				for($i=0; $i<count($email_list)-1; $i++){
					$title = '';
					$link = '';
					$summeries_emails = ($i +1);
					if($i == 0){
						$title = 'Add Email Address';
						$link = '<div class="accordian_open" style="float:left;margin:0px;" style="float:left"><a href="javascript:void(0);" onclick="render_another_summaries_emails();" > </a></div>';
					}else{
						$title = 'Another Email Address';
						$link = '<div class="accordian_delete" style="float:left;margin:0px;"><a onclick="remove_email_summaries_db(\''.$this->data->summaries_info[0]->id.'\', \'sumarries_'.($i+1).'\',\''.trim($email_list[$i]).'\')" style="cursor: pointer;" href="javascript:void(0)"></a></div>';
					}
					$this->data->summaries .= '<div class="row_dat" id="sumarries_'.($i+1).'"><div class="lbel" style="width:155px !important;">'.$title.'</div><div class="lbl_inpuCnt"><input type="text" class="account_med" name="summaries_email_'.($i+1).'" id="summaries_email_'.($i+1).'"   value="'.trim($email_list[$i]).'" /></div>'.$link.'<div  class="clear"></div></div>';
				}
			}
		}
	}

	function edit_setting(){
		$this->_response_type('json');

		$this->data->html = "Unable to update Account Information.";
		$this->data->result = "failed";
		$this->data->div_class = 'error';

		$rules = array(
			array(
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'trim|required|valid_email'
			)
		);
		$account_info = array();
		$this->form_validation->set_rules($rules);
		if($this->input->post("edit_setting_button")){
			if ($this->form_validation->run()){
				$account_info['email'] = $this->input->post('email');
				$user_id  = $this->input->post('merchantID');
				$checkEmail = $this->User->validateEmail($user_id,$account_info['email']);
				if($checkEmail){
					$this->data->html = $account_info['email'].' email already exist';
					$this->data->result = "failed";
					$this->data->div_class = 'error';
				}else{
					if($this->User->update($this->user_id, $account_info)){
						$this->data->html = "Account Information updated successfully.";
						$this->data->result = "success";
						$this->data->div_class = 'success';
					}
				}
			}
		}
	}
	
    /**
     * Page where user can edit their profile.
     * 
     * @author Christophe
     * @param string $user_uuid
     */
    public function edit_profile()
    {
        $this->load->library('form_validation');
        $this->load->library('Vision_users');
        
        $view_data['success_msg'] = '';
        $view_data['error_msg'] = '';
        
        $user_id = intval($this->user_id);
        
        $user = $this->User->get_user_by_id($user_id);
        
        $this->form_validation->set_rules('first_name', 'First Name', 'xss_clean|required|trim');
        $this->form_validation->set_rules('last_name', 'Last Name', 'xss_clean|required|trim');
        $this->form_validation->set_rules('email', 'Email Address', 'trim|valid_email|xss_clean|required');
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'xss_clean');
        
        if ($this->form_validation->run() == FALSE)
        {
            // display the form & set the flash data error message if there is one
            //$view_data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
        }
        else
        {
            $update_data = array(
                'first_name' => $this->input->post('first_name', TRUE),       
                'last_name' => $this->input->post('last_name', TRUE),
                'email' => $this->input->post('email', TRUE),
                'phone_number' => $this->input->post('phone_number', TRUE),
            );
            
            $this->User->update_user($user_id, $update_data);
            
            $this->session->set_flashdata('success_msg', 'Your profile has been successfully updated.');
            
            redirect('/account/profile');
            exit();
        }
        
        $view_data['user'] = $user;
        
        $this->load->view('account/edit_profile', $view_data);        
    }

    /**
     * Page where logged in user can change their password.
     * 
     * @author Christophe
     */
    public function change_password()
    {
        $this->load->library('form_validation');
        $this->load->library('Vision_users');
        
        $view_data['success_msg'] = '';
        $view_data['error_msg'] = '';
        
        $this->form_validation->set_rules('new', 'Password', 'required|min_length[8]|max_length[30]|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', 'Confirm Password', 'required');
        
        if ($this->form_validation->run() == FALSE)
        {
            // display the form & set the flash data error message if there is one
            $view_data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
        }
        else
        {            
            $email = $this->session->userdata('user_email');
            
            $old_password = '';
            $new_password = $this->input->post('new', TRUE);
            
            $change = $this->vision_users->change_password($email, $old_password, $new_password);
            
            if ($change['type'] == 'success')
            {                	
                $view_data['success_msg'] = $change['message'];
            }
            else
            {
                $view_data['error_msg'] = $change['message'];
            }
        }        
        
        $this->load->view('account/change_password', $view_data);
    }

    /**
     * Handle uploading profile photo to Amazon S3.
     * 
     * @author Christophe
     */
    public function upload_profile_photo()
    {
        $this->load->model('users_m');
        
        //var_dump($_FILES);
        //var_dump($_POST);
        //exit();  
        
        if ($_FILES['profile_photo']['name'] == '')
        {
            $this->session->set_flashdata("error_msg", 'Error: No file selected. Please click on Browse to select a new photo.');
            
            redirect(base_url() . 'account/edit_profile');
        }
        
        if ($_FILES['profile_photo']['error'] == 0)
        {
            //upload and update the file
            $logo_name = $this->config->item('environment') . '_' . time() . '_' . $this->user_id . '_profile.jpg';
            $thumb_name = $this->config->item('environment') . '_' . time() . '_' . $this->user_id . '_profile_thumb.jpg';
            
            $config['upload_path'] = './uploaded_files/brand_logo_images/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['overwrite'] = true;
            $config['remove_spaces'] = true;
            $config['file_name'] = $logo_name;
            
            $this->load->library('upload', $config);
            
            if (!$this->upload->do_upload('profile_photo'))
            {
                $this->session->set_flashdata("error_msg", '<p>Unable to upload image.</p>' . $this->upload->display_errors());
                
                redirect(base_url() . 'account/edit_profile');
            }
            else
            {
                $config['source_image'] = './uploaded_files/brand_logo_images/' . $logo_name;
                $config['maintain_ratio'] = TRUE;
                $config['create_thumb'] = TRUE;
                $config['width'] = 64;
                $config['height'] = 64;
                
                $this->load->library('image_lib', $config);
                $this->image_lib->resize();
                
                if (!$this->image_lib->resize()) 
                {
                    $upload_error = $this->image_lib->display_errors();
                    
                    $this->image_lib->clear();
                    
                    $this->session->set_flashdata("error_msg", 'Error: ' . $upload_error);
                    
                    redirect(base_url() . 'account/edit_profile');
                    
                    exit;
                }
                else
                {
                    $has_error = false;
                    
                    $this->load->library('S3');
                    
                    $s3 = new S3($this->config->item('s3_access_key'), $this->config->item('s3_secret_key'));
                    
                    $s3Folder = 'stickyvision/profile_photos/';
                    
                    if (file_exists($config['upload_path'] . $logo_name)) 
                    {
                        if ($put = $s3->putObjectFile($config['upload_path'] . $logo_name, $this->config->item('s3_bucket_name'), $s3Folder . $logo_name, S3::ACL_PUBLIC_READ)) 
                        {
                             
                        }
                        else
                        {
                            $has_error = true;
                        	
                            $this->file_upload_error = 'Could not upload logo to server.';
                        }
                        
                        @unlink($config['upload_path'] . $logo_name);
                    }
                    
                    if (file_exists($config['upload_path'] . $thumb_name)) 
                    {
                        if ($s3->putObjectFile($config['upload_path'] . $thumb_name, $this->config->item('s3_bucket_name'), $s3Folder . $thumb_name, S3::ACL_PUBLIC_READ)) 
                        {
                            
                        }
                        else
                        {
                            $has_error = true;
                            
                            $this->file_upload_error = 'Could not upload thumbnail to server.';
                        }
                        
                        @unlink($config['upload_path'] . $thumb_name);
                    }
                    
                    unset($s3);
                    
                    // update user record
                    $update_data = array(
                        'profile_img' => $logo_name,              
                        'profile_img_thumb' => $thumb_name            
                    );
                    
                    $this->users_m->update_user($this->user_id, $update_data);
                
                    $this->session->set_flashdata("success_msg", 'Profile photo has been uploaded successfully.');
                
                    redirect('/account/profile');
                }
            }
        }
	}

	function getWidth($width, $height, $fixed_height = 64){
		$ratio = $fixed_height / $height;
		return $width * $ratio;
	}

	function check_email_exist($email, $type){
		if(!in_array($email, $this->arrayData[$type])){
			$this->arrayData[$type][] = $email;
			return true;
		}else{
			return false;
		}
	}

	function save_notifications_settings(){
		$this->_response_type('json');

		$violation_emails = $_POST['violation_emails'];
		$summaries_emails = $_POST['summaries_emails'];
		$this->arrayData['violation'] = array();
		$this->arrayData['summaries'] = array();

		if($violation_emails == 'Yes'){
			$this->db->delete('notifications_setting', array('user_id' => $this->user_id, 'section' => 'violation'));

			$counter = 1;
			$data['user_id'] = $this->user_id;
			$data['section'] = 'violation';
			$data['email'] = '';
			while(isset($_POST['violation_email_'.$counter])){
				if(trim($_POST['violation_email_'.$counter]) != '' && isset($_POST['violation_email_'.$counter])){
					if($this->check_email_exist($_POST['violation_email_'.$counter], 'violation'))
						$data['email'] .= $_POST['violation_email_'.$counter].', ';
				}
				$counter++;
			}
			if(trim($data['email']) != '')
				$this->db->insert('notifications_setting', $data);
		}else{
			$this->db->delete('notifications_setting', array('user_id' => $this->user_id, 'section' => 'violation'));
		}
		if($summaries_emails == 'Yes'){

			$this->db->delete('notifications_setting', array('user_id' => $this->user_id, 'section' => 'summaries'));

			$counter = 1;
			$data['user_id'] = $this->user_id;
			$data['section'] = 'summaries';
			$data['email'] = '';
			while(isset($_POST['summaries_email_'.$counter])){
				if(trim($_POST['summaries_email_'.$counter]) != '' && isset($_POST['summaries_email_'.$counter])){
					if($this->check_email_exist($_POST['summaries_email_'.$counter], 'summaries'))
						$data['email'] .= $_POST['summaries_email_'.$counter].', ';
				}
				$counter++;
			}
			if(trim($data['email']) != '')
				$this->db->insert('notifications_setting', $data);
		}else{
			$this->db->delete('notifications_setting', array('user_id' => $this->user_id, 'section' => 'summaries'));
		}
		$this->data->html = "Notifications setting has been changed successfully.";
		$this->data->result = "success";
		$this->data->div_class = 'success';
	}

	function remove_emails_db(){
		$this->_response_type('json');

		$id = $this->input->post('id');
		$email = $this->input->post('email');
		$rowData = $this->db->get_where('notifications_setting', array('id'=>$id))->result('array');
		$data_save = str_replace($email.',', '', $rowData[0]['email']);
		$this->db->where('id', $id);
		$this->db->update('notifications_setting', array('email'=>$data_save));
		$this->data->message = 'success';
	}

	function get_merchants_names($queryString){
		if(strlen($queryString) >0) {
			$query = "SELECT id as Id FROM {$this->_table_users} WHERE user_name LIKE '%".$queryString."%' AND user_active = '1' AND id!=".$this->user_id." LIMIT 10";
			$result = $this->db->query($query)->result('array');
			if($result != null){
				echo json_encode($result);
			}else{
				echo 'No Record Found';
			}
			exit;
		}
	}
	
    /**
     * Function to test out the auto-login feature with Interact.
     * 
     * @author Christophe
     */	
    public function test_interact_auto_login()
    {
        $this->load->model('users_m');
        
        //$user = $this->users_m->get_user_by_id($this->user_id);
        
        $request_url = 'https://interact.juststicky.com/api/v1/auth/external';
        
        /*
        $params_array = array(
        		'first_name' => $user['first_name'],
        		'last_name' => $user['last_name'],
            'email' => $user['email'],            
            'widget_id' => 1084,
        		'org_uuid' => '{89374-track8347-street-3435}'
        );
        */
        
        $params_array = array(
        		'first_name' => 'Christophe',
        		'last_name' => 'Sautot',
        		'email' => 'christophe@eventstaffapp.com',
        		//'widget_id' => 1084,
        		//'org_uuid' => '{89374-track8347-street-3435}'
            'org_uuid' => 'juststicky_support'
        );
        
        //$params = json_encode($params_arr);
        
        //var_dump($params);
        
        //url-ify the data for the POST
        //foreach ($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        //rtrim($fields_string, '&');
        
        // see: http://php.net/manual/en/function.curl-setopt.php
        $curl_handle = curl_init($request_url);
        curl_setopt($curl_handle, CURLOPT_POST, TRUE);
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $params_array);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
        
        $response = curl_exec($curl_handle);
        
        var_dump($response);
        
        $json_return = json_decode($response, TRUE);
        
        curl_close($curl_handle);
        
        var_dump($json_return); exit();  
    }	
	
    /**
     * Page where user can view their profile and details on their account.
     * 
     * @author Christophe
     */	
    public function profile()
    {        
        $view_data = array();
        
        $user = $this->User->get_user_by_id($this->user_id);
        
        $user['role_label'] = intval($user['role_id']) == 2 ? 'Admin' : 'Viewer';
        
        $view_data['user'] = $user;
        
        $this->load->view('account/profile', $view_data);
    }
	
    /**
     * Page where user can view other team members that are part of their store/organization.
     * 
     * @author Christophe
     */
    public function team()
    {        
        $this->page_title = 'Team Members - TrackStreet';
        
        if ($this->role_id != USER_TYPE_ADMIN)
        {
            $this->session->set_flashdata('error_msg', 'Error: You do not have the permissions to perform this action.');
            
            redirect('/');
            exit();
        }
        
        $members = $this->Store->get_users_by_store_id($this->store_id);

        for ($i = 0; $i < count($members); $i++)
        {
            $user = $this->User->get_user_by_id($members[$i]['user_id']);

            if (intval($user['role_id']) == 1)
            {
                $role_label = 'Viewer';
            }
            else
            {
                $role_label = 'Admin';
            }
            
            $members[$i]['first_name'] = $user['first_name'];
            $members[$i]['last_name'] = $user['last_name'];
            $members[$i]['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $members[$i]['email'] = $user['email'];
            $members[$i]['role'] = $role_label;
            $members[$i]['created'] = $user['created'] == '0000-00-00 00:00:00' ? 'N/A' : date('M j, Y', strtotime($user['created']));
            $members[$i]['uuid'] = $user['uuid'];
            $members[$i]['status'] = intval($user['user_active']) == 1 ? 'Active' : 'Disabled';         
        }
        
        $view_data['members'] = $members;
        $view_data['store_id'] = $this->store_id;
        
        $this->load->view('account/team', $view_data);
    }
    
    /**
     * Page where an admin user can add or connect an existing user to the 
     * store/brand they are managing.
     * 
     * @author Christophe
     */
    public function team_add()
    {
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('Vision_users');
        $this->load->model('users_m');
        $this->load->model('store_m');
        
        $this->page_title = 'Add Team Member - TrackStreet';
        
        $form_error_msgs = array();
        
        $this->form_validation->set_rules('first_name', 'First Name', 'xss_clean|required|trim');
        $this->form_validation->set_rules('last_name', 'Last Name', 'xss_clean|required|trim');
        $this->form_validation->set_rules('email', 'Email Address', 'trim|valid_email|xss_clean|required');
        $this->form_validation->set_rules('role_id', 'Permissions Role', 'xss_clean|required');
        
        // check to see if current user is an admin for this store
        if ($this->role_id != USER_TYPE_ADMIN)
        {
            $this->session->set_flashdata('error_msg', 'Error: You do not have the permissions to perform this action.');
            
            redirect('/');
            exit();
        }
        
        if ($this->form_validation->run() === FALSE)
        {
            // validation failed, or first load
        }
        else
        {                        
            $user_name = $this->input->post('first_name', TRUE) . ' ' . $this->input->post('last_name', TRUE);
            
            $email = $this->input->post('email', TRUE);
            
            $store = $this->store_m->get_store_by_id_array($this->store_id);
            
            // check to see if a TrackStreet user already exists with this email address
            $existing_user = $this->User->get_user_by_email($email);
            
            // get user info for admin creating member
            $admin = $this->User->get_user_by_id($this->user_id);
            
            if (empty($existing_user))
            {                                
                $insert_data = array(
                		'uuid' => uuid(),
                		'user_name' => $user_name,
                		'first_name' => $this->input->post('first_name', TRUE),
                		'last_name' => $this->input->post('last_name', TRUE),
                    'company_name' => $admin['company_name'],            
                		'email' => $email,
                		'salt' => md5(rand(10000, 999999999) . $user_name),
                		'password' => '', // password will be set by member when they confirm via email link
                		'signup_date' => date('Y-m-d H:i:s'),
                		'user_active' => 1,
                		'role_id' => $this->input->post('role_id', TRUE),
                		'terms_accepted' => 0,
                		'created' => date('Y-m-d H:i:s'),
                		'modified' => date('Y-m-d H:i:s')
                );
                
                $user_id = $this->users_m->insert_user($insert_data);
                
                $user_id = intval($user_id);
            }
            else
            {
                $user_id = intval($existing_user['id']);
            }
            
            // check to see if user has already been connected to this
            $user_store = $this->users_m->get_user_store_record($user_id, $this->store_id);

            if (empty($user_store))
            {
                $user = $this->users_m->get_user_by_id($user_id);
                
                // insert users_stores record
                $insert_data = array(
                    'store_id' => $this->store_id,
                    'user_id' => $user_id
                );

                $this->users_m->insert_user_store($insert_data);
                
                if (empty($existing_user))
                {
                    // send welcome email to new member
                    $subject = '[TrackStreet] Your New Account';
                    
                    $html_message = 'Dear ' . $user_name . ',<br/><br/>';
                    $html_message .= $admin['first_name'] . ' ' . $admin['last_name'] . ' has created a new account for you.<br/><br/>';
                    $html_message .= 'Please confirm your account signup by clicking on the link below:<br/><br/>';
                    $html_message .= '<a href="' . base_url() . 'signup/confirm/' . $user['uuid'] . '">Confirm My Account Here</a><br/><br/>';
                    $html_message .= 'Questions? Please feel free to contact us at any time by emailing support@trackstreet.com<br/><br/>';
                    $html_message .= 'Regards,<br/>Team TrackStreet<br/><br/><br/>';
                    
                    $text_message = strip_tags($html_message);
                    
                    $this->vision_users->sendSESEmail($email, $subject, $html_message, $text_message);
                    
                    $this->session->set_flashdata('success_msg', 'New team member has been successfully added to TrackStreet and sent a welcome email with account detials.');
                }
                else
                {
                    // send email to existing user about being added to a store
                    $subject = '[TrackStreet] Account Added to ' . $store['store_name'];
                    
                    $html_message = 'Dear ' . $existing_user['first_name'] . ' ' . $existing_user['last_name'] . ',<br/><br/>';
                    $html_message .= $admin['first_name'] . ' ' . $admin['last_name'] . ' has created a new account for you.<br/><br/>';
                    $html_message .= 'Please confirm your account signup by clicking on the link below:<br/><br/>';
                    $html_message .= '<a href="' . base_url() . '/login">Log In Here</a><br/><br/>';
                    $html_message .= 'Questions? Please feel free to contact us at any time by emailing support@trackstreet.com<br/><br/>';
                    $html_message .= 'Regards,<br/>Team TrackStreet<br/><br/><br/>';
                    
                    $text_message = strip_tags($html_message);
                    
                    $this->vision_users->sendSESEmail($email, $subject, $html_message, $text_message);
                    
                    $this->session->set_flashdata('success_msg', 'New team member has been successfully connected to your store/brand.');
                }
                
                redirect('/account/team');
                exit();
            }
            else
            {
                $this->session->set_flashdata('error_msg', 'Error: Team member with that email address has already been added and connected to this brand/store.');
                
                redirect('/account/team_add');
                exit();
            }  
        }
        
        // create dropdown for user role select
        $role_type_options = array(
    			1 => 'Viewer',
    			2 => 'Admin'
    		);
    		
    		$role_start_value = set_value('role_id', '');
    		$view_data['role_dropdown'] = form_dropdown('role_id', $role_type_options, $role_start_value);
        
        $this->load->view('account/team_add', $view_data);
    }
    
    /**
     * Functionality where admin can resend invite email to a team member.
     * 
     * @author Christophe
     * @param string $user_uuid
     */
    public function team_resend_invite($user_uuid)
    {
        $this->load->library('Vision_users');
        
        // check to see if current user is an admin for this store
        if ($this->role_id != USER_TYPE_ADMIN)
        {
            $this->session->set_flashdata('error_msg', 'Error: You do not have the permissions to perform this action.');
            
            redirect('/');
            exit();
        }
        
        // find user being disabled
        $user = $this->User->get_user_by_uuid($user_uuid);
        
        if (empty($user))
        {
            $this->session->set_flashdata('error_msg', 'Error: Team member not found.');
            
            redirect('/account/team');
            exit();
        }
        else
        {
            $user_store = $this->Store->get_user_store_record($this->store_id, $user['id']);
            
            if (empty($user_store))
            {
                $this->session->set_flashdata('error_msg', 'Error: Team member not found.');
                
                redirect('/account/team');
                exit();
            }
            
            $admin = $this->User->get_user_by_id($this->user_id);
            
            // send welcome email to new member
            $subject = '[TrackStreet] Your New Account';
            
            $html_message = 'Dear ' . $user['first_name'] . ' ' . $user['last_name'] . ',<br/><br/>';
            $html_message .= $admin['first_name'] . ' ' . $admin['last_name'] . ' has created a new account for you.<br/><br/>';
            $html_message .= 'Please confirm your account signup by clicking on the link below:<br/><br/>';
            $html_message .= '<a href="' . base_url() . 'signup/confirm/' . $user['uuid'] . '">Confirm My Account Here</a><br/><br/>';
            $html_message .= 'Questions? Please feel free to contact us at any time by emailing support@trackstreet.com<br/><br/>';
            $html_message .= 'Regards,<br/>Team TrackStreet<br/><br/><br/>';
            
            $text_message = 'Dear ' . $user['first_name'] . ' ' . $user['last_name'] . ',\r\n\r\n';
            $text_message .= $admin['first_name'] . ' ' . $admin['last_name'] . ' has created a new account for you.\r\n\r\n';
            $text_message .= 'Please confirm your account signup by clicking on the link below:\r\n\r\n';
            $text_message .= base_url() . 'signup/confirm/' . $user['uuid'] . '\r\n\r\n';
            $text_message .= 'Questions? Please feel free to contact us at any time by emailing support@trackstreet.com\r\n\r\n';
            $text_message .= 'Regards,\r\nTeam TrackStreet\r\n\r\n';
            
            $this->vision_users->sendSESEmail($user['email'], $subject, $html_message, $text_message); 

            $this->session->set_flashdata('success_msg', 'Team member resent welcome email with account details.');

            redirect('/account/team');
            exit();
        }        
    }
    
    /**
     * Re-activate a user account.
     * 
     * @author Christophe
     * @param string $user_uuid
     */
    public function team_active($user_uuid)
    {
        // check to see if current user is an admin for this store
        if ($this->role_id != USER_TYPE_ADMIN)
        {
            $this->session->set_flashdata('error_msg', 'Error: You do not have the permissions to perform this action.');
            
            redirect('/');
            exit();
        }
        
        // find user being disabled
        $user = $this->User->get_user_by_uuid($user_uuid);
        
        if (empty($user))
        {
            $this->session->set_flashdata('error_msg', 'Error: Team member not found.');
            
            redirect('/account/team');
            exit();
        }
        else
        {
            $user_store = $this->Store->get_user_store_record($this->store_id, $user['id']);
            
            if (empty($user_store))
            {
                $this->session->set_flashdata('error_msg', 'Error: Team member not found.');
                
                redirect('/account/team');
                exit();
            }
            
            // set user to deleted status
            $update_data = array(
            		'user_active' => 1
            );
            
            $this->User->update_user($user['id'], $update_data);
            
            $this->session->set_flashdata('success_msg', 'Team member has been successfully re-activated.');
            
            redirect('/account/team');
            exit();
        }        
    }
    
    /**
     * Set a user to disabled status.
     * 
     * @author Christophe
     * @param string $user_uuid
     */
    public function team_disable($user_uuid)
    {
        // check to see if current user is an admin for this store
        if ($this->role_id != USER_TYPE_ADMIN)
        {
            $this->session->set_flashdata('error_msg', 'Error: You do not have the permissions to perform this action.');
            
            redirect('/');
            exit();
        }    
            
        // find user being disabled
        $user = $this->User->get_user_by_uuid($user_uuid);
        
        if (empty($user))
        {
            $this->session->set_flashdata('error_msg', 'Error: Team member not found.');
            
            redirect('/account/team');
            exit();
        }
        else
        {
            $user_store = $this->Store->get_user_store_record($this->store_id, $user['id']);
            
            if (empty($user_store))
            {
                $this->session->set_flashdata('error_msg', 'Error: Team member not found.');
                
                redirect('/account/team');
                exit();
            }
            
            // set user to deleted status
            $update_data = array(
                'user_active' => 0                
            );
            
            $this->User->update_user($user['id'], $update_data);
            
            $this->session->set_flashdata('success_msg', 'Team member has been successfully disabled.');
            
            redirect('/account/team');
            exit();
        }
    }
    
    /**
     * Edit a team member.
     * 
     * @author Christophe
     * @param string $user_uuid
     */
    public function team_edit($user_uuid)
    {
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('Vision_users');
        
        $this->page_title = 'Edit Team Member - TrackStreet';
        
        $user = $this->User->get_user_by_uuid($user_uuid);
        
        // check to see if current user is an admin for this store
        if ($this->role_id != USER_TYPE_ADMIN)
        {
            $this->session->set_flashdata('error_msg', 'Error: You do not have the permissions to perform this action.');
            
            redirect('/');
            exit();
        }
        
        // find user being disabled
        $user = $this->User->get_user_by_uuid($user_uuid);
        
        if (empty($user))
        {
            $this->session->set_flashdata('error_msg', 'Error: Team member not found.');
            
            redirect('/account/team');
            exit();
        }
        else
        {
            $user_store = $this->Store->get_user_store_record($this->store_id, $user['id']);
        
            if (empty($user_store))
            {
                $this->session->set_flashdata('error_msg', 'Error: Team member not found.');
                
                redirect('/account/team');
                exit();
            }
        }
        
        $this->form_validation->set_rules('first_name', 'First Name', 'xss_clean|required|trim');
        $this->form_validation->set_rules('last_name', 'Last Name', 'xss_clean|required|trim');
        $this->form_validation->set_rules('email', 'Email Address', 'trim|valid_email|xss_clean|required');
        $this->form_validation->set_rules('role_id', 'Permissions Role', 'xss_clean|required');
        
        if ($this->form_validation->run() === FALSE)
        {
        	// validation failed, or first load
        }
        else
        {
            $user = $this->User->get_user_by_uuid($user_uuid);
            
            $user_name = $this->input->post('first_name', TRUE) . ' ' . $this->input->post('last_name', TRUE);
            $first_name = $this->input->post('first_name', TRUE);
            $last_name = $this->input->post('last_name', TRUE);
            $email = $this->input->post('email', TRUE);
            $role_id = intval($this->input->post('role_id', TRUE));
            
            if (!empty($user))
            {
                $update_data = array(
                    'user_name' => $user_name,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'role_id' => $role_id                                                                
                );
                
                $this->User->update_user($user['id'], $update_data);
                
                $this->session->set_flashdata('success_msg', 'Team member has been successfully updated.');
                
                redirect('/account/team');
                exit();
            }
            else
            {
                $this->session->set_flashdata('error_msg', 'Error: Team member not found.');
                
                redirect('/account/team');
                exit();
            }
        }  

        $role_type_options = array(
        		1 => 'Viewer',
        		2 => 'Admin'
        );
        
        $start_value = set_value('role_id', $user['role_id']);
        $view_data['role_dropdown'] = form_dropdown('role_id', $role_type_options, $start_value);
        
        $view_data['user'] = $user;
        $view_data['user_uuid'] = $user_uuid;
        
        $this->load->view('account/team_edit', $view_data);
    }

    /**
     * Page where user can add a new team member if they are an admin user.
     * 
     * @author Christophe
     */
    public function add_team_member()
    {
        // check to see if current user is an admin for this store
        if ($this->role_id != USER_TYPE_ADMIN)
        {
            $this->session->set_flashdata('error_msg', 'Error: You do not have the permissions to perform this action.');
            
            redirect('/');
            exit();
        }
        
        $view_data = array();
        
        $this->load->view('account/add_team_member', $view_data);        
    }
}