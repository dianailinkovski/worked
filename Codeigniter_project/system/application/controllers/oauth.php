<?php

class Oauth extends MY_Controller 
{    
    public function Account()
    {
        parent::__construct();
    }
	
    /**
     * See:
     * 
     * https://github.com/PHPMailer/PHPMailer/wiki/Using-Gmail-with-XOAUTH2
     * https://github.com/PHPMailer/PHPMailer/blob/master/get_oauth_token.php
     * https://github.com/thephpleague/oauth2-client
     * https://github.com/thephpleague/oauth2-google
     * 
     * @author Christophe
     */
    public function token()
    {        
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        
        //session_start();
        
        //If this automatic URL doesn't work, set it yourself manually
        //$redirectUri = isset($_SERVER['HTTPS']) ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        //$redirectUri = 'http://localhost/phpmailer/get_oauth_token.php';
        
        if ($this->config->item('environment') == 'production')
        {
            $redirectUri = 'https://app.trackstreet.com/oauth/token';
        }
        else if ($this->config->item('environment') == 'local')
        {
            $redirectUri = 'http://localvision.juststicky.com:8888/oauth/token';
        }
        else
        {
            $redirectUri = 'http://dev.trackstreet.com/oauth/token';
        }
        
        //var_dump($redirectUri); exit();
        
        //These details obtained are by setting up app in Google developer console.
        //$clientId = 'RANDOMCHARS-----duv1n2.apps.googleusercontent.com';
        $clientId = '926686706631-hfvemdq29jq7rls3cev19dhk3u1vnm68.apps.googleusercontent.com';
        //$clientSecret = 'RANDOMCHARS-----lGyjPcRtvP';
        $clientSecret = 'tgaKWHuROprvasinKyeitMlk';
        
        //Set Redirect URI in Developer Console as [https/http]://<yourdomain>/<folder>/get_oauth_token.php
        $provider = new League\OAuth2\Client\Provider\Google(
            array(
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'redirectUri' => $redirectUri,
                'scopes' => array('https://mail.google.com/'),
                'accessType' => 'offline'
            )
        );
        
        if (!isset($_GET['code'])) 
        {
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            
            $_SESSION['oauth2state'] = $provider->getState();
            
            header('Location: ' . $authUrl);
            
            exit;
            
            
        } 
        // Check given state against previously stored one to mitigate CSRF attack
        else if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) 
        {
            unset($_SESSION['oauth2state']);
            
            exit('Invalid state');
        } 
        else 
        {
            // Try to get an access token (using the authorization code grant)
            $token = $provider->getAccessToken(
                'authorization_code',
                array(
                    'code' => $_GET['code']
                )
            );
            
            // Use this to get a new access token if the old one expires
            echo 'Refresh Token: ' . $token->getRefreshToken();
        }
    }
}

?>