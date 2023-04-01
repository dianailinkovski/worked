<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->cms->blocs['people']->assetsUrl.'/css/people.css');

$numberOfColumns = $bloc->columns;

if ($numberOfColumns > 1):
	if ($numberOfColumns == 2):
		$containerClasses = "col-sm-6";
	elseif ($numberOfColumns == 3):
		$containerClasses = "col-sm-6 col-md-4";
	endif;
	?>

<div class="row">

<?PHP
endif;

$colIndex = 1;
foreach ($bloc->people as $person):
	
	$personName = CHtml::encode($person->name);
	$personImage = ($person->image == '') ? Yii::app()->cms->blocs['people']->assetsUrl.'/images/default.jpg' : '/'.$person->imageHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($person->image, 'm'));
	//$personImage = ($person->image == '') ? '' : '/'.$person->imageHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($person->image, 'm'));

	if ($numberOfColumns > 1):
	?>
	
	<div class="<?PHP echo $containerClasses; ?>">
	
	<?PHP endif; ?>

		<dl class="vcard<?PHP echo ($personImage != "") ? " image-layout" : ""; ?>">

			<?PHP if ($personImage != ""): ?>
			<dd class="photo"><img src="<?PHP echo Yii::app()->baseUrl.$personImage; ?>" alt="<?php echo $personName; ?>" title="<?php echo $personName; ?>" class="img-responsive"></dd>
			<?PHP endif; ?>

			<dt class="fn"><?php echo $personName; ?></dt>

			<dd class="org"><?PHP echo Yii::app()->name; ?></dd>
		
			<?PHP if ($person->title != ""): ?>
			<dd class="title"><?php echo CHtml::encode($person->title); ?></dd>
			<?PHP endif; ?>
			
			<?PHP if ($person->department != ""): ?>
			<dd class="category department"><?php echo CHtml::encode($person->department); ?></dd>
			<?PHP endif; ?>
			
			<?PHP if ($person->description != ""): ?>
			<dd class="description"><?php echo CHtml::encode($person->description); ?></dd>
			<?PHP endif; ?>
			
			<?PHP if ($person->telephone != ""): ?>
			<dd class="tel phone1"><span class="vcard-label"><?PHP echo Yii::t('blocs', 'Téléphone'); ?> :</span><?php echo CHtml::encode($person->telephone); ?></dd>
			<?PHP endif; ?>
			
			<?PHP if ($person->telephone2 != ""): ?>
			<dd class="tel phone2"><?php echo CHtml::encode($person->telephone2); ?></dd>
			<?PHP endif; ?>
			
			<?PHP if ($person->fax != ""): ?>
			<dd class="fax"><span class="vcard-label"><?PHP echo Yii::t('blocs', 'Télécopieur'); ?> :</span><?php echo CHtml::encode($person->fax); ?></dd>
			<?PHP endif; ?>
			
			<?PHP 
			if ($person->email != ""): 
				$personEmail = CHtml::encode($person->email);
			?>
			<dd class="email-desc">
				<span class="vcard-label"><?PHP echo Yii::t('blocs', 'Courriel'); ?> :</span>
				<a class="email" href="mailto:<?php echo $personEmail; ?>"><?php echo $personEmail; ?></a>
			</dd>
			<?PHP endif; ?>
		</dl>
		
	<?PHP
	if ($numberOfColumns > 1):
	?>
		
	</div>
	
		<?PHP
		// Columns clearing and index reset
		if ($numberOfColumns == 2 and  $colIndex%2 == 0):
			echo '<div class="clearfix visible-sm-block visible-md-block visible-lg-block"></div>';
		elseif ($numberOfColumns == 3 and $colIndex%2 == 0):
			echo '<div class="clearfix visible-sm-block"></div>';
		endif;
		if ($numberOfColumns == 3 and $colIndex%3 == 0):
			echo '<div class="clearfix visible-md-block visible-lg-block"></div>';
		endif;

	endif;
	
	$colIndex++;

endforeach;

/*
if ($colIndex > 1):
	echo "</div>";
endif;
*/
if ($numberOfColumns > 1):	// End <div class="row">
?>

</div>

<?PHP
endif;
?>