<?php
$this->pageTitle=Yii::t('memberModule.common', 'Récupération de mot de passe').$this->titleSeparator.Yii::app()->name;
$this->breadcrumbs=array(
	Yii::t('memberModule.common', 'Récupération de mot de passe'),
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
			'id'=>'recover-form',
		)); ?>

			<fieldset>
						
				<div class="form-group">
					<?php echo $form->label($memberRecoverForm,'email', array('class'=>'control-label')); ?>
					<?php echo $form->textField($memberRecoverForm,'email', array('class'=>'form-control')); ?>
					<?php echo $form->error($memberRecoverForm,'email'); ?>
				</div>
			
				<?php echo CHtml::submitButton(Yii::t('memberModule.common', 'Envoyer'), array('class'=>'btn btn-primary')); ?>
				
			</fieldset>
		
		<?php $this->endWidget(); ?>

	</div>

</section>