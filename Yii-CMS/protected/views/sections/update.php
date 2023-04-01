<?php
$this->breadcrumbs=array(
	Yii::t('admin', 'Sections')=>array('admin'),
	$model->name
);

$this->menu=array(
	array('label'=>Yii::t('admin', 'Create Section'), 'url'=>array('create')),
	array('label'=>Yii::t('admin', 'Manage Section'), 'url'=>array('admin')),
);
?>

<h1><?php echo Yii::t('admin', 'Update Section {id}', array('{id}'=>CHtml::encode($model->name))); ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>