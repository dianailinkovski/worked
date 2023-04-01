<?php
$this->breadcrumbs=array(
	$this->sectionParentLabel=>array('admin/admin'),
	$this->sectionLabel,
);

$this->title='Liste des '.$this->sectionLabel;

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'cv-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'date',
		'cv',
		array(
			'type'=>'raw',
			'value'=>"'<a href=\"/files/_user/jobcv/'.\$data->cv.'\" target=\"_blank\" title=\"Télécharger\"><img src=\"/images/admin/downarrow.png\" alt=\"Télécharger\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"'.\$this->grid->controller->createUrl('admincv/read', array('id'=>\$data->id)).'\" title=\"Voir les détails de la postulation\"><img src=\"/images/admin/eye.png\" alt=\"Voir les détails\" /></a>'"
		),
	),
)); ?>
