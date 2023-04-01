<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'contact-form',
	'enableAjaxValidation'=>true,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-danger',
	'clientOptions'=>array(
		'validateOnSubmit'=>false,
		'validateOnChange'=>false,
	),
)); 
?>

	<p class="text-small"><span class="required">*</span> = <?php echo Yii::t('common', 'champs obligatoires'); ?></p>
	
	<fieldset>
	
		<div class="form-group">
			<?php echo $form->labelEx($formModel,'personName', array('class'=>'control-label')); ?>
			<?php echo $form->textField($formModel,'personName', array('class'=>'form-control', 'required'=>'required')); ?>
			<?php echo $form->error($formModel,'personName'); ?>
		</div>
		
		<?php if ($this->getEmailFromConnectedMember && isset($this->controller->memberModel->email)): ?>
		
		<div class="form-group">
			<label class="control-label"><?php echo Yii::t('common', 'Entrez votre adresse courriel'); ?></label>
			<p class="form-control-static"><?php echo CHtml::encode($this->controller->memberModel->email); ?></p>
		</div>

		<?php else: ?>
		
		<div class="form-group">
			<?php echo $form->labelEx($formModel,'email', array('class'=>'control-label')); ?>
			<?php echo $form->emailField($formModel,'email', array('class'=>'form-control', 'required'=>'required')); ?>
			<?php echo $form->error($formModel,'email'); ?>
		</div>
		
		<?php endif; ?>
		
		<div class="form-group">
			<?php echo $form->labelEx($formModel,'message', array('class'=>'control-label')); ?>
			<?php echo $form->textArea($formModel,'message', array('class'=>'form-control', 'required'=>'required', 'style'=>'height:100px;')); ?>
			<?php echo $form->error($formModel,'message'); ?>
		</div>
	
	</fieldset>
	
	<div class="submit-button-wrapper">
		<?php echo CHtml::ajaxSubmitButton(Yii::t('common', 'Envoyer'), null, array(
	    'dataType'=>'html',
	    'type'=>'post',
		'beforeSend'=>'function(){
			$("#contact-form").addClass("form-processing");
		}',
	    'success'=>'function(data) {
			var jsonData = $.parseJSON($("#contact-form-json", $("<div>" + data + "</div>")).html());
				
			$("#contact-form [id$=\"_em_\"]").text("");                                                    
		    $("#contact-form [id$=\"_em_\"]").hide();
			$("#contact-form *:input[type!=\"submit\"]").removeClass("error");
				
			if (jsonData.status == "success") {
				$("#contact-form *:input[type!=\"submit\"]").val("");
				$("#contact-form-confirmation").modal("show");
	        } else if (jsonData.status == "error") {
				alert("'.Yii::t('common', 'Une erreur est survenue lors de l’envoi de votre demande. Veuillez réessayer ou contactez-nous par téléphone.').'");
			} else {
			    $.each(jsonData, function(key, val) {
					$("#contact-form #"+key+"_em_").text(val);                                                    
					$("#contact-form #"+key+"_em_").show();
					$("#contact-form #"+key).addClass("error");
			    });
	        }  
			$("#contact-form").removeClass("form-processing");     
	    }'
	    ), array(
	        'class'=>'btn btn-success btn-processing', 
	        'title'=>'Envoyer',
	        'id'=>'contact-form-submit-button',
	    	'name'=>'contact-form-submit-button',
	    )); ?>
	</div>
	
	<div id="contact-form-confirmation" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Confirmation" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel"><?php echo Yii::t('common', 'Confirmation'); ?></h4>
				</div>
				<div class="modal-body">
					<p><?php echo Yii::t('common', 'Votre message a été envoyé avec succès.'); ?></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo Yii::t('common', 'Fermer'); ?></button>
				</div>
			</div>
		</div>
	</div>
	
<?php $this->endWidget(); ?>