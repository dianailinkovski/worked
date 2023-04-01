<?php 
Yii::app()->clientScript->registerCssFile(Yii::app()->cms->blocs['achievement']->assetsUrl.'/css/achievement.css');

$galleryTitle = CHtml::encode($bloc->name);
$galleryTitleEncode = mb_strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/u'), array('', '-', ''), Helper::removeAccents($galleryTitle)));

// Obtention des photos de l'album flickr.
$images = "";
$photosArray = array();

if (empty($bloc->set_id))
	$curlUrl = 'https://api.flickr.com/services/rest/?method=flickr.people.getPublicPhotos&api_key=cd80122ae0a0f805b279d80715dd7861&user_id='.$bloc->user_id.'&extras=url_m%2Curl_z%2Curl_c%2Curl_l&format=json&nojsoncallback=1';
else
	$curlUrl = 'https://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key=cd80122ae0a0f805b279d80715dd7861&photoset_id='.$bloc->set_id.'&extras=url_m%2Curl_z%2Curl_c%2Curl_l&format=json&nojsoncallback=1';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $curlUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$responseRaw = curl_exec($ch);

if (curl_errno($ch))
{
	$curlError = curl_error($ch);
	curl_close($ch);
            
	throw new CHttpException(500, $curlError);
}
else {
	curl_close($ch);
	$mObject = json_decode($responseRaw, false);
}

if (empty($bloc->set_id))
	$photosArray = $mObject->photos->photo;
else
	$photosArray = $mObject->photoset->photo;

// Listing des photos sous forme de array.
$photos = array();
foreach ($photosArray as $item):
	
	// Trouve la plus grande taille disponible pour la version agrandie.
	if (isset($item->url_l)):
		$src = $item->url_l;
	elseif (isset($item->url_c)):
		$src = $item->url_c;
	elseif (isset($item->url_z)):
		$src = $item->url_z;
	else:
		$src = $item->url_m;
	endif;

	$photos[] = array("thumb"=>$item->url_m, "full"=>$src, "title"=>CHtml::encode($item->title), "isprimary"=>(isset($item->isprimary) ? $item->isprimary : null));
endforeach;

if (!empty($bloc->set_id))
{
	// Tri du array pour avoir la photo de couverture en premier.
	foreach ($photos as $key => $row) {
	    $title[$key]  = $row['title'];
	    $isprimary[$key] = $row['isprimary'];
	}
	array_multisort($isprimary, SORT_DESC, $title, SORT_ASC, $photos);
}

?>

<dl class="photoset">
							
	<dt><?php echo $galleryTitle; ?></dt>
	
	<dd class="photoset-cover">
		<a title="<?PHP echo Yii::t('blocs', 'Afficher les photos de cet album'); ?>" href="<?PHP echo "#".$galleryTitleEncode; ?>" data-lightbox="<?PHP echo $galleryTitleEncode; ?>">
			<img class="img-responsive" alt="<?php echo $galleryTitle; ?>" src="<?PHP echo $photos[0]["thumb"]; ?>">
		</a>
	</dd>
	
	<dd class="photoset-description">
		<?PHP echo $bloc->description; ?>
	</dd>
	
	<dd class="photoset-photos">
		
		<a class="btn btn-success btn-xs photoset-link" title="<?PHP echo Yii::t('blocs', 'Afficher les photos de cet album'); ?>" href="<?PHP echo "#".$galleryTitleEncode; ?>" data-lightbox="<?PHP echo $galleryTitleEncode; ?>"><?PHP echo Yii::t('blocs', 'Voir les photos'); ?></a>
		
		<ul id="<?PHP echo $galleryTitleEncode; ?>" title="<?php echo $galleryTitle; ?>">
			<?PHP foreach ($photos as $photo): ?>
			<li><a href="<?PHP echo $photo["full"]; ?>"><?PHP echo $photo["title"]; ?></a></li>
			<?PHP endforeach; ?>
		</ul>
		
	</dd>
	
</dl>

<?PHP
$this->widget('ext.prettyPhoto.PrettyPhoto', array('id'=>'pretty_photo_'.$galleryTitleEncode));
Yii::app()->clientScript->registerScript('prettyPhotoBinding', "
	
	$('.photoset-photos ul').each(function()
	{
		var photosetId = $(this).attr('id');
		var photosetName = $(this).attr('title');
		var photosetEntry = $(this).find('li a');

		var photosetEntryTitles = new Array;
		var photosetEntryDescriptions = new Array;
		var photosetEntryUrls = new Array;

		photosetEntry.each(function(index)
		{
			photosetEntryTitles[index] = photosetName;
			photosetEntryDescriptions[index] = $(this).text();
			photosetEntryUrls[index] = $(this).attr('href');
		});
		
		$(\"a[data-lightbox^='\"+photosetId+\"']\").bind('click', function()
		{
			$.prettyPhoto.open(photosetEntryUrls, photosetEntryTitles, photosetEntryDescriptions);
			
			return false;
		});
		
	});
	
	", CClientScript::POS_READY);
?>