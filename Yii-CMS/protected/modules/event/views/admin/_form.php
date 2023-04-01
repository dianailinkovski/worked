<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'event-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<?php echo $form->errorSummary(array_merge(AdminHelper::blocsErrors($blocs), array($event))); ?>
	
	<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

		<div class="row">
			<?php echo $form->labelEx($event,'title'.$suffix); ?>
			<?php echo $form->textField($event,'title'.$suffix, array('style'=>'width: 70%;')); ?>
			<?php echo $form->error($event,'title'.$suffix); ?>
		</div>
	
	<?php endforeach; ?>

	<fieldset class="separator">
	    
	    <legend>Planification</legend>

		<div class="row">
			<?php echo $form->labelEx($event,'date_start'); ?>
			<?php
			$this->widget('ext.jquery-ui-timepicker.BJuiDateTimePicker',array(
				'model'=>$event,
				'attribute'=>'date_start',
				'type'=>'datetime', // available parameter is datetime or time
				'language'=>Yii::app()->language, // default to english
				'themeUrl'=>'',
				'options'=>array( 
					// put your js options here check http://trentrichardson.com/examples/timepicker/#slider_examples for more info
					'timeFormat'=>'HH:mm:ss',
					'dateFormat'=>'yy-mm-dd',
					'showSecond'=>false,
					'hourGrid'=>4,
					'minuteGrid'=>10,
				),
				'htmlOptions'=>array(
					'style'=>'width: 30%;',
				)
			));
			?>
			<?php echo $form->error($event,'date_start'); ?>
		</div>
		
		<div class="row">
			<?php echo $form->labelEx($event,'date_end'); ?>
			<?php
			$this->widget('ext.jquery-ui-timepicker.BJuiDateTimePicker',array(
				'model'=>$event,
				'attribute'=>'date_end',
				'type'=>'datetime', // available parameter is datetime or time
				'language'=>Yii::app()->language, // default to english
				'themeUrl'=>'',
				'options'=>array( 
					// put your js options here check http://trentrichardson.com/examples/timepicker/#slider_examples for more info
					'timeFormat'=>'HH:mm:ss',
					'dateFormat'=>'yy-mm-dd',
					'showSecond'=>false,
					'hourGrid'=>4,
					'minuteGrid'=>10,
				),
				'htmlOptions'=>array(
					'style'=>'width: 30%;',
				)
			));
			?>
			<p class="hint">L'événement s'affichera automatiquement dans la section Archives après cette date/heure.</p>
			<?php echo $form->error($event,'date_end'); ?>
		</div>
	
	</fieldset>

	
	<fieldset class="separator">
	    
	    <legend>Emplacement</legend>
		
		<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>
	
			<div class="row">
				<?php echo $form->labelEx($event,'location'.$suffix); ?>
				<?php echo $form->textField($event,'location'.$suffix, array('style'=>'width: 80%;')); ?>
				<?php echo $form->error($event,'location'.$suffix); ?>
			</div>
		
		<?php endforeach; ?>

		<div class="row">
			<?php echo $form->labelEx($event,'location_map'); ?>
			<?php echo $form->textArea($event,'location_map', array('style'=>'width: 80%; height: 100px;')); ?>
			<?php echo $form->error($event,'location_map'); ?>
		</div>
		
	</fieldset>


	<fieldset class="separator">
	    
	    <legend>Description sommaire de l'événement</legend>
	
		<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>
		
			<div class="row">
				<?php echo $form->labelEx($event,'summary'.$suffix); ?>
				<?php echo $form->textArea($event,'summary'.$suffix, array('style'=>'width: 80%; height: 100px;')); ?>
				<?php echo $form->error($event,'summary'.$suffix); ?>
			</div>
		
		<?php endforeach; ?>
		
	</fieldset>
	
	<fieldset class="separator">
	    
	    <legend>Détails de l'événement</legend>
		
		<?php $this->widget('application.components.widgets.AdminBlocsWidget',array(
			'id'=>'blocsForm',
			'models' => $blocs,
			'form' => $form,
		)); ?>

	</fieldset>
	
	
	<fieldset class="separator">
	    
	    <legend>Image reliée</legend>
		
		<div class="row">
			<?php echo $form->labelEx($event, 'image'); ?>
			<?php echo $event->imageHandler->makeField($form); ?>
			<?php echo $form->error($event, 'image'); ?>
		</div>
			
		<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>
	
			<div class="row">
				<?php echo $form->labelEx($event,'image_label'.$suffix); ?>
				<?php echo $form->textField($event,'image_label'.$suffix, array('style'=>'width: 70%;')); ?>
				<?php echo $form->error($event,'image_label'.$suffix); ?>
			</div>
		
		<?php endforeach; ?>
		
	</fieldset>	
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($event->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
