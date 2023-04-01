<?php
/**
 * Require www in the URL.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Filter
 */

class noWwwFilter extends CFilter 
{
	/**
	 * If www present, redirect to no www.
	 */
	protected function preFilter($filterChain) 
	{
		$www = substr(Yii::app()->getRequest()->serverName, 0, 4) == 'www.';
		
	    if ($www)
	    {
	        $url = 'http://' .
	            substr(Yii::app()->getRequest()->serverName, 4).
	            Yii::app()->getRequest()->requestUri;
	        
	        Yii::app()->request->redirect($url);
	        
	        return false;
	    }
	    return true;
	}
}