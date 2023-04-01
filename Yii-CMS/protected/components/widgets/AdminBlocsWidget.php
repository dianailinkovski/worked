<?php
/**
 * Generates the TabularInputWidget and the javascript that drives the blocs.
 *
 * @see BlocsWidget
 *
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Widget
 */

class AdminBlocsWidget extends CWidget
{
	/**
	 * @var string the unique id for the tabular widget. Required.
	 */
	public $id;
	/**
	 * @var array the blocs models. Must be key 0 => the main blocs, other keys ex: 'blocDocumentDocumentForm' => the sub models. Required.
	 */
	public $models;
	/**
	 * @var CActiveForm the from in which the widget is contained.
	 */
	public $form;

	/**
	 * Generates the TabularInputWidget and the javascript that drives the blocs.
	 */
	public function run()
	{
		// Preparing variables.
		$layouts = array();
		$layoutSelect = array();
		$afterAddItem = '';
		$beforeAddItem = '';
		$afterInit = '';
		$sortableStart = '';
		$sortableStop = '';
		$beforeDeleteItem = '';
		$afterDeleteItem = '';

		foreach (Yii::app()->params['blocs'] as $bloc)
		{
			$modelClassName = 'Bloc'.ucfirst($bloc);
			$newBlocModel = new $modelClassName;
			$newBlocModel->title_anchor = true;
			$newBlocModel->title_page = true;
			$layouts['application.components.blocs.'.$bloc.'.views.admin'] = $newBlocModel;
			$layoutSelect['application.components.blocs.'.$bloc.'.views.admin'] = Yii::t('admin', 'bloc_'.$bloc);
			$afterAddItem .= Yii::app()->cms->blocs[$bloc]->afterAddItem()."\n";
			$beforeAddItem .= Yii::app()->cms->blocs[$bloc]->beforeAddItem()."\n";
			$afterInit .= Yii::app()->cms->blocs[$bloc]->afterInit()."\n";
			$sortableStart .= Yii::app()->cms->blocs[$bloc]->sortableStart()."\n";
			$sortableStop .= Yii::app()->cms->blocs[$bloc]->sortableStop()."\n";
			$beforeDeleteItem .= Yii::app()->cms->blocs[$bloc]->beforeDeleteItem()."\n";
			$afterDeleteItem .= Yii::app()->cms->blocs[$bloc]->afterDeleteItem()."\n";
		}
		$renderData = array();
		foreach ($this->models as $key => $models)
		{
			if ($key !== 0)
				$renderData[$key] = $models;
		}

		// Instanciating widget.
		$this->widget('application.components.widgets.TabularInput.TabularInputWidget', array(
			'id'=>$this->id,
			'form'=>(isset($this->form) ? $this->form : null),
			'models'=>$this->models[0],
			'layoutExpression'=>"'application.components.blocs.'.lcfirst(mb_substr(get_class(\$model), 4)).'.views.admin'",
			'layout'=>$layouts,
			'layoutSelect'=>$layoutSelect,
			'itemTitleExpression'=>"Yii::t('admin', \$model->tableName())",
			'orderAttribute'=>'rank',
			'nestedWidgets'=>array('{formId}-{itemId}-blocDocumentDocumentForm', '{formId}-{itemId}-blocPeoplePeopleForm', '{formId}-{itemId}-blocCitationCitationForm', '{formId}-{itemId}-blocFeatureFeatureForm'),
			'renderData'=>$renderData,
			'sortable'=>array(
				'start'=>"js:function(event, ui){
					".AdminHelper::tabularInputCkEditorSortableStart()."
					".$sortableStart."
				}",
				'stop'=>"js:function(event, ui){
					".AdminHelper::tabularInputCkEditorSortableStop()."
					".$sortableStop."
				}",
			),
			'beforeDeleteItem'=>"function(id, itemId){
				".AdminHelper::tabularInputCkEditorBeforeDeleteItem()."
				".$beforeDeleteItem."
			}",
			'afterDeleteItem'=>"function(id, itemId){
				".$afterDeleteItem."
			}",
			'beforeAddItem'=>"function(id, itemId){
				".$beforeAddItem."
			}",
			'afterAddItem'=>"function(id, itemId){
				".AdminHelper::tabularInputAfterAddItemDatetimePicker()."
				".$afterAddItem."
			}",
			'afterInit'=>"function(id){
				".$afterInit."
			}"
		));
	}
}
