<?php $this->beginContent('//layouts/main'); ?>

<div class="container-fluid">
	<!-- breadcrumbs -->
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
								'tagName'=>'ol',
								'homeLink'=>'<li><a href="'.Yii::app()->homeUrl.'">'.Yii::t('zii','Home').'</a></li>',
								'activeLinkTemplate'=>'<li><a href="{url}">{label}</a></li>',
								'inactiveLinkTemplate'=>'<li class="active">{label}</li>',
								'links'=>$this->breadcrumbs,
								'separator'=>'',
								'htmlOptions'=>array('class'=>'breadcrumb')
		)); ?>
	<?php endif ?>
</div>


<main id="content" role="main">

	<div class="container-fluid">
	
		<?php echo $content; ?>
	
	</div>
	
</main>
			
<?php $this->endContent(); ?>