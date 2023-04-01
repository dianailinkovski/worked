<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'content-page-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<?php echo $form->errorSummary(array_merge(AdminHelper::blocsErrors($blocs), array($contentPage))); ?>
	
	<?php foreach (Yii::app()->languageManager->suffixes as $suffix): ?>

	<div class="row">
		<?php echo $form->labelEx($contentPage,'title'.$suffix); ?>
		<?php echo $form->textField($contentPage,'title'.$suffix, array('style'=>'width: 70%;')); ?>
		<?php echo $form->error($contentPage,'title'.$suffix); ?>
	</div>
	
	<?php endforeach; ?>
	
	<fieldset class="separator">
	
		<legend>Paramètres de page</legend>
		
		<div class="row">
			<label>Emplacement de la page dans l'arborescence</label>
			
			<?php $this->widget('application.components.widgets.TreeLocationWidget',array(
					'id'=>'treeTable',
					'model'=>$contentPage->alias ? $contentPage->alias : new CmsAlias,
					'attribute'=>'location',
					'widgetAttribute'=>'locationWidget',
					'allowChildrenAttribute'=>'allow_children',
					'columnAttribute'=>'title',
					'pathAttribute'=>'alias',
					'inputHtmlOptions' => array('style'=>'width: 60%;')
			)); ?>
		</div>
		
		<?php if (Yii::app()->user->id == 'Admin-1' && isset($this->module->layouts)): // Temporary user condition before we set up admin types. ?>
		<div class="row">
			<?php echo $form->labelEx($contentPage,'layout'); ?>
			<?php echo $form->dropDownList($contentPage,'layout', $this->module->layouts, array('empty'=>'')); ?>
			<?php echo $form->error($contentPage,'layout'); ?>
		</div>
		<?php endif; ?>

	</fieldset>
		
	<fieldset class="separator">
	    
	    <legend><?php echo Yii::t('contentModule.admin', 'Content'); ?></legend>
		
		<?php 
		$this->widget('application.components.widgets.AdminBlocsWidget',array(
				'id' => 'blocsForm',
				'models' => $blocs,
				'form'=>$form,
		)); ?>

	</fieldset>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($contentPage->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->