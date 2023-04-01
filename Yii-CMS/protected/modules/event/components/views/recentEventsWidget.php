<section class="mod-event mod-type-listing portlet">
<?php
if ($events):
	$i = 1;
	foreach ($events as $eventEntry) :
	?>
	
	<article class="entry<?php echo ($eventEntry->image != '' ? ' article-image-layout' : '').($i%2 == 0 ? ' odd' : ''); ?>">
				
		<header>
			<h1 class="article-title"><a href="<?php echo $this->controller->createUrl('/event/default/detail', array('n'=>$eventEntry->title_url)); ?>" title="<?php echo Yii::t('eventModule.common', 'Voir les détails'); ?>"><?php echo CHtml::encode($eventEntry->title); ?></a></h1>
			<p class="article-date">
				<?php
				if ($eventEntry->date_start == $eventEntry->date_end):
					echo Yii::t('eventModule.common', 'Le'); ?> <?php echo Helper::formatDate($eventEntry->date_start, "reg");
				else:
					echo Yii::t('eventModule.common', 'Du'); ?> <?php echo Helper::formatDate($eventEntry->date_start, "reg")." ".Yii::t('eventModule.common', 'au'); ?> <?php echo Helper::formatDate($eventEntry->date_end, "reg");
				endif;
				?>
			</p>
		</header>
		
		<div class="row clearfix">
			<?php if($eventEntry->image != ""): ?>
			<div class="article-image col-sm-3">
				<p class="article-image"><img src="<?php echo Yii::app()->request->baseUrl."/".$eventEntry->imageHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($eventEntry->image, 'm')); ?>" alt="<?php echo CHtml::encode($eventEntry->title); ?>" class="img-responsive"></p>
			</div>
			<?PHP endif; ?>
			
			<div class="article-abstract<?PHP echo ($eventEntry->image != "") ? " col-sm-9" : ""; ?>">
				<p class="article-abstract"><?PHP echo CHtml::encode($eventEntry->summary); ?></p>
				<a href="<?php echo $this->controller->createUrl('/event/default/detail', array('n'=>$eventEntry->title_url)); ?>" class="btn btn-primary" title="<?php echo Yii::t('eventModule.common', 'Voir les détails'); ?>"><?php echo Yii::t('eventModule.common', 'Voir les détails'); ?></a>
			</div>
		
		</div>
		
	</article>
		
		<?php
		$i++;
	endforeach;
else : ?>

	<p><?php echo Yii::t('eventModule.common', 'Aucun événement à venir prochainement.'); ?></p>

<?php endif; ?>
</section>