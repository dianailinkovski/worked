<?php
$this->breadcrumbs=array(
	Yii::t('admin', 'Sections')=>array('admin'),
	Yii::t('admin', 'Create'),
);

$this->menu=array(
	array('label'=>Yii::t('admin', 'Manage Section'), 'url'=>array('admin')),
);
?>

<h1><?php echo Yii::t('admin', 'Create Section'); ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>