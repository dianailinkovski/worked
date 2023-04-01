<?php
$this->breadcrumbs=array(
	$this->sectionLabel,
);

$this->menu=array(
	array('label'=>'Créer '.$this->sectionLabel, 'url'=>array('create')),
);

$this->title='Gestion '.$this->sectionLabel;

$this->beginWidget('application.components.widgets.XPanel.XPanel');

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'member-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'first_name',
		'last_name',
		'email',
		'created_at',
		'last_login_date',
		array(
			'class'=>'AdminButtonColumn'
		),
	),
)); 

$this->endWidget();
?>