<?php echo $this->renderPartial('//adminLayouts/_blocHeader', array('form'=>$form, 'model'=>$model, 'formId'=>$formId, 'itemId'=>$itemId)); ?>

<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

<div class="ckeditor-group">

	<div><?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']html'.$suffix, array('class'=>'control-label col-xs-12')); ?></div>

	<div class="form-group">
		<?php $this->widget('application.components.widgets.ckeditor.CKEditorWidget',array(
		    'model'=>$model,
		    'attribute'=>'['.$formId.']['.$itemId.']html'.$suffix,
			'textareaAttributes'=>array('class'=>'ckEditor from-control'),
		)); ?>
	</div>

	<div><?php echo $form->error($model,'['.$formId.']['.$itemId.']html'.$suffix, array('class'=>'error text-left col-xs-12')); ?></div>

</div>

<?php endforeach; ?>

<?php echo $form->hiddenField($model,'['.$formId.']['.$itemId.']rank'); ?>