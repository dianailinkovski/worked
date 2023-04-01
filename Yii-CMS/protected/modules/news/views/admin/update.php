<?php
$this->breadcrumbs=array(
	$this->sectionLabel=>array('admin'),
	$news->title
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create', 'section_id'=>(int)$_GET['section_id'])),
	array('label'=>'Gestion '.$this->sectionLabel, 'url'=>array('admin', 'section_id'=>(int)$_GET['section_id'])),
);

$this->title='Modification '.$this->sectionLabel.' "'.CHtml::encode($news->title).'"';

echo $this->renderPartial('_form', array('news'=>$news,'blocs'=>$blocs)); 
?>