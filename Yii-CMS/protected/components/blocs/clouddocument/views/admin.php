<?php echo $this->renderPartial('//adminLayouts/_blocHeader', array('form'=>$form, 'model'=>$model, 'formId'=>$formId, 'itemId'=>$itemId)); ?>

<a href="javascript:;" id="kloudlessChoosify-<?php echo $formId.'-'.$itemId; ?>" class="kloudlessChoosify">Choisir un dossier</a>

<div class="form-group">
	<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']path', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
	<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']path', array('class'=>'form-control')); ?></div>
	<?php echo $form->error($model,'['.$formId.']['.$itemId.']path', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
</div>

<?php echo $form->hiddenField($model,'['.$formId.']['.$itemId.']rank'); ?>