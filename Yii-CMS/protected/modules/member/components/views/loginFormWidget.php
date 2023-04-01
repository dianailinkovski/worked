<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form-widget',
	'htmlOptions'=>array(
		'role'=>'form'	
	),
)); ?>
	
	<div class="form-group">
		<?php echo $form->emailField($memberLoginForm,'email', array('class'=>'form-control', 'placeholder'=>Yii::t('memberModule.common', 'Adresse courriel'))); ?>
		<?php echo $form->error($memberLoginForm,'email'); ?>
	</div>

	<div class="form-group">
		<?php echo $form->passwordField($memberLoginForm,'password', array('class'=>'form-control', 'placeholder'=>Yii::t('memberModule.common', 'Mot de passe'))); ?>
		<?php echo $form->error($memberLoginForm,'password'); ?>
	</div>

	<div class="checkbox">
		<?php echo $form->checkBox($memberLoginForm,'rememberMe'); ?>
		<?php echo $form->label($memberLoginForm,'rememberMe'); ?>
		<?php echo $form->error($memberLoginForm,'rememberMe'); ?>
	</div>

	<?php echo CHtml::submitButton(Yii::t('memberModule.common', 'Connexion'), array('class'=>'btn btn-success btn-lg')); ?>
	<?php echo CHtml::link(Yii::t('memberModule.common', 'J’ai perdu mon mot de passe'), array('/member/default/recover'), array('class'=>'btn btn-link')); ?>

<?php $this->endWidget(); ?>