<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends MY_Controller
{
    protected $_acl = array(
    		'*' => 'public'
    );    
    
    public function Auth()
    {
		    parent::__construct();
    }
    
    /**
     * Page where user can go about resetting their password if they forgot it.
     *
     * @author Christophe
     */
    public function forgot_password()
    {
    
    }    
    
    /**
     * Page where user can log in to their account.
     *
     * @author Christophe
     */
    public function login()
    {
        $this->load->library('form_validation');
        $this->load->library('Vision_users');
        $this->load->model('users_m');
        
        $view_data['success_msg'] = '';
        $view_data['error_msg'] = '';
        
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required');
        
        if ($this->form_validation->run() == FALSE)
        {
            // display the form & set the flash data error message if there is one
            $view_data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
        }
        else
        {
            $email = $this->input->post('email', TRUE);
            $password = $this->input->post('password', TRUE);
            
            // check to see if user credentials are ok
            $check_msg = $this->vision_users->check_credentials($email, $password);
            
            if ($check_msg['type'] == 'success')
            {    
                $user_uuid = $check_msg['message'];
                
                $user = $this->users_m->get_user_by_uuid($user_uuid);
                
                if (empty($user))
                {
                    redirect('/logout');
                }
                else
                {
                    // record user login record to table
                    $insert_data = array(
                        'user_id' => $user['id'],
                        'login_datetime' => date('Y-m-d H:i:s'),
                        'ip' => $this->input->ip_address(),
                        'user_agent' => $this->input->user_agent()                                                    
                    );
                    
                    $this->users_m->insert_user_login_record($insert_data);
                    
                    $this->session->set_userdata('user_uuid', $user_uuid);
                    
                    redirect('/');
                }
            }
            else
            {
                $view_data['error_msg'] = $check_msg['message']; 
                //$this->session->set_flashdata('error_msg', $check_msg['message']);
            }
        }        
        
        $this->load->view('auth/login', $view_data);
    }
    
    /**
     * Log out user.
     * 
     * @author Christophe
     */
    public function logout()
    {        
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('permission_id');
        $this->session->unset_userdata('store_id');
        $this->session->unset_userdata('store_data');
        $this->session->unset_userdata('brands');
        $this->session->unset_userdata('user_brand_default');
        $this->session->unset_userdata('user_uuid');
        
        $this->session->destroy();
        
        redirect('/login');
    }
}

?>