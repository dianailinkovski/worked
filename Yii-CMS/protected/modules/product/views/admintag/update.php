<?php
$this->breadcrumbs=array(
	$this->sectionLabel=>array('admin'),
	$productTag->name
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create')),
	array('label'=>'Gestion '.$this->sectionLabel, 'url'=>array('admin')),
);

$this->title='Modification '.$this->sectionLabel.' "'.CHtml::encode($productTag->name).'"';

echo $this->renderPartial('_form', array('productTag'=>$productTag)); 
?>