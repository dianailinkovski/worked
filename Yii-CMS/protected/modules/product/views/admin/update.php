<?php
$this->breadcrumbs=array(
	$this->sectionLabel=>array('admin'),
	$product->name
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create')),
	array('label'=>'Gestion '.$this->sectionLabel, 'url'=>array('admin')),
);

$this->title='Modification '.$this->sectionLabel.' "'.CHtml::encode($product->name).'"';

echo $this->renderPartial('_form', array('product'=>$product,'productImages'=>$productImages,'productTab1'=>$productTab1,'productTab2'=>$productTab2)); 
?>