<?php
/**
 * Require not having www in the URL.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Filter
 */

class wwwFilter extends CFilter 
{
	/**
	 * If www no present, redirect to www.
	 */
	protected function preFilter($filterChain) 
	{
		$www = substr(Yii::app()->getRequest()->serverName, 0, 4) == 'www.';
		
	    if (!$www)
	    {
	        $url = 'http://www.' .
	            Yii::app()->getRequest()->serverName.
	            Yii::app()->getRequest()->requestUri;
	        
	        Yii::app()->request->redirect($url);
	        
	        return false;
	    }
	    return true;
	}
}