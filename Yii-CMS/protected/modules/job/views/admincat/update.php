<?php
$this->breadcrumbs=array(
	$this->sectionParentLabel=>array('admin/admin'),
	$this->sectionLabel=>array('admin'),
	$model->id
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create')),
	array('label'=>'Gestion '.$this->sectionLabel, 'url'=>array('admin')),
);

$this->title='Modification '.$this->sectionLabel.' "'.$model->id.'"';

echo $this->renderPartial('_form', array('model'=>$model)); 
?>