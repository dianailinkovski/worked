<?php
$newsTitle = CHtml::encode($news->title);
$newsImagePath = Yii::app()->request->baseUrl."/".$news->imageHandler->dir."/";

$this->breadcrumbs = Helper::breadcrumbsFromAlias(true);
$this->breadcrumbs[] = $news->title;
$this->pageTitle = Helper::titleFromBreadcrumbs();

Yii::app()->clientScript->registerMetaTag($news->summary, 'description');

// facebook OG Meta
Yii::app()->facebook->ogTags['og:site_name'] = Yii::app()->name;
Yii::app()->facebook->ogTags['og:title'] = $news->title;
Yii::app()->facebook->ogTags['og:type'] = "article";
Yii::app()->facebook->ogTags['og:description'] = $news->summary;
Yii::app()->facebook->ogTags['og:image'] = ($news->image != "") ? "http://".Yii::app()->request->serverName.$newsImagePath.Helper::encodeFileName(Helper::fileSuffix($news->image, 'm')) : "http://".Yii::app()->request->serverName.Yii::app()->request->baseUrl."/images/admin/admin_logo.jpg";
?>


<article class="mod-news mod-type-detail<?php echo ($news->image != "" ? " article-image-layout" : ""); ?>">

	<header>
		<h1 class="page-title"><?PHP echo $newsTitle; ?></h1>
		<p class="article-date"><?php echo Yii::t('newsModule.common', 'Publiée le')." ".Helper::formatDate($news->date, "reg"); ?></p>
	</header>
	
	<!-- Social networks -->
	<ul id="social-network-share-buttons" class="clearfix">
	
		<li id="facebook-share-button">
			<div class="fb-share-button" data-href="http://<?PHP echo Yii::app()->request->serverName.'/'.Yii::app()->request->baseUrl.Yii::app()->request->pathInfo; ?>" data-layout="button_count"></div>
		</li>
		
		<li>
			<script src="https://apis.google.com/js/platform.js" async defer>{lang: 'fr'}</script>
			<div class="g-plus" data-action="share" data-annotation="bubble"></div>
		</li>
		
		<li>
			<a class="twitter-share-button" href="https://twitter.com/share" data-url="http://<?PHP echo Yii::app()->request->serverName.'/'.Yii::app()->request->baseUrl.Yii::app()->request->pathInfo; ?>" data-text="<?PHP echo $newsTitle; ?>"  data-count="horizontal">Tweet</a>
			<script type="text/javascript">
				window.twttr=(function(d,s,id){var t,js,fjs=d.getElementsByTagName(s)[0];if(d.getElementById(id)){return}js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);return window.twttr||(t={_e:[],ready:function(f){t._e.push(f)}})}(document,"script","twitter-wjs"));
			</script>
		</li>
		
	</ul>
	<!-- /Social networks -->
	
	<div class="row">
	
		<?PHP if ($news->image != ""): ?>
		<div class="article-image col-sm-4 col-sm-push-8">
			
			<?PHP
			$this->beginWidget('ext.prettyPhoto.PrettyPhoto', array(
			  'id'=>'pretty_photo',
			  'options'=>Yii::app()->params['prettyPhotoOptions'],
			));
			?>
	
			<a href="<?PHP echo $newsImagePath.Helper::encodeFileName(Helper::fileSuffix($news->image, 'l')); ?>" title="<?PHP echo $newsTitle; ?>">
				<img src="<?PHP echo $newsImagePath.Helper::encodeFileName(Helper::fileSuffix($news->image, 'm')); ?>" alt="<?PHP echo $newsTitle; ?>" title="<?php echo Yii::t('newsModule.common', 'Cliquez pour agrandir limage'); ?>" class="img-responsive" />
			</a>
			
			<?PHP
			$this->endWidget('ext.prettyPhoto.PrettyPhoto');
			?>
			
			<p class="article-image-caption"><?php echo CHtml::encode($news->image_label); ?></p>
			
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
			'parentId'=>$news->id,
			'uniqueId'=>'news',
		));
		
		
		// Source
		if ($news->source != ""): ?>
		
		<footer>
							
			<p class="article-source">
				Source : 
				<?PHP
				if ($news->source_url != ""):
				?>
				<a href="<?php echo CHtml::encode($news->source_url); ?>" title="<?php echo Yii::t('newsModule.common', 'Afficher la source de la nouvelle'); ?>" target="_blank"><?php echo CHtml::encode($news->source); ?></a>
				<?PHP
				else:
					echo CHtml::encode($news->source);
				endif;
				?>
			</p>
	
		</footer>
		
		<?PHP endif; ?>
			
		</div>
		
	</div>
		
</article>

<a class="back" href="<?PHP echo $this->createUrl('index', array('cms_section_id'=>$news->section_id)); ?>"><?php echo Yii::t('newsModule.common', 'Retour aux nouvelles'); ?></a>
