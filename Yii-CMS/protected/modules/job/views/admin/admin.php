<?php
$this->breadcrumbs=array(
	$this->sectionLabel,
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create')),
);

$this->title='Gestion '.$this->sectionLabel;

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'news-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'title',
		array('name'=>'category_id','value'=>'$data->category->name'),
		array('name'=>'active','value'=>"str_replace(array(1, 0), array('1'=>'Oui', '0'=>'Non'), \$data['active'])", 'headerHtmlOptions'=>array('style'=>'width:20%;')),
		array(
			'class'=>'AdminButtonColumn'
		),
	),
)); ?>
