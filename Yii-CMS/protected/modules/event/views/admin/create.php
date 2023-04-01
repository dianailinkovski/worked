<?php
$this->breadcrumbs=array(
	$this->sectionLabel=>array('admin'),
	'Création',
);

$this->menu=array(
	array('label'=>'Gestion '.$this->sectionLabel, 'url'=>array('admin', 'section_id'=>$_GET['section_id'])),
);

$this->title='Création '.$this->sectionLabel;

echo $this->renderPartial('_form', array('event'=>$event,'blocs'=>$blocs)); 
?>