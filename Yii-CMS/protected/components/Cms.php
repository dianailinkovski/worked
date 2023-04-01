<?php
/**
 * Cms Application component
 * 
 * Cms application-wide variables and methods.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Core
 */

class Cms extends CApplicationComponent
{	
	/**
	 * @var int|null the id of the current section (derived from the current alias) or null if none
	 */
	public $currentSectionId=null;
	/**
	 * @var CmsAlias the current Alias if an alias was processed by CmsUrlManager.
	 */
	public $currentAlias;
	/**
	 * @var array the blocs (CmsBloc) that were initialized are stored here
	 */
	public $blocs = array();
	/**
	 * @var array the cms modules (extended from CmsModule). It is filled automatically you don't need to do it yourself. Defaults to array().
	 */
	public $modules = array();
}