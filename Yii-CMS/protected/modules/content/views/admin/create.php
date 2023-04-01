<?php
$this->breadcrumbs=array(
	Yii::t('contentModule.admin', 'Pages')=>array('admin'),
	Yii::t('admin', 'Create'),
);


$this->menu=array(
	array('label'=>Yii::t('contentModule.admin', 'Manage Page'), 'url'=>array('admin')),
);

$this->title=Yii::t('contentModule.admin', 'Create Page');

echo $this->renderPartial('_form', array('contentPage'=>$contentPage,'blocs'=>$blocs)); 
?>