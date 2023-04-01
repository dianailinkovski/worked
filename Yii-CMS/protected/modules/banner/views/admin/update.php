<?php
$shortText = CHtml::encode(strip_tags($banner->text));
if (strlen($shortText) > 100)
	$shortText = trim(substr($shortText, 0, 100)).'...';

$this->breadcrumbs=array(
	$this->sectionLabel=>array('admin'),
	$shortText
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create')),
	array('label'=>'Gestion '.$this->sectionLabel, 'url'=>array('admin')),
);

$this->title='Modification '.$this->sectionLabel;

echo $this->renderPartial('_form', array('banner'=>$banner)); 
?>