<?php
$this->breadcrumbs=array(
	$this->sectionLabel=>array('admin'),
	$contest->title
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create')),
	array('label'=>'Gestion '.$this->sectionLabel, 'url'=>array('admin')),
);

$this->title='Informations générales :  "'.CHtml::encode($contest->title).'"';

echo $this->renderPartial('_form', array('contest'=>$contest)); 
?>