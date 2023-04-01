<?php
$this->breadcrumbs=array(
	$this->sectionLabel=>array('admin'),
	$event->title
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create', 'section_id'=>$_GET['section_id'])),
	array('label'=>'Gestion '.$this->sectionLabel, 'url'=>array('admin', 'section_id'=>$_GET['section_id'])),
);

$this->title='Modification '.$this->sectionLabel.' "'.CHtml::encode($event->title).'"';

echo $this->renderPartial('_form', array('event'=>$event,'blocs'=>$blocs)); 
?>