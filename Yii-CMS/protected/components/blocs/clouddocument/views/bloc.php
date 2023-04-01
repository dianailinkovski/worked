<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->cms->blocs['clouddocument']->assetsUrl.'/css/clouddocument.css');

$client = new Dropbox\Client(Yii::app()->params['dropboxToken'], 'dropbox-client');

/*
$pathError = Dropbox\Path::findError($bloc->path);
if ($pathError !== null) {
	throw new CHttpException(500, "Invalid <dropbox-path>: $pathError\n");
}
*/

// Getting metadata from db if not changed, otherwise getting from request and updating db.
if ($bloc->previous_folder_hash == '') {
	$metadata = $client->getMetadataWithChildren($bloc->path);
	
	if ($metadata === null) {
		//throw new CHttpException(500, "No file or folder at that path.\n");
	}
	$bloc->previous_folder_hash = $metadata['hash'];
	$bloc->previous_folder = serialize($metadata);
	$bloc->save(false);
}
else {
	$metadata = $client->getMetadataWithChildrenIfChanged($bloc->path, $bloc->previous_folder_hash);

	if ($metadata[0] == true) {
		if ($metadata[1] === null) {
			//throw new CHttpException(500, "No file or folder at that path.\n");
		}
		$metadata = $metadata[1];
		$bloc->previous_folder_hash = $metadata['hash'];
		$bloc->previous_folder = serialize($metadata);
		$bloc->save(false);
	} 
	else {
		$metadata = unserialize($bloc->previous_folder);
	}
}

// If it's a folder, remove the 'contents' list from $metadata; print that stuff out after.
$children = null;
if ($metadata['is_dir']) {
    $children = $metadata['contents'];
    unset($metadata['contents']);
}

if ($children !== null && count($children) > 0) {
    foreach ($children as $child) {
        $name = Dropbox\Path::getName($child['path']);
        if (!$child['is_dir']) {
        	?>
			<dl>
				<dt class="<?PHP echo preg_replace('/[^A-Za-z0-9_\-]/', '-', $child['mime_type']); ?>" title="<?PHP echo Helper::formatMimeType($child['mime_type']); ?>"><a href="javascript:;" title="<?PHP echo CHtml::encode($name); ?>" dropboxpath="<?PHP echo base64_encode($child['path']); ?>"><?PHP echo CHtml::encode($name); ?></a></dt>
				<dd class="publication_date"><?PHP echo Yii::t('blocs', 'Publié le')." ".Helper::formatDate($child['client_mtime'], "reg"); ?></dd>
			</dl>
			<?php
        }
    }
}

Yii::app()->clientScript->registerScript('dropboxLinks', "
	$('[dropboxpath]').click(function() {
		var url = '".$this->createUrl('/site/getdropboxlink', array('path'=>'000'))."';
		url = url.replace('000', $(this).attr('dropboxpath'));
		$.post(url, function(data) {
			if (typeof data[0] == 'string') {
				window.location = data[0];
			}
		}, 'json');
	});
", CClientScript::POS_READY);
?>