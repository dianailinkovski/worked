<?php
$this->pageTitle=Yii::app()->name . ' - Aide';
$this->breadcrumbs=array(
	'Aide',
);
?>
<h1>Aide</h1>

<p>
Pour les champs de recherche, vous pouvez utiliser les opérateurs (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
ou <b>=</b>) au début de votre entrée pour spécifier comment la recherche devrait être comparée.
</p>

<p class="note"><?php echo Yii::t('admin', 'Fields with {asterix} are required.', array('{asterix}'=>'<span class="red">*</span>')); ?></p>