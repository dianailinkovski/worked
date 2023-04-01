<?php 
Yii::app()->clientScript->registerCssFile(Yii::app()->cms->blocs['googlemap']->assetsUrl.'/css/googlemap.css');
preg_match('/src="([^"]+)/u', $bloc->iframe, $url); 

if (isset($url[1])):

	$url = str_replace('"', '', $url[1]);
	?>
	
<div class="map-container">
	
	<div class="overlay" onClick="style.pointerEvents='none'"></div>
	<iframe src="<?php echo $url; ?>"></iframe>
	<div class="enlarge-link"><small><a href="<?php echo $url; ?>" target="_blank"><?php echo Yii::t('blocs', 'Agrandir le plan'); ?></a></small></div>

</div>

<?PHP
endif;
?>
