<div class="row">
	<?php echo $form->labelEx($model,'['.$widgetId.']['.$id.']title'); ?>
	<?php echo $form->textArea($model,'['.$widgetId.']['.$id.']title', array('style'=>'width: 90%; height: 60px;')); ?>
	<?php echo $form->error($model,'['.$widgetId.']['.$id.']title'); ?>
</div>

<div class="row">
	<?php echo $form->labelEx($model,'['.$widgetId.']['.$id.']type'); ?>
	<?php echo $form->dropDownList($model,'['.$widgetId.']['.$id.']type', $this->typeArr, array('empty'=>'')); ?>
	<?php echo $form->error($model,'['.$widgetId.']['.$id.']type'); ?>
</div>

<div class="row required-checkbox">
	<?php echo $form->checkbox($model,'['.$widgetId.']['.$id.']required'); ?>
	<?php echo $form->labelEx($model,'['.$widgetId.']['.$id.']required'); ?>
	<?php echo $form->error($model,'['.$widgetId.']['.$id.']required'); ?>
</div>

<div class="row result-checkbox">
	<?php echo $form->checkbox($model,'['.$widgetId.']['.$id.']result'); ?>
	<?php echo $form->labelEx($model,'['.$widgetId.']['.$id.']result'); ?>
	<?php echo $form->error($model,'['.$widgetId.']['.$id.']result'); ?>
</div>

<?php $this->widget('application.widgets.TabularInput.TabularInputWidget', array(
	'id'=>$widgetId.'multiTabular'.$id,
	'form'=>$form,
	'models'=>isset($multiTabular[$id]) ? $multiTabular[$id] : array(),
	'layout'=>array('_multi'=>new ContestFieldMulti),
	'orderAttribute'=>'rank',
	'itemTitleExpression'=>"mb_strlen(\$model->title) > 80 ? mb_substr(CHtml::encode(\$model->title), 0, 80).'&hellip;' : CHtml::encode(\$model->title)",
)); ?>

<?php echo $form->hiddenField($model,'['.$widgetId.']['.$id.']rank'); ?>