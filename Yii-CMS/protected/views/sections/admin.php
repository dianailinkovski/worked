<?php
$this->breadcrumbs=array(
	Yii::t('admin', 'Sections')
);

$this->menu=array(
	array('label'=>Yii::t('admin', 'Create Section'), 'url'=>array('create')),
);
?>

<h1><?php echo Yii::t('admin', 'Manage Section') ?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'cms-sections-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'name',
		'module',
		array(
			'class'=>'CButtonColumn',
			'template'=>'{update}{delete}',
		),
	),
)); ?>
