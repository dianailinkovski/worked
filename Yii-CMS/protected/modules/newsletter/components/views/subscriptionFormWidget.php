<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'newsletter-subscription-form',
	'enableAjaxValidation'=>true,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-danger',
	'clientOptions'=>array(
		'validateOnSubmit'=>false,
		'validateOnChange'=>false,
	),
)); 
?>

	<fieldset>
	
		<div class="form-group">
			<?php echo $form->labelEx($formModel,'email', array('class'=>'control-label')); ?>
			<?php echo $form->textField($formModel,'email', array('class'=>'form-control', 'required'=>'required')); ?>
			<?php echo $form->error($formModel,'email'); ?>
		</div>
	
	</fieldset>
	
	<div class="submit-button-wrapper">
		<?php echo CHtml::ajaxSubmitButton('Envoyer', null, array(
	    'dataType'=>'html',
	    'type'=>'post',
		'beforeSend'=>'function(){
			$("#newsletter-subscription-form").addClass("form-processing");
		}',
	    'success'=>'function(data) {
			var jsonData = $.parseJSON($("#newsletter-subscription-form-json", $("<div>" + data + "</div>")).html());
				
			$("#newsletter-subscription-form [id$=\"_em_\"]").text("");                                                    
		    $("#newsletter-subscription-form [id$=\"_em_\"]").hide();
			$("#newsletter-subscription-form *:input[type!=\"submit\"]").removeClass("error");
				
			if (jsonData.status == "success") {
				$("#newsletter-subscription-form *:input[type!=\"submit\"]").val("");
				$("#newsletter-subscription-form-confirmation").modal("show");
			} else {
			    $.each(jsonData, function(key, val) {
					$("#newsletter-subscription-form #"+key+"_em_").text(val);                                                    
					$("#newsletter-subscription-form #"+key+"_em_").show();
					$("#newsletter-subscription-form #"+key).addClass("error");
			    });
	        }  
			$("#newsletter-subscription-form").removeClass("form-processing");     
	    }'
	    ), array(
	        'class'=>'btn btn-success btn-processing', 
	        'title'=>'Envoyer',
	        'id'=>'newsletter-subscription-form-submit-button',
	    	'name'=>'newsletter-subscription-form-submit-button',
	    )); ?>
    </div>
	
	<div id="newsletter-subscription-form-confirmation" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Confirmation" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel"><?php echo Yii::t('common', 'Confirmation'); ?></h4>
				</div>
				<div class="modal-body">
					<p><?php echo Yii::t('newsletterModule.common', 'Votre adresse courriel a été ajoutée avec succès.'); ?></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo Yii::t('common', 'Fermer'); ?></button>
				</div>
			</div>
		</div>
	</div>
	
<?php $this->endWidget(); ?>