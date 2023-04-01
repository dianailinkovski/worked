<?php if (Yii::app()->languageManager->multilang): ?>
<div class="note">Si vous ne traduisez pas le fichier, envoyez-le seulement dans le 1<sup>er</sup> champ.</div>
<?php endif; ?>

<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

<div class="form-group">
	<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']file'.$suffix, array('class'=>'control-label col-sm-3 col-xs-12')); ?>
	<div class="col-sm-6 col-xs-12"><?php echo $model->{'fileHandler'.$suffix}->makeField($form, '['.$formId.']['.$itemId.']file'.$suffix, array('class'=>'form-control'), array(), array('class'=>'img-responsive')); ?></div>
	<?php echo $form->error($model,'['.$formId.']['.$itemId.']file'.$suffix, array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
</div>

<?php endforeach; ?>

<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']title'.$suffix, array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']title'.$suffix, array('class'=>'form-control')); ?></div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']title'.$suffix, array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>
    
<?php endforeach; ?>


<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

	<div class="form-group">
		<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']description'.$suffix, array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		<div class="col-sm-6 col-xs-12"><?php echo $form->textArea($model,'['.$formId.']['.$itemId.']description'.$suffix, array('class'=>'form-control', 'rows'=>'8')); ?></div>
		<?php echo $form->error($model,'['.$formId.']['.$itemId.']description'.$suffix, array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
	</div>

<?php endforeach; ?>

 <div class="form-group">
    <?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']datetime', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
    <div class="col-sm-6 col-xs-12">
        <div class="input-group date" id="datetimepicker-model">
            <?php echo $form->textField($model,'['.$formId.']['.$itemId.']datetime', array('class'=>'form-control')); ?>
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>
    </div>
    <?php echo $form->error($model,'['.$formId.']['.$itemId.']datetime', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
</div>
<script type="text/javascript">
$(function () {
    $('#datetimepicker-model').datetimepicker({
    	locale: '<?php echo Yii::app()->language; ?>',
    	format: 'YYYY-MM-DD HH:mm:ss'
    });
});
</script>

<?php echo $form->hiddenField($model,'['.$formId.']['.$itemId.']rank'); ?>
