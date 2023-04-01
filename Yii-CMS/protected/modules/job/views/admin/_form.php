<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'job-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<?php echo $form->errorSummary(array_merge(AdminHelper::blocsErrors($blocs), array($model))); ?>
	
	<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

		<div class="row">
			<?php echo $form->labelEx($model,'title'.$suffix); ?>
			<?php echo $form->textField($model,'title'.$suffix,array('size'=>60,'maxlength'=>255)); ?>
			<?php echo $form->error($model,'title'.$suffix); ?>
		</div>
	
	<?php endforeach; ?>
	
	<div class="row">
		<?php echo $form->labelEx($model,'category_id'); ?>
		<?php echo $form->dropDownList($model,'category_id', CHtml::listData(JobCategory::model()->findAll(array('order'=>'name ASC')), 'id', 'name'), array('empty'=>'')); ?>
		<?php echo $form->error($model,'category_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->dropDownList($model,'type',array(1=>Yii::t('jobModule.admin', 'Permanent'), 2=>Yii::t('jobModule.admin', 'Temps partiel'), 3=>Yii::t('jobModule.admin', 'Saisonnier'), 4=>'Banque de candidatures'), array('empty'=>'')); ?>
		<?php echo $form->error($model,'type'); ?>
	</div>
	
	<div id="hide-content">
	
		<fieldset class="separator">
		    
		    <legend>Contenu de l'offre d'emploi</legend>
		    
			<p class="note">Sélectionnez un type de bloc de contenu puis cliquez le bouton <strong>+</strong></p>

			<?php $this->widget('application.components.widgets.AdminBlocsWidget',array(
				'id'=>'blocsForm',
				'models' => $blocs,
				'form' => $form,
			)); ?>
	
		</fieldset>
		
		<fieldset>
		
			<legend>Information sur la postulation</legend>
			
			<div class="row">
				<?php echo $form->labelEx($model,'publication_date'); ?>
				<?php
				$this->widget('zii.widgets.jui.CJuiDatePicker', array(
					'model' => $model,
					'attribute' => 'publication_date',
					'language'=>Yii::app()->language, // default to english
					'themeUrl'=>'',
					'options' => array(
						//'showOn' => 'both',             // also opens with a button
						'dateFormat' => 'yy-mm-dd',     // format of "2012-12-25"
						'showOtherMonths' => true,      // show dates in other months
						'selectOtherMonths' => true,    // can seelect dates in other months
						'changeYear' => true,           // can change year
						'changeMonth' => true,          // can change month
						'yearRange' => '2010:2099',     // range of year
						'minDate' => '2010-01-01',      // minimum date
						'maxDate' => '2099-12-31',      // maximum date
						'showButtonPanel' => true,      // show button panel
					),
					'htmlOptions' => array(
						'size' => '10',         // textField size
						'maxlength' => '10',    // textField maxlength
					),
				));
				?>
				<?php echo $form->error($model,'publication_date'); ?>
			</div>
		
			<div class="row">
				<?php echo $form->labelEx($model,'start_date'); ?>
				<?php
				$this->widget('zii.widgets.jui.CJuiDatePicker', array(
					'model' => $model,
					'attribute' => 'start_date',
					'language'=>Yii::app()->language, // default to english
					'themeUrl'=>'',
					'options' => array(
						//'showOn' => 'both',             // also opens with a button
						'dateFormat' => 'yy-mm-dd',     // format of "2012-12-25"
						'showOtherMonths' => true,      // show dates in other months
						'selectOtherMonths' => true,    // can seelect dates in other months
						'changeYear' => true,           // can change year
						'changeMonth' => true,          // can change month
						'yearRange' => '2010:2099',     // range of year
						'minDate' => '2010-01-01',      // minimum date
						'maxDate' => '2099-12-31',      // maximum date
						'showButtonPanel' => true,      // show button panel
					),
					'htmlOptions' => array(
						'size' => '10',         // textField size
						'maxlength' => '10',    // textField maxlength
					),
				));
				?>
				<?php echo $form->error($model,'start_date'); ?>
			</div>
	
			<div class="row">
				<?php echo $form->labelEx($model,'postulation_end_date'); ?>
				<?php
				$this->widget('ext.jquery-ui-timepicker.BJuiDateTimePicker',array(
				    'model'=>$model,
				    'attribute'=>'postulation_end_date',
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
				    	'size'=>19,
				    	'maxlength'=>19
				    )
				));
				?>
				<?php echo $form->error($model,'postulation_end_date'); ?>
			</div>
			
			<div class="row">
				<?php echo $form->labelEx($model,'nb_available'); ?>
				<?php echo $form->textField($model,'nb_available',array('size'=>10,'maxlength'=>5)); ?>
				<?php echo $form->error($model,'nb_available'); ?>
			</div>
	
		</fieldset>
	
	</div>
	
	<div class="row">
		<?php echo $form->checkBox($model,'active'); ?>
		<?php echo $form->labelEx($model,'active'); ?>
		
		<?php echo $form->error($model,'active'); ?>
	</div>
		
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
			
<script>
$(document).ready(function() {

	cacheContent();

	$('#Job_type').change(function (){
		cacheContent();
	});

	function cacheContent()
	{

		// Cache les champs qui ne concerne pas les Banques de candidatures
		if ($('#Job_type').val() == 4)
		{
			
			$('#hide-content').hide();

			// Met une valeur bidon si c'est vide
			if ($('#Job_publication_date').val() == '')
			{
				$('#Job_publication_date').val('2014-01-01');
			}

			// Met une valeur bidon si c'est vide
			if ($('#Job_postulation_end_date').val() == '')
			{
				$('#Job_postulation_end_date').val('2014-01-01 00:00:00');
			}
			
		}
		else
		{
			$('#hide-content').show();

			// Vide le champ pour que la personne met une vrai date
			if ($('#Job_publication_date').val() == '2014-01-01')
			{
				$('#Job_publication_date').val('');
			}

			// Vide le champ pour que la personne met une vrai date
			if ($('#Job_postulation_end_date').val() == '2014-01-01 00:00:00')
			{
				$('#Job_postulation_end_date').val('');
			}
		}
	};
});
</script>