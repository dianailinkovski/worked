<?php
$this->breadcrumbs=array(
	$this->sectionLabel=>array('admin'),
	'Création',
);

$this->menu=array(
	array('label'=>'Gestion '.$this->sectionLabel, 'url'=>array('admin')),
);

$this->title='Création '.$this->sectionLabel;

echo $this->renderPartial('_form', array('product'=>$product,'productImages'=>$productImages,'productTab1'=>$productTab1,'productTab2'=>$productTab2)); 
?>