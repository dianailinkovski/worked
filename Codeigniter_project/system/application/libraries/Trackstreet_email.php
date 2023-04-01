<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Trackstreet_email
{   
    public function Trackstreet_email() 
    {
        $this->CI =& get_instance();
    }
    
    /**
     * Send an email using a store's SMTP settings.
     * 
     * @author Christophe
     */
    public function send_smtp_email($store_id, $smtp_settings)
    {
        $this->CI->load->model('store_m');
        $this->CI->load->library('Vision_users');
        
        $store_id = intval($store_id);
        
        $store = $this->CI->store_m->get_store_by_id_array($store_id);
        
        $smtpInfo = array(
        		'host' => $smtp_settings['smtp_host'],
        		'port' => $smtp_settings['smtp_port'],
        		'use_ssl' => $smtp_settings['smtp_ssl'],
        		'use_tls' => $smtp_settings['smtp_tls'],
        		'username' => $smtp_settings['smtp_username'],
        		'password' => $smtp_settings['smtp_password']
        );
        
        $this->CI->load->library('SMTP_auth', $smtpInfo, 'smtp');
        
        $to = $smtp_settings['email_to'];
        $from = $smtp_settings['email_from'];       
        $email_subject = $smtp_settings['email_subject'];
        $email_body = $smtp_settings['email_body'];
        
        $support_team_error_msg = '';
        
        try
        {
            send_smtp(
                $this->CI->smtp,
                $to,
                $email_subject,
                $email_body,
                $from,
                FALSE
            );
        }
        catch (phpmailerException $e)
        {
            $support_team_error_msg .= $e->getMessage();
        }
        catch (Exception $e)
        {
            $support_team_error_msg .= $e->getMessage();
        }
        
        if ($support_team_error_msg == '')
        {
            return TRUE;
        }
        else
        {
            // send notification to TrackStreet staff about store being added
            $email = $this->CI->config->item('environment') == 'production' ? 'christophe@trackstreet.com, chris@trackstreet.com' : 'christophe@trackstreet.com';
             
            $subject = '[TrackStreet] Error with sending SMTP email for store: ' . $store['store_name'];
             
            $html_message = "<p>Store: {$store['store_name']}</p>";
            $html_message .= "<p>Check on SMTP for: {$from}</p>";
            $html_message .= "<p>Subject of Email: {$email_subject}</p>";
            $html_message .= "<p>Error:</p>";
            $html_message .= "<p>{$support_team_error_msg}</p>";
            
            $text_message = strip_tags($html_message);
             
            $this->CI->vision_users->sendSESEmail($email, $subject, $html_message, $text_message);
            
            return FALSE;
        }        
    }
}

?>