<div class="row">

	<div class="col-sm-8">

		<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']title'.$suffix, array('class'=>'control-label col-sm-4 col-xs-12')); ?>
				<div class="col-sm-8 col-xs-12"><?php echo $form->textField($model,'['.$formId.']['.$itemId.']title'.$suffix, array('class'=>'form-control')); ?></div>
				<?php //echo $form->error($model,'['.$formId.']['.$itemId.']title'.$suffix, array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
			</div>

		<?php endforeach; ?>

	</div>

	<div class="col-sm-4">

		<div class="form-group">
			<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']title_anchor', array('class'=>'control-label col-sm-11 col-xs-12')); ?>
			<div class="col-sm-1 col-xs-12"><?php echo $form->checkBox($model,'['.$formId.']['.$itemId.']title_anchor'); ?></div>
			<?php //echo $form->error($model,'['.$formId.']['.$itemId.']title_anchor', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
		</div>

		<div class="form-group">
			<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']title_page', array('class'=>'control-label col-sm-11 col-xs-12')); ?>
			<div class="col-sm-1 col-xs-12"><?php echo $form->checkBox($model,'['.$formId.']['.$itemId.']title_page'); ?></div>
			<?php //echo $form->error($model,'['.$formId.']['.$itemId.']title_page', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
		</div>

	</div>

</div>

<div class="ln_solid"></div>