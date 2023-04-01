<?php
/**
 * Filter to require an unsecure connection.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Filter
 */

class HttpFilter extends CFilter 
{
	/**
	 * If connection is secure, redirect to unsecure page.
	 */
	protected function preFilter($filterChain) 
	{
	    if (Yii::app()->getRequest()->isSecureConnection) 
	    {
	        $url = 'http://'.
	            Yii::app()->getRequest()->serverName.
	            Yii::app()->getRequest()->requestUri;
	        
	        Yii::app()->request->redirect($url);
	        
	        return false;
	    }
	    return true;
	}
}