<?PHP
Yii::app()->clientScript->registerCssFile(Yii::app()->cms->blocs['image']->assetsUrl.'/css/image.css');

$imageSmall = '/'.$bloc->imageHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($bloc->image, 's'));
$imageMedium = '/'.$bloc->imageHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($bloc->image, 'm'));
$imageLarge = '/'.$bloc->imageHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($bloc->image, 'l'));
$imageTitle = CHtml::encode($bloc->image_title);
?>

<div class="image-container" data-image-large="<?PHP echo $imageLarge; ?>" data-image-medium="<?PHP echo $imageMedium; ?>" data-image-small="<?PHP echo $imageSmall; ?>">
	
	<img src="<?PHP echo Yii::app()->baseUrl.$imageSmall; ?>" alt="<?php echo $imageTitle; ?>" title="<?php echo $imageTitle; ?>" class="img-responsive" />

</div>

<?php Yii::app()->clientScript->registerScript('blocImageResponsive', '

/////////////////////////////////// Bloc Image ///////////////////////////////
var twoColumnLayout = detectColumn();
var widthOffsets = new Array(560, 768, 992);
var currentOffset;

$(window).resize(function(e)
{
	var windowWidth = window.innerWidth;
	
	if (
		(windowWidth < widthOffsets[0] && currentOffset != 0) || 
		(windowWidth >= widthOffsets[0] && windowWidth < widthOffsets[1] &&  currentOffset != 1) || 
		(windowWidth >= widthOffsets[1] && windowWidth < widthOffsets[2] &&  currentOffset != 2) || 
		(windowWidth >= widthOffsets[2] &&  currentOffset != 3)
	)
	{
		setCurrentOffset(windowWidth);
		detectImageSrc();
	}
});

function detectImageSrc()
{
	$(".bloc-image .image-container").each(function()
	{
		var imageSrc;
		var elImage = $(this).children("img");
		
		if (currentOffset == 0) {
			imageSrc = $(this).data("imageSmall");
		} else if (currentOffset == 1 || (currentOffset == 3 && twoColumnLayout)) {
			imageSrc = $(this).data("imageMedium");
		} else if (currentOffset == 2 || (currentOffset == 3 && !twoColumnLayout)) {
			imageSrc = $(this).data("imageLarge");
		}
		
		elImage.attr("src", imageSrc);
	});
}

function setCurrentOffset(windowWidth)
{
	if (windowWidth < widthOffsets[0]) {
		currentOffset = 0;
	} else if (windowWidth >= widthOffsets[0] && windowWidth < widthOffsets[1]) {
		currentOffset = 1;
	} else if (windowWidth >= widthOffsets[1] && windowWidth < widthOffsets[2]) {
		currentOffset = 2;
	} else {
		currentOffset = 3;
	}
}

function detectColumn()
{
	var column1 = $("#column1");
	
	if (column1.length > 0) {
		return true;
	} else {
		return false;
	}
}

setCurrentOffset(window.innerWidth);
detectImageSrc();

', CClientScript::POS_READY); ?>