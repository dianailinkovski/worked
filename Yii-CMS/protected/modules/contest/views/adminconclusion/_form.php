<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'contest-conclusion-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<?php echo $form->errorSummary(AdminHelper::blocsErrors($blocs)); ?>
	
	<?php $this->widget('application.widgets.AdminBlocsWidget',array(
		'models' => $blocs,
		'form' => $form,
	)); ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton(Yii::t('admin', 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->