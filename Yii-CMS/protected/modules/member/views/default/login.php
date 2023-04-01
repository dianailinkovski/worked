<?php
$this->pageTitle=Yii::t('memberModule.common', 'Accès partenaires').$this->titleSeparator.Yii::app()->name;
$this->breadcrumbs=array(
	Yii::t('memberModule.common', 'Accès partenaires'),
);
?>

<header class="row">

	<div class="col-sm-6">
		<h1 class="page_title"><?PHP echo Yii::t('memberModule.common', 'Accès partenaires'); ?></h1>
	</div>

</header>

<section class="row">

	<div class="col-sm-6">

		<p><?PHP echo Yii::t('memberModule.common', 'Veuillez remplir le formulaire suivant avec vos informations de connexion'); ?></p>
		
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'login-form',
		)); ?>

			<div class="form-group">
				<?php echo $form->label($memberLoginForm,'email', array('class'=>'control-label')); ?>
				<?php echo $form->textField($memberLoginForm,'email', array('class'=>'form-control')); ?>
				<?php echo $form->error($memberLoginForm,'email'); ?>
			</div>
		
			<div class="form-group">
				<?php echo $form->label($memberLoginForm,'password', array('class'=>'control-label')); ?>
				<?php echo $form->passwordField($memberLoginForm,'password', array('class'=>'form-control')); ?>
				<?php echo $form->error($memberLoginForm,'password'); ?>
			</div>
		
			<div class="form-group remember-me">
				<div class="input-group clearfix">
					<div class="pull-left"><?php echo $form->checkBox($memberLoginForm,'rememberMe', array('class'=>'form-control')); ?>&nbsp;</div>
					<div class="pull-right"><?php echo $form->label($memberLoginForm,'rememberMe', array('class'=>'control-label')); ?></div>
				</div>
				<?php echo $form->error($memberLoginForm,'rememberMe'); ?>
			</div>
			
			<div class="form-group">
				<?php echo CHtml::link(Yii::t('memberModule.common', 'J’ai perdu mon mot de passe'), '/member/default/recover'); ?>
			</div>
			
			<div class="form-group">
				<?php echo CHtml::submitButton(Yii::t('memberModule.common', 'Connexion'), array('class'=>'btn btn-success')); ?>
			</div>

		<?php $this->endWidget(); ?>

	</div>

</section>