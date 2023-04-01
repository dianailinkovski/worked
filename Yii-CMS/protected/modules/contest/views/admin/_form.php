<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'contest-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<?php echo $form->errorSummary($contest); ?>

	<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

		<div class="row">
			<?php echo $form->labelEx($contest,'title'.$suffix); ?>
			<?php echo $form->textField($contest,'title'.$suffix, array('style'=>'width: 70%;')); ?>
			<?php echo $form->error($contest,'title'.$suffix); ?>
		</div>
	
	<?php endforeach; ?>
	
	<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>
	
		<div class="row">
			<?php echo $form->labelEx($contest,'summary'.$suffix); ?>
			<?php echo $form->textArea($contest,'summary'.$suffix, array('style'=>'width: 70%; height: 100px;')); ?>
			<?php echo $form->error($contest,'summary'.$suffix); ?>
		</div>
	
	<?php endforeach; ?>
	
	<div class="row">
		<?php echo $form->labelEx($contest, 'image'); ?>
		<?php echo $contest->imageHandler->makeField($form); ?>
		<?php echo $form->error($contest, 'image'); ?>
		<p class="hint">L'image devrait avoir une largeur d'un moins 800px pour assurer une bonne qualité à l'écran.</p>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($contest,'max_participation'); ?>
		<?php echo $form->textField($contest,'max_participation', array('style'=>'width: 5%;')); ?>
		<?php echo $form->error($contest,'max_participation'); ?>
		<p class="hint">Champ vide = nbr. de participants illimité</p>
	</div>
	
	<div class="row">
		<?php echo $form->checkbox($contest,'multiple_entries'); ?>
		<?php echo $form->labelEx($contest,'multiple_entries'); ?>
		<?php echo $form->error($contest,'multiple_entries'); ?>
		<p class="hint">Cochez cette case pour permettre plus d'une participation au même concours/sondage à partir du même ordinateur ou appareil mobile.<br/>Décochez cette case pour limiter à 1 le nombre de participations à partir du même ordinateur ou appareil mobile (ceci ne sert qu'à DIMINUER le nombre de participations en double et non à les éliminer complètement. Les administrateurs devront filtrer manuellement les participations en double avant le tirage)</p>
	</div>
	
	<div class="row">
		<?php echo $form->checkbox($contest,'send_notification_email'); ?>
		<?php echo $form->labelEx($contest,'send_notification_email'); ?>
		<?php echo $form->error($contest,'send_notification_email'); ?>
		<p class="hint">Cochez cette case pour qu'un courriel soit envoyé automatiquement suite à une participation.<br/>Adresse de destination : <?PHP echo $this->module->notificationEmail; ?></p>
	</div>

	<div class="row">
		<?php echo $form->labelEx($contest,'start_date'); ?>
		<?php
		$this->widget('ext.jquery-ui-timepicker.BJuiDateTimePicker',array(
		    'model'=>$contest,
		    'attribute'=>'start_date',
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
		    	'style'=>'width: 20%;',
		    )
		));
		?>
		<?php echo $form->error($contest,'start_date'); ?>
		<p class="hint">Avant cette date, la page de détail n'affichera pas le formulaire de participation.</p>
	</div>

	<div class="row">
		<?php echo $form->labelEx($contest,'end_date'); ?>
		<?php
		$this->widget('ext.jquery-ui-timepicker.BJuiDateTimePicker',array(
		    'model'=>$contest,
		    'attribute'=>'end_date',
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
		    	'style'=>'width: 20%;',
		    )
		));
		?>
		<?php echo $form->error($contest,'end_date'); ?>
		<p class="hint">Lorsque la date de fin spécifiée sera passée, le formulaire de participation ne sera plus accessible et le contenu de l'onglet "Page conclusion" s'affichera.<br/>Laissez ce champ vide pour afficher le concours/sondage pour un temps illimité.<br/>(Le statut du concours/sondage doit être à "Actif" pour que la date de fin soit prise en compte.)</p>
	</div>

	<div class="row">
		<?php echo $form->labelEx($contest,'status'); ?>
		<?php echo $form->dropDownList($contest,'status', array('inactive'=>'Inactif', 'active'=>'Actif', 'archived'=>'Archivé'), array('empty'=>'')); ?>
		<?php echo $form->error($contest,'status'); ?>
		<p class="hint"><strong>Actif :</strong> le concours/sondage sera visible sur le site Web. Si la date de fin <strong>n'est pas passée</strong>, la page de détail affichera le contenu de l'onglet "Description et règlements" ainsi que le formulaire de participation. Dans le cas contraire, la page de détail affichera le contenu de l'onglet "Page conclusion".<br/><strong>Inactif :</strong> Le concours/sondage ne sera pas visible sur le site Web.<br/><strong>Archivé :</strong> Le concours/sondage sera listé dans la section Archives et la page de détail affichera le contenu de l'onglet "Page conclusion".</p>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($contest->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->