<?php
$productTitle = CHtml::encode($product->name);

$this->breadcrumbs = Helper::breadcrumbsFromAlias(true);
$this->breadcrumbs[$category->name] = $this->createUrl('listing', array('c'=>$category->name_url));
$this->breadcrumbs[] = $product->name;
$this->pageTitle = Helper::titleFromBreadcrumbs();

Yii::app()->clientScript->registerMetaTag($product->summary, 'description');
// facebook OG Meta
Yii::app()->facebook->ogTags['og:site_name'] = Yii::app()->name;
Yii::app()->facebook->ogTags['og:title'] = $productTitle;
Yii::app()->facebook->ogTags['og:type'] = "article";

?>

<article class="mod-product mod-type-detail">

	<div class="row">
	
		<?PHP 
		$abstractContainerClass = "col-sm-6";
		if(isset($product->images[0])):
		?>
			
			<div class="col-sm-6">
					<div class="article-image">
					
						<?php 
						$this->beginWidget('ext.prettyPhoto.PrettyPhoto', array(
								'id'=>'pretty_photo',
								'options'=>Yii::app()->params['prettyPhotoOptions'],
						));
						
						$i = 0;
						foreach ($product->images as $productImage):
							if($i == 0)
							{ 
								Yii::app()->facebook->ogTags['og:image'] = ($productImage != "") ? "http://".Yii::app()->request->baseUrl."/".$productImage->fileHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($productImage->file, 'm')) : "http://".Yii::app()->request->serverName.Yii::app()->request->baseUrl."/images/admin/admin_logo.jpg";
							}
						?>
	
						<a href="<?php echo Yii::app()->request->baseUrl."/".$productImage->fileHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($productImage->file, 'm')); ?>" title="<?PHP echo $productTitle; ?>"<?php echo $i == 0 ? '' : ' class="hidden-photo"' ; ?>>
							<img src="<?php echo Yii::app()->request->baseUrl."/".$productImage->fileHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($productImage->file, 'm')); ?>" alt="<?PHP echo $productTitle; ?>" title="<?php echo Yii::t('productModule.common', 'Cliquez pour agrandir limage'); ?>" class="img-responsive" />
						</a>
						
						<?php
						$i ++;
						endforeach;
						$this->endWidget('ext.prettyPhoto.PrettyPhoto');
						?>
					</div>
				</div>
		
				<?PHP
				$abstractContainerClass = "col-sm-6";
		endif;
		?>
		
		<div class="<?PHP echo $abstractContainerClass; ?>">
			
			<header>
				<h1 class="page-title"><?PHP echo $productTitle; ?></h1>
			</header>
			
			<div class="tag">
			<?php foreach ($product->tags as $tag): ?>
				<span style="background-color: #<?php echo CHtml::encode($tag->color); ?>"><?php echo CHtml::encode($tag->name); ?></span>
			<?php endforeach; ?>
			</div>
			
			<p>
			<?php if(($price = Shop::getProductPrice($product)) != $product->price_regular): ?>
				<span class="price regular-price"><?php echo CHtml::encode($product->price_regular); ?>$</span>
				<span class="price sale-price"><?php echo CHtml::encode($price); ?>$</span>
			<?php else: ?>
				<span class="price"><?php echo CHtml::encode($product->price_regular); ?>$</span>
			<?php endif; ?>
			</p>
			
			<form class="form-inline" method="GET" action="<?php echo $this->createUrl('/shop/default/index'); ?>">
				<div class="form-group">
					<label for="quantite"><?php echo Yii::t('productModule.common', 'Quantité'); ?></label>
					<input id="quantite" min="1" class="form-control" type="number" value="1" name="add[<?php echo $product->id; ?>]" />
				</div>
				<input type="submit" class="btn btn-success btn-lg" value="<?php echo Yii::t('productModule.common', 'Ajouter au panier'); ?>" />
			</form>
			
			<p class="summary"><?php echo CHtml::encode($product->summary); ?></p>
			
			<!-- Social networks -->
			<ul id="social-network-share-buttons" class="clearfix">
			
				<li id="facebook-share-button">
					<div class="fb-share-button" data-href="http://<?PHP echo Yii::app()->request->serverName.'/'.Yii::app()->request->baseUrl.Yii::app()->request->pathInfo; ?>" data-layout="button_count"></div>
				</li>
				
				<li>
					<script src="https://apis.google.com/js/platform.js" async defer>{lang: 'fr'}</script>
					<div class="g-plus" data-action="share" data-annotation="bubble"></div>
				</li>
				
				<li>
					<a class="twitter-share-button" href="https://twitter.com/share" data-url="http://<?PHP echo Yii::app()->request->serverName.'/'.Yii::app()->request->baseUrl.Yii::app()->request->pathInfo; ?>" data-text="<?PHP echo $productTitle; ?>"  data-count="horizontal">Tweet</a>
					<script type="text/javascript">
						window.twttr=(function(d,s,id){var t,js,fjs=d.getElementsByTagName(s)[0];if(d.getElementById(id)){return}js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);return window.twttr||(t={_e:[],ready:function(f){t._e.push(f)}})}(document,"script","twitter-wjs"));
					</script>
				</li>
				
			</ul>
			<!-- /Social networks -->
			
			<div id="stock-share">
				<div class="stock"><strong><?php echo Yii::t('productModule.common', 'DISPONIBILITÉ'); ?> :</strong> <?php echo $product->out_of_stock == 0 ? Yii::t('productModule.common', 'En stock') : Yii::t('productModule.common', 'Stock épuisé'); ?></div>
			</div>
			
		</div>
		
	</div>
	
	<?php if(!empty($product->blocs) || !empty($product->blocs2)): ?>
	<div class="tabs">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<?php if(!empty($product->blocs)): ?>
			<li class="active"><a href="#tab-1" role="tab" data-toggle="tab"><?php echo Yii::t('productModule.common', 'Détails du produit'); ?></a></li>
			<?php endif; ?>
			<?php if(!empty($product->blocs2)): ?>
			<li<?php echo empty($product->blocs) ? ' class="active"' : '' ;?>><a href="#tab-2" role="tab" data-toggle="tab"><?php echo Yii::t('productModule.common', 'Informations nutritionnelles'); ?></a></li>
			<?php endif; ?>
		</ul>
		
		<!-- Tab panes -->
		<div class="tab-content">
			
			<?php if(!empty($product->blocs)): ?>
			<div class="tab-pane active" id="tab-1">
				<?PHP
				// Blocs
				$this->widget('application.components.widgets.Blocs.BlocsWidget', array(
					'parentId'=>$product->id,
					'uniqueId'=>'product_tab_1',
				));
				?>
			</div>
			<?php endif; ?>
			
			<?php if(!empty($product->blocs2)): ?>
			<div class="tab-pane<?php echo empty($product->blocs) ? ' active' : '' ;?>" id="tab-2">
				<?PHP
				// Blocs
				$this->widget('application.components.widgets.Blocs.BlocsWidget', array(
					'parentId'=>$product->id,
					'uniqueId'=>'product_tab_2',
				));
				?>
			</div>
			<?php endif; ?>
			
		</div>
		
	</div>
	<?php endif; ?>
		
</article>

<a class="back" href="<?PHP echo $this->createUrl('listing', array('c'=>$category->name_url)); ?>"><?php echo Yii::t('productModule.common', 'Retour aux produits'); ?></a>
