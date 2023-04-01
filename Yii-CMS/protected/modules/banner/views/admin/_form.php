<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'banner-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<?php echo $form->errorSummary($banner); ?>

	<div class="row">
		<?php echo $form->labelEx($banner,'active'); ?>
		<?php echo $form->checkbox($banner,'active'); ?>
		<?php echo $form->error($banner,'active'); ?>
	</div>
	
	<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

		<div class="row">
			<?php echo $form->labelEx($banner,'text'.$suffix); ?>
			<?php $this->widget('application.widgets.ckeditor.CKEditorWidget',array(
			    'model'=>$banner,
			    'attribute'=>'text'.$suffix,
			    'config'=>$this->ckEditorConfigSimple,
				'textareaAttributes'=>array('class'=>'ckEditor'),
			)); ?>
			<?php echo $form->error($banner,'text'.$suffix); ?>
		</div>
	
	<?php endforeach; ?>

	<div class="row">
		<?php echo $form->labelEx($banner,'color'); ?>
		<?php
		$this->widget('ext.SMiniColors.SActiveColorPicker', array(
		    'model' => $banner,
		    'attribute' => 'color',
		    'hidden'=>false, // defaults to false - can be set to hide the textarea with the hex
		    'options' => array(), // jQuery plugin options
		    'htmlOptions' => array(), // html attributes
		));
		?>
		<?php echo $form->error($banner,'color'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($banner,'location'); ?>
		<?php echo $form->dropDownList($banner,'location', array('top'=>Yii::t('bannerModule.admin', 'Haut de page'), 'bottom'=>Yii::t('bannerModule.admin', 'Bas de page')), array('empty'=>'')); ?>
		<?php echo $form->error($banner,'location'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($banner,'presence'); ?>
		<?php echo $form->dropDownList($banner,'presence', array('all'=>Yii::t('bannerModule.admin', 'Afficher sur toutes les pages'), 'home'=>Yii::t('bannerModule.admin', 'Afficher sur la page d’accueil seulement')), array('empty'=>'')); ?>
		<?php echo $form->error($banner,'presence'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($banner->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->