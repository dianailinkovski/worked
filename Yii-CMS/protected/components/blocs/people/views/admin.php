<?php echo $this->renderPartial('//adminLayouts/_blocHeader', array('form'=>$form, 'model'=>$model, 'formId'=>$formId, 'itemId'=>$itemId)); ?>

<div class="form-group">
	<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']columns', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
	<div class="col-sm-6 col-xs-12">Afficher sur <?php echo $form->dropDownList($model,'['.$formId.']['.$itemId.']columns', array('1'=>'1', '2'=>'2', '3'=>'3'), array('empty'=>'', 'class'=>'form-control')); ?> colonne(s)</div>
	<?php echo $form->error($model,'['.$formId.']['.$itemId.']columns', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
</div>

<fieldset>

	<legend><?php echo Yii::t('blocs', 'Personnel'); ?></legend>

	<?php $this->widget('application.components.widgets.TabularInput.TabularInputWidget', array(
		'id'=>$formId.'-'.$itemId.'-blocPeoplePeopleForm',
		'form'=>$form,
		'layout'=>array('application.components.blocs.people.views.admin_peoplePeople'=>new BlocPeoplePeople),
		'models'=>(isset($blocPeoplePeople[$itemId]) ? $blocPeoplePeople[$itemId] : array()),
		'itemTitleExpression'=>"CHtml::encode(\$model->name)",
		'orderAttribute'=>'rank'
	)); ?>
	
</fieldset>

<?php echo $form->hiddenField($model,'['.$formId.']['.$itemId.']rank'); ?>