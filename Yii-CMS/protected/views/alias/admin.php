<?php
$this->breadcrumbs=array(
	'Alias',
);

$this->menu=array(
	array('label'=>'Création Alias', 'url'=>array('create')),
);
?>

<h1>Gestion Alias</h1>

<?php
$this->widget('ext.QTreeGridView.CQTreeGridView', array(
    'id'=>'cmsalias-grid',
    'ajaxUpdate' => false,
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        'alias',
    	'title',
        array(
            'class'=>'CButtonColumn',
            'template'=>'{update}{delete}',
        ),
    ),
));
?>