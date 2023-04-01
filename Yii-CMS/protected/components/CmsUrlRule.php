<?php 
/**
 * Base class for all Cms Url rule classes
 * 
 * It has a few more parameters than a regular Url Rule
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Core
 */

abstract class CmsUrlRule extends CBaseUrlRule
{
	/**
	 * @var string in createUrl, this will hold the language the url should have
	 */
    public $language;
	/**
	 * @var array in createUrl, if the params must be changed, fill this variable (including all get params)
	 */
    public $newParams;
	/**
	 * @var boolean in createUrl, if true, it leaves the route as it is and doesn't search through aliases to transform the route
	 */
    public $cancelAliasResolution;
}