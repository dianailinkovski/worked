<?php
if(isset($archives)):
	$this->breadcrumbs = Helper::breadcrumbsFromAlias(true);
	$this->breadcrumbs[]='Archives';
else:
	$this->breadcrumbs = Helper::breadcrumbsFromAlias();
endif;

$this->pageTitle = Helper::titleFromBreadcrumbs();

Yii::app()->clientScript->registerMetaTag(Yii::t('eventModule.common', 'meta_description'), 'description');

$eventCount = $eventProvider->getItemCount();
?>

<article class="mod-event mod-type-listing">

	<header>
		<h1 class="page-title"><?php echo Yii::t('eventModule.common', 'Événements'); ?></h1>
	</header>
	
	<div class="row">
		<div class="lien-archives">
			<?php if ($archives !== null): ?>
			<a class="back" href="<?php echo $this->createUrl('index', array('cms_section_id'=>Yii::app()->cms->currentSectionId)); ?>"><?php echo Yii::t('eventModule.common', 'Retour aux évènements à venir'); ?></a>
			<?php else: ?>
			<a href="<?php echo $this->createUrl('index', array('cms_section_id'=>Yii::app()->cms->currentSectionId, 'archives'=>$this->module->archivesVarName[Yii::app()->language])); ?>"><?php echo Yii::t('eventModule.common', 'Archives'); ?></a>
			<?php endif; ?>
		</div>
	</div>
	
	<?php if ($eventCount == 0): ?>
		<?php if ($archives !== null): ?>
		<p class="pas-devent"><?php echo Yii::t('eventModule.common', 'Aucun événement dans les archives.'); ?></p>
		<?php else: ?>
		<p class="pas-devent"><?php echo Yii::t('eventModule.common', 'Aucun événement à venir prochainement.'); ?></p>
	<?php endif; ?>
	
		
	<?php
	else:
		$i=1;
		foreach ($eventProvider->getData() as $event):
			$eventTitle = CHtml::encode($event->title);
			$detailUrl = ($archives === null ? $this->createUrl('detail', array('n'=>$event->title_url, 'cms_section_id'=>$event->section_id)) : $this->createUrl('detail', array('archives'=>$this->module->archivesVarName[Yii::app()->language], 'n'=>$event->title_url, 'cms_section_id'=>$event->section_id)));
		?>
		
		<article class="entry<?php echo ($event->image != '' ? ' article-image-layout' : '').($i%2 == 0 ? ' odd' : ''); ?>">
		
			<header>
				<h1><a href="<?php echo $detailUrl; ?>"><?php echo $eventTitle; ?></a></h1>
				<p class="article-date">
				<?php
				$start_date = substr($event->date_start, 0, 10);
				$end_date = substr($event->date_end, 0, 10);
			
				if ($start_date == $end_date):
					echo Yii::t('eventModule.common', 'Le')." ".Helper::formatDate($start_date, "reg");
				else:
					echo Yii::t('eventModule.common', 'Du')." ".Helper::formatDate($start_date, "reg")." ".Yii::t('eventModule.common', 'au')." ".Helper::formatDate($end_date , "reg");
				endif;
				?>
				</p>
			</header>
			
			<div class="row">
			
				<?php if ($event->image != ""): ?>
				
				<div class="article-image col-sm-4 col-sm-push-8">
					<a href="<?php echo $detailUrl; ?>">
						<img src="<?php echo Yii::app()->request->baseUrl."/".$event->imageHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($event->image, 's')); ?>" alt="<?php echo $eventTitle; ?>" class="img-responsive" />
					</a>
					<p class="article-image-caption"><?php echo CHtml::encode($event->image_label); ?></p>
				</div>
				
					<?php
					$abstractContainerClass = "col-sm-8 col-sm-pull-4";
				else:
					$abstractContainerClass = "col-sm-12";
				endif;
				?>
				
				<div class="article-abstract <?PHP echo $abstractContainerClass; ?>">
				
					<p><?php echo CHtml::encode($event->summary); ?></p>
									
					<a class="btn btn-primary" href="<?php echo $detailUrl; ?>"><?php echo Yii::t('eventModule.common', 'Voir les détails'); ?></a>
				
				</div>
				
			</div>
			
		</article>
		
			<?php
			$i++;
		endforeach;
	endif; ?>


	<?php if ($eventProvider->pagination->pageCount > 1): ?>
	<footer>
		
		<ul class="pagination pagination-sm">

			<?php $linkParams = array('index'); 

			if ($eventProvider->pagination->currentPage > 0): 
					$linkParams['page'] = $eventProvider->pagination->currentPage ?>

				<li><?php echo CHtml::link('&laquo;', $linkParams); ?></li>
			<?php
			endif; 
		
			$a = $eventProvider->pagination->currentPage - 5;
			$b = $eventProvider->pagination->currentPage + 5;
			$c = $eventProvider->pagination->pageCount;
		
			for ($i = ($a < 1 ? 1 : $a+1); $i <= ($b > $c-1 ? $c : $b+1); $i++): 
				$linkParams['page'] = $i;
				
				if ($i == $eventProvider->pagination->currentPage + 1): ?>
				
			<li class="active"><span><?php echo $i; ?></span></li>
			<?php else: ?>
			<li><?php echo CHtml::link($i, $linkParams); ?></li>
			<?php endif;
		
			endfor; 

			 if ($eventProvider->pagination->currentPage < $eventProvider->pagination->pageCount-1): 
					$linkParams['page'] = $eventProvider->pagination->currentPage+2 ?>

				<li><?php echo CHtml::link('&raquo;', $linkParams); ?></li>
			<?php endif; ?>

		</ul>
	
	</footer>
	<?php endif; ?>
	
</article>
