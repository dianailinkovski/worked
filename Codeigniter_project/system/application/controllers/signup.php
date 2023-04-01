<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Signup extends MY_Controller
{
    protected $_acl = array(
    		'*' => 'public'
    );    
    
    public function Operation()
    {
        parent::__construct();
    }
    
    /**
     * Handle account confirmation link that is sent to new team members in welcome email.
     * 
     * Show them terms.
     * 
     * @author Christophe
     * @param string $user_uuid
     */
    public function confirm($user_uuid)
    {
        $this->load->helper(array('form', 'url'));
        $this->load->library('vision_users');
        $this->load->library('form_validation');
        $this->load->model('users_m');
        $this->load->model('terms_m');
        
        $user = $this->users_m->get_user_by_uuid($user_uuid);
        
        if (empty($user))
        {
            echo 'Error: User not found.'; exit();
        }
        else
        {            
            $this->form_validation->set_rules('terms_accept', 'Accept Terms Checkbox', 'required');
            
            $view_data['error_msg'] = '';
            
            if ($this->form_validation->run() == FALSE)
            {
                $view_data['error_msg'] = 'After reviewing the terms, please check the "I accept" checkbox and click on the Accept button to continue.';
            }
            else
            {
                //var_dump($_POST); exit();
                
                if (isset($_POST['terms_accept']))
                {
                    $user = $this->users_m->get_user_by_uuid($user_uuid);

                    // update user
                    $update_data = array(
                        'terms_accepted' => 1                
                    );
                    
                    $this->users_m->update_user($user['id'], $update_data);
                    
                    // Christophe: hack for demo accounts that don't have an email address
                    if ($user['email'] == '')
                    {
                        redirect('/signup/s/' . $user['uuid']);
                    }
                    else
                    {
                        redirect('/signup/password/' . $user['uuid']);
                    }
                }
            }
            
            $user_type_id = $user['role_id'];
            
            $terms = $this->terms_m->get_terms_by_user_type($user_type_id);
            
            $view_data['user_uuid'] = $user_uuid;
            $view_data['terms'] = $terms;
            
            $this->load->view('signup/confirm', $view_data);            
        }
    }
    
    /**
     * Page where people can go about resetting their password.
     * 
     * @author Christophe
     */
    public function forgot_password()
    {
        $this->load->helper(array('form', 'url'));
        $this->load->library('vision_users');
        $this->load->library('form_validation');
        $this->load->model('users_m');
        
        $this->form_validation->set_rules('email', 'Email Address', 'trim|valid_email|required');
        
        if ($this->form_validation->run() == FALSE)
        {
            // display the form & set the flash data error message if there is one
            //$view_data['error_msg'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
        }
        else
        {
            $email = $this->input->post('email', TRUE);
            
            $user = $this->users_m->get_user_by_email($email);
            
            if (empty($user))
            {
                $this->session->set_flashdata('error_msg', 'Error: An account with the entered email address was not found.');
            }
            else
            {
                if (intval($user['user_active']) != 1)
                {
                    $this->session->set_flashdata('error_msg', 'Error: This account is disabled. Please contact your manager to re-activate.');
                }
                else
                {
                    $email = $user['email'];
                    
                    $subject = '[TrackStreet] Password Reset Request';
                    
                    $html_message = 'Dear ' . $user['first_name'] . ' ' . $user['last_name'] .',<br/><br/>';
                    $html_message .= 'A request was made to reset your password for your TrackStreet account. If you made this request, please click on the following link to set a new password.<br/><br/>';
                    $html_message .= '<a href="' . base_url() . 'signup/reset_password/' . $user['uuid'] . '">Change Your Password Here</a><br/><br/>';
                    $html_message .= 'If you did not make this request, you can disregard this message as nothing has changed with your account.<br/><br/>';
                    $html_message .= 'Questions? Please feel free to contact us at any time by emailing support@juststicky.com<br/><br/>';
                    $html_message .= 'Regards,<br/>Team TrackStreet<br/><br/><br/>';
                    
                    $text_message = 'Dear ' . $user['first_name'] . ' ' . $user['last_name'] .',\r\n\r\n';
                    $text_message .= 'A request was made to reset your password for your TrackStreet account. If you made this request, please click on the following link to set a new password.\r\n\r\n';
                    $text_message .= base_url() . 'signup/reset_password/' . $user['uuid'] . '\r\n\r\n';
                    $text_message .= 'If you did not make this request, you can disregard this message as nothing has changed with your account.\r\n\r\n';
                    $text_message .= 'Questions? Please feel free to contact us at any time by emailing support@juststicky.com\r\n\r\n';
                    $text_message .= 'Regards,\r\nTeam TrackStreet\r\n\r\n';
                    
                    $this->vision_users->sendSESEmail($email, $subject, $html_message, $text_message);
                    
                    $this->session->set_flashdata('success_msg', 'Next, please check your email for a link to change your password.');
                }
            }
            
            redirect('/signup/forgot_password');
        }
        
        $view_data = array();
        
        $this->load->view('signup/forgot_password', $view_data);
    }
    
    /**
     * Page where user will first pick their software.
     *
     * @author Christophe
     * @param string $user_uuid
     */
    public function password($user_uuid)
    {
        $this->load->helper(array('form', 'url'));
        $this->load->library('vision_users');
        $this->load->library('form_validation');
        $this->load->model('users_m');
        
        $view_data['success_msg'] = '';
        $view_data['error_msg'] = '';
        
        $user = $this->users_m->get_user_by_uuid($user_uuid);
        
        // check to see if user has accepted their terms first
        if (intval($user['terms_accepted']) != 1)
        {
            redirect('/signup/confirm/' . $user['uuid']);
            exit();
        }
        
        // check to see if they have already set a password
        if ($user['password'] != '')
        {
            redirect('/signup/s/' . $user['uuid']);
            exit();
        }
        
        $this->form_validation->set_rules('new', 'Password', 'required|min_length[8]|max_length[20]|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', 'Confirm Password', 'required');
    
        if ($this->form_validation->run() == FALSE)
        {
            // display the form & set the flash data error message if there is one
            //$view_data['error_msg'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
        }
        else
        {     
            $user = $this->users_m->get_user_by_uuid($user_uuid);
            
            if ($user['password'] != '')
            {
                $update_data = array('password' => '');
                
                $this->users_m->update_user($user['id'], $update_data);
            }
            
            $email = $user['email'];
            
            $old_password = '';
            $new_password = $this->input->post('new', TRUE);
            
            $change = $this->vision_users->change_password($email, $old_password, $new_password);
            
            if ($change['type'] == 'success')
            {                	
                redirect('/signup/s/' . $user['uuid']);
            }
            else
            {
                $view_data['error_msg'] = $change['message'];
            }
        }
        
        $view_data['user_uuid'] = $user_uuid;
        
        $this->load->view('signup/password', $view_data);
    } 
    
    /**
     * Page where user can put in a new password.
     * 
     * @author Christophe
     * @param string $user_uuid
     */
    public function reset_password($user_uuid)
    {
        $this->load->model('users_m');
        
        $user = $this->users_m->get_user_by_uuid($user_uuid);
        
        if (empty($user))
        {            
            $this->session->set_flashdata('error_msg', 'Error: User not found.');
            
            redirect('/');
        }
        else
        {
            // clear their password and redirect them to page wehre they can pick a new password
            $update_data = array(
                'password' => ''                
            );
            
            $this->users_m->update_user($user['id'], $update_data);
            
            redirect('/signup/password/' . $user_uuid);
        }
    }

    /**
     * Sign in user after they have set their password.
     * 
     * @author Christophe
     */
    public function s($user_uuid)
    {
        $this->load->model('users_m');
        
        $user = $this->users_m->get_user_by_uuid($user_uuid);
        
        if (empty($user))
        {
            // @todo log error
        }
        else
        {
            $this->session->set_userdata('user_uuid', $user['uuid']);
        }
        
        redirect('/');
    }
}

?>