<?php
$this->breadcrumbs=array(
	$this->sectionLabel=>array('admin'),
	$user->id
);

$this->menu=array(
	array('label'=>'Créer '.$this->sectionLabel, 'url'=>array('create')),
	array('label'=>'Gestion '.$this->sectionLabel, 'url'=>array('admin')),
);

$this->title='Modification '.$this->sectionLabel.' #'.$user->id;

echo $this->renderPartial('_form', array('user'=>$user)); 
?>