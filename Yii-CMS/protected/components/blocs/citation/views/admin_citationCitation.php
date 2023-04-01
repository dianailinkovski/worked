<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']name'.$suffix, array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']name'.$suffix, array('class'=>'form-control')); ?></div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']name'.$suffix, array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>

<?php endforeach; ?>

<?php foreach (Yii::app()->languageManager->suffixes as $suffix): ?>

<div class="form-group">
	<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']citation'.$suffix, array('class'=>'control-label col-sm-3 col-xs-12')); ?>
	<div class="col-sm-6 col-xs-12"><?php echo $form->textArea($model,'['.$formId.']['.$itemId.']citation'.$suffix, array('class'=>'form-control', 'rows'=>'8')); ?></div>
	<?php echo $form->error($model,'['.$formId.']['.$itemId.']citation'.$suffix, array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
</div>

<?php endforeach; ?>

<?php echo $form->hiddenField($model,'['.$formId.']['.$itemId.']rank'); ?>