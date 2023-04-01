<?php
// TODO:  move this to CI config directory

   define ('DATE_FORMAT', 'Y-m-d\TH:i:s\Z');

   /************************************************************************
    * REQUIRED
    *
    * * Access Key ID and Secret Acess Key ID, obtained from:
    * http://aws.amazon.com
    *
    * IMPORTANT: Your Secret Access Key is a secret, and should be known
    * only by you and AWS. You should never include your Secret Access Key
    * in your requests to AWS. You should never e-mail your Secret Access Key
    * to anyone. It is important to keep your Secret Access Key confidential
    * to protect your account.
    ***********************************************************************/
   /* define('AWS_ACCESS_KEY_ID', 'AKIAJ3DOUH4U7EYBNXNQ');
    define('AWS_SECRET_ACCESS_KEY', 'NGHRNsSo0N8SoJif5WssQ19vHe/pbUes1XZEQR80');*/
	 define('AWS_ACCESS_KEY_ID', 'AKIAIJVCDXXRLPZFJKXA');
    define('AWS_SECRET_ACCESS_KEY', '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');

   /************************************************************************
    * REQUIRED
    * 
    * All MWS requests must contain a User-Agent header. The application
    * name and version defined below are used in creating this value.
    ***********************************************************************/
    define('APPLICATION_NAME', 'TestApp');
    define('APPLICATION_VERSION', '1.0');
    
   /************************************************************************
    * REQUIRED
    * 
    * All MWS requests must contain the seller's merchant ID and
    * marketplace ID.
    ***********************************************************************/
    ///define ('MERCHANT_ID', 'A2JGQ753531ZVA');
    define ('MARKETPLACE_ID', 'ATVPDKIKX0DER');
    
   /************************************************************************ 
    * OPTIONAL ON SOME INSTALLATIONS
    *
    * Set include path to root of library, relative to Samples directory.
    * Only needed when running library from local directory.
    * If library is installed in PHP include path, this is not needed
    ***********************************************************************/   
    define ('AMAZON_LIB_ROOTPATH', dirname(BASEPATH)."/system/application/libraries/");
    set_include_path(AMAZON_LIB_ROOTPATH. PATH_SEPARATOR . '../../.');    
   
   /************************************************************************ 
    * OPTIONAL ON SOME INSTALLATIONS  
    * 
    * Autoload function is reponsible for loading classes of the library on demand
    * 
    * NOTE: Only one __autoload function is allowed by PHP per each PHP installation,
    * and this function may need to be replaced with individual require_once statements
    * in case where other framework that define an __autoload already loaded.
    * 
    * However, since this library follow common naming convention for PHP classes it
    * may be possible to simply re-use an autoload mechanism defined by other frameworks
    * (provided library is installed in the PHP include path), and so classes may just 
    * be loaded even when this function is removed
    ***********************************************************************/   
     function __autoload($className){
        //echo $className."<br>";
		$filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		//echo $filePath."<br>";		
        $includePaths = explode(PATH_SEPARATOR, AMAZON_LIB_ROOTPATH);
        foreach($includePaths as $includePath){
			//echo $includePath;
            if(file_exists($includePath . DIRECTORY_SEPARATOR . $filePath)){
				//echo $filePath;
                require_once $filePath;
                return;
            }
        }
    }
  


