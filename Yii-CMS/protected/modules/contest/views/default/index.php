<?php
if (isset($archives)):
	$this->breadcrumbs = Helper::breadcrumbsFromAlias(true);
	$this->breadcrumbs[] = Yii::t('contestModule.common', 'Archives');
else:
	$this->breadcrumbs = Helper::breadcrumbsFromAlias();
endif;

$this->pageTitle = Helper::titleFromBreadcrumbs();

Yii::app()->clientScript->registerMetaTag(Yii::t('contestModule.common', 'meta_description'), 'description');

$contestCount = $contestProvider->getItemCount();
?>

<article class="mod-contest mod-type-listing">

	<header>
		<h3 class="page-title"><?php echo (isset($archives) ? Yii::app()->cms->currentAlias->title.' '.Yii::t('contestModule.common', 'archivés') : Yii::app()->cms->currentAlias->title); ?></h3>
	</header>
	
	<?php if ($contestCount == 0): ?>
	
	<p><?php echo Yii::t('contestModule.common', 'Aucun').mb_strtolower(Yii::app()->cms->currentAlias->title, 'UTF-8').Yii::t('contestModule.common', 'disponible'); ?>.</p>
		
	<?php endif; ?>
	
	<div class="lien-archives">
		<?PHP
		if (isset($archives)):
			$btnLink = $this->createUrl('index');
			$btnText = Yii::t('contestModule.common', 'Retour aux').mb_strtolower(Yii::app()->cms->currentAlias->title, 'UTF-8').Yii::t('contestModule.common', 'actifs');
			$btnClasses = "back";
		else:
			$btnLink = $this->createUrl('index', array('archives'=>$this->module->archivesVarName[Yii::app()->language]));
			$btnText = Yii::t('contestModule.common', 'Voir les').mb_strtolower(Yii::app()->cms->currentAlias->title, 'UTF-8').' '.Yii::t('contestModule.common', 'archivés');
			$btnClasses = "btn btn-default btn-large";
		endif;
		?>
		<a href="<?PHP echo $btnLink; ?>" class="<?PHP echo $btnClasses; ?>"><?PHP echo $btnText; ?></a>
	</div>
	
	<?PHP
	if ($contestCount != 0):
		$i=1;
		foreach ($contestProvider->getData() as $contest):
			$contestTitle = CHtml::encode($contest->title);
		?>
		
		<article class="entry<?php echo ($contest->image != '' ? ' article-image-layout' : '').($i%2 == 0 ? ' odd' : ''); ?>">
		
			<header>
				<h4><a href="<?php echo $this->createUrl('detail', array('n'=>$contest->title_url)); ?>"><?php echo $contestTitle; ?></a></h4>
			</header>

			<div class="article-content clearfix">
			
				<?php
				if ($contest->image != ""):
					if (Yii::app()->user->getState('siteVersion') == 'mobile'):
						$imageSize = "l";
					else:
						$imageSize = "m";
					endif;
					?>
				
				<div class="article-image">
					<?PHP if (Yii::app()->user->getState('siteVersion') == 'desktop'): ?>
					<a href="<?php echo $this->createUrl('detail', array('n'=>$contest->title_url)); ?>">
						<img src="<?php echo Yii::app()->request->baseUrl."/".$contest->imageHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($contest->image, $imageSize)); ?>" alt="<?php echo $contestTitle; ?>" class="img-responsive" />
					</a>
					<?PHP else: ?>
					<a href="<?php echo $this->createUrl('detail', array('n'=>$contest->title_url)); ?>" style="background-image:url(<?php echo Yii::app()->request->baseUrl."/".$contest->imageHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($contest->image, $imageSize)); ?>);" title="<?php echo $contestTitle; ?>">
					</a>
					<?PHP endif; ?>
				</div>
				
				<?php endif; ?>

				<div class="article-abstract">
				
					<p><?php echo CHtml::encode($contest->summary); ?></p>
									
					<a class="btn btn-primary" href="<?php echo $this->createUrl('detail', array('n'=>$contest->title_url)); ?>"><?php echo Yii::t('contestModule.common', 'Voir les détails'); ?></a>
				
				</div>
				
			</div>
			
		</article>
		
			<?php
			$i++;
		endforeach;
	endif; ?>


	<?php if ($contestProvider->pagination->pageCount > 1): ?>
	<footer>
		
		<ul class="pagination pagination-sm">

			<?php
			$linkParams = array('index');
		
			$a = $contestProvider->pagination->currentPage - 5;
			$b = $contestProvider->pagination->currentPage + 5;
			$c = $contestProvider->pagination->pageCount;
		
			for ($i = ($a < 1 ? 1 : $a+1); $i <= ($b > $c-1 ? $c : $b+1); $i++): 
				$linkParams['page'] = $i;
				
				if ($i == $contestProvider->pagination->currentPage + 1): ?>
				
			<li class="active"><span><?php echo $i; ?></span></li>
			<?php else: ?>
			<li><?php echo CHtml::link($i, $linkParams); ?></li>
			<?php endif;
		
			endfor; ?>
		</ul>
	
	</footer>
	<?php endif; ?>
	
</article>
