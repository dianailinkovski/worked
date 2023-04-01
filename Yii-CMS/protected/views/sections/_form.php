<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'cms-section-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
	
<?php foreach (Yii::app()->languageManager->languages as $l => $lang) :
    if($l === Yii::app()->sourceLanguage) $suffix = '';
    else $suffix = '_'.$l;
    ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'.$suffix); ?>
		<?php echo $form->textField($model,'name'.$suffix,array('size'=>50)); ?>
		<?php echo $form->error($model,'name'.$suffix); ?>
	</div>
	
	<?php endforeach; ?>
	
	<?php 
	$dropListModules = array();
	foreach (Yii::app()->cms->modules as $module)
	{
		$dropListModules[$module] = $module;	
	}
	?>

	<div class="row">
		<?php echo $form->labelEx($model,'module'); ?>
		<?php echo $form->DropDownList($model,'module',$dropListModules); ?>
		<?php echo $form->error($model,'module'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
