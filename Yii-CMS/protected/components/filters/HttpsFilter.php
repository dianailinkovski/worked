<?php
/**
 * Filter to require a secure connection.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Filter
 */

class HttpsFilter extends CFilter 
{
	/**
	 * If connection is unsecure, redirect to secure page.
	 */
	protected function preFilter($filterChain) 
	{
	    if (!Yii::app()->getRequest()->isSecureConnection)
	    {
	        $url = 'https://' .
	            Yii::app()->getRequest()->serverName.
	            Yii::app()->getRequest()->requestUri;
	        
	        Yii::app()->request->redirect($url);
	        
	        return false;
	    }
	    return true;
	}
}