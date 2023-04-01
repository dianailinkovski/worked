<?php
$this->pageTitle=Yii::app()->name . ' - Connexion';
$this->breadcrumbs=array(
	'Connexion',
);
?>

<a class="hiddenanchor" id="toregister"></a>
<a class="hiddenanchor" id="tologin"></a>

<div id="wrapper">

    <div id="login" class="animate form">

        <section class="login_content">

			<?php $form=$this->beginWidget('CActiveForm', array(
				'id'=>'login-form',
				'enableClientValidation'=>true,
				'clientOptions'=>array(
					'validateOnSubmit'=>true,
				),
			)); ?>

                <h1>Administration</h1>
                <div>
                	<?php echo $form->error($model,'username'); ?>
					<?php echo $form->textField($model,'username', array('placeholder'=>$model->attributeLabels()['username'], 'class'=>'form-control', 'required'=>'required')); ?>
					
                </div>
                <div>
                	<?php echo $form->error($model,'password'); ?>
					<?php echo $form->passwordField($model,'password', array('placeholder'=>$model->attributeLabels()['password'], 'class'=>'form-control', 'required'=>'required')); ?>
                </div>
                <div>
                	<?php echo $form->error($model,'rememberMe'); ?>
					<?php echo $form->checkBox($model,'rememberMe'); ?>
					<?php echo $form->label($model,'rememberMe'); ?>
                </div>
                <div class="login-buttons">
                    <?php echo CHtml::submitButton('Connexion', array('class'=>'btn btn-default submit')); ?>
                    <a class="reset_pass" href="#">Perdu votre mot de passe?</a>
                </div>
                <div class="clearfix"></div>

			<?php $this->endWidget(); ?>
            <!-- form -->
        </section>
        <!-- content -->
    </div>

</div>

<?php
/*
Yii::app()->clientScript->registerScript('googlePlusLogin', "
	(function() {
       var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
       po.src = 'https://apis.google.com/js/client:plusone.js';
       var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
     })();
			
	function signinCallback(authResult) {
		console.log(authResult);
	  if (authResult['access_token']) {
	    document.getElementById('signinButton').setAttribute('style', 'display: none');
	  } else {
	    console.log('Sign-in state: ' + authResult['error']);
	  }
	}
", CClientScript::POS_END);
*/
?>