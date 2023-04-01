<?php
$this->breadcrumbs=array(
	$this->sectionLabel=>array('admin'),
	'Création',
);

$this->menu=array(
	array('label'=>'Gestion '.$this->sectionLabel, 'url'=>array('admin')),
);
?>

<h1>Création <?php echo $this->sectionLabel; ?></h1>

<?php echo $this->renderPartial('_form', array('cmsAlias'=>$cmsAlias, 'cmsAliasRoutes'=>$cmsAliasRoutes)); ?>