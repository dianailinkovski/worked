<?php
$this->breadcrumbs=array(
	$this->sectionLabel,
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create')),
);

$this->title='Gestion '.$this->sectionLabel;

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'tags-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'rowHtmlOptionsExpression'=>"array('style'=>'color: white; background-color: #'.\$data['color'].';')",
	'columns'=>array(
		'name',
		array(
			'class'=>'AdminButtonColumn'
		),
	),
)); 
?>