<?php
$contestTitle = CHtml::encode($contest->title);
$contestImagePath = Yii::app()->request->baseUrl."/".$contest->imageHandler->dir."/";

$this->breadcrumbs = Helper::breadcrumbsFromAlias(true);
$this->breadcrumbs[] = $contest->title;

$this->pageTitle = Helper::titleFromBreadcrumbs();
?>

<article class="mod-contest mod-type-detail<?php echo ($contest->image != "" ? " article-image-layout" : ""); ?>">

	<header>
		<h3 class="page-title"><?php echo $contestTitle; ?></h3>
	</header>
	
	<?PHP if ($contest->end_date != ""): ?>
	<p class="well well-sm"><?PHP echo Yii::t('contestModule.common', 'Ce'); echo mb_strtolower(Yii::app()->cms->currentAlias->title, 'UTF-8'); echo Yii::t('contestModule.common', 'a pris fin le'); echo Helper::formatDate($contest->end_date, "reg"); echo Yii::t('contestModule.common', 'à'); echo substr($contest->end_date, 11, 5); ?>.</p>
	<?PHP else: ?>
	<p class="well well-sm"><?PHP echo Yii::t('contestModule.common', 'Ce'); echo mb_strtolower(Yii::app()->cms->currentAlias->title, 'UTF-8'); echo Yii::t('contestModule.common', 'est terminé'); ?>.</p>
	<?PHP endif; ?>
	
	<?PHP if ($contest->image != ""): ?>
	<div class="article-image clearfix">
		
		<?PHP
		$this->beginWidget('ext.prettyPhoto.PrettyPhoto', array(
		  'id'=>'pretty_photo',
		  'options'=>Yii::app()->params['prettyPhotoOptions'],
		));
		?>

		<a href="<?PHP echo $contestImagePath.Helper::encodeFileName(Helper::fileSuffix($contest->image, 'l')); ?>" title="<?PHP echo $contestTitle; ?>">
			<img src="<?PHP echo $contestImagePath.Helper::encodeFileName(Helper::fileSuffix($contest->image, 'l')); ?>" alt="<?PHP echo $contestTitle; ?>" title="<?php echo Yii::t('contestModule.common', 'Cliquez pour agrandir limage'); ?>" class="img-responsive" />
		</a>
		
		<?PHP
		$this->endWidget('ext.prettyPhoto.PrettyPhoto');
		?>
		
	</div>

	<?PHP
	endif;
	
	$this->widget('application.widgets.BlocsWidget', array(
		'models'=>$contest->blocsConclusion,
	));
	?>
	
</article>

<a href="<?PHP echo $this->createUrl('index'); ?>" class="back"><?PHP echo Yii::t('contestModule.common', 'Retour aux').mb_strtolower(Yii::app()->cms->currentAlias->title, 'UTF-8'); ?></a>