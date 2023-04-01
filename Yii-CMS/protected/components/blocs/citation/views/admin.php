<?php echo $this->renderPartial('//adminLayouts/_blocHeader', array('form'=>$form, 'model'=>$model, 'formId'=>$formId, 'itemId'=>$itemId)); ?>

<?php $this->widget('application.components.widgets.TabularInput.TabularInputWidget', array(
	'id'=>$formId.'-'.$itemId.'-blocCitationCitationForm',
	'form'=>$form,
	'layout'=>array('application.components.blocs.citation.views.admin_citationCitation'=>new BlocCitationCitation),
	'models'=>(isset($blocCitationCitation[$itemId]) ? $blocCitationCitation[$itemId] : array()),
	'itemTitleExpression'=>"CHtml::encode(\$model->name)",
	'orderAttribute'=>'rank',
)); ?>

<?php echo $form->hiddenField($model,'['.$formId.']['.$itemId.']rank'); ?>