<?php
$this->breadcrumbs=array(
	$this->sectionLabel,
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create')),
);

$this->title='Gestion '.$this->sectionLabel;

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'banners-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'rowCssClassExpression'=>"\$data['active'] == '1' ? 'grid-view-yellow-row' : (\$row % 2 == 0 ? 'even' : 'odd')",
	'columns'=>array(
		array('name'=>'text', 'type'=>'raw', 'value'=>"(strlen(CHtml::encode(strip_tags(\$data['text']))) > 100 ? trim(substr(CHtml::encode(strip_tags(\$data['text'])), 0, 100)).'&hellip;' : CHtml::encode(strip_tags(\$data['text'])))"),
		array(
			'class'=>'AdminButtonColumn'
		),
	),
)); 
?>