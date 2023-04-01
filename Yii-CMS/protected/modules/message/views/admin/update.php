<?php
$this->breadcrumbs=array(
	$this->sectionLabel=>array('admin'),
	$message->id
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create')),
	array('label'=>'Gestion '.$this->sectionLabel, 'url'=>array('admin')),
);

$this->title='Modification '.$this->sectionLabel.' "'.$message->id.'"';

echo $this->renderPartial('_form', array('message'=>$message)); 
?>