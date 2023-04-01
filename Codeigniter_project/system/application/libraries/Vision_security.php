<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class to handle items related to security for TrackStreet.
 * 
 * @author Christophe Sautot
 */
class Vision_Security 
{
    public function Vision_Security() 
    {
    	$this->CI =& get_instance();
    }
    
    /**
     * Function to check to see if we should be using a SSL connection (https).
     * 
     * @author Christophe
     */
    public function do_ssl_check()
    {		
        if ($this->CI->config->item('environment') == 'production')
        {     
        		if ($_SERVER['SERVER_PORT'] == '443') 
        		{
        		    // ssl in use - we are good
        		} 
        		else 
        		{
                // redirect to SSL version
                $uri_string = $this->CI->uri->uri_string();
                
                // keep any flash session vars we might be trying to use
                $this->CI->session->keep_flashdata('success_msg');
                $this->CI->session->keep_flashdata('error_msg');
                $this->CI->session->keep_flashdata('message');
                
                header('Location: https://app.trackstreet.com' . $uri_string);
                	
                exit();
        		}       	
        }		
    }
	
}

?>
