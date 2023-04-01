<?php Yii::app()->clientScript->registerCssFile(Yii::app()->cms->blocs['youtube']->assetsUrl.'/css/youtube.css'); ?>

<div class="video-container">
	
	<iframe src="http://www.youtube.com/embed/<?php echo preg_replace('/(.*)\?v\=([a-zA-Z0-9-_]*)(.*)/', '$2', $bloc->link); ?>?rel=0&amp;hl=<?php echo Yii::app()->language; ?>" allowfullscreen></iframe>
	
</div>