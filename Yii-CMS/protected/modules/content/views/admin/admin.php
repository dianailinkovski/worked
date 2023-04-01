<?php
$this->breadcrumbs=array(
	Yii::t('contentModule.admin', 'Pages'),
);

$this->menu=array(
	array('label'=>Yii::t('contentModule.admin', 'Create Page'), 'url'=>array('create')),
);

$this->title=Yii::t('contentModule.admin', 'Manage Page');
?>

<div id="statusMsg"></div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'content-pages-grid',
	'dataProvider'=>$model->search(),
	'ajaxUpdateError'=>'function(xhr,ts,et,err){ $("#statusMsg").text(err); }',
	'filter'=>$model,
	'columns'=>array(
		'title',
		array(
			'class'=>'AdminButtonColumn',
			'afterDelete'=>'function(link,success,data){ if(success) $("#statusMsg").html(data); }',
		),
	),
)); ?>
