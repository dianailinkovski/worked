<?php
$this->breadcrumbs=array(
	Yii::t('admin', 'Settings')
);
?>

<h1><?php echo Yii::t('admin', 'Settings'); ?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'settings-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($flickrUsers); ?>
	
	<?php /*

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name'); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>
	
	*/ ?>
	
	<fieldset>
	
		<p class="note">id de l'utilisateur ex: "49229296@N07" (voir http://idgettr.com/ pour aller le chercher à partir du nom)</p>
    
	    <legend>Utilisateurs Flickr</legend>
	
		<?php $this->widget('application.components.widgets.TabularInput.TabularInputWidget', array(
			'id'=>'flickrUsersForm',
			'form'=>$form,
			'layout'=>array('application.views.settings._flickr'=>new FlickrUser),
			'models'=>$flickrUsers,
			'itemTitleExpression'=>"CHtml::encode(\$model->user_id)",
			'template'=>'noheader-noborder',
		)); ?>
		
	</fieldset>
	
	<?php echo CHtml::hiddenField('sent', '1'); ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton(Yii::t('admin', 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
