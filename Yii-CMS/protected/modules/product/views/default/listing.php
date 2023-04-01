<?php
$this->breadcrumbs = Helper::breadcrumbsFromAlias(true);
if ($category)
	$this->breadcrumbs[] = $category->name;

$this->pageTitle = Helper::titleFromBreadcrumbs();

Yii::app()->clientScript->registerMetaTag(Yii::t('productModule.common', 'meta_description'), 'description');

$productCount = $productProvider->getItemCount();
?>

<article class="mod-product mod-type-listing">

	<header class="hidden-md hidden-lg">
		<h1 class="page-title"><?php echo $category ? CHtml::encode($category->name) : Yii::t('productModule.common', 'Promotions'); ?></h1>
	</header>

	<div id="classing-bar" class="row">
		<span class="number-items">(<?php echo Yii::t('productModule.common', '{n} Item|{n} Items', $productCount); ?>)</span>
		<span>
			<label><?php echo Yii::t('productModule.common', 'Trier par'); ?></label>
			<select onchange="javascript:window.location='<?php echo $this->createUrl('listing', array('c'=>$c, 'order'=>99)); ?>'.replace('99', this.options[this.selectedIndex].value);">
				<option value="0" <?php echo $order == 0 ? ' selected' : ''; ?>><?php echo Yii::t('productModule.common', 'Défaut'); ?></option>
				<option value="1" <?php echo $order == 1 ? ' selected' : ''; ?>><?php echo Yii::t('productModule.common', 'Prix croissant'); ?></option>
				<option value="2" <?php echo $order == 2 ? ' selected' : ''; ?>><?php echo Yii::t('productModule.common', 'Prix décroissant'); ?></option>
			</select>
		</span>
	</div>
	
	<?php if ($productCount == 0): ?>
	
	<p><?php echo Yii::t('productModule.common', 'Aucun produit nest disponible dans cette catégorie.'); ?></p>
		
	<?php
	else:
		$i=1;
		foreach ($productProvider->getData() as $product):
			$productTitle = CHtml::encode($product->name);
		?>
		
		<article class="entry">
		
			<div class="row">
			
				<?php 
				$abstractContainerClass = "col-sm-12";
				foreach ($product->images as $productImage): 
					if($productImage->rank == 1):	?>
				
					<div class="article-image col-sm-4">
						<a href="<?php echo $this->createUrl('detail', array('c'=>($category ? $category->name_url : $product->categories[0]->name_url), 'n'=>$product->name_url)); ?>">
							<img src="<?php echo Yii::app()->request->baseUrl."/".$productImage->fileHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($productImage->file, 'm')); ?>" alt="<?php echo $productTitle; ?>" class="img-responsive" />
						</a>
					</div>
				
					<?php
					endif;
					$abstractContainerClass = "col-sm-8";
				endforeach;
				?>
				
				<div class="article-abstract <?PHP echo $abstractContainerClass; ?>">
					<header>
						<h1><a href="<?php echo $this->createUrl('detail', array('c'=>($category ? $category->name_url : $product->categories[0]->name_url), 'n'=>$product->name_url)); ?>"><?php echo $productTitle; ?></a></h1>
					</header>

					<div class="tag">
					<?php foreach ($product->tags as $tag): ?>
						<span style="background-color: #<?php echo CHtml::encode($tag->color); ?>"><?php echo CHtml::encode($tag->name); ?></span>
					<?php endforeach; ?>
					</div>

					<p><?php echo CHtml::encode($product->summary); ?></p>
					
					<p>
					<?php if(($price = Shop::getProductPrice($product)) != $product->price_regular): ?>
						<span class="price regular-price"><?php echo Yii::t('productModule.common', '{n}$', CHtml::encode($product->price_regular)); ?></span>
						<span class="price sale-price"><?php echo Yii::t('productModule.common', '{n}$', CHtml::encode($price)); ?></span>
					<?php else: ?>
						<span class="price"><?php echo Yii::t('productModule.common', '{n}$', CHtml::encode($product->price_regular)); ?></span>
					<?php endif; ?>
					</p>
					
					<form class="form-inline" method="GET" action="<?php echo $this->createUrl('/shop/default/index'); ?>">
						<div class="form-group">
							<label for="quantite"><?php echo Yii::t('productModule.common', 'Quantité'); ?></label>
							<input id="quantite" min="1" class="form-control" type="number" value="1" name="add[<?php echo $product->id; ?>]" />
						</div>
						<input type="submit" class="btn btn-success" value="<?php echo Yii::t('productModule.common', 'Ajouter au panier'); ?>" />
						<a class="btn btn-default" href="<?php echo $this->createUrl('detail', array('c'=>($category ? $category->name_url : $product->categories[0]->name_url), 'n'=>$product->name_url)); ?>"><?php echo Yii::t('productModule.common', 'Détails'); ?></a>
					</form>
				</div>
				
			</div>
			
		</article>
		
			<?php
			$i++;
		endforeach;
	endif; ?>


	<?php if ($productProvider->pagination->pageCount > 1): ?>
	<footer>
		
		<ul class="pagination pagination-md">

			<?php
			$linkParams = array('index');
			$linkParams['c'] = $c;
			$linkParams['order'] = $order;

			if ($productProvider->pagination->currentPage > 0): 
					$linkParams['page'] = $productProvider->pagination->currentPage ?>

				<li><?php echo CHtml::link('&laquo;', $linkParams); ?></li>
			<?php
			endif; 
		
			$a = $productProvider->pagination->currentPage - 5;
			$b = $productProvider->pagination->currentPage + 5;
			$c = $productProvider->pagination->pageCount;
		
			for ($i = ($a < 1 ? 1 : $a+1); $i <= ($b > $c-1 ? $c : $b+1); $i++): 
				$linkParams['page'] = $i;
				
				if ($i == $productProvider->pagination->currentPage + 1): ?>
				
			<li class="active"><span><?php echo $i; ?></span></li>
			<?php else: ?>
			<li><?php echo CHtml::link($i, $linkParams); ?></li>
			<?php endif;
		
			endfor;

			if ($productProvider->pagination->currentPage < $productProvider->pagination->pageCount-1): 
					$linkParams['page'] = $productProvider->pagination->currentPage+2 ?>

				<li><?php echo CHtml::link('&raquo;', $linkParams); ?></li>
			<?php endif; ?>

		</ul>
	
	</footer>
	<?php endif; ?>
	
</article>
