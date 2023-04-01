<?php header("Content-Type:text/xml; charset=utf-8"); ?>
<?php echo '<?'; ?>xml version="1.0" encoding="UTF-8"<?php echo '?>'; ?>
<response>
	<?php foreach ($subs as $sub): ?>
	<entry>
		<id><?php echo CHtml::encode($sub->id); ?></id>
		<email><?php echo CHtml::encode($sub->email); ?></email>
		<subject><?php echo ($this->module->multiLang ? CHtml::encode($this->module->emailSubject[$sub->language]) : CHtml::encode($this->module->emailSubject)); ?></subject>
		<bodyurl><?php echo CHtml::encode($this->createAbsoluteUrl('getnewsletter', array('language'=>$sub->language))); ?></bodyurl>
	</entry>
	<?php endforeach; ?>
</response>