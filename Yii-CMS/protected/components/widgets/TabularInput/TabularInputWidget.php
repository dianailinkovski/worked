<?php
/**
 * Creates a highly customizable, infinitely nestable javascript based system to manage a form and do CRUD operations in javascript only.
 *
 * Different preset layouts are available using jquery ui classes, but you can also create your own.
 *
 * Multiple javascript events are available to operate during certain actions being performed.
 *
 * You are not limited to a single layout, you can use multiple layouts and determine their use via a layoutExpression.
 * You can also use a layoutSelect to select which layout will be used when the user presses the add button.
 *
 * You can also choose to have ordering of the items via jquery sortable (by drag and dropping).
 * 
 * Being infinitely nestable, a strict nomenclature is used to identify all the levels and items of the hierarchy.
 *
 * New items are id as n{n} (n0, n1, n2...) but they are not necessarily all present, could be n0, n1, n3, n7...
 *
 * 4 variables are passed on to the view file:
 * 		formId: the id of this particular form. If the form is a sub form, id will be dynamic and vary with the parent form.
 * 		itemId: the id of the item being added (can also be n{n} if it's a new item).
 * 		model: the model being managed in this form. Can be empty(new) for new items being added.
 * 		form: the CActiveForm widget to which the form belongs to (optional)
 *
 * Obviously your forms elements name must contain both formId and itemId to be unique. They will be passed on to php as an array of data (tabular input).
 *
 * @see FormManager to easily create the action logic to support this.
 * 
 * Here is an example of the widget being instanced inside a "book" form:
 *
 * 		$this->widget('application.components.widgets.TabularInput.TabularInputWidget', array(
 * 			'id'=>'chapterForm',
 * 			'form'=>$form,
 * 			'template'=>'noheader-noborder',
 * 			'models'=>$chapters,
 *  		'layout'=>array('_chapter'=>new Chapter),
 *  		'nestedWidgets'=>array('{formId}-{itemId}-pageForm'),
 * 			'orderAttribute'=>'rank',
 * 		)); ?>
 *
 * Here is an example of a nested widget:
 *
 * 		<?php $this->widget('application.components.widgets.TabularInput.TabularInputWidget', array(
 * 			'id'=>$formId.'-'.$itemId.'-pageForm',
 * 			'form'=>$form,
 * 			'layout'=>array('_page'=>new Page),
 * 			'models'=>(isset($pages[$itemId]) ? $pages[$itemId] : array()),
 * 			'itemTitleExpression'=>"CHtml::encode(\$model->title)",
 * 			'orderAttribute'=>'rank',
 * 			'afterAddItem'=>"function(id, itemId){
 * 				alert('item added');
 * 			}"
 * 		)); ?>
 *
 * And here is an example of the form elements in a view file:
 *
 * 		<?php echo $form->textField($model,'['.$formId.']['.$itemId.']title'); ?>
 * 		<?php echo $form->hiddenField($model,'['.$formId.']['.$itemId.']rank'); ?>
 *
 * All parameters are optional unless explicitely stated.
 * 
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Widget
 */
class TabularInputWidget extends CWidget
{
	/**
	 * General id of the widget
	 * Required
	 * Important: do not use underscores or n{n} (n0, n1, n2...) or "itemIdPh" or it will conflict with the javascript logic.
	 * @var string
	 */
	public $id;
	/**
	 * @var CActiveForm if using CActiveForm widget, pass it here to continue using it in the view file.
	 */
	public $form;
	/**
	 * @var array the models to initiate the widget with. Defaults to empty array.
	 */
	public $models=array();
	/**
	 * Layout view file to manage
	 * Required.
	 * array of key=>value pairs where key is the layout file passed to render function and value is an empty(new) model used to generate new views.
	 * If multiple different view files are used, layoutExpression is used to differentiate them and layoutSelect to choose between them.
	 * @var array
	 */
	public $layout;
	/**
	 * Expression to differentiate between the different layouts depending on a model attribute or other.
	 * The data contains array('model'=>$model) the model.
	 * @see CComponent:evaluateExpression()
	 * @var mixed
	 */
	public $layoutExpression;
	/**
	 * @var bool wether to include a layout select. If you use a custom template, include {layoutSelect} where you want it to be. Defaults to false.
	 */
	public $layoutSelect=false;
	/**
	 * @var string the title of the item if you use it in your template.
	 */
	public $itemTitle='';
	/**
	 * @var string the title of the item of a new item (if you want to differentiate) if you use it in your template.
	 */
	public $itemTitleNew;
	/**
	 * Expression if you want a dynamic item title depending on a model attribute or other.
	 * The data contains array('model'=>$model) the model.
	 * @see CComponent:evaluateExpression()
	 * @var mixed
	 */
	public $itemTitleExpression;
	/**
	 * Javascript event that triggers after widget is initiated
	 * Must be a function with a signature as such: function(id) 
	 * Where id is the id of the id of the tabular input widget
	 * @var string
	 */
	public $afterInit;
	/**
	 * Javascript event that triggers before an item is added
	 * Must be a function with a signature as such: function(id) 
	 * Where id is the id of the id of the tabular input widget
	 * You can return nothing or a string containing the layout to add
	 * @var string
	 */
	public $beforeAddItem;
	/**
	 * Javascript event that triggers after an item is added
	 * Must be a function with a signature as such: function(id, itemId) 
	 * Where id is the id of the id of the tabular input widget and itemId is the id of the item being added
	 * @var string
	 */
	public $afterAddItem;
	/**
	 * Javascript event that triggers before an item is deleted
	 * Must be a function with a signature as such: function(id, itemId) 
	 * Where id is the id of the id of the tabular input widget and itemId is the id of the item being deleted
	 * Return false to cancel deletion, anything else will make it continue
	 * @var string
	 */
	public $beforeDeleteItem;
	/**
	 * Javascript event that triggers after an item is deleted
	 * Must be a function with a signature as such: function(id, itemId) 
	 * Where id is the id of the id of the tabular input widget and itemId is the id of the item being deleted
	 * @var string
	 */
	public $afterDeleteItem;
	/**
	 * An array that will be CJavaScript encoded and passed to JQueryUI if you want to customize the sortable
	 * @see http://api.jqueryui.com/sortable/
	 * @var array
	 */
	public $sortable;
	/**
	 * The model attribute containing the order of the items. 
	 * Usually you make a hidden field for that attribute and it will be automatically updated when changes are made with sortable.
	 * @var string
	 */
	public $orderAttribute;
	/**
	 * @var array extra data you might want to pass on to your view file.
	 */
	public $renderData=array();
	/**
	 * The widget must be made aware of any nested widgets inside the items it manages.
	 * All you need to do is give their ids (including {formId} and {itemId} where present) and it will initialize them properly.
	 * Exemple: array('{formId}-{itemId}-nameOfSubForm', '{formId}-{itemId}-nameOfSubForm2')
	 * @var array
	 */
	public $nestedWidgets;
	/**
	 * @var string the template for the add button. Defaults to "+" or a preset template defined by formattingMethod.
	 */
	public $addButton;
	/**
	 * @var string the template for the add button. Defaults to "X" or a preset template defined by formattingMethod.
	 */
	public $deleteButton;
	/**
	 * @var string the template for the add button. Defaults to "+-" or a preset template defined by formattingMethod.
	 */
	public $collapseAllButton;
	/**
	 * @var string the template for the add button. Defaults to "+" or a preset template defined by formattingMethod.
	 */
	public $collapseButtonOn;
	/**
	 * @var string the template for the add button. Defaults to "-" or a preset template defined by formattingMethod.
	 */
	public $collapseButtonOff;
	/**
	 * @var bool if the items are initially collapsed. Default depends on your template.
	 */
	public $initiallyCollapsed;
	/**
	 * @var string the text in the confirm dialog when user deletes an item, if you want one.
	 */
	public $deleteConfirmDialog;
	/**
	 * The template used for the widget.
	 * Either use a custom template or choose one of the presets (in $template, leave $itemTemplate empty if you use a preset). 
	 * If you do a custom template you must specify both $template and $itemTemplate.
	 * The following presets are available: collapsible-header, header, noheader, noheader-noborder.
	 * If you use a custom template, you must include the following placeholders: {items}, {add} and optionally if you need them, the following: {collapseAll}, {layoutSelect}
	 * @var string 
	 */
	public $template='collapsible-header';
	/**
	 * The template used for an item.
	 * Either use a custom template or choose one of the presets (in $template, leave $itemTemplate empty if you use a preset).
	 * If you do a custom template you must specify both $template and $itemTemplate.
	 * If you use a custom template, you must include the following placeholders: {content}, {deleteButton} and optionally if you need them, the following: {title}, {collapseButton}
	 * @var string 
	 */
	public $itemTemplate;
	/**
	 * @var string|false the formatting method to use. Currently supports values 'bootstrap' and 'jqueryui'. If you use bootstrap you are responsible for including the library. Set to false if you want no 3rd party library used. Defaults to bootstrap.
	 */
	public $formattingMethod='bootstrap';
	/**
	 * The URL of the CSS file used by this widget. 
	 * Defaults to null, meaning using the included CSS file.
	 * If this is set to false, no CSS file will be registered.
	 * @var string
	 */
	public $cssFile;
	

	/**
	 * @var int internally keeps track of items iteration.
	 */
	private $_newItemsIt;
	/**
	 * @var string|null the url of the assets. Defaults to null for default assets folder.
	 */
	private $_assetsUrl=null;


	public function init()
	{
		$this->registerAssets();
		$this->defaultProperties();
		
		return parent::init();
	}

	public function run()
	{
		$column = '
		<div class="tabularColumn">
		';

		// Initialize and order items
		if (isset($this->orderAttribute)) 
		{
			$orderArr = array();
			$itemsArr = array();
		}
		$this->_newItemsIt = 0;
		foreach($this->models as $id => $model) 
		{
			if ($model->isNewRecord) 
			{
        		$itemId = 'n'.$this->_newItemsIt;
        		$this->_newItemsIt++;
			} 
			else
        		$itemId = $id;
			
			if (isset($this->orderAttribute)) 
			{
				$orderArr[] = $model->{$this->orderAttribute};
				$itemsArr[] = array($itemId, $model);
			}
			else
				$column .= $this->makeItem($itemId, $model);
		}
		if (isset($this->orderAttribute)) 
		{
			array_multisort($orderArr, $itemsArr);

			foreach($itemsArr as $item) 
			{
				$column .= $this->makeItem($item[0], $item[1]);
			}
		}
		
		$column .= '
		</div>
		';
		
		$html = str_replace('{collapseAll}', CHtml::link($this->collapseAllButton, 'javascript:;', array('class'=>'tabularRowCollapseAll')), $this->template);
		
		$html = str_replace('{items}', $column, $html);
		
		$html = str_replace('{add}', CHtml::link($this->addButton, 'javascript:;', array('class'=>'tabularRowAdd')), $html);
		
		if ($this->layoutSelect)
			$html = str_replace('{layoutSelect}', CHtml::dropDownList($this->id.'_layoutSelect', '', $this->layoutSelect, array('empty'=>'', 'class'=>'tabularRowLayoutSelect'.($this->formattingMethod == 'bootstrap' ? ' form-control' : ''))), $html);
		else
			$html = str_replace('{layoutSelect}', '', $html);

	    echo '
	    <div id="'.$this->id.'" class="tabularInputWidget">
			'.$html.'
		</div>
		';
	    
	    $this->registerClientScript();
	}

	/**
	 * Render an item
	 * @param string $itemId the id of the item
	 * @param string $model the model with which to create the item
	 * @return string the output of the render
	 */
	protected function makeItem($itemId, $model) 
	{
		if (isset($this->itemTitleExpression))
			$itemTitle = $this->evaluateExpression($this->itemTitleExpression, array('model'=>$model));
		else
			$itemTitle = $this->itemTitle;
			
		if (isset($this->layoutExpression)) 
		{
			$layout = $this->evaluateExpression($this->layoutExpression, array('model'=>$model));
		}
		else {
			if (count($this->layout) > 1)
				throw new CHttpException('403', 'If you use more than one layout, you must use an expression to differentiate between them.');
			else {
				$layoutKeys=array_keys($this->layout);
				$layout = $layoutKeys[0];
			}
		}

		$output = str_replace('{deleteButton}', CHtml::link($this->deleteButton, 'javascript:;', array('class'=>'tabularRowDelete')), $this->itemTemplate);
			
		$output = str_replace('{collapseButton}', CHtml::link($this->collapseButtonOn, 'javascript:;', array('class'=>'tabularRowCollapse tabularRowCollapseOn')), $output);
		
		if (isset($this->itemTitleNew) && $model->isNewRecord)
			$output = str_replace('{title}', '&nbsp;'.$this->itemTitleNew, $output);
		else
			$output = str_replace('{title}', '&nbsp;'.$itemTitle, $output);

		$renderArray = array('formId'=>$this->id, 'itemId'=>$itemId, 'model'=>$model);
		if (isset($this->form))
			$renderArray['form'] = $this->form;

		$output = str_replace('{content}', '
		<div class="tabularItem-content">
        	'.$this->controller->renderPartial($layout, array_merge($this->renderData, $renderArray), true).'
		</div>', $output);
		
		$output = '
		<div class="tabularItem'.(count($model->errors) != 0 ? ' tabularItem-nohide' : '').'" id="'.($this->id.'_'.$itemId).'">
			'.$output.'
		</div>
		';
		
		return $output;
	}
	
	protected function defaultProperties()
	{
		$this->id = CHtml::encode(str_replace('_', '-', $this->id));

		if ($this->template == 'collapsible-header' && !isset($this->itemTemplate))
		{
			$this->template = '<div class="tabularCollapseAll">{collapseAll}</div>{items}<div class="tabularAdd">'.($this->formattingMethod == 'bootstrap' ? '<div class="input-group col-sm-6 col-md-5 col-lg-4">' : '').'{layoutSelect}'.($this->formattingMethod == 'bootstrap' ? '<span class="input-group-btn">' : '').'{add}'.($this->formattingMethod == 'bootstrap' ? '</span></div>' : '').'</div>';
			if ($this->formattingMethod == 'jqueryui') 
				$this->itemTemplate = '<div class="tabularItem-wrapper ui-widget ui-widget-content ui-helper-clearfix ui-corner-all"><div class="tabularItem-header ui-widget-header ui-corner-all">{collapseButton}{title}{deleteButton}</div>{content}</div>';
			elseif ($this->formattingMethod == 'bootstrap') 
				$this->itemTemplate = '<div class="tabularItem-wrapper"><div class="tabularItem-header">{collapseButton}{title}{deleteButton}</div>{content}</div>';
			else
				$this->itemTemplate = '<div class="tabularItem-wrapper"><div class="tabularItem-header">{collapseButton}{title}{deleteButton}</div>{content}</div>';
			$initiallyCollapsed = true;
		}
		else if ($this->template == 'header' && !isset($this->itemTemplate))
		{
			$this->template = '{items}<div class="tabularAdd">{layoutSelect}{add}</div>';
			if ($this->formattingMethod == 'jqueryui') 
				$this->itemTemplate = '<div class="tabularItem-wrapper ui-widget ui-widget-content ui-helper-clearfix ui-corner-all"><div class="tabularItem-header ui-widget-header ui-corner-all">{title}{deleteButton}</div>{content}</div>';
			elseif ($this->formattingMethod == 'bootstrap') 
				$this->itemTemplate = '<div class="tabularItem-wrapper"><div class="tabularItem-header">{title}{deleteButton}</div>{content}</div>';
			else
				$this->itemTemplate = '<div class="tabularItem-wrapper"><div class="tabularItem-header">{title}{deleteButton}</div>{content}</div>';
			$initiallyCollapsed = false;
		}
		else if ($this->template == 'noheader' && !isset($this->itemTemplate))
		{
			$this->template = '{items}<div class="tabularAdd">{layoutSelect}{add}</div>';
			if ($this->formattingMethod == 'jqueryui') 
				$this->itemTemplate = '<div class="tabularItem-wrapper ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" style="float: left;">{content}</div><div style="float: left;">{deleteButton}</div><div style="clear: both;"></div>';
			elseif ($this->formattingMethod == 'bootstrap') 
				$this->itemTemplate = '<div class="tabularItem-wrapper" style="float: left;">{content}</div><div style="float: left;">{deleteButton}</div><div style="clear: both;"></div>';
			else
				$this->itemTemplate = '<div class="tabularItem-wrapper" style="float: left;">{content}</div><div style="float: left;">{deleteButton}</div><div style="clear: both;"></div>';
			$initiallyCollapsed = false;
		}
		else if ($this->template == 'noheader-noborder' && !isset($this->itemTemplate))
		{
			$this->template = '{items}<div class="tabularAdd">{layoutSelect}{add}</div>';
			$this->itemTemplate = '<div class="tabularItem-wrapper" style="float: left;">{content}</div><div style="float: left;">{deleteButton}</div><div style="clear: both;"></div>';
			$initiallyCollapsed = false;
		}
		else
			$initiallyCollapsed = true;
		
		if ($this->initiallyCollapsed === null)
			$this->initiallyCollapsed = $initiallyCollapsed;

		if (!isset($this->collapseButtonOn))
		{
			if ($this->formattingMethod == 'jqueryui')
				$this->collapseButtonOn = '<span class="ui-icon ui-icon-minusthick" style="display: inline-block"></span>';
			elseif ($this->formattingMethod == 'bootstrap')
				$this->collapseButtonOn = '<span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>';
			else
				$this->collapseButtonOn = '&ndash;';
		}
		
		if (!isset($this->collapseButtonOff))
		{
			if ($this->formattingMethod == 'jqueryui')
				$this->collapseButtonOff = '<span class="ui-icon ui-icon-plusthick" style="display: inline-block"></span>';
			elseif ($this->formattingMethod == 'bootstrap')
				$this->collapseButtonOff = '<span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span>';
			else
				$this->collapseButtonOff = '+';
		}
		
		if (!isset($this->deleteButton))
		{
			if ($this->formattingMethod == 'jqueryui')
				$this->deleteButton = '<span class="ui-icon ui-icon-circle-close" style="display: inline-block"></span>';
			elseif ($this->formattingMethod == 'bootstrap')
				$this->deleteButton = '<span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span>';
			else
				$this->deleteButton = 'X';
		}
			
		if (!isset($this->addButton))
		{
			if ($this->formattingMethod == 'jqueryui')
				$this->addButton = '<span class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">+</span></span>';
			elseif ($this->formattingMethod == 'bootstrap')
				$this->addButton = '<button class="btn btn-default" type="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>';
			else
				$this->addButton = '+';
		}
			
		if (!isset($this->collapseAllButton))
		{
			if ($this->formattingMethod == 'jqueryui')
				$this->collapseAllButton = '<span class="ui-icon ui-icon-plusthick" style="display: inline-block"></span><span class="ui-icon ui-icon-minusthick" style="display: inline-block"></span>';
			else if ($this->formattingMethod == 'bootstrap')
				$this->collapseAllButton = '<span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span>';
			else
				$this->collapseAllButton = '+-';
		}
	}
	
    protected function registerAssets()
    {
        if ($this->_assetsUrl===null)
        {
        	Yii::app()->clientScript->registerCoreScript('jquery');
        	if ($this->formattingMethod == 'jqueryui')
        		Yii::app()->clientScript->registerCoreScript('jquery.ui');
        		
            $this->_assetsUrl = Yii::app()->assetManager->publish(dirname(__FILE__).'/assets');

            $cs = Yii::app()->getClientScript();
            $cs->registerScriptFile($this->_assetsUrl.'/js/jquery.tabularInputWidget.js');
            
	        if($this->cssFile!==false)
			{
				if($this->cssFile===null)
					$this->cssFile=$this->_assetsUrl.'/css/styles.css';
				Yii::app()->getClientScript()->registerCssFile($this->cssFile);
			}

            return $this->_assetsUrl;
        }
    }
	
	/**
	 * Register the tabularInputWidget.
	 *
	 * If we're dealing with a nested widget, only register the options so that the parent widget knows them when creating a sub-widget.
	 */
	protected function registerClientScript()
	{
		$emptyLayout = array();
		foreach($this->layout as $layout => $model)
			$emptyLayout[$layout] = $this->makeItem('itemIdPh', $model);
		
		if (strpos($this->id, 'itemIdPh') === false) 
		{
			$script = "
			$('#".CJavaScript::quote($this->id)."').tabularInputWidget({
				emptyLayout: ".CJavaScript::encode($emptyLayout).",
				initiallyCollapsed: ".CJavaScript::encode($this->initiallyCollapsed).",
				initialItemsCount: ".$this->_newItemsIt.(isset($this->collapseButtonOn) ? ",
				collapseButtonOn: ".CJavaScript::encode($this->collapseButtonOn) : "").(isset($this->collapseButtonOff) ? ",
				collapseButtonOff: ".CJavaScript::encode($this->collapseButtonOff) : "").(isset($this->afterInit) ? ",
				afterInit: ".$this->afterInit : "").(isset($this->beforeAddItem) ? ",
				beforeAddItem: ".$this->beforeAddItem : "").(isset($this->afterAddItem) ? ",
				afterAddItem: ".$this->afterAddItem : "").(isset($this->beforeDeleteItem) ? ",
				beforeDeleteItem: ".$this->beforeDeleteItem : "").(isset($this->afterDeleteItem) ? ",
				afterDeleteItem: ".$this->afterDeleteItem : "").(isset($this->deleteConfirmDialog) ? ",
				deleteConfirmDialog: ".CJavaScript::encode($this->deleteConfirmDialog) : "").(isset($this->sortable) ? ",
				sortable: ".CJavaScript::encode($this->sortable) : "").(isset($this->orderAttribute) ? ",
				orderAttribute: ".CJavaScript::encode($this->orderAttribute) : "").(isset($this->nestedWidgets) ? ",
				nestedWidgets: ".CJavaScript::encode($this->nestedWidgets) : "")."
			});
			";
			Yii::app()->clientScript->registerScript(__CLASS__.'#'.$this->id, $script, CClientScript::POS_READY);
		} 
		else {
			$script = "
			$.fn.tabularInputWidget('nestedWidgetOptions', {
				'id': '".CJavaScript::quote($this->id)."', 
				'options': {
					emptyLayout: ".CJavaScript::encode($emptyLayout).",
					initiallyCollapsed: ".CJavaScript::encode($this->initiallyCollapsed).",
					initialItemsCount: ".$this->_newItemsIt.(isset($this->collapseButtonOn) ? ",
					collapseButtonOn: ".CJavaScript::encode($this->collapseButtonOn) : "").(isset($this->collapseButtonOff) ? ",
					collapseButtonOff: ".CJavaScript::encode($this->collapseButtonOff) : "").(isset($this->afterInit) ? ",
					afterInit: ".$this->afterInit : "").(isset($this->beforeAddItem) ? ",
					beforeAddItem: ".$this->beforeAddItem : "").(isset($this->afterAddItem) ? ",
					afterAddItem: ".$this->afterAddItem : "").(isset($this->beforeDeleteItem) ? ",
					beforeDeleteItem: ".$this->beforeDeleteItem : "").(isset($this->afterDeleteItem) ? ",
					afterDeleteItem: ".$this->afterDeleteItem : "").(isset($this->deleteConfirmDialog) ? ",
					deleteConfirmDialog: ".CJavaScript::encode($this->deleteConfirmDialog) : "").(isset($this->sortable) ? ",
					sortable: ".CJavaScript::encode($this->sortable) : "").(isset($this->orderAttribute) ? ",
					orderAttribute: ".CJavaScript::encode($this->orderAttribute) : "").(isset($this->nestedWidgets) ? ",
					nestedWidgets: ".CJavaScript::encode($this->nestedWidgets) : "")."
				}
			});
			";
			Yii::app()->clientScript->registerScript(__CLASS__.'#'.$this->id, $script, CClientScript::POS_READY);
		}
	}
}