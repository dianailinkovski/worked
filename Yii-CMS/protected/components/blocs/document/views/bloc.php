<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->cms->blocs['document']->assetsUrl.'/css/document.css');

$currentDate = date('Y-m-d H:i:s');

foreach ($bloc->documents as $document):
	
	if ($document->datetime <= $currentDate):
		?>
	
		<dl>
			<dt class="<?PHP echo preg_replace('/[^A-Za-z0-9_\-]/', '-', $document->mime_type); ?>" title="<?PHP echo Helper::formatMimeType($document->mime_type); ?>"><a href="<?PHP echo Yii::app()->baseUrl.'/files/_user/bloc_document/'.Helper::encodeFileName($document->file); ?>" title="<?PHP echo CHtml::encode($document->title); ?>"><?PHP echo CHtml::encode($document->title); ?></a></dt>
			<dd class="publication_date"><?PHP echo Yii::t('blocs', 'Publié le')." ".Helper::formatDate($document->datetime, "reg"); ?></dd>
			<?PHP if ($document->description != ""): ?>
			<dd class="description"><?php echo CHtml::encode($document->description); ?></dd>
			<?PHP endif; ?>
		</dl>

	<?php
	endif;
	
endforeach;
?>