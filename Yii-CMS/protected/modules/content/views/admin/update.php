<?php
$this->breadcrumbs=array(
	Yii::t('contentModule.admin', 'Pages')=>array('admin'),
	$contentPage->title,
);

$this->menu=array(
	array('label'=>Yii::t('contentModule.admin', 'Create Page'), 'url'=>array('create')),
	array('label'=>Yii::t('contentModule.admin', 'Manage Page'), 'url'=>array('admin')),
);

$this->title=Yii::t('contentModule.admin', 'Update Page {title}', array('{title}'=>CHtml::encode($contentPage->title)));

echo $this->renderPartial('_form', array('contentPage'=>$contentPage,'blocs'=>$blocs)); 
?>