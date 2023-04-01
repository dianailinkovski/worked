<?php
$this->breadcrumbs=array(
	$this->sectionLabel,
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create', 'section_id'=>(int)$_GET['section_id'])),
);

$this->title='Gestion '.$this->sectionLabel;

$model->section_id = (int)$_GET['section_id'];
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'news-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'title',
		'date',
		array(
			'class'=>'AdminButtonColumn'
		),
	),
)); ?>