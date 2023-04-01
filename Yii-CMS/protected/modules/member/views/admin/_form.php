<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'member-form',
	'htmlOptions'=>array('class'=>'form-horizontal')
)); ?>

	<?php $this->beginWidget('application.components.widgets.XPanel.XPanel'); ?>

		<?php echo $form->errorSummary($member); ?>

		<div class="form-group">
			<?php echo $form->labelEx($member,'email', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
			<div class="col-sm-6 col-xs-12"><?php echo $form->textField($member,'email', array('class'=>'form-control')); ?></div>
			<?php echo $form->error($member,'email', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
		</div>

		<div class="form-group">
			<?php echo $form->labelEx($member,'password', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
			<div class="col-sm-6 col-xs-12"><?php echo $form->passwordField($member,'password', array('class'=>'form-control')); ?></div>
			<?php echo $form->error($member,'password', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
		</div>

		<div class="form-group">
			<?php echo $form->labelEx($member,'confirm_password', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
			<div class="col-sm-6 col-xs-12"><?php echo $form->passwordField($member,'confirm_password', array('class'=>'form-control')); ?></div>
			<?php echo $form->error($member,'confirm_password', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
		</div>

		<div class="form-group">
			<?php echo $form->labelEx($member,'first_name', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
			<div class="col-sm-6 col-xs-12"><?php echo $form->textField($member,'first_name', array('class'=>'form-control')); ?></div>
			<?php echo $form->error($member,'first_name', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
		</div>

		<div class="form-group">
			<?php echo $form->labelEx($member,'last_name', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
			<div class="col-sm-6 col-xs-12"><?php echo $form->textField($member,'last_name', array('class'=>'form-control')); ?></div>
			<?php echo $form->error($member,'last_name', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
		</div>

		<div class="form-group">
			<div class="col-sm-6 col-xs-12 col-sm-offset-3">
				<a class="btn btn-primary" href="<?php echo $this->createUrl('admin'); ?>" role="button"><?php echo Yii::t('admin', 'Cancel'); ?></a>
				<?php echo CHtml::submitButton($member->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Save'), array('class'=>'btn btn-success')); ?>
			</div>
		</div>

	<?php $this->endWidget(); ?>

<?php $this->endWidget(); ?>