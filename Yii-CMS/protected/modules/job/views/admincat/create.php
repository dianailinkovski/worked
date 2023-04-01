<?php
$this->breadcrumbs=array(
	$this->sectionParentLabel=>array('admin/admin'),
	$this->sectionLabel=>array('admin'),
	Yii::t('admin', 'Création'),
);

$this->menu=array(
	array('label'=>'Gestion '.$this->sectionLabel, 'url'=>array('admin')),
);

$this->title='Création '.$this->sectionLabel;

echo $this->renderPartial('_form', array('model'=>$model)); 
?>