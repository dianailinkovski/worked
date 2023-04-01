<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'contest-confirmation-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<?php echo $form->errorSummary(array_merge(array_reduce(array_map('array_values', $multiTabular), 'array_merge', array()), $fieldsTabular)); ?>

	<?php $this->widget('application.widgets.TabularInput.TabularInputWidget', array(
		'id'=>'fieldsTabular',
		'form'=>$form,
		'models'=>$fieldsTabular,
		'layout'=>array('_field'=>new ContestField),
		'nestedWidgets'=>array('_multi'=>'{widgetId}multiTabular{id}'),
		'renderData'=>array('multiTabular'=>$multiTabular),
		'itemTitleExpression'=>"CHtml::encode(str_replace(array_keys(Yii::app()->controller->typeArr), array_values(Yii::app()->controller->typeArr), \$model->type))",
		'orderAttribute'=>'rank',
		'afterAddItem'=>"function(id, itemId){
			$('#'+id+'_'+itemId).find('.tabularInputWidget').hide();
		}",
	)); ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton(Yii::t('admin', 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
	
<?php Yii::app()->clientScript->registerScript('formMulti', "
		
$('body').on('change', 'select[id$=\"type\"][id^=\"ContestField_fieldsTabular\"]', function(){
	var widget = $(this).closest('.tabularPortlet').find('.tabularInputWidget');
		
	if ($(this).val() != 'radio' && $(this).val() != 'checkbox') {
		widget.find('.tabularPortlet').each(function(){
			var id = $(this).attr('id');
			widget.tabularInputWidget('deleteItem', id.substr(id.lastIndexOf('_')+1));
		});
		
		widget.hide();
	} else {
		widget.show();
	}
		
	if ($(this).val() == 'title') {
		$(this).parent().next('.required-checkbox').hide().next('.result-checkbox').hide();
	} else {
		$(this).parent().next('.required-checkbox').show().next('.result-checkbox').show();
	}
});
		
$('select[id$=\"type\"][id^=\"ContestField_fieldsTabular\"]').each(function(){
	if ($(this).val() != 'radio' && $(this).val() != 'checkbox') {
		$(this).closest('.tabularPortlet').find('.tabularInputWidget').hide();
	}
		
	if ($(this).val() == 'title') {
		$(this).parent().next('.required-checkbox').hide().next('.result-checkbox').hide();
	}
});
", CClientScript::POS_READY); ?>