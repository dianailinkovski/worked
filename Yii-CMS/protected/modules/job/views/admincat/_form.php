<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'jobcategory-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
	
	<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'.$suffix); ?>
		<?php echo $form->textField($model,'name'.$suffix, array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'name'.$suffix); ?>
	</div>
	
	<?php endforeach; ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
