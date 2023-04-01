<?php header("Content-Type:text/xml; charset=utf-8"); ?>
<?php echo '<?'; ?>xml version="1.0" encoding="UTF-8"<?php echo '?>'; ?>
<response>
	<send><?php echo $send; ?></send>
	<from><?php echo Yii::app()->params['mail']['From']; ?></from>
	<fromname><?php echo Yii::app()->params['mail']['FromName']; ?></fromname>
</response>