<?PHP
Yii::app()->clientScript->registerCssFile(Yii::app()->cms->blocs['feature']->assetsUrl.'/css/feature.css');

$nbrOfFeatures = count($bloc->features);
$featuresLayoutType = $bloc->layout;
$pageLayoutType = ($this->layout == '//layouts/column1') ? 1 : 2;

$xtraClass = "";
if ($nbrOfFeatures > 2 and $pageLayoutType == 1) $xtraClass = " col-md-4";
?>

<div class="row">

<?PHP
$index = 1;
foreach ($bloc->features as $feature):

	$featureImage = ($feature->image == '') ? '' : '/'.$feature->imageHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($feature->image, 'm'));
	?>

	<?PHP if ($featuresLayoutType == 1):	// Displaying features column style. ?>
	
		<div class="col-sm-6<?PHP echo $xtraClass; ?>">
		
			<div class="feature">
				
				<?PHP if ($featureImage != ''): ?>
				<img src="<?PHP echo $featureImage; ?>" alt="<?PHP echo CHtml::encode($feature->title); ?>">
				<?PHP endif; ?>
				
				<h3><?PHP echo CHtml::encode($feature->title); ?></h3>
	
				<?PHP echo $feature->description; ?>
	
			</div>
			
		</div>
		
		<?PHP if ($index % 2 == 0): // Clearing after 2 columns on small screen. ?>
		<div class="clearfix visible-sm-block"></div>
		<?PHP endif; ?>
	
		<?PHP if ($pageLayoutType == 1 and $index % 3 == 0): // Clearing after 3 columns on medium or large screen (if page layout is 1 column). ?>
		<div class="clearfix visible-md-block visible-lg-block"></div>
		<?PHP elseif ($pageLayoutType == 2 and $index % 2 == 0): // Clearing after 2 columns on medium or large screen (if page layout is 2 columns). ?>
		<div class="clearfix visible-md-block visible-lg-block"></div>
		<?PHP
		endif;
	
	else:		// Displaying features row style.
	?>
	
		<div class="feature feature-layout-row clearfix">
					
			<?PHP if ($featureImage != ''): ?>
			<div class="col-sm-4">
				<img src="<?PHP echo $featureImage; ?>" alt="<?PHP echo CHtml::encode($feature->title); ?>">
			</div>
			<?PHP endif; ?>
			
			<div class="col-sm-8">
				<h3><?PHP echo CHtml::encode($feature->title); ?></h3>
	
				<?PHP echo $feature->description; ?>
			</div>
	
		</div>
	
	<?PHP
	endif;
	
	$index++;
endforeach;
?>

</div>