<?php echo $this->renderPartial('//adminLayouts/_blocHeader', array('form'=>$form, 'model'=>$model, 'formId'=>$formId, 'itemId'=>$itemId)); ?>

<fieldset class="separator">
	    
    <legend>Coordonnées</legend>

    <div class="form-group">
    	<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']name', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
    	<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']name', array('class'=>'form-control')); ?></div>
    	<?php echo $form->error($model,'['.$formId.']['.$itemId.']name', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
    </div>

    <div class="form-group">
    	<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']address', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
    	<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']address', array('class'=>'form-control')); ?></div>
    	<?php echo $form->error($model,'['.$formId.']['.$itemId.']address', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
    </div>

	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']city', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']city', array('class'=>'form-control')); ?></div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']city', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>

	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']province', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']province', array('class'=>'form-control')); ?></div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']province', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>
	
	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']postal_code', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']postal_code', array('class'=>'form-control')); ?></div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']postal_code', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>
	
	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']country', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']country', array('class'=>'form-control')); ?></div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']country', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>
	
	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']phone1', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']phone1', array('class'=>'form-control')); ?></div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']phone1', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>
	
	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']phone2', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']phone2', array('class'=>'form-control')); ?></div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']phone2', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>

	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']phone_toll_free', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']phone_toll_free', array('class'=>'form-control')); ?></div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']phone_toll_free', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>
	
	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']fax', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']fax', array('class'=>'form-control')); ?></div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']fax', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>
	
	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']email', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']email', array('class'=>'form-control')); ?></div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']email', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>
	
	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']google_maps', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']google_maps', array('class'=>'form-control')); ?></div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']google_maps', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>

</fieldset>


<fieldset class="separator">
	    
    <legend>Photo</legend>

	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']image', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $model->imageHandler->makeField($form, '['.$formId.']['.$itemId.']image', array('class'=>'form-control'), array(), array('class'=>'img-responsive')); ?></div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']image', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>
	
	<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>
	
		<div class="form-group">
			<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']image_title'.$suffix, array('class'=>'control-label col-sm-3 col-xs-12')); ?>
			<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']image_title'.$suffix, array('class'=>'form-control')); ?></div>
			<?php echo $form->error($model,'['.$formId.']['.$itemId.']image_title'.$suffix, array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
		</div>
	
	<?php endforeach; ?>
	
</fieldset>


<fieldset class="separator">
	    
    <legend>Autres informations</legend>

    <div class="form-group">
    	<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']display_contact_form', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
    	<div class="col-sm-6 col-xs-12"><?php echo $form->checkBox($model,'['.$formId.']['.$itemId.']display_contact_form', array('class'=>'form-control')); ?></div>
    	<?php echo $form->error($model,'['.$formId.']['.$itemId.']display_contact_form', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
    </div>
	
	<?php foreach (Yii::app()->languageManager->suffixes as $suffix): ?>

	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']comment'.$suffix, array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']comment'.$suffix, array('class'=>'form-control')); ?></div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']comment'.$suffix, array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>
		
	<?php endforeach; ?>
	
</fieldset>
	
<?php echo $form->hiddenField($model,'['.$formId.']['.$itemId.']rank'); ?>