<?php
$this->breadcrumbs = Helper::breadcrumbsFromAlias();
$this->pageTitle = Helper::titleFromBreadcrumbs();
?>

<article class="mod-content mod-type-detail">
		
	<header>
		<h1 class="page-title"><?php echo CHtml::encode($page->title); ?></h1>
	</header>
	
	<?php $this->widget('application.components.widgets.Blocs.BlocsWidget', array(
		'parentId'=>$page->id,
		'uniqueId'=>'content',
	)); ?>

</article>