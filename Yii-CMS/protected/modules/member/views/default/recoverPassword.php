<?php
$this->pageTitle=Yii::t('memberModule.common', 'Récupération de mot de passe').$this->titleSeparator.Yii::app()->name;
$this->breadcrumbs=array(
	Yii::t('memberModule.common', 'Récupération de mot de passe') => array('/member/default/recover'),
	Yii::t('memberModule.common', 'Changement de mot de passe'),
);
?>

<header class="row">

	<div class="col-sm-6">
		<h1 class="mainTitle"><?PHP echo Yii::t('memberModule.common', 'Récupération de mot de passe'); ?></h1>
	</div>
	
</header>

<section class="row">

	<div class="col-sm-6">

		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'recover-password-form',
		)); ?>

			<fieldset>

				<div class="form-group">
					<?php echo $form->label($memberModel,'password', array('class'=>'control-label')); ?>
					<?php echo $form->passwordField($memberModel,'password', array('class'=>'form-control')); ?>
					<?php echo $form->error($memberModel,'password'); ?>
				</div>
			
				<div class="form-group">
					<?php echo $form->label($memberModel,'confirm_password', array('class'=>'control-label')); ?>
					<?php echo $form->passwordField($memberModel,'confirm_password', array('class'=>'form-control')); ?>
					<?php echo $form->error($memberModel,'confirm_password'); ?>
				</div>
			
				<?php echo CHtml::submitButton(Yii::t('memberModule.common', 'Envoyer'), array('class'=>'btn btn-primary')); ?>
				
			</fieldset>
		
		<?php $this->endWidget(); ?>

	</div>

</section>