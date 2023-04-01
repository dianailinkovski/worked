<?php echo $this->renderPartial('//adminLayouts/_blocHeader', array('form'=>$form, 'model'=>$model, 'formId'=>$formId, 'itemId'=>$itemId)); ?>

<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

<div class="form-group">
	<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']image'.$suffix, array('class'=>'control-label col-sm-3 col-xs-12')); ?>
	<p class="hint">Pour un affichage optimal, l'image doit avoir une largeur d'un moins 1024 pixels (1 megapixel).</p>
	<div class="col-sm-6 col-xs-12"><?php echo $model->{'imageHandler'.$suffix}->makeField($form, '['.$formId.']['.$itemId.']image'.$suffix, array('class'=>'form-control'), array(), array('class'=>'img-responsive')); ?></div>
	<?php echo $form->error($model,'['.$formId.']['.$itemId.']image'.$suffix, array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
</div>

<?php endforeach; ?>

<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']image_title'.$suffix, array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']image_title'.$suffix, array('class'=>'form-control')); ?></div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']image_title'.$suffix, array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>

<?php endforeach; ?>

<?php echo $form->hiddenField($model,'['.$formId.']['.$itemId.']rank'); ?>