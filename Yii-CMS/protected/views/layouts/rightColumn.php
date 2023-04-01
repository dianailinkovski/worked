<?php $this->beginContent($this->rightColumnLayoutParent); ?>

<div class="row has-sidebar">

	<div class="col-md-8">

		<?php echo $content; ?>
		
	</div>
	
	<div class="col-md-4">

		<?php $this->renderPartial($this->rightColumnLayoutType, $this->rightColumnLayoutTypeData); ?>
		
	</div>
	
</div>

<!-- /MAIN CONTENT -->

<?php $this->endContent(); ?>