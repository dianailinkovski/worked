<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']title'.$suffix, array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']title'.$suffix, array('class'=>'form-control')); ?></div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']title'.$suffix, array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>

<?php endforeach; ?>

<?php foreach (Yii::app()->languageManager->suffixes as $suffix): ?>

	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']description'.$suffix, array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12">
			<?php $this->widget('application.components.widgets.ckeditor.CKEditorWidget',array(
			    'model'=>$model,
			    'attribute'=>'['.$formId.']['.$itemId.']description'.$suffix,
				'textareaAttributes'=>array('class'=>'ckEditor from-control'),
				'config'=>$this->ckEditorConfigSimple,
			)); ?>
		</div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']description'.$suffix, array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>

<?php endforeach; ?>

<div class="form-group">
	<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']image', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
	<div class="col-sm-6 col-xs-12"><?php echo $model->imageHandler->makeField($form, '['.$formId.']['.$itemId.']image', array('class'=>'form-control'), array(), array('class'=>'img-responsive')); ?></div>
	<?php echo $form->error($model,'['.$formId.']['.$itemId.']image', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
</div>

<?php echo $form->hiddenField($model,'['.$formId.']['.$itemId.']rank'); ?>