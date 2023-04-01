<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'productcategory-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<?php echo $form->errorSummary($productCategory); ?>
	
	<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

		<div class="row">
			<?php echo $form->labelEx($productCategory,'name'.$suffix); ?>
			<?php echo $form->textField($productCategory,'name'.$suffix, array('style'=>'width: 60%;')); ?>
			<?php echo $form->error($productCategory,'name'.$suffix); ?>
		</div>
	
	<?php endforeach; ?>

	<div class="row">
		<?php echo $form->labelEx($productCategory,'location'); ?>
		<?php $this->widget('application.components.widgets.TreeLocationWidget',array(
				'id'=>'treeTable',
		        'model'=>$productCategory,
		        'attribute'=>'location',
				'widgetAttribute'=>'locationWidget',
				'columnAttribute'=>'name',
				'pathAttribute'=>'name_url',
				'inputHtmlOptions' => array('style'=>'width: 47%;')
		 )); ?>
		 <?php echo $form->error($productCategory,'location'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($productCategory, 'image'); ?>
		<?php echo $productCategory->imageHandler->makeField($form); ?>
		<?php echo $form->error($productCategory, 'image'); ?>
	</div>
	
	<fieldset class="separator">
	    
	    <legend>Description</legend>
	
		<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>
		
			<div class="row">
				<?php echo $form->labelEx($productCategory,'description'.$suffix); ?>
				<?php echo $form->textArea($productCategory,'description'.$suffix, array('style'=>'width: 60%; height: 200px;')); ?>
				<?php echo $form->error($productCategory,'description'.$suffix); ?>
			</div>
		
		<?php endforeach; ?>
		
	</fieldset>

	<div class="row buttons">
		<?php echo CHtml::submitButton($productCategory->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
