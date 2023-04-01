<?php
/**
 * Creates a text field with a selector that pops up a jquery dialog where you can drag & drop into a tree style view
 * to choose which item you want selected.
 *
 * @see CQTreeGridView
 *
 * @author Bruno LL. <https://github.com/brunoll>
 * @link https://github.com/todo
 * @copyright Copyright © 2015 Societe G-NeTiX Inc. - All Rights Reserved
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @package Widget
 */
class TreeLocationWidget extends CInputWidget
{
	/**
	 * @var string the id of the button. Required.
	 */
	public $id;
	/**
	 * @var string value of the button
	 */
	public $buttonValue;
	/**
	 * @var array html options of the button
	 */
	public $buttonHtmlOptions=array();
	/**
	 * @var array value of the input (root of the path)
	 */
	public $inputValue;
	/**
	 * @var array html options of the input
	 */
	public $inputHtmlOptions=array();
	/**
	 * @var string name of the attribute in the model for CQTreeGridView (just an attribute that holds data for CQTreeGridView). Required.
	 * @see CQTreeGridView::$widgetAttribute
	 */
	public $widgetAttribute;
	/**
	 * @var string name of the attribute that shows up in the input field. Required.
	 * @see CQTreeGridView::$pathAttribute
	 */
	public $pathAttribute;
	/**
	 * @var string name of the attribute that shows up in the tree. Required.
	 */
	public $columnAttribute;
	/**
	 * @var string name of the attribute in the model (boolean) for if item can have children or not
	 * @see CQTreeGridView::$allowChildrenAttribute
	 */
	public $allowChildrenAttribute;
	

	public function run()
	{
		list($name,$id)=$this->resolveNameID();
		if(isset($this->htmlOptions['id']))
			$id=$this->htmlOptions['id'];
		else
			$this->htmlOptions['id']=$id;
			
		if(isset($this->htmlOptions['name']))
			$name=$this->htmlOptions['name'];
			
		if(!isset($this->buttonValue))
			$this->buttonValue=Yii::t('admin', 'Browse…');
			
		if(!isset($this->buttonHtmlOptions['id']))
			$this->buttonHtmlOptions['id']=$this->htmlOptions['id'].'_button';
			
		if(!isset($this->inputHtmlOptions['id']))
			$this->inputHtmlOptions['id']=$this->htmlOptions['id'].'_input';
			
		$this->inputHtmlOptions['disabled'] = true;
		
		$htmlId = $this->htmlOptions['id'];

		$this->registerClientScript();
		
		$modelClass = get_class($this->model);

		$dataProvider = new CActiveDataProvider($modelClass, array(
			'criteria' => array(
				'order' => $this->model->tree->hasManyRoots ? $this->model->tree->rootAttribute.','.$this->model->tree->leftAttribute : $this->model->tree->leftAttribute,
			),
			'pagination' => false,
		));

		if($this->hasModel()) 
		{
			if (!$this->model->isNewRecord)
			{
				$ancestors = $this->model->ancestors()->findAll();
				foreach ($ancestors as $ancestor)
				{
					$this->inputValue.= '/'.$ancestor->{$this->pathAttribute};
				}		
			}
			echo CHtml::activeHiddenField($this->model, $this->attribute, array_merge(array('value'=>'nochange'), $this->htmlOptions));

			$dataProviderDatas = $dataProvider->getData();
			
			foreach ($dataProviderDatas as $dataProviderData)
			{
				$dataProviderData->{$this->widgetAttribute} = array();
				
				if ($dataProviderData->primaryKey == $this->model->primaryKey)
					$dataProviderData->{$this->widgetAttribute}['allowDraggable'] = true;
				else
					$dataProviderData->{$this->widgetAttribute}['allowDraggable'] = false;
			}
		} 
		else 
			throw new CHttpException(500, "TreeLocationWidget needs model");

		echo '<div class="input-group">
		'.CHtml::textField($name.'_input',$this->inputValue,$this->inputHtmlOptions)
		.'<span class="input-group-btn">'.CHtml::button($this->buttonValue, array_merge($this->buttonHtmlOptions, array('class'=>'btn btn-primary', 'data-toggle'=>'modal', 'data-target'=>'#'.$this->id.'_modal'))).'</span>
		</div>
		<div class="modal fade" tabindex="-1" role="dialog" id="'.$this->id.'_modal">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">'.CHtml::encode($this->model->attributeLabels()[$this->attribute]).'</h4>
		      </div>
		      <div class="modal-body">';

				$this->widget('ext.QTreeGridView.CQTreeGridView', array(
				    'id'=>$this->id,
				    'ajaxUpdate' => false,
					'hideHeader' => true,
					'allowChildrenAttribute' => $this->allowChildrenAttribute,
					'currentModelId' => $this->model->primaryKey,
					'widgetAttribute' => $this->widgetAttribute,
					'pathAttribute' => $this->pathAttribute,		
					'itemsCssClass' => 'items locationWidgetRow',
					'dropFunction' => 'function(e, ui) {
		                if ($(this).hasClass("initialized")) {
							var action = "child";
							var id = $(this).attr("id");
							var to = id;
							$(ui.draggable).treeTable_appendBranchTo(this);
		                }
		                if ($(this).hasClass("before")) {
							var id = $(this).attr("id").replace("before-", "");
							var action = "before";
							var to = id;
						
							var classes = $("#"+id).attr("class").split(" ");
							for (var i = 0, l = classes.length; i<l; ++i) {
								if (classes[i].match(/child-of-[0-9]+/g)){
									var parentId = classes[i].replace("child-of-", "");
									break;
								}
							}
							var firstTr = $(this).closest("table tbody").children("tr:first");
							$(ui.draggable).treeTable_appendBranchTo(firstTr[0]);
							$("tr[class*=\'child-of-"+parentId+"\']").reverse().each(function(){
								$(this).treeTable_appendBranchTo(firstTr[0]);
								$(this).treeTable_appendBranchTo($("#"+parentId)[0]);
								$("#after-"+id).insertAfter($(this));
								$("#before-"+id).insertBefore($(this));
								if ($(this).attr("id") == id)
									$(ui.draggable).treeTable_appendBranchTo($("#"+parentId)[0]);
							});
		                }
		                if ($(this).hasClass("after")) {
							var id = $(this).attr("id").replace("after-", "");
							var action = "after";
							var to = id;

							var classes = $("#"+id).attr("class").split(" ");
							for (var i = 0, l = classes.length; i<l; ++i) {
								if (classes[i].match(/child-of-[0-9]+/g)){
									var parentId = classes[i].replace("child-of-", "");
									break;
								}
							}
							var firstTr = $(this).closest("table tbody").children("tr:first");
							$(ui.draggable).treeTable_appendBranchTo(firstTr[0]);
							$("tr[class*=\'child-of-"+parentId+"\']").reverse().each(function(){
								if ($(this).attr("id") == id)
									$(ui.draggable).treeTable_appendBranchTo($("#"+parentId)[0]);
								$(this).treeTable_appendBranchTo(firstTr[0]);
								$(this).treeTable_appendBranchTo($("#"+parentId)[0]);
								$("#after-"+id).insertAfter($(this));
								$("#before-"+id).insertBefore($(this));
							});
		                }
						$("#'.$this->inputHtmlOptions['id'].'").val($(this).attr("path"));

						var arr = {
							"action":action,
							"to":to
						};
						$("#'.$htmlId.'").val(JSON.stringify(arr));
					}',
				    'selectableRows' => 0,
					'rowCssClassExpression' => '$data->primaryKey == '.($this->model->isNewRecord ? 0 : $this->model->primaryKey).' ? "dragrow" : ('.($this->allowChildrenAttribute === null ? 'true' : '$data->'.$this->allowChildrenAttribute).' ? "dropRow" : "")',
					'summaryText' => '',
					'dataProvider'=>$dataProvider,
				    'columns'=>array(
				        array('name'=>$this->columnAttribute, 'type'=>'raw', 'value'=>'$data->primaryKey == '.($this->model->isNewRecord ? 0 : $this->model->primaryKey).' ? "<span style=\"white-space: nowrap;\"><span class=\"glyphicon glyphicon-hand-right\" aria-hidden=\"true\"></span>&nbsp;&nbsp;</span>'.Yii::t('admin', 'Glissez-moi').'" : CHtml::encode($data->'.$this->columnAttribute.')'),
				    ),
				));

		echo '
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
		      </div>
		    </div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		';
	}
	
	public function registerClientScript()
	{
		$script = ($this->model->isNewRecord ? "$('#".$this->id." tr:first').after('<tr id=\"0\" class=\"even initialized ui-draggable dragrow\" path=\"\"><td style=\"padding-left: 19px;\"><span style=\"white-space: nowrap;\"><span class=\"glyphicon glyphicon-hand-right\" aria-hidden=\"true\"></span>&nbsp;&nbsp;</span>".Yii::t('admin', 'Glissez-moi')."</td></tr>');" : "");
		Yii::app()->clientScript->registerScript('TreeStructureWidget#'.$this->htmlOptions['id'], $script, CClientScript::POS_READY);
	}
}