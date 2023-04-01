<?php echo $this->renderPartial('//adminLayouts/_blocHeader', array('form'=>$form, 'model'=>$model, 'formId'=>$formId, 'itemId'=>$itemId)); ?>

<div class="form-group">
	<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']iframe', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
	<div class="col-sm-6 col-xs-12"><?php echo $form->textArea($model,'['.$formId.']['.$itemId.']iframe', array('class'=>'form-control', 'rows'=>'8')); ?></div>
	<?php echo $form->error($model,'['.$formId.']['.$itemId.']iframe', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
</div>

<?php echo $form->hiddenField($model,'['.$formId.']['.$itemId.']rank'); ?>