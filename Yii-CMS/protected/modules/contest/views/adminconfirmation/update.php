<?php
$this->breadcrumbs=array(
	'Concours/Sondage'=>array('/contest/admin/admin'),
	$contest->title=>array('/contest/admin/update', 'id'=>(int)$_GET['id']),
	'Page de confirmation',
);

$this->menu=array(
	array('label'=>'Création Concours/Sondage', 'url'=>array('/contest/admin/create')),
	array('label'=>'Gestion Concours/Sondage', 'url'=>array('/contest/admin/admin')),
);

$this->title='Page de confirmation :  "'.CHtml::encode($contest->title).'"';

echo $this->renderPartial('_form', array('blocs'=>$blocs)); 
?>