<?php
/**
 * CKEditor Widget
 *
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Widget
 */
class CKEditorWidget extends CInputWidget
{
	/**
	 * @var string the path to the root of the ckeditor library. Required.
	 */
	public $ckBasePath;
	/**
	 * @var array the config of the ckeditor.
	 * @see http://docs.ckeditor.com/#!/api/CKEDITOR.config
	 */
	public $config;
	/**
	 * @var array the settings of the textarea element
	 */
	public $textareaAttributes=array();

	public function run()
	{
		$controller = Yii::app()->controller;

		if (!isset($this->ckBasePath))
		{
			if (isset($controller->ckBasePath))
				$this->ckBasePath = $controller->ckBasePath;
			else
				throw new CHttpException(500,'Parameter "ckBasePath" has to be set!');
		}
		if (!isset($this->config) && isset($controller->ckEditorConfig))
			$this->config = $controller->ckEditorConfig;
		
		if (!$this->hasModel() && !isset($this->name))
			throw new CHttpException(500,'Parameters "model" and "attribute" or "name" have to be set!');

		Yii::app()->clientScript->registerScriptFile($this->ckBasePath.'ckeditor.js');

		$this->render('CKEditorWidget',array(
			'ckBasePath'=>$this->ckBasePath,
			'model'=>$this->model,
			'attribute'=>$this->attribute,
			'name'=>$this->name,
			'value'=>$this->value,
			'config'=>$this->config,
			'textareaAttributes'=>$this->textareaAttributes,
		));
	}
}
?>
