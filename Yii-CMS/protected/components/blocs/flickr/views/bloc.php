<?php Yii::app()->clientScript->registerCssFile(Yii::app()->cms->blocs['flickr']->assetsUrl.'/css/flickr.css'); ?>

<div class='gallery <?PHP echo ($bloc->show_as_carrousel == 1) ? "type-carousel": "type-list"; ?>'>

	<?php
	$images = "";
	if (empty($bloc->set_id))
	{
		$curl = curl_init("https://api.flickr.com/services/rest/?method=flickr.people.getPublicPhotos&api_key=cd80122ae0a0f805b279d80715dd7861&user_id=".$bloc->user_id."&format=json&per_page=".$bloc->nbr_images. "&nojsoncallback=1");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($curl);
		curl_close($curl);
		$mObject = json_decode( $data, false ); // stdClass object
		if (isset($mObject->photos))
		{
			foreach ($mObject->photos->photo as $item)
			{
				$src = "http://farm" . $item->farm . ".static.flickr.com/" . $item->server . "/" . $item->id . "_" . $item->secret;
				$images .= "<a id='prettyPhoto' href='".$src."_c.jpg'><img src='".$src."_s.jpg' alt='".CHtml::encode($item->title)."'></img>";
			}
		}
	}
	else{
		$curl = curl_init("https://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key=cd80122ae0a0f805b279d80715dd7861&photoset_id=".$bloc->set_id."&format=json&per_page=".$bloc->nbr_images. "&nojsoncallback=1");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($curl);
		curl_close($curl);
		$mObject = json_decode( $data, false ); // stdClass object
		if (isset($mObject->photoset))
		{
			foreach ($mObject->photoset->photo as $item)
			{
				$src = "http://farm" . $item->farm . ".static.flickr.com/" . $item->server . "/" . $item->id . "_" . $item->secret;
				$images .= "<a href='".$src."_c.jpg'><img src='".$src."_s.jpg' alt='".CHtml::encode($item->title)."'></img>";
			}
		}
	}
	$this->beginWidget('ext.prettyPhoto.PrettyPhoto', array(
	  'id'=>'pretty_photo_'.$bloc->id,
	  // prettyPhoto options
	  'options'=>array(
		'opacity'=>0.60,
		'modal'=>true,
		'overlay_gallery'=> false
	  ),
	  'htmlOptions' => array('class'=>'pretty_photo'),
	));

	echo $images;

	$this->endWidget('ext.prettyPhoto.PrettyPhoto');
	
	$setId = ($bloc->set_id == 0) ? "" : $bloc->set_id;
	?>
	
	<a class="flickr_button" href="http://www.flickr.com/photos/<?PHP echo $bloc->user_id; ?>/sets/<?PHP echo $setId; ?>" title="<?PHP echo Yii::t('blocs', 'Voir plus de photos sur flickr'); ?>">
		<img src="http://l.yimg.com/g/images/goodies/white-see-my-photos_fr.png" width="159" height="26" alt="flickr">
	</a>
	

</div>