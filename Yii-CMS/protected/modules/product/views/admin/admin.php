<?php
$this->breadcrumbs=array(
	$this->sectionLabel,
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create')),
);

$this->title='Gestion '.$this->sectionLabel;

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'products-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'refnum',
		'name',
		'price_regular',
		'price_sale',
		array('name'=>'out_of_stock', 'value'=>"str_replace(array('1', '0'), array('Oui', 'Non'), \$data['out_of_stock'])"),
		array(
			'class'=>'AdminButtonColumn',
		),
	),
)); ?>