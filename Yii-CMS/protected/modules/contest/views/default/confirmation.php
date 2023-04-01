<?php
$contestTitle = CHtml::encode($contest->title);

$this->breadcrumbs = Helper::breadcrumbsFromAlias(true);
$this->breadcrumbs[] = $contest->title;

$this->pageTitle = Helper::titleFromBreadcrumbs();
?>

<article class="mod-contest mod-type-detail">

	<header>
		<h3 class="page-title"><?php echo $contestTitle; ?> (confirmation)</h3>
	</header>
	
	<?PHP
	$this->widget('application.widgets.BlocsWidget', array(
		'models'=>$contest->blocsConfirmation,
	));
	?>

</article>
	
<a href="<?PHP echo $this->createUrl('index'); ?>" class="back"><?PHP echo Yii::t('contestModule.common', 'Retour aux').mb_strtolower(Yii::app()->cms->currentAlias->title, 'UTF-8'); ?></a>