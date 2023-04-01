<?php
$this->breadcrumbs = Helper::breadcrumbsFromAlias(true);
$this->breadcrumbs[] = Yii::t('jobModule.common', 'Confirmation de postulation');
$this->pageTitle = Helper::titleFromBreadcrumbs();
?>

<article class="mod-job mod-type-listing">
	
	<header>
		<h1 class="page-title"><?php echo Yii::t('jobModule.common', 'Confirmation de postulation'); ?></h1>
	</header>
	
	<section class="section-bloc bloc-editor">
		<p><?php echo Yii::t('jobModule.common', 'Merci, nous avons bien reçu votre candidature.'); ?></p>
	</section>
	
</article>

<a href="<?PHP echo $this->createUrl('index'); ?>" class="back"><?PHP echo Yii::t('jobModule.common', 'Retour à la liste des emplois'); ?></a>