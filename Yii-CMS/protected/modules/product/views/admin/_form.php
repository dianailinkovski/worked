<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'product-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<?php echo $form->errorSummary(array_merge($productImages, AdminHelper::blocsErrors($productTab1), AdminHelper::blocsErrors($productTab2), array($product))); ?>
	
	<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>

		<div class="row">
			<?php echo $form->labelEx($product,'name'.$suffix); ?>
			<?php echo $form->textField($product,'name'.$suffix, array('style'=>'width: 60%;')); ?>
			<?php echo $form->error($product,'name'.$suffix); ?>
		</div>
	
	<?php endforeach; ?>
	
	<div class="row">
		<?php echo $form->labelEx($product,'refnum'); ?>
		<?php echo $form->textField($product,'refnum', array('style'=>'width: 60%;')); ?>
		<?php echo $form->error($product,'refnum'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($product,'categories'); ?>
		<?php echo $form->listBox($product,'categories', CHtml::listData(Yii::app()->db->createCommand("SELECT CONCAT(REPEAT('-----', level-2), name) AS name, id FROM product_category ORDER BY lft LIMIT 18446744073709551615 OFFSET 1")->query(),'id', 'name'), array("multiple"=>"multiple", 'style'=>'height: 300px;')); ?>
		<?php echo $form->error($product,'categories'); ?>
	</div>
	<p class="note">Utilisez la touche "CTRL" pour sélection multiple.</p>
	
	<div class="row">
		<?php echo $form->labelEx($product,'tags'); ?>
		<?php echo $form->listBox($product,'tags', CHtml::listData(ProductTag::model()->findAll(), 'id', 'name'), array("multiple"=>"multiple", 'style'=>'height: 150px;')); ?>
		<?php echo $form->error($product,'tags'); ?>
	</div>
	<p class="note">Utilisez la touche "CTRL" pour sélection multiple.</p>

	<div class="row">
		<?php echo $form->labelEx($product,'price_regular'); ?>
		<?php echo $form->textField($product,'price_regular', array('style'=>'width: 60%;')); ?>
		<?php echo $form->error($product,'price_regular'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($product,'price_sale'); ?>
		<?php echo $form->textField($product,'price_sale', array('style'=>'width: 60%;')); ?>
		<?php echo $form->error($product,'price_sale'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($product,'sale_start'); ?>
		<?php
		$this->widget('ext.jquery-ui-timepicker.BJuiDateTimePicker',array(
		    'model'=>$product,
		    'attribute'=>'sale_start',
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
		    	'style'=>'width: 60%;',
		    )
		));
		?>
		<?php echo $form->error($product,'sale_start'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($product,'sale_end'); ?>
		<?php
		$this->widget('ext.jquery-ui-timepicker.BJuiDateTimePicker',array(
		    'model'=>$product,
		    'attribute'=>'sale_end',
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
		    	'style'=>'width: 60%;',
		    )
		));
		?>
		<?php echo $form->error($product,'sale_end'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($product,'weight'); ?>
		<?php echo $form->textField($product,'weight', array('style'=>'width: 30%;')); ?>
		<?php echo $form->error($product,'weight'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($product,'width'); ?>
		<?php echo $form->textField($product,'width', array('style'=>'width: 30%;')); ?>
		<?php echo $form->error($product,'width'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($product,'height'); ?>
		<?php echo $form->textField($product,'height', array('style'=>'width: 30%;')); ?>
		<?php echo $form->error($product,'height'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($product,'length'); ?>
		<?php echo $form->textField($product,'length', array('style'=>'width: 30%;')); ?>
		<?php echo $form->error($product,'length'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($product,'taxes'); ?>
		<?php echo $form->checkbox($product,'taxes'); ?>
		<?php echo $form->error($product,'taxes'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($product,'out_of_stock'); ?>
		<?php echo $form->checkbox($product,'out_of_stock'); ?>
		<?php echo $form->error($product,'out_of_stock'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($product,'in_store_only'); ?>
		<?php echo $form->checkbox($product,'in_store_only'); ?>
		<?php echo $form->error($product,'in_store_only'); ?>
	</div>
	
	<fieldset class="separator">
	    
	    <legend>Images</legend>

		<?php 
		$this->widget('application.components.widgets.TabularInput.TabularInputWidget', array(
			'id'=>'imagesForm',
			'form'=>$form,
			'template'=>'noheader-noborder',
			'models'=>$productImages,
			'layout'=>array('_image'=>new ProductImage),
			'orderAttribute'=>'rank',
		)); ?>

	</fieldset>
	
	<fieldset class="separator">
	    
	    <legend>Résumé du produit</legend>
	
		<?php foreach (Yii::app()->languageManager->suffixes as $suffix) : ?>
		
			<div class="row">
				<?php echo $form->labelEx($product,'summary'.$suffix); ?>
				<?php echo $form->textArea($product,'summary'.$suffix, array('style'=>'width: 60%; height: 200px;')); ?>
				<?php echo $form->error($product,'summary'.$suffix); ?>
			</div>
		
		<?php endforeach; ?>
		
	</fieldset>
	
	<fieldset class="separator">
	    
	    <legend>Détails du produit</legend>
	    
		<p class="note">Sélectionnez un type de bloc de contenu puis cliquez le bouton <strong>+</strong></p>
		
		<?php $this->widget('application.components.widgets.AdminBlocsWidget',array(
			'id' => 'productTab1Form',
			'models' => $productTab1,
			'form' => $form,
		)); ?>

	</fieldset>
	
	<fieldset class="separator">
	    
	    <legend>Informations nutritionnelles</legend>
	    
		<p class="note">Sélectionnez un type de bloc de contenu puis cliquez le bouton <strong>+</strong></p>
		
		<?php $this->widget('application.components.widgets.AdminBlocsWidget',array(
			'id' => 'productTab2Form',
			'models' => $productTab2,
			'form' => $form,
		)); ?>

	</fieldset>

	<div class="row buttons">
		<?php echo CHtml::submitButton($product->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
