<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Operation extends Controller
{
    public function Operation()
    {
        parent::__construct();
    }
    
    public function index()
    {
        echo 'Operation not found.'; exit();
    }
    
    /**
     * Test our Amazon SES library.
     * 
     * @author Christophe
     */
    public function test_amazon_ses()
    {
        $email = 'christophe@trackstreet.com';
        $subject = 'Test SES from Vision';
        $html_message = 'HTML message';
        $text_message = 'text message';
        
        
        /*
        $this->load->library('Amazon_ses');
        
        $this->amazon_ses->to('christophe@trackstreet.com');
        $this->amazon_ses->subject('test subject');
        $this->amazon_ses->message('test message body');
        $this->amazon_ses->send();
        */
        
        $this->load->library('Vision_users');
        
        $this->vision_users->sendSESEmail($email, $subject, $html_message, $text_message);
        
        echo 'Done'; exit();
    }
}

?>