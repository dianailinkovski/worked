<?php
$this->breadcrumbs=array(
	'Concours/Sondage'=>array('/contest/admin/admin'),
	$contest->title=>array('/contest/admin/update', 'id'=>(int)$_GET['id']),
	'Page d’introduction',
);

$this->menu=array(
	array('label'=>'Création Concours/Sondage', 'url'=>array('/contest/admin/create')),
	array('label'=>'Gestion Concours/Sondage', 'url'=>array('/contest/admin/admin')),
);

$this->title='Description et règlements :  "'.CHtml::encode($contest->title).'"';

echo $this->renderPartial('_form', array('blocs'=>$blocs)); 
?>