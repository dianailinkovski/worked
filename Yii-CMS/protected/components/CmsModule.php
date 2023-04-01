<?php 
/**
 * Abstract class CmsModule, cms modules must extend from this and implement the different functions as needed.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Core
 */

abstract class CmsModule extends CWebModule
{
	/**
	 * @var boolean whether the module can be instanced or not (used multiple times on the site). Defaults to false.
	 */
    public $instantiable = false;
    
	/**
	 * @return array the module's url rewrite rules
	 * @see CUrlManager::$rules
	 */
    public static function getUrlRules()
    {
    	return array();
    }
    
	/**
	 * @return string the module's url rewrite class
	 * @see CmsUrlRule
	 */
    public static function getUrlRuleClass(){}
    
	/**
	 * The admin menu items the module will add.
	 * 
	 * It's the same as @see CMenu::$items except in the label you can add {sectionName} in the label to be replaced with the section name
	 * and in the url, section_id GET variable will be added
	 * 
	 * @return array the admin menu items
	 * @see CMenu::$items
	 */
	public static function getAdminMenu()
	{
		return array();
	}
	
	/**
	 * The tabs that will show up in the admin, serving as a sub-menu.
	 * 
	 * You must define an array with 3 parameters : 
	 * url: (array) the url the tab will link to @see CHtml::normalizeUrl()
	 * label: (string) the label the tab will show
	 * controller: the name of the module's controller (lower case, without "controller") that will make this tab register as "active"
	 * 
	 * @return array the admin tabs
	 */
	public static function getAdminTabs()
	{
		return array();
	}
}