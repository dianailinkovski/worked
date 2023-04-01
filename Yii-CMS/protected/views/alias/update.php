<?php
$this->breadcrumbs=array(
	$this->sectionLabel=>array('admin'),
	$cmsAlias->title
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create')),
	array('label'=>'Gestion '.$this->sectionLabel, 'url'=>array('admin')),
);
?>

<h1>Modification <?php echo $this->sectionLabel; ?> "<?php echo CHtml::encode($cmsAlias->title); ?>"</h1>

<?php echo $this->renderPartial('_form', array('cmsAlias'=>$cmsAlias, 'cmsAliasRoutes'=>$cmsAliasRoutes)); ?>