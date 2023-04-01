<p class="note">Pour les pages de contenu, créer une route "content" (le alias est lié à la page dans le module). Pour les pages statiques CViewAction et les pages de contenu créez un lien avec route "/" et paramêtre GET "keyword" avec nom du mot clé du alias. Pour avoir un page sans contenu (parent seulement) créer un alias sans route. Vous pouvez aussi entrer plusieurs routes pour ensuite les différencier dans les règles de rewrite. Le lien créé par le mot clé va mener vers la 1ère route seulement.</p>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'cmsalias-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($cmsAlias); ?>
	
	<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

		<div class="row">
			<?php echo $form->labelEx($cmsAlias,'title'.$suffix); ?>
			<?php echo $form->textField($cmsAlias,'title'.$suffix); ?>
			<?php echo $form->error($cmsAlias,'title'.$suffix); ?>
		</div>
	
	<?php endforeach; ?>
	
	<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

		<div class="row">
			<?php echo $form->labelEx($cmsAlias,'alias'.$suffix); ?>
			<?php echo $form->textField($cmsAlias,'alias'.$suffix); ?>
			<?php echo $form->error($cmsAlias,'alias'.$suffix); ?>
		</div>
	
	<?php endforeach; ?>

	<div class="row">
		<?php echo $form->labelEx($cmsAlias,'keyword'); ?>
		<?php echo $form->textField($cmsAlias,'keyword'); ?>
		<?php echo $form->error($cmsAlias,'keyword'); ?>
	</div>
	<p class="note">Attention: ne pas changer après la création initiale du alias s'il y a des liens vers ce alias</p>

	<fieldset style="width: 45%">
		<legend><?php echo Yii::t('admin', 'Routes'); ?></legend>
		<?php $this->widget('application.components.widgets.TabularInput.TabularInputWidget', array(
			'id'=>'routesForm',
			'form'=>$form,
			'models'=>$cmsAliasRoutes,
			'layout'=>array('_route'=>new CmsAliasRoute),
			'template'=>'noheader-noborder',
		)); ?>
	</fieldset>

	<div class="row">
		<?php echo $form->labelEx($cmsAlias,'section_id'); ?>
		<?php echo $form->dropDownList($cmsAlias,'section_id', CHtml::listData(CmsSection::model()->findAll(), 'id', 'name'), array('empty'=>'')); ?>
		<?php echo $form->error($cmsAlias,'section_id'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($cmsAlias,'allow_children'); ?>
		<?php echo $form->checkbox($cmsAlias,'allow_children'); ?>
		<?php echo $form->error($cmsAlias,'allow_children'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($cmsAlias,'location'); ?>
		<?php $this->widget('application.components.widgets.TreeLocationWidget',array(
				'id'=>'treeTable',
		        'model'=>$cmsAlias,
		        'attribute'=>'location',
				'widgetAttribute'=>'locationWidget',
				'allowChildrenAttribute'=>'allow_children',
				'columnAttribute'=>'title',
				'pathAttribute'=>'alias',
				'inputHtmlOptions' => array('style'=>'width: 47%;')
		 )); ?>
		 <?php echo $form->error($cmsAlias,'location'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($cmsAlias->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>