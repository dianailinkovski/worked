<?php echo $this->renderPartial('//adminLayouts/_blocHeader', array('form'=>$form, 'model'=>$model, 'formId'=>$formId, 'itemId'=>$itemId)); ?>

<div class="form-group">
	<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']layout', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
	<div class="col-sm-6 col-xs-12"><?php echo $form->dropDownList($model,'['.$formId.']['.$itemId.']layout', array('1'=>'Colonnes (adapté pour les textes courts)', '2'=>'Lignes (adapté pour les textes longs)'), array('empty'=>'', 'class'=>'form-control')); ?></div>
	<?php echo $form->error($model,'['.$formId.']['.$itemId.']layout', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
</div>

<fieldset>

	<legend>Caractéristiques</legend>

	<?php $this->widget('application.components.widgets.TabularInput.TabularInputWidget', array(
		'id'=>$formId.'-'.$itemId.'-blocFeatureFeatureForm',
		'form'=>$form,
		'layout'=>array('application.components.blocs.feature.views.admin_featureFeature'=>new BlocFeatureFeature),
		'models'=>(isset($blocFeatureFeature[$itemId]) ? $blocFeatureFeature[$itemId] : array()),
		'itemTitleExpression'=>"CHtml::encode(\$model->title)",
		'orderAttribute'=>'rank',
		'sortable'=>array(
			'start'=>"js:function(event, ui){
				".AdminHelper::tabularInputCkEditorSortableStart()."
			}",
			'stop'=>"js:function(event, ui){
				".AdminHelper::tabularInputCkEditorSortableStop()."
			}",
		),
	)); ?>
	
</fieldset>

<?php echo $form->hiddenField($model,'['.$formId.']['.$itemId.']rank'); ?>