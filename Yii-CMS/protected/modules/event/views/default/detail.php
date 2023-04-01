<?php
$eventTitle = CHtml::encode($event->title);
$eventLocation = CHtml::encode($event->location);
$eventImagePath = Yii::app()->request->baseUrl."/".$event->imageHandler->dir."/";

if ($archives !== null):
	$this->breadcrumbs = Helper::breadcrumbsFromAlias(true);
	$this->breadcrumbs['Archives'] = array('index', 'archives'=>$archives);
else:
	$this->breadcrumbs = Helper::breadcrumbsFromAlias(true);
endif;
$this->breadcrumbs[] = $event->title;

$this->pageTitle = Helper::titleFromBreadcrumbs();

Yii::app()->clientScript->registerMetaTag($event->summary, 'description');

// facebook OG Meta
Yii::app()->facebook->ogTags['og:site_name'] = Yii::app()->name;
Yii::app()->facebook->ogTags['og:title'] = $event->title;
Yii::app()->facebook->ogTags['og:type'] = "activity";
Yii::app()->facebook->ogTags['og:description'] = $event->summary;
Yii::app()->facebook->ogTags['og:image'] = ($event->image != "") ? "http://".Yii::app()->request->serverName.$eventImagePath.Helper::encodeFileName(Helper::fileSuffix($event->image, 'm')) : "http://".Yii::app()->request->serverName.Yii::app()->request->baseUrl."/images/admin/admin_logo.jpg";
?>


<article class="mod-event mod-type-detail<?php echo ($event->image != "" ? " article-image-layout" : ""); ?>">

	<header>
		<h1 class="page-title"><?PHP echo $eventTitle; ?></h1>
		<div class="fb-like" data-send="false" data-show-faces="false"></div>
		<div class="event-specs">
			<p class="article-date"><span class="article-label"><?PHP echo Yii::t('eventModule.common', 'Quand'); ?> : </span>
			<?php
			$start_date = substr($event->date_start, 0, 10);
			$end_date = substr($event->date_end, 0, 10);
			$start_time = substr($event->date_start, 11, 5);
			$end_time = substr($event->date_end, 11, 5);
			
			if ($start_date == $end_date):
				echo Yii::t('eventModule.common', 'Le')." ".Helper::formatDate($start_date, "reg");
				if($start_time != "00:00" && $end_time != "00:00"):
					echo ", ".Yii::t('eventModule.common', 'de')." ".$start_time." ".Yii::t('eventModule.common', 'à')." ".$end_time;
				elseif($start_time != "00:00" && $end_time == "00:00"):
					echo ", ".Yii::t('eventModule.common', 'à')." ".$start_time;
				endif;
			else:
				echo Yii::t('eventModule.common', 'Du')." ".Helper::formatDate($start_date, "reg")." ".Yii::t('eventModule.common', 'au')." ".Helper::formatDate($end_date, "reg");
				if($start_time != "00:00" && $end_time != "00:00"):
					echo ", ".Yii::t('eventModule.common', 'de')." ".$start_time." ".Yii::t('eventModule.common', 'à')." ".$end_time;
				elseif($start_time != "00:00" && $end_time == "00:00"):
					echo ", ".Yii::t('eventModule.common', 'à')." ".$start_time;
				endif;
			endif;
			?>
			</p>
			<p class="event-location"><span class="article-label"><?PHP echo Yii::t('eventModule.common', 'Où'); ?> : </span><?php echo $eventLocation; ?> 
			<?php 
			preg_match('/src="([^"]+)/u', $event->location_map, $mapUrl); 
			
			if (isset($mapUrl[1])):

				$mapUrl = CHtml::encode($mapUrl[1]);
				?>
				<a href="<?php echo $mapUrl; ?>" title="<?PHP echo Yii::t('eventModule.common', 'Afficher la carte de l’emplacement'); ?>" id="event-map-link"><span class="glyphicon glyphicon-map-marker"></span> <?PHP echo Yii::t('eventModule.common', 'Localiser'); ?></a>
			</p>
			<!-- Modal content (Google Map) -->
			<div class="modal fade" id="event-map-modal" tabindex="-1" role="dialog" aria-labelledby="Google Map" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title"><?php echo $eventLocation; ?></h4>
						</div>
						<div class="modal-body">
							<div class="map-container">
								<iframe id="event-map" src=""></iframe>
								<div class="enlarge-link"><small><a href="<?php echo $mapUrl; ?>" target="_blank"><?PHP echo Yii::t('eventModule.common', 'Afficher sur Google Maps'); ?></a></small></div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal"><?PHP echo Yii::t('eventModule.common', 'Fermer'); ?></button>
						</div>
					</div>
				</div>
			</div>
			<?PHP
			else:
				echo "</p>";
			endif;
			?>
		</div>
	</header>
	
	<div class="row">
	
		<?PHP if ($event->image != ""): ?>
		<div class="article-image col-sm-4 col-sm-push-8">
			
			<?PHP
			$this->beginWidget('ext.prettyPhoto.PrettyPhoto', array(
			  'id'=>'pretty_photo',
			  'options'=>Yii::app()->params['prettyPhotoOptions'],
			));
			?>
	
			<a href="<?PHP echo $eventImagePath.Helper::encodeFileName(Helper::fileSuffix($event->image, 'l')); ?>" title="<?PHP echo $eventTitle; ?>">
				<img src="<?PHP echo $eventImagePath.Helper::encodeFileName(Helper::fileSuffix($event->image, 'm')); ?>" alt="<?PHP echo $eventTitle; ?>" title="<?php echo Yii::t('eventModule.common', 'Cliquez pour agrandir limage'); ?>" class="img-responsive" />
			</a>
			
			<?PHP
			$this->endWidget('ext.prettyPhoto.PrettyPhoto');
			?>
			
			<p class="article-image-caption"><?php echo CHtml::encode($event->image_label); ?></p>
			
		</div>
		<?PHP
			$abstractContainerClass = "col-sm-8 col-sm-pull-4";
		else:
			$abstractContainerClass = "col-sm-12";
		endif;
		?>
		
		 <div class="<?PHP echo $abstractContainerClass; ?>">
		
		<?PHP
		// Blocs
		$this->widget('application.components.widgets.Blocs.BlocsWidget', array(
			'parentId'=>$event->id,
			'uniqueId'=>'event',
		));
		?>
		
		</div>
		
	</div>
	
</article>

<a class="back" href="<?php echo ($archives === null ? $this->createUrl('index', array('cms_section_id'=>$event->section_id)) : $this->createUrl('index', array('cms_section_id'=>$event->section_id, 'archives'=>$archives))); ?>">&lt; <?php echo Yii::t('eventModule.common', 'Retour aux événements'); ?></a>


<?php
// La map Google doit être loadée après l'ouverture de la modal sinon l'affichage bug.
Yii::app()->clientScript->registerScript('loadGoogleMap', "
	
	$('#event-map-link').click(function(e){
		var frameSrc = $(this).attr('href');
		
		$('#event-map-modal').on('show.bs.modal', function () {
			$('#event-map').attr('src',frameSrc);
		});
		$('#event-map-modal').modal('show');
		
		e.preventDefault();
	});
	
	", CClientScript::POS_READY);
?>
