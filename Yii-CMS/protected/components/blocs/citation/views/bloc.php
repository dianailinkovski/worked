<?php Yii::app()->clientScript->registerCssFile(Yii::app()->cms->blocs['citation']->assetsUrl.'/css/citation.css'); ?>

<?php foreach ($bloc->citations as $citation): ?>

<blockquote>
	<p><?php echo CHtml::encode($citation->citation); ?></p>
	<cite><?php echo CHtml::encode($citation->name); ?></cite>
</blockquote>
	
<?php endforeach; ?>