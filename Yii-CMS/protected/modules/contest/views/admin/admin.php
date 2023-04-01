<?php
$this->breadcrumbs=array(
	$this->sectionLabel,
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create')),
);

$this->title='Gestion '.$this->sectionLabel;

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'contest-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'title',
		'end_date',
		array('name'=>'status','value'=>"str_replace(array('active',  'inactive', 'archived'), array('active'=>'Actif', 'inactive'=>'Inactif', 'archived'=>'Archivé'), \$data['status'])", 'headerHtmlOptions'=>array('style'=>'width:12%;')),
		array(
			'class'=>'AdminButtonColumn'
		),
	),
)); 
?>