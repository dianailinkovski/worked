<?php
if ($news):
	$i = 1;
	foreach ($news as $newsEntry) :
	?>
	
	<article class="entry<?php echo ($newsEntry->image != '' ? ' article-image-layout' : '').($i%2 == 0 ? ' odd' : ''); ?>">
				
		<header>
			<?php if ($newsEntry->image != ""): ?>
			<p class="article-image"><img src="<?php echo Yii::app()->request->baseUrl."/".$newsEntry->imageHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($newsEntry->image, 'm')); ?>" alt="<?php echo CHtml::encode($newsEntry->title); ?>" class="img-responsive"></p>
			<?PHP endif; ?>

			<p class="article-date"><?php echo Yii::t('newsModule.common', 'Publiée le'); ?> <?php echo Helper::formatDate($newsEntry->date, "reg"); ?></p>			
			<h1 class="article-title"><a href="<?php echo $this->controller->createUrl('/news/default/detail', array('n'=>$newsEntry->title_url)); ?>" title="<?php echo Yii::t('newsModule.common', 'Lire cette nouvelle'); ?>"><?php echo CHtml::encode($newsEntry->title); ?></a></h1>
		</header>
		
		<div class="article-abstract">
		
			<p><?php echo CHtml::encode($newsEntry->summary); ?></p>
		
			<a href="#" class="article-read-more btn btn-sm btn-primary" title="<?php echo Yii::t('newsModule.common', 'Lire la suite'); ?>"><?php echo Yii::t('newsModule.common', 'Lire la suite'); ?></a>
			
		</div>
		
	</article>
		
		<?php
		$i++;
	endforeach;
else : ?>

	<p><?php echo Yii::t('newsModule.common', 'Aucune nouvelle récente.'); ?></p>

<?php endif; ?>