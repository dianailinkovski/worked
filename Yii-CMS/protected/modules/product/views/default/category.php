<?php
$this->breadcrumbs = Helper::breadcrumbsFromAlias();
$this->pageTitle = Helper::titleFromBreadcrumbs();

Yii::app()->clientScript->registerMetaTag(Yii::t('productModule.common', 'meta_description'), 'description');
?>

<article id="section-produits" class="mod-product">

	<header>
		<h1 class="page-title"><?php echo CHtml::encode($this->pageTitle); ?></h1>
	</header>
	
	<p class="lead"><?php echo Yii::t('productModule.common', 'Paragraphe produits'); ?></p>
	
	<?php if (count($categories) == 0): ?>
	
	<p><?php echo Yii::t('productModule.common', 'Aucun produit nest disponible.'); ?></p>
		
	<?php
	else: ?>
	
	<div class="row">
		<?php 
		foreach ($categories as $category):
			$categoryTitle = CHtml::encode($category->name);
		?>
		
			<div class="categorie-produit col-sm-6 col-md-4 col-lg-3">
				<a href="<?php echo $this->createUrl('listing', array('c'=>$category->name_url)); ?>"></a>
				<img src="<?php echo Yii::app()->request->baseUrl."/".$category->imageHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($category->image, 'm')); ?>" alt="<?php echo $categoryTitle; ?>" class="img-responsive" />
				<span class="wrapper-nom-produit"><?php echo $categoryTitle; ?></span>
			</div>
			
			<?php
		endforeach;
		?>
	</div>
	<?php 
	endif; ?>

</article>