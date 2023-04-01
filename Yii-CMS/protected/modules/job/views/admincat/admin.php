<?php
$this->breadcrumbs=array(
	$this->sectionParentLabel=>array('admin/admin'),
	$this->sectionLabel,
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create')),
);

$this->title='Gestion '.$this->sectionLabel;

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'category-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		array(
			'class'=>'AdminButtonColumn',
			'afterDelete'=>"function(link, success, data){if (data != '') alert('Des items sont classés sous celle-ci. Vous devez les supprimer ou changer leur emplacement avant de supprimer celle-ci.');}"
		),
	),
)); ?>
