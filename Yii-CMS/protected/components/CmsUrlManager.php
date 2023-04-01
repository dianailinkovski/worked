<?php 
/**
 * The cms url manager.
 * 
 * Implements the alias system, the multi-language system and the cms module rules and classes on top of the default CUrlManager.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Core
 */

class CmsUrlManager extends CUrlManager
{
	/**
	 * @var boolean cancel alias resolution at the application level. It can also be canceled by specific url rule classes. Defaults to false.
	 */
	public $cancelAliasResolution = false;

	/**
	 * @var boolean whether the current request's route has an alias but that's not been used, this is set by CmsUrlManager. Defaults to false.
	 */
	private $_aliasNotUsed = false;
	/**
	 * @var array read-only. The rule classes defined in the cms modules. Defaults to array().
	 */
	private $_moduleUrlRuleClasses = array();
	/**
	 * @var array transfering the rules private from the parent to here here because we need it for parseUrl. Defaults to array().
	 */
	private $_rules = array();
	
	/**
	 * Replaces empty GET params with @@@ codes because there are a few problems.
	 * 
	 * @param string $item the param value passed by reference.
	 * @param string $key the param key passed by reference.
	 */
	private function fixParams(&$item, &$key)
	{
		// Having "//" in the URL bugs IIS
		if ($item === '' || $item === null)
			$item = '@@@';
	}

	/**
	 * Forces aliases to be used
	 * 
	 * Doing it as a method so that you can call it in a controller or not call it in another controller.
	 * 
	 * @throws CHttpException if an alias exists for the current requested route but wasn't used.
	 */
	public function forceAliases()
	{
		if ($this->_aliasNotUsed)
			throw new CHttpException(404, Yii::t('yii','Unable to resolve the request "{route}".', array('{route}'=>Yii::app()->request->pathInfo)));
	}

	/**
	 * Cms createUrl
	 *
	 * See comments in code to understand procedure.
	 *
	 * You can cancel alias transformation by setting Cms::cancelAliasResultion to true.
	 *
	 * You can create links directly to aliases by using the "keyword" GET parameter (refers to the alias keyword).
	 *
	 * You can choose a specific section id by setting the "cms_section_id" GET parameter (not needed if you use the keyword parameter or if there is only one alias for your route).
	 * 
	 * @see CUrlManager::createUrl().
	 */
    public function createUrl($route,$params=array(),$ampersand='&')
    {
    	array_walk_recursive($params, array($this, 'fixParams'));
    	
    	// Set language
        if (count(Yii::app()->languageManager->languages) > 1 && !isset($params['language']))
        {
            $language = '/'.Yii::app()->language.'/';
            $urlLanguage = Yii::app()->language;
        } 
        elseif (count(Yii::app()->languageManager->languages) > 1  && isset($params['language']))
		{
			$language = '/'.$params['language'].'/';
			$urlLanguage = $params['language'];
			unset($params['language']);
		}
    	else {
			$language = '/';
			$urlLanguage = Yii::app()->language;
		}
		
		$alias = false; // The alias for the route.
		$url = false; // The full url that will include the modifications from url rules and classes ($route doesn't).
		
		// Processing keyword type of url (urls with no route and a param keyword are used to directly link the alias with the alias keyword).
		// Note that rewrite rules don't work with this.
        if ($route == '' && isset($params['keyword']))
		{
			// Checking if it's in cache first
			if (($keywordAlias = Yii::app()->cache->get('createUrl-keyword-alias_'.$params['keyword']) !== false)
			   && ($keywordRoute = Yii::app()->cache->get('createUrl-keyword-route_'.$params['keyword']) !== false))
			{
				$alias = $keywordAlias;
				$route = $keywordRoute;
				unset($params['keyword']);
			}
			elseif (($aliasModel = CmsAlias::model()->localized($urlLanguage)->findByAttributes(array('keyword'=>$params['keyword']))))
			{
				$ancestors = $aliasModel->ancestors()->localized($urlLanguage)->findAll();
					
				$path = '';
				foreach ($ancestors as $ancestor)
				{
					if ($ancestor->level != 1)
						$path .= '/'.$ancestor->alias;
				}
				$path .= '/'.$aliasModel->localized($urlLanguage)->alias;

				if (($routes = $aliasModel->routes))
					$route = $routes[0]->route;

				$alias = $path;

				Yii::app()->cache->set('createUrl-keyword-alias_'.$params['keyword'], $alias);
				Yii::app()->cache->set('createUrl-keyword-route_'.$params['keyword'], $route);

				unset($params['keyword']);
			}
		}
		
        if (isset($params['cms_section_id']) && $this->cancelAliasResolution == false) {
			$sectionId = $params['cms_section_id'];
			unset($params['cms_section_id']);
		}

		// Running the module url rule classes.
		foreach ($this->_moduleUrlRuleClasses as $moduleUrlRuleClass)
		{
			$moduleRule = Yii::createComponent($moduleUrlRuleClass);
			$moduleRule->language = $urlLanguage;

			$break = false;
			if (($url = $moduleRule->createUrl($this,$route,$params,$ampersand)) !== false)
				$break = true;
				
			if (isset($moduleRule->newParams))
				$params = $moduleRule->newParams;
				
			if (isset($moduleRule->cancelAliasResolution) && $moduleRule->cancelAliasResolution)
				$this->cancelAliasResolution = true;
				
			if ($break)
				break;
		}
		// Running parent implementation of createUrl which in turn executes all url rules defined in our modules.
		if ($url === false)
			$url = $this->createUrlParent($route,$params,$ampersand);

		if (!$this->cancelAliasResolution)
		{
			if ($alias === false && !(($routeTraverse=trim($route,'/')) === '')) 
			{
				// Checking if it's in cache first
				if (($aliasInCache = Yii::app()->cache->get('createUrl_'.$routeTraverse.'_'.(isset($sectionId) ? $sectionId : 'na'))) === false)
				{
					$routeTraverseOriginal = $routeTraverse;

					// Loops from the end of the url to the beginning until it finds a matching alias and then loops trough that alias parents to form the final url.
					while(true) 
					{
						if (($aliasRouteModel = CmsAliasRoute::model()->with(
							(isset($sectionId) ? 
								array('alias'=>array(
									'condition'=>'alias.section_id=:section_id', 
									'params'=>array(':section_id'=>$sectionId)))
									: 'alias')
							)->findByAttributes(array('route'=>array($routeTraverse, $routeTraverse.'/')))))
						{
							$alias .= '/'.$aliasRouteModel->alias->alias;

							$aliasAncestors = array_reverse($aliasRouteModel->alias->ancestors()->localized($urlLanguage)->findAll());
							if (!empty($aliasAncestors))
							{
								foreach ($aliasAncestors as $aliasAncestor)
								{
									$alias = '/'.$aliasAncestor->alias.$alias;
								}
							}
							$pos = true;
							break;
						}
						elseif (($pos=strrpos($routeTraverse,'/')) !== false)
							$routeTraverse = substr($routeTraverse, 0, $pos);
						else
							break;
					}
					Yii::app()->cache->set('createUrl_'.$routeTraverseOriginal.'_'.(isset($sectionId) ? $sectionId : 'na'), ($alias == '' ? '@@@' : $alias));

				}			
				else {
					$alias = ($aliasInCache == '@@@' ? '' : $aliasInCache);
					$pos = true;
				}
			}
			else
				$pos = true;
			
			if ($pos === true)
				// Replace the route part of the url with the alias.
				return $language.preg_replace('/^'.preg_quote(trim($route, '/'), '/').'/', trim($alias, '/'), ltrim($url, '/'), 1);
			else
				return $language.trim($url, '/');
		}
		else
			return $language.trim($url, '/');
    }

	/**
	 * Cms parseUrl
	 *
	 * See comments in code to understand the procedure.
	 * 
	 * You can cancel alias resolution by setting Cms::cancelAliasResultion to true.
	 * 
	 * @see CUrlManager::parseUrl().
	 */
    public function parseUrl($request)
    {	
    	// Install and admin bypass normal execution because we don't want the language or aliases.
    	if($request->getPathInfo() == 'install')
    		return 'install';
    	else if($request->getPathInfo() == 'admin')
    		return 'admin';
    	else if($this->getUrlFormat()===self::PATH_FORMAT)
		{
			$pathInfo=$request->getPathInfo();
			$pathInfo = preg_replace('/@@@/', '', $pathInfo);
			
			// Set language.
	    	if (Yii::app()->languageManager->multilang && preg_match('/^('.implode('|',array_keys(Yii::app()->languageManager->languages)).')(\/(.*))?$/', $pathInfo, $matches))
	        {
	        	$_GET['language'] = $matches[1];
	        	$pathInfo = isset($matches[3]) ? $matches[3] : '';
	        }
			Yii::app()->languageManager->setLanguage();

			// Replacing the alias in the pathInfo with the proper route.
			if (!$this->cancelAliasResolution)
			{
				$originalRoute = $pathInfo;
				$route = $pathInfo;

				if (!(($route=trim($route,'/')) === '')) 
				{
					$route = $route.'/';
					$lft = null;
					$rgt = null;
					$noAliasCondition = '';
					$noAliasParams = array();
					$aliasModelFound = null;

					// Looping through the url backwards until we find (or not) the alias in the cache
					$routeBackwards = '/'.rtrim($route, '/');
					while (($pos=strrpos($routeBackwards,'/')) !== false) 
					{
						if (($aliasId = Yii::app()->cache->get('parseUrl_/'.Yii::app()->language.$routeBackwards)) === false)
							$routeBackwards = substr($routeBackwards, 0, $pos);
						else
						{
							Yii::app()->cms->currentAlias = CmsAlias::model()->with('routes')->findByPk($aliasId);
							Yii::app()->cms->currentSectionId = Yii::app()->cms->currentAlias->section_id;

							$route = substr($route, strlen($routeBackwards)); // We'll need the GET part of the route in $route later
							if ($route === false)
								$route = '';

							break;
						}
					}
					
					// If not found in cache
					if (!isset(Yii::app()->cms->currentAlias))
					{
						// Looping from beginning to end of url as long as it finds a corresponding alias.
						$posTotal = 0;
						while (($pos=strpos($route,'/')) !== false) 
						{
							$id = substr($route,0,$pos);
							$route = (string)substr($route,$pos+1);
							
							$params = array(':id'=>$id, ':lang'=>Yii::app()->language);
							if ($lft !== null)
							{
								$params[':lft'] = $lft;
								$params[':rgt'] = $rgt;
							}
							if ($noAliasCondition == '' && ($aliasModel = CmsAlias::model()->multilang()->find('i18nCmsAlias.l_alias=:id AND i18nCmsAlias.lang_id=:lang'.($lft === null ? ' AND t.level = 2' : ' AND t.lft > :lft AND t.rgt < :rgt'), $params)))
							{
								$lft = $aliasModel->lft;
								$rgt = $aliasModel->rgt;

								$aliasModelFound = $aliasModel;
								$posTotal += ($posTotal == 0 ? $pos : $pos+1);
							} 
							else // Operations needed to check if any alias exists for this route further down.
							{
								if ($aliasModelFound == null)
								{
									$noAliasParamsCount = count($noAliasParams);
									$path = $noAliasCondition == '' ? $id : $noAliasParams['path'.($noAliasParamsCount-1)].'/'.$id;
									$noAliasCondition .= 't.route = :path'.$noAliasParamsCount.' OR ';
									$noAliasParams['path'.$noAliasParamsCount] = $path;
								}
								else
									break;
							}
						}
						
						// If alias found
						if ($aliasModelFound != null)
						{
							Yii::app()->cms->currentAlias = CmsAlias::model()->with('routes')->findByPk($aliasModelFound->primaryKey); // Must get the localized version (non multilang()).
							Yii::app()->cms->currentSectionId = Yii::app()->cms->currentAlias->section_id;
							Yii::app()->languageManager->checkAliasLanguageRestrictions();
							Yii::app()->cache->set('parseUrl_/'.Yii::app()->language.substr('/'.trim($originalRoute,'/').'/', 0, $posTotal+1), Yii::app()->cms->currentAlias->id); // Adding route to cache

							// Checking to see if ancestors of alias are valid.
							$route = $originalRoute;
							$aliasAncestors = Yii::app()->cms->currentAlias->ancestors()->findAll();
							if (!empty($aliasAncestors))
							{
								for ($i = 1; $i < count($aliasAncestors); $i++)
								{
									$aliasAncestor = $aliasAncestors[$i];
									$pos = strpos($route,'/');
									$id = substr($route,0,$pos);
									$route = (string)substr($route,$pos+1);
		
									if ($id != $aliasAncestor->alias)
									{
										throw new CHttpException(404,Yii::t('yii','Unable to resolve the request "{route}".',
											array('{route}'=>$originalRoute)));
									}
								}
							}
							if (($pos = strpos($route,'/')))
								$route = substr($route, $pos+1); // We'll need the GET part of the route in $route later
							else
								$route = '';
						}
						
						// If no alias was found, checking to see if any alias exists for this route.
						if ($noAliasCondition != '') 
						{
							if (CmsAliasRoute::model()->count(substr($noAliasCondition, 0, -4), $noAliasParams) > 0)
								$this->_aliasNotUsed = true;
						}
					}
				}
			}

			// Setting $pathInfo
			if (isset(Yii::app()->cms->currentAlias))
			{
				if (($pathRoute = Yii::app()->cms->currentAlias->routes))
					$pathInfo = $pathRoute[0]->route.($route !== '' ? '/'.(trim($route,'/')) : ''); // If more than 1 is not important because it will be sorted out in rewrites rules.
				else
					$pathInfo = '404'; // Page without content (serves as parent to other pages only) if we land here then we need to throw and error.
			}

			// Now that we have the proper route, we can roll through the modules url rewrite classes.
			foreach ($this->_moduleUrlRuleClasses as $moduleUrlRuleClass)
			{
				$moduleRule = Yii::createComponent($moduleUrlRuleClass);

				if (($moduleRoute = $moduleRule->parseUrl($this,$request,$pathInfo,$pathInfo)) !== false)
					return $moduleRoute;
			}
			
			// Now that we have the proper route, we can roll through the modules url rewrite rules.
			foreach($this->_rules as $i=>$rule)
			{
				if(is_array($rule))
					$this->_rules[$i]=$rule=Yii::createComponent($rule);
				if(($r=$rule->parseUrl($this,$request,$pathInfo,$pathInfo))!==false)
					return isset($_GET[$this->routeVar]) ? $_GET[$this->routeVar] : $r;
			}
			
			// Strict parsing requires that a rewrite rule be used. If this point was reached we throw an exception, otherwise return the pathInfo.
			if($this->useStrictParsing)
				throw new CHttpException(404,Yii::t('yii','Unable to resolve the request "{route}".',
					array('{route}'=>$pathInfo)));
			else
				return $pathInfo;
		}
		// Path format not used so just returning the route.
		else if(isset($_GET[$this->routeVar]))
			return $_GET[$this->routeVar];
		else if(isset($_POST[$this->routeVar]))
			return $_POST[$this->routeVar];
		else
			return '';
    }
    

    // No choice but to override the following 3 methods because we need the $_rules private for parseUrl.
    
	/**
	 * @see CUrlManager::processRules().
	 */
	protected function processRules()
	{
		// Must add CMS rules and classes here before the cache is evaluated otherwise it will not work
		$app = Yii::app();

		foreach ($app->getModules() as $moduleName => $config) 
	    {
	    	$moduleClassName = ucfirst($moduleName).'Module';
	    	if (!class_exists($moduleClassName, false))
	    	{
	    		try // Ignoring errors.
	    		{
	    			Yii::import('application.modules.'.$moduleName.'.'.$moduleClassName, true);
	    			
	    			if (is_subclass_of($moduleClassName, 'CmsModule'))
	    			{
	    				Yii::app()->cms->modules[] = $moduleName;
				        $this->addRules($moduleClassName::getUrlRules());
				        
				        if (($ruleClass = $moduleClassName::getUrlRuleClass()))
				        	$this->_moduleUrlRuleClasses[] = $ruleClass;
	    			}
	    		}
	    		catch(Exception $e){}
	    	}
	    }

		if(empty($this->rules) || $this->getUrlFormat()===self::GET_FORMAT)
			return;
		if($this->cacheID!==false && ($cache=Yii::app()->getComponent($this->cacheID))!==null)
		{
			$hash=md5(serialize($this->rules));
			if(($data=$cache->get(self::CACHE_KEY))!==false && isset($data[1]) && $data[1]===$hash)
			{
				$this->_rules=$data[0];
				return;
			}
		}
		foreach($this->rules as $pattern=>$route)
			$this->_rules[]=$this->createUrlRule($route,$pattern);
		if(isset($cache))
			$cache->set(self::CACHE_KEY,array($this->_rules,$hash));
	}
	/**
	 * @see CUrlManager::addRules().
	 */
	public function addRules($rules,$append=true)
	{
		if ($append)
		{
			foreach($rules as $pattern=>$route)
				$this->_rules[]=$this->createUrlRule($route,$pattern);
		}
		else
		{
			$rules=array_reverse($rules);
			foreach($rules as $pattern=>$route)
				array_unshift($this->_rules, $this->createUrlRule($route,$pattern));
		}
	}
	
	/**
	 * @see CUrlManager::createUrl().
	 */
	public function createUrlParent($route,$params=array(),$ampersand='&')
	{
		unset($params[$this->routeVar]);
		foreach($params as $i=>$param)
			if($param===null)
				$params[$i]='';

		if(isset($params['#']))
		{
			$anchor='#'.$params['#'];
			unset($params['#']);
		}
		else
			$anchor='';
		$route=trim($route,'/');
		foreach($this->_rules as $i=>$rule)
		{
			if(is_array($rule))
				$this->_rules[$i]=$rule=Yii::createComponent($rule);
			if(($url=$rule->createUrl($this,$route,$params,$ampersand))!==false)
			{
				if($rule->hasHostInfo)
					return $url==='' ? '/'.$anchor : $url.$anchor;
				else
					return $this->getBaseUrl().'/'.$url.$anchor;
			}
		}
		return $this->createUrlDefault($route,$params,$ampersand).$anchor;
	}
}