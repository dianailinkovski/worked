<?php
$this->breadcrumbs=array(
	$this->sectionLabel=>array('admin'),
	$job->title
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create')),
	array('label'=>'Gestion '.$this->sectionLabel, 'url'=>array('admin')),
);

$this->title='Modification '.$this->sectionLabel.' "'.$job->id.'"';

echo $this->renderPartial('_form', array('model'=>$job,'blocs'=>$blocs)); 
?>