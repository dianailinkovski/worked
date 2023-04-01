<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'news-form',
	'htmlOptions'=>array('enctype'=>'multipart/form-data','class'=>'form-horizontal')
)); ?>

	<?php echo $form->errorSummary(array_merge(AdminHelper::blocsErrors($blocs), array($news))); ?>

	<?php $this->beginWidget('application.components.widgets.XPanel.XPanel', array('title'=>'Général')); ?>

		<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>
		
			<div class="form-group">
				<?php echo $form->labelEx($news,'title'.$suffix, array('class'=>'control-label col-sm-3 col-xs-12')); ?>
				<div class="col-sm-6 col-xs-12"><?php echo $form->textField($news,'title'.$suffix, array('class'=>'form-control')); ?></div>
				<?php echo $form->error($news,'title'.$suffix, array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
			</div>
		
		<?php endforeach; ?>

		 <div class="form-group">
		    <?php echo $form->labelEx($news,'date', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
		    <div class="col-sm-6 col-xs-12">
		        <div class="input-group date" id="datetimepicker-news">
		            <?php echo $form->textField($news,'date', array('class'=>'form-control')); ?>
		            <span class="input-group-addon">
		                <span class="glyphicon glyphicon-calendar"></span>
		            </span>
		        </div>
		    </div>
		    <?php echo $form->error($news,'date', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
		</div>
		<script type="text/javascript">
		$(function () {
		    $('#datetimepicker-news').datetimepicker({
		    	locale: '<?php echo Yii::app()->language; ?>',
		    	format: 'YYYY-MM-DD HH:mm:ss'
		    });
		});
		</script>

	<?php $this->endWidget(); ?>

	<?php $this->beginWidget('application.components.widgets.XPanel.XPanel', array('title'=>'Image')); ?>

		<div class="form-group">
			<?php echo $form->labelEx($news,'image', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
			<div class="col-sm-6 col-xs-12"><?php echo $news->imageHandler->makeField($form, '', array('class'=>'form-control'), array(), array('class'=>'img-responsive')); ?></div>
			<?php echo $form->error($news,'image', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
		</div>

		<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>
		
			<div class="form-group">
				<?php echo $form->labelEx($news,'image_label'.$suffix, array('class'=>'control-label col-sm-3 col-xs-12')); ?>
				<div class="col-sm-6 col-xs-12"><?php echo $form->textField($news,'image_label'.$suffix, array('class'=>'form-control')); ?></div>
				<?php echo $form->error($news,'image_label'.$suffix, array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
			</div>
		
		<?php endforeach; ?>

	<?php $this->endWidget(); ?>

	<?php $this->beginWidget('application.components.widgets.XPanel.XPanel', array('title'=>'Résumé de la nouvelle')); ?>
	
		<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>
		
			<div class="form-group">
				<?php echo $form->labelEx($news,'summary'.$suffix, array('class'=>'control-label col-sm-3 col-xs-12')); ?>
				<div class="col-sm-6 col-xs-12"><?php echo $form->textArea($news,'summary'.$suffix, array('class'=>'form-control', 'rows'=>'8')); ?></div>
				<?php echo $form->error($news,'summary'.$suffix, array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
			</div>
		
		<?php endforeach; ?>
			
	<?php $this->endWidget(); ?>

	<?php $this->beginWidget('application.components.widgets.XPanel.XPanel', array('title'=>'Source de la nouvelle')); ?>

		<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>
		
			<div class="form-group">
				<?php echo $form->labelEx($news,'source'.$suffix, array('class'=>'control-label col-sm-3 col-xs-12')); ?>
				<div class="col-sm-6 col-xs-12"><?php echo $form->textField($news,'source'.$suffix, array('class'=>'form-control')); ?></div>
				<?php echo $form->error($news,'source'.$suffix, array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
			</div>
		
		<?php endforeach; ?>

		<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>
		
			<div class="form-group">
				<?php echo $form->labelEx($news,'source_url'.$suffix, array('class'=>'control-label col-sm-3 col-xs-12')); ?>
				<div class="col-sm-6 col-xs-12"><?php echo $form->textField($news,'source_url'.$suffix, array('class'=>'form-control')); ?></div>
				<?php echo $form->error($news,'source_url'.$suffix, array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
			</div>
		
		<?php endforeach; ?>
		
	<?php $this->endWidget(); ?>

	<?php $this->beginWidget('application.components.widgets.XPanel.XPanel', array('title'=>'Contenu de la nouvelle')); ?>
	
		<?php $this->widget('application.components.widgets.AdminBlocsWidget',array(
			'id' => 'blocsForm',
			'models' => $blocs,
			'form' => $form,
		)); ?>

	<?php $this->endWidget(); ?>

	<div class="ln_solid"></div>

	<div class="form-group text-center">
		<div class="col-sm-6 col-xs-12 col-sm-offset-3">
			<a class="btn btn-primary" href="<?php echo $this->createUrl('admin'); ?>" role="button"><?php echo Yii::t('admin', 'Cancel'); ?></a>
			<?php echo CHtml::submitButton($news->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Save'), array('class'=>'btn btn-success')); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>