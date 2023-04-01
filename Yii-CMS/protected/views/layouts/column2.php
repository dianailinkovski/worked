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
	<!-- /breadcrumbs -->
</div>

<main id="content" role="main">

	<div class="container-fluid">
	
		<div class="row">
		
			<div id="column1" class="col-md-8 col-md-push-4">
		
				<?php echo $content; ?>
				
			</div>
			
			<div id="column2" class="col-md-4 col-md-pull-8">
				
				<p class="visible-sm-block">Menu de section</p>
				<!-- CONTEXT MENU -->	
				<?php $this->renderPartial($this->sidebarViewFile, $this->sidebarData); ?>
				<!-- /CONTEXT MENU -->
				
			</div>
			
		</div>
		
	</div>
	
</main>
<!-- /MAIN CONTENT -->

<?php $this->endContent(); ?>