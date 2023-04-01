<?php 
/**
 * Url rules for content pages
 *
 * Aliases are created in the admin automatically, using a special route "content". 
 *
 * You can create url with path to default controller index action with id parameter being content page id.
 *
 * Note that you can also create url with keywords method.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Module
 */

class ContentUrlRule extends CmsUrlRule
{
	public $cancelAliasResolution = false;
	
	public function __construct()
	{
		Yii::import('application.modules.content.models.*');
	}
	
    public function createUrl($manager,$route,$params,$ampersand)
    {
		return false;
    }
    
	public function parseUrl($manager,$request,$pathInfo,$rawPathInfo)
    {
    	$alias = Yii::app()->cms->currentAlias;

    	if (isset($alias))
    	{
    		if (($cache = Yii::app()->cache->get('parseUrl-content_'.$alias->id)) !== false) 
    		{
				$requestPath = $request->getPathInfo();
				$afterRoute = substr($requestPath, strpos($requestPath, $alias->alias)+strlen($alias->alias));
    			return $cache.'/'.trim($afterRoute, '/');
    		}
   			else {
	    		foreach ($alias->routes as $route)
	    		{
	    			if ($route->route == 'content')
	    			{
			    		$pageModel = ContentPage::model()->findByAttributes(array('alias_id'=>$alias->id));
			    		$return = 'content/default/index/id/'.$pageModel->id;
			    		Yii::app()->cache->set('parseUrl-content_'.$alias->id, $return);
						$requestPath = $request->getPathInfo();
						$afterRoute = substr($requestPath, strpos($requestPath, $alias->alias)+strlen($alias->alias));

			    		return $return.'/'.trim($afterRoute, '/');
	    			}
	    		}
			}
    	}
    	return false;
    }
}