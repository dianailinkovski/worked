<?php
$this->pageTitle = Yii::t('memberModule.common', 'Mon compte').$this->titleSeparator.Yii::app()->name;
$this->breadcrumbs=array(
	Yii::t('memberModule.common', 'Mon compte')
);
?>

<header class="row">

	<div class="col-sm-6">
		<h1><?php echo Yii::t('memberModule.common', 'Mon compte'); ?></h1>
	</div>

</header>

<section class="row">

	<div class="col-sm-6">
	
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'member-form',
			'errorMessageCssClass'=>'alert alert-danger',
		)); ?>
			
			<p class="notice"><?php echo Yii::t('memberModule.common', 'Les champs avec {asterix} sont obligatoires.', array('{asterix}'=>'<span class="red">*</span>')); ?></p>
			
			<?php echo $form->errorSummary($model, null, null, array('class'=>'ui-state-error')); ?>
			
			<fieldset>
	
				<div class="form-group">
					<?php echo $form->labelEx($model,'email', array('class'=>'control-label')); ?>
					<?php echo $form->textField($model,'email', array('class'=>'form-control', 'required'=>'required')); ?>
					<?php echo $form->error($model,'email'); ?>
				</div>
			
				<div class="form-group">
					<?php echo $form->labelEx($model,'current_password', array('class'=>'control-label')); ?>
					<?php echo $form->passwordField($model,'current_password', array('class'=>'form-control')); ?>
					<?php echo $form->error($model,'current_password'); ?>
				</div>
			
				<div class="form-group">
					<?php echo $form->labelEx($model,'password', array('class'=>'control-label')); ?>
					<?php echo $form->passwordField($model,'password', array('class'=>'form-control')); ?>
					<?php echo $form->error($model,'password'); ?>
				</div>
				
				<div class="form-group">
					<?php echo $form->labelEx($model,'confirm_password', array('class'=>'control-label')); ?>
					<?php echo $form->passwordField($model,'confirm_password', array('class'=>'form-control')); ?>
					<?php echo $form->error($model,'confirm_password'); ?>
				</div>

				<div class="form-group">
					<?php echo $form->labelEx($model,'first_name', array('class'=>'control-label')); ?>
					<?php echo $form->textField($model,'first_name', array('class'=>'form-control', 'required'=>'required')); ?>
					<?php echo $form->error($model,'first_name'); ?>
				</div>

				<div class="form-group">
					<?php echo $form->labelEx($model,'last_name', array('class'=>'control-label')); ?>
					<?php echo $form->textField($model,'last_name', array('class'=>'form-control', 'required'=>'required')); ?>
					<?php echo $form->error($model,'last_name'); ?>
				</div>
			
				<?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('memberModule.common', 'Créer mon compte') : Yii::t('memberModule.common', 'Save'), array('class'=>'btn btn-success btn-lg')); ?>
		
			</fieldset>
			
		<?php $this->endWidget(); ?>
	
	</div>
	
</section>