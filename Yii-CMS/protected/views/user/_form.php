<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form-horizontal')
)); ?>

	<?php $this->beginWidget('application.components.widgets.XPanel.XPanel'); ?>

	<?php echo $form->errorSummary($user); ?>

	<div class="form-group">
		<?php echo $form->labelEx($user,'username', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->textField($user,'username', array('class'=>'form-control')); ?></div>
		<?php echo $form->error($user,'username', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>

	<div class="form-group">
		<?php echo $form->labelEx($user,'password', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->passwordField($user,'password', array('class'=>'form-control')); ?></div>
		<?php echo $form->error($user,'password', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>

	<div class="form-group">
		<?php echo $form->labelEx($user,'confirm_password', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->passwordField($user,'confirm_password', array('class'=>'form-control')); ?></div>
		<?php echo $form->error($user,'confirm_password', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>

	<div class="form-group">
		<div class="col-sm-6 col-xs-12 col-sm-offset-3">
			<a class="btn btn-primary" href="<?php echo $this->createUrl('admin'); ?>" role="button"><?php echo Yii::t('admin', 'Cancel'); ?></a>
			<?php echo CHtml::submitButton($user->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Save'), array('class'=>'btn btn-success')); ?>
		</div>
	</div>

	<?php $this->endWidget(); ?>

<?php $this->endWidget(); ?>