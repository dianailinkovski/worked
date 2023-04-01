<div class="row">
	<?php echo $model->fileHandler->makeField($form, '['.$formId.']['.$itemId.']file'); ?>
	<?php echo $form->error($model, '['.$formId.']['.$itemId.']file'); ?>
</div>

<?php echo $form->hiddenField($model,'['.$formId.']['.$itemId.']rank'); ?>