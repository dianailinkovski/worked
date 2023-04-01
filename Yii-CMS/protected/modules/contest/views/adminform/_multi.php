<div class="row">
	<?php echo $form->labelEx($model,'['.$widgetId.']['.$id.']title'); ?>
	<?php echo $form->textArea($model,'['.$widgetId.']['.$id.']title', array('style'=>'width: 90%; height: 60px;')); ?>
	<?php echo $form->error($model,'['.$widgetId.']['.$id.']title'); ?>
</div>

<?php echo $form->hiddenField($model,'['.$widgetId.']['.$id.']rank'); ?>