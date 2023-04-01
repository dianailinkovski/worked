<?php
$this->breadcrumbs=array(
	Yii::t('newsModule.common', 'Nouvelles'),
);
$this->breadcrumbs = Helper::breadcrumbsFromAlias();
$this->pageTitle = Helper::titleFromBreadcrumbs();

Yii::app()->clientScript->registerMetaTag(Yii::t('newsModule.common', 'meta_description'), 'description');

$newsCount = $newsProvider->getItemCount();
?>

<article class="mod-news mod-type-listing">

	<header>
		<h1 class="page-title"><?php echo CHtml::encode(Yii::app()->cms->currentAlias->title); ?></h1>
	</header>
	
	<?php if ($newsCount == 0): ?>
	
	<p><?php echo Yii::t('newsModule.common', 'Aucune nouvelle nest disponible.'); ?></p>
		
	<?php
	else:
		$i=1;
		foreach ($newsProvider->getData() as $news):
			$newsTitle = CHtml::encode($news->title);
		?>
		
		<article class="entry<?php echo ($news->image != '' ? ' article-image-layout' : '').($i%2 == 0 ? ' odd' : ''); ?>">
		
			<header>
				<h1><a href="<?php echo $this->createUrl('detail', array('n'=>$news->title_url, 'cms_section_id'=>$news->section_id)); ?>"><?php echo $newsTitle; ?></a></h1>
				<p class="article-date"><?php echo Yii::t('newsModule.common', 'Publiée le'); ?> <?php echo Helper::formatDate($news->date, "reg"); ?></p>
			</header>
			
			<div class="row">
			
				<?php if ($news->image != ""): ?>
				
				<div class="article-image col-sm-4 col-sm-push-8">
					<a href="<?php echo $this->createUrl('detail', array('n'=>$news->title_url, 'cms_section_id'=>$news->section_id)); ?>">
						<img src="<?php echo Yii::app()->request->baseUrl."/".$news->imageHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($news->image, 's')); ?>" alt="<?php echo $newsTitle; ?>" class="img-responsive" />
					</a>
					<p class="article-image-caption"><?php echo CHtml::encode($news->image_label); ?></p>
				</div>
				
					<?php
					$abstractContainerClass = "col-sm-8 col-sm-pull-4";
				else:
					$abstractContainerClass = "col-sm-12";
				endif;
				?>
				
				<div class="article-abstract <?PHP echo $abstractContainerClass; ?>">
				
					<p><?php echo CHtml::encode($news->summary); ?></p>
									
					<a class="btn btn-primary" href="<?php echo $this->createUrl('detail', array('n'=>$news->title_url, 'cms_section_id'=>$news->section_id)); ?>"><?php echo Yii::t('newsModule.common', 'Lire la suite'); ?></a>
				
				</div>
				
			</div>
			
		</article>
		
			<?php
			$i++;
		endforeach;
	endif; ?>


	<?php if ($newsProvider->pagination->pageCount > 1): ?>
	<footer>
		
		<ul class="pagination pagination-sm">

			<?php $linkParams = array('index');

			if ($newsProvider->pagination->currentPage > 0): 
					$linkParams['page'] = $newsProvider->pagination->currentPage ?>

				<li><?php echo CHtml::link('&laquo;', $linkParams); ?></li>
			<?php
			endif; 
		
			$a = $newsProvider->pagination->currentPage - 5;
			$b = $newsProvider->pagination->currentPage + 5;
			$c = $newsProvider->pagination->pageCount;
		
			for ($i = ($a < 1 ? 1 : $a+1); $i <= ($b > $c-1 ? $c : $b+1); $i++): 
				$linkParams['page'] = $i;
				
				if ($i == $newsProvider->pagination->currentPage + 1): ?>
				
			<li class="active"><span><?php echo $i; ?></span></li>
			<?php else: ?>
			<li><?php echo CHtml::link($i, $linkParams); ?></li>
			<?php endif;
		
			endfor;

			if ($newsProvider->pagination->currentPage < $newsProvider->pagination->pageCount-1): 
					$linkParams['page'] = $newsProvider->pagination->currentPage+2 ?>

				<li><?php echo CHtml::link('&raquo;', $linkParams); ?></li>
			<?php endif; ?>

		</ul>
	
	</footer>
	<?php endif; ?>
	
</article>
