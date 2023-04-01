<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'message-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($message); ?>

	<div class="row">
		<?php echo $form->labelEx($message,'datetime'); ?>
		<?php
		$this->widget('ext.jquery-ui-timepicker.BJuiDateTimePicker',array(
		    'model'=>$message,
		    'attribute'=>'datetime',
		    'type'=>'datetime',
		    'language'=>Yii::app()->language, 
		    'themeUrl'=>'',
		    'options'=>array( 
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
		<?php echo $form->error($message,'datetime'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($message,'message'); ?>
	
		<?php $this->widget('application.components.widgets.ckeditor.CKEditorWidget',array(
		    'model'=>$message,
		    'attribute'=>'message',
		    'textareaAttributes'=>array('class'=>'ckEditor'),
			'config'=>$this->ckEditorConfigSimple
		)); ?>
		
		<?php echo $form->error($message,'message'); ?>
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton($message->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
