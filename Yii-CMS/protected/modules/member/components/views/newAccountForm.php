<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'newaccount-form-portlet',
	'htmlOptions'=>array(
		'role'=>'form'	
	),
)); ?>

	<div class="row">
	
		<div class="col-sm-6">
								
			<div class="form-group">
				<?php echo $form->textField($member,'first_name', array('class'=>'form-control', 'placeholder'=>Yii::t('memberModule.common', 'Prénom'))); ?>
				<?php echo $form->error($member,'first_name'); ?>
			</div>								
			
		</div>
		
		<div class="col-sm-6">
		
			<div class="form-group">
				<?php echo $form->textField($member,'last_name', array('class'=>'form-control', 'placeholder'=>Yii::t('memberModule.common', 'Nom'))); ?>
				<?php echo $form->error($member,'last_name'); ?>
			</div>
			
		</div>
		
	</div>
	
	<div class="form-group">
		<?php echo $form->emailField($member,'email', array('class'=>'form-control', 'placeholder'=>Yii::t('memberModule.common', 'Adresse courriel'))); ?>
		<?php echo $form->error($member,'email'); ?>
	</div>
	
	<div class="form-group">
		<?php echo $form->passwordField($member,'password', array('class'=>'form-control', 'placeholder'=>Yii::t('memberModule.common', 'Password'))); ?>
		<?php echo $form->error($member,'password'); ?>
	</div>
	
	<div class="form-group">
		<?php echo $form->passwordField($member,'confirm_password', array('class'=>'form-control', 'placeholder'=>Yii::t('memberModule.common', 'Confirmation mot de passe'))); ?>
		<?php echo $form->error($member,'confirm_password'); ?>
	</div>

	<?php echo CHtml::submitButton(Yii::t('memberModule.common', 'Créer mon compte'), array('class'=>'btn btn-success btn-lg')); ?>

<?php $this->endWidget(); ?>