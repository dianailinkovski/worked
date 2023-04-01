<?php
$this->breadcrumbs=array(
	$this->sectionLabel,
);

$this->menu=array(
	array('label'=>'Création '.$this->sectionLabel, 'url'=>array('create')),
);

$this->title='Gestion '.$this->sectionLabel;

$this->widget('ext.QTreeGridView.CQTreeGridView', array(
    'id'=>'productcategory-grid',
    'ajaxUpdate' => false,
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
    	'name',
        array(
            'class'=>'AdminButtonColumn',
        ),
    ),
));

Yii::app()->clientScript->registerScript('ProductsHideFirstRow', "
	$('#1 img').fadeOut(0);
", CClientScript::POS_READY);
?>