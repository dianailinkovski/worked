<?php echo $this->renderPartial('//adminLayouts/_blocHeader', array('form'=>$form, 'model'=>$model, 'formId'=>$formId, 'itemId'=>$itemId)); ?>

<fieldset>

	<legend>Documents</legend>

	<?php $this->widget('application.components.widgets.TabularInput.TabularInputWidget', array(
		'id'=>$formId.'-'.$itemId.'-blocDocumentDocumentForm',
		'form'=>$form,
		'layout'=>array('application.components.blocs.document.views.admin_documentDocument'=>new BlocDocumentDocument),
		'models'=>(isset($blocDocumentDocument[$itemId]) ? $blocDocumentDocument[$itemId] : array()),
		'itemTitleExpression'=>"CHtml::encode(\$model->title)",
		'orderAttribute'=>'rank',
		'afterAddItem'=>"function(id, itemId){
			".AdminHelper::tabularInputAfterAddItemDatetimePicker()."
		}"
	)); ?>
	
</fieldset>

<?php echo $form->hiddenField($model,'['.$formId.']['.$itemId.']rank'); ?>