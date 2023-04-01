<?php
/**
 * The language manager.
 * 
 * Language related stuff goes here.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Core
 */

class CmsLanguageManager extends CApplicationComponent
{	
	/**
	 * @var array the site's languages.
	 * Define as languageCode => label ex: en => English. The language code is what shows up in the url. Defaults to empty array.
	 */
	public $languages=array();
	/**
	 * @var string the default language (user will be redirected to this language if none is specified).
	 * Refers to the language code in $language variable.
	 */
	public $defaultLanguage;
	/**
	 * @var array the GET variables that need to be translated (if the user switches language).
	 * Define as varName => array(languageCode => translation). Defaults to empty array.
	 */
	public $translatedGetVars=array();

	
	private $_languageRestricted=false;

	private $_suffixes;

	private $_multilang;
	

	public function init()
	{
		$app = Yii::app();
		
	    if (!$this->languages)
        	$this->languages = array($app->language => $app->language);
        	
		if (count($this->languages) > 1)
			$this->_multilang = true;
	}
	
	/**
	 * Multilang getter.
	 * @return boolean whether or not there are multiple languages.
	 */
	public function getMultilang()
	{
		return $this->_multilang;	
	}

	/**
	 * Check if alias is only in 1 language if so restrict only to that language.
	 */
	public function checkAliasLanguageRestrictions()
	{
		$app = Yii::app();
		
		$id = $app->cms->currentAlias->id;
		$sql = "SELECT COUNT(*) FROM cms_alias_lang WHERE cms_alias_id = :id";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":id", $id, PDO::PARAM_INT);
		$dataReader=$command->query();
		while(($row=$dataReader->read())!==false) { 
			$count = $row['COUNT(*)'];
		}
		if ($this->_multilang && $count == 1)
			$this->restrictLanguage($app->language);
	}
	
	/**
	 * Restrict to only 1 possible language.
	 * You may call this function in your controllers.
	 * @throws CHttpException if language is not right one.
	 */
	public function restrictLanguage($language)
	{
		$this->_languageRestricted = true;
		
		if (Yii::app()->language != $language)
			throw new CHttpException(404, 'The requested page does not exist.');
	}
	
	/**
	 * Language suffixes.
	 * Mostly for use with MultilangualBehavior for looping through languages.
	 * @return array (languageLabel => suffix) default language is empty suffix, the others are _lang.
	 */
	public function getSuffixes()
	{
		if (!isset($this->_suffixes))
		{
			$this->_suffixes = array();
			foreach ($this->languages as $abbr => $label) 
			{
				$this->_suffixes[$label] = ($abbr == Yii::app()->sourceLanguage ? '' : '_'.$abbr);
			}
		}
		return $this->_suffixes;
	}

	/**
	 * Sets the language.
	 * Determines the language from GET, cookies or preferred browser language.
	 * @throws CHttpException if error.
	 */
	public function setLanguage($languages = array(), $defaultLanguage = '')
	{	
		$app = Yii::app();

		if (count($this->languages) > 1) 
		{
			if (isset($_GET['language']))
			{
				if (in_array($_GET['language'], array_keys($this->languages))) 
	            	$language = $_GET['language'];
				else
	            	throw new CHttpException(404,'The requested page does not exist.');
			}
			else if ($app->request->pathInfo == '')
			{
			    if (isset($app->request->cookies['language']) 
			    	&& in_array($app->request->cookies['language']->value, array_keys($this->languages)))
		        		$language = $app->request->cookies['language']->value;
		        else
		        	if (!in_array(($language = $app->request->getPreferredLanguage()), array_keys($this->languages)))
		        		$language = $this->defaultLanguage;

				$app->request->redirect('/'.$language.'/');
			}
			else
				throw new CHttpException(404,'The requested page does not exist.');

	        $app->setLanguage($language);
	        $this->setLanguageCookie();
		} 
	 	else
			$app->setLanguage($this->defaultLanguage);
	}
	
	public function setLanguageCookie($language=null)
	{
		if (!$this->_languageRestricted)
		{
			$app = Yii::app();
			$cookie = new CHttpCookie('language', (isset($language) ? $language : $app->language));
			$cookie->expire = time() + (60*60*24*365);
			$app->request->cookies['language'] = $cookie;
		}
	}
	
	/**
	 * Get the url to change language, to use for your language switching link.
	 * translatedGetVars are translated into the the proper language for the link.
	 * @return string the url to put in your link
	 */
	public function changeLanguageUrl($language)
	{
		$app = Yii::app();
		$get = $_GET;
		
		$get['language'] = $language;

		foreach ($this->translatedGetVars as $translatedGetVar => $translatedGetVarValue)
		{
			if (array_key_exists($translatedGetVar, $get))
				$get[$translatedGetVar] = $translatedGetVarValue[$language];
		}		
		if (isset(Yii::app()->cms->currentSectionId) && !isset($get['cms_section_id']))
			$get['cms_section_id'] = Yii::app()->cms->currentSectionId;
			
		return $app->controller->createUrl('/'.$app->controller->route, $get);
	}
}