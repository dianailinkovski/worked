<?php
if ($categories):	?>
	<div class="row row-centered">
	<?php 
	foreach ($categories as $category) :
	?>
				
		<div class="categorie-produit col-sm-6 col-md-4 col-lg-3 col-centered">
			<a href="<?php echo $this->controller->createUrl('/product/default/listing', array('c'=>$category->name_url)); ?>"></a>
			<img src="<?php echo Yii::app()->request->baseUrl."/".$category->imageHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($category->image, 'm')); ?>" alt="<?php echo $category->name; ?>" class="img-responsive" />
			<span class="wrapper-nom-produit"><?php echo $category->name; ?></span>
		</div>
					
		<?php
	endforeach;	?>
	</div>
<?php 	
else : ?>

	<p><?php echo Yii::t('productModule.common', 'Aucun produit nest disponible.'); ?></p>

<?php endif; ?>