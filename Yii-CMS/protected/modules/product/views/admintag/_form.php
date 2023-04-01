<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'tags-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($productTag); ?>
	
	<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

		<div class="row">
			<?php echo $form->labelEx($productTag,'name'.$suffix); ?>
			<?php echo $form->textField($productTag,'name'.$suffix, array('style'=>'width: 60%;')); ?>
			<?php echo $form->error($productTag,'name'.$suffix); ?>
		</div>
	
	<?php endforeach; ?>
	
	<div class="row">
		<?php echo $form->labelEx($productTag,'color'); ?>
		<?php $this->widget('application.extensions.colorpicker.JColorPicker', array(
		    'model' => $productTag,
		    'attribute' => 'color',
		)); ?>
		<?php echo $form->error($productTag,'color'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($productTag->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
