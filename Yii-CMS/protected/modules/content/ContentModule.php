<?php
/**
 * Content module
 *
 * Manages editable pages with blocs, generates multilang aliases for pages automatically
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Module
 */

class ContentModule extends CmsModule
{
	public function init()
	{
		$this->setImport(array(
			'content.models.*',
			'content.components.*',
		));
	}

	public static function getAdminMenu()
	{
		return array(
			array('label'=>'{sectionName}', 'url'=>array('/content/admin/admin'), 'icon'=>'edit')
		);
	}
	
	public static function getUrlRuleClass()
	{
		return 'application.modules.content.components.ContentUrlRule';
	}

	public function getLayouts()
	{
		return array(
			'standard'	=> 'Standard (1 seule zone de contenu)',
			'right_column_1' => '2 zones de contenu (avec contenu custom type 1)',
			'right_column_2' => '2 zones de contenu (avec contenu custom type 2)',
		);
	}
}