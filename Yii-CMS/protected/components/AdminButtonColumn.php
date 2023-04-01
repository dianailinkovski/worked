<?php
/**
 * Standard AdminButtonColumn for the CMS
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Other
 */

class AdminButtonColumn extends CButtonColumn
{
	public $template='{update} {delete}';

	public function init() 
	{
		if (isset(Yii::app()->cms->currentSectionId)) 
		{
			if ($this->updateButtonUrl == 'Yii::app()->controller->createUrl("update",array("id"=>$data->primaryKey))') 
				$this->updateButtonUrl = 'Yii::app()->controller->createUrl("update",array("id"=>$data->primaryKey,"section_id"=>Yii::app()->cms->currentSectionId))';
			if ($this->deleteButtonUrl == 'Yii::app()->controller->createUrl("delete",array("id"=>$data->primaryKey))') 
				$this->deleteButtonUrl = 'Yii::app()->controller->createUrl("delete",array("id"=>$data->primaryKey,"section_id"=>Yii::app()->cms->currentSectionId))';
		}
		return parent::init();	
	}
}